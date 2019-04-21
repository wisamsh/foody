<?php
/*
Plugin Name: WordPress Importer
Plugin URI: https://wordpress.org/plugins/wordpress-importer/
Description: Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.
Author: wordpressdotorg
Author URI: https://wordpress.org/
Version: 0.6.4
Text Domain: wordpress-importer
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/** Display verbose errors */
define('FOODY_IMPORT_DEBUG', defined('WP_DEBUG') && WP_DEBUG);

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if (!class_exists('WP_Importer')) {
    $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
    if (file_exists($class_wp_importer))
        /** @noinspection PhpIncludeInspection */
        require $class_wp_importer;
}

// include WXR file parsers
require dirname(__FILE__) . '/parsers.php';

/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @package WordPress
 * @subpackage Importer
 */
if (class_exists('WP_Importer')) {
    class Foody_Import extends WP_Importer
    {
        var $max_wxr_version = 1.2; // max. supported WXR version

        var $id; // WXR attachment ID

        // information to import from WXR file
        var $version;
        var $authors = array();
        var $posts = array();
        var $terms = array();
        var $categories = array();
        var $tags = array();
        var $base_url = '';

        // mappings from old information to new
        var $processed_authors = array();
        var $author_mapping = array();
        var $processed_terms = array();
        var $processed_posts = array();
        var $post_orphans = array();
        var $processed_menu_items = array();
        var $menu_item_orphans = array();
        var $missing_menu_items = array();

        var $fetch_attachments = false;
        var $url_remap = array();
        var $featured_images = array();

        public static function isDebug()
        {
            return defined(FOODY_IMPORT_DEBUG) && FOODY_IMPORT_DEBUG;
        }

        /**
         * Registered callback function for the WordPress Importer
         *
         * Manages the three separate stages of the WXR import process
         */
        function dispatch()
        {
            $this->header();

            $step = empty($_GET['step']) ? 0 : (int)$_GET['step'];
            switch ($step) {
                case 0:
                    $this->greet();
                    break;
                case 1:
                    check_admin_referer('import-upload');
                    if ($this->handle_upload())
                        $this->import_options();
                    break;
                case 2:
                    check_admin_referer('import-wordpress');
                    $this->fetch_attachments = (!empty($_POST['fetch_attachments']) && $this->allow_fetch_attachments());
                    $this->id = (int)$_POST['import_id'];
                    $file = get_attached_file($this->id);
                    set_time_limit(0);
                    $this->import($file);
                    break;
            }

            $this->footer();
        }

        /**
         * The main controller for the actual import stage.
         *
         * @param string $data WXR data
         */
        function import($data)
        {
            add_filter('import_post_meta_key', array($this, 'is_valid_meta_key'));
            add_filter('http_request_timeout', array(&$this, 'bump_request_timeout'));

            $this->import_start($data);

            wp_suspend_cache_invalidation(true);
            $this->process_categories();
            $this->process_tags();
            $this->process_terms();
            $this->process_posts();
            wp_suspend_cache_invalidation(false);


            $this->import_end();
        }

        /**
         * Parses the WXR file and prepares us for the task of processing parsed data
         *
         * @param string $data WXR data string
         */
        function import_start($data)
        {

            $import_data = $this->parse($data);

            if (is_wp_error($import_data)) {
                echo '<p><strong>' . __('Sorry, there has been an error.', 'wordpress-importer') . '</strong><br />';
                echo esc_html($import_data->get_error_message()) . '</p>';
                $this->footer();
                Foody_WhiteLabelLogger::error('invalid export file', ['error' => $import_data]);
                die();
            }

            $this->version = $import_data['version'];
            $this->posts = $import_data['posts'];
            $this->terms = $import_data['terms'];
            $this->categories = $import_data['categories'];
            $this->tags = $import_data['tags'];
            $this->base_url = esc_url($import_data['base_url']);

            wp_defer_term_counting(true);
            wp_defer_comment_counting(true);

            do_action('import_start');
        }

        /**
         * Performs post-import cleanup of files and the cache
         */
        function import_end()
        {
            wp_import_cleanup($this->id);

            wp_cache_flush();
            foreach (get_taxonomies() as $tax) {
                delete_option("{$tax}_children");
                _get_term_hierarchy($tax);
            }

            wp_defer_term_counting(false);
            wp_defer_comment_counting(false);

            echo '<p>' . __('All done.', 'wordpress-importer') . ' <a href="' . admin_url() . '">' . __('Have fun!', 'wordpress-importer') . '</a>' . '</p>';
            echo '<p>' . __('Remember to update the passwords and roles of imported users.', 'wordpress-importer') . '</p>';

            do_action('import_end');
        }

        /**
         * Handles the WXR upload and initial parsing of the file to prepare for
         * displaying author import options
         *
         * @return bool False if error uploading or invalid file, true otherwise
         */
        function handle_upload()
        {
            $file = wp_import_handle_upload();

            if (isset($file['error'])) {
                echo '<p><strong>' . __('Sorry, there has been an error.', 'wordpress-importer') . '</strong><br />';
                echo esc_html($file['error']) . '</p>';
                return false;
            } else if (!file_exists($file['file'])) {
                echo '<p><strong>' . __('Sorry, there has been an error.', 'wordpress-importer') . '</strong><br />';
                printf(__('The export file could not be found at <code>%s</code>. It is likely that this was caused by a permissions problem.', 'wordpress-importer'), esc_html($file['file']));
                echo '</p>';
                return false;
            }

            $this->id = (int)$file['id'];
            $import_data = $this->parse($file['file']);
            if (is_wp_error($import_data)) {
                echo '<p><strong>' . __('Sorry, there has been an error.', 'wordpress-importer') . '</strong><br />';
                echo esc_html($import_data->get_error_message()) . '</p>';
                return false;
            }

            $this->version = $import_data['version'];
            if ($this->version > $this->max_wxr_version) {
                echo '<div class="error"><p><strong>';
                printf(__('This WXR file (version %s) may not be supported by this version of the importer. Please consider updating.', 'wordpress-importer'), esc_html($import_data['version']));
                echo '</strong></p></div>';
            }

            $this->get_authors_from_import($import_data);

            return true;
        }

        /**
         * Retrieve authors from parsed WXR data
         *
         * Uses the provided author information from WXR 1.1 files
         * or extracts info from each post for WXR 1.0 files
         *
         * @param array $import_data Data returned by a WXR parser
         */
        function get_authors_from_import($import_data)
        {
            if (!empty($import_data['authors'])) {
                $this->authors = $import_data['authors'];
                // no author information, grab it from the posts
            } else {
                foreach ($import_data['posts'] as $post) {
                    $login = sanitize_user($post['post_author'], true);
                    if (empty($login)) {
                        printf(__('Failed to import author %s. Their posts will be attributed to the current user.', 'wordpress-importer'), esc_html($post['post_author']));
                        echo '<br />';
                        continue;
                    }

                    if (!isset($this->authors[$login]))
                        $this->authors[$login] = array(
                            'author_login' => $login,
                            'author_display_name' => $post['post_author']
                        );
                }
            }
        }

        /**
         * Display pre-import options, author importing/mapping and option to
         * fetch attachments
         */
        function import_options()
        {
            $j = 0;
            ?>
            <form action="<?php echo admin_url('admin.php?import=wordpress&amp;step=2'); ?>" method="post">
                <?php wp_nonce_field('import-wordpress'); ?>
                <input type="hidden" name="import_id" value="<?php echo $this->id; ?>"/>

                <?php if (!empty($this->authors)) : ?>
                    <h3><?php _e('Assign Authors', 'wordpress-importer'); ?></h3>
                    <p><?php _e('To make it easier for you to edit and save the imported content, you may want to reassign the author of the imported item to an existing user of this site. For example, you may want to import all the entries as <code>admin</code>s entries.', 'wordpress-importer'); ?></p>
                    <?php if ($this->allow_create_users()) : ?>
                        <p><?php printf(__('If a new user is created by WordPress, a new password will be randomly generated and the new user&#8217;s role will be set as %s. Manually changing the new user&#8217;s details will be necessary.', 'wordpress-importer'), esc_html(get_option('default_role'))); ?></p>
                    <?php endif; ?>
                    <ol id="authors">
                        <?php foreach ($this->authors as $author) : ?>
                            <li><?php $this->author_select($j++, $author); ?></li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>

                <?php if ($this->allow_fetch_attachments()) : ?>
                    <h3><?php _e('Import Attachments', 'wordpress-importer'); ?></h3>
                    <p>
                        <input type="checkbox" value="1" name="fetch_attachments" id="import-attachments"/>
                        <label for="import-attachments"><?php _e('Download and import file attachments', 'wordpress-importer'); ?></label>
                    </p>
                <?php endif; ?>

                <p class="submit"><input type="submit" class="button"
                                         value="<?php esc_attr_e('Submit', 'wordpress-importer'); ?>"/></p>
            </form>
            <?php
        }

        /**
         * Display import options for an individual author. That is, either create
         * a new user based on import info or map to an existing user
         *
         * @param int $n Index for each author in the form
         * @param array $author Author information, e.g. login, display name, email
         */
        function author_select($n, $author)
        {
            _e('Import author:', 'wordpress-importer');
            echo ' <strong>' . esc_html($author['author_display_name']);
            if ($this->version != '1.0') echo ' (' . esc_html($author['author_login']) . ')';
            echo '</strong><br />';

            if ($this->version != '1.0')
                echo '<div style="margin-left:18px">';

            $create_users = $this->allow_create_users();
            if ($create_users) {
                if ($this->version != '1.0') {
                    _e('or create new user with login name:', 'wordpress-importer');
                    $value = '';
                } else {
                    _e('as a new user:', 'wordpress-importer');
                    $value = esc_attr(sanitize_user($author['author_login'], true));
                }

                echo ' <input type="text" name="user_new[' . $n . ']" value="' . $value . '" /><br />';
            }

            if (!$create_users && $this->version == '1.0')
                _e('assign posts to an existing user:', 'wordpress-importer');
            else
                _e('or assign posts to an existing user:', 'wordpress-importer');
            wp_dropdown_users(array('name' => "user_map[$n]", 'multi' => true, 'show_option_all' => __('- Select -', 'wordpress-importer')));
            echo '<input type="hidden" name="imported_authors[' . $n . ']" value="' . esc_attr($author['author_login']) . '" />';

            if ($this->version != '1.0')
                echo '</div>';
        }

        /**
         * Create new posts based on import information
         *
         * Posts marked as having a parent which doesn't exist will become top level items.
         * Doesn't create a new post if: the post type doesn't exist, the given post ID
         * is already noted as imported or a post with the same title and date already exists.
         * Note that new/updated terms, comments and meta are imported for the last of the above.
         */
        function process_posts()
        {
            $this->posts = apply_filters('wp_import_posts', $this->posts);

            foreach ($this->posts as $post) {
                $post = apply_filters('wp_import_post_data_raw', $post);

                if (!post_type_exists($post['post_type'])) {
                    printf(__('Failed to import &#8220;%s&#8221;: Invalid post type %s', 'wordpress-importer'),
                        esc_html($post['post_title']), esc_html($post['post_type']));
                    echo '<br />';
                    do_action('wp_import_post_exists', $post);
                    continue;
                }

                if (isset($this->processed_posts[$post['post_id']]) && !empty($post['post_id']))
                    continue;

                if ($post['status'] == 'auto-draft')
                    continue;

                $post_type_object = get_post_type_object($post['post_type']);

                $post_exists = post_exists($post['post_title'], '', $post['post_date']);

                /**
                 * Filter ID of the existing post corresponding to post currently importing.
                 *
                 * Return 0 to force the post to be imported. Filter the ID to be something else
                 * to override which existing post is mapped to the imported post.
                 *
                 * @see post_exists()
                 * @since 0.6.2
                 *
                 * @param int $post_exists Post ID, or 0 if post did not exist.
                 * @param array $post The post array to be inserted.
                 */
                $post_exists = apply_filters('wp_import_existing_post', $post_exists, $post);

                if ($post_exists && get_post_type($post_exists) == $post['post_type']) {
                    printf(__('%s &#8220;%s&#8221; already exists.', 'wordpress-importer'), $post_type_object->labels->singular_name, esc_html($post['post_title']));
                    echo '<br />';
                    $comment_post_ID = $post_id = $post_exists;
                    $this->processed_posts[intval($post['post_id'])] = intval($post_exists);
                } else {
                    $post_parent = (int)$post['post_parent'];
                    if ($post_parent) {
                        // if we already know the parent, map it to the new local ID
                        if (isset($this->processed_posts[$post_parent])) {
                            $post_parent = $this->processed_posts[$post_parent];
                            // otherwise record the parent for later
                        } else {
                            $this->post_orphans[intval($post['post_id'])] = $post_parent;
                            $post_parent = 0;
                        }
                    }

                    // map the post author
                    $author = sanitize_user($post['post_author'], true);
                    if (isset($this->author_mapping[$author]))
                        $author = $this->author_mapping[$author];
                    else
                        $author = (int)get_current_user_id();

                    $postdata = array(
                        'import_id' => $post['post_id'], 'post_author' => $author, 'post_date' => $post['post_date'],
                        'post_date_gmt' => $post['post_date_gmt'], 'post_content' => $post['post_content'],
                        'post_excerpt' => $post['post_excerpt'], 'post_title' => $post['post_title'],
                        'post_status' => $post['status'], 'post_name' => $post['post_name'],
                        'comment_status' => $post['comment_status'], 'ping_status' => $post['ping_status'],
                        'guid' => $post['guid'], 'post_parent' => $post_parent, 'menu_order' => $post['menu_order'],
                        'post_type' => $post['post_type'], 'post_password' => $post['post_password']
                    );

                    $original_post_ID = $post['post_id'];
                    $postdata = apply_filters('wp_import_post_data_processed', $postdata, $post);

                    $postdata = wp_slash($postdata);


                    $comment_post_ID = $post_id = wp_insert_post($postdata, true);
                    do_action('wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post);


                    if (is_wp_error($post_id)) {
                        printf(__('Failed to import %s &#8220;%s&#8221;', 'wordpress-importer'),
                            $post_type_object->labels->singular_name, esc_html($post['post_title']));
                        if (defined('IMPORT_DEBUG') && IMPORT_DEBUG)
                            echo ': ' . $post_id->get_error_message();
                        echo '<br />';
                        continue;
                    }

                    if ($post['is_sticky'] == 1)
                        stick_post($post_id);
                }

                // map pre-import ID to local ID
                $this->processed_posts[intval($post['post_id'])] = (int)$post_id;

                if (!isset($post['terms']))
                    $post['terms'] = array();

                $post['terms'] = apply_filters('wp_import_post_terms', $post['terms'], $post_id, $post);

                // add categories, tags and other terms
                if (!empty($post['terms'])) {
                    $terms_to_set = array();
                    foreach ($post['terms'] as $term) {
                        // back compat with WXR 1.0 map 'tag' to 'post_tag'
                        $taxonomy = ('tag' == $term['domain']) ? 'post_tag' : $term['domain'];
                        $term_exists = term_exists($term['slug'], $taxonomy);
                        $term_id = is_array($term_exists) ? $term_exists['term_id'] : $term_exists;
                        if (!$term_id) {
                            $t = wp_insert_term($term['name'], $taxonomy, array('slug' => $term['slug']));
                            if (!is_wp_error($t)) {
                                $term_id = $t['term_id'];
                                do_action('wp_import_insert_term', $t, $term, $post_id, $post);
                            } else {
                                printf(__('Failed to import %s %s', 'wordpress-importer'), esc_html($taxonomy), esc_html($term['name']));
                                if (defined('IMPORT_DEBUG') && IMPORT_DEBUG)
                                    echo ': ' . $t->get_error_message();
                                echo '<br />';
                                do_action('wp_import_insert_term_failed', $t, $term, $post_id, $post);
                                continue;
                            }
                        }
                        $terms_to_set[$taxonomy][] = intval($term_id);
                    }

                    foreach ($terms_to_set as $tax => $ids) {
                        $tt_ids = wp_set_post_terms($post_id, $ids, $tax);
                        do_action('wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post);
                    }
                    unset($post['terms'], $terms_to_set);
                }

                if (!isset($post['comments']))
                    $post['comments'] = array();

                $post['comments'] = apply_filters('wp_import_post_comments', $post['comments'], $post_id, $post);

                if (!isset($post['postmeta']))
                    $post['postmeta'] = array();

                $post['postmeta'] = apply_filters('wp_import_post_meta', $post['postmeta'], $post_id, $post);

                // add/update post meta
                if (!empty($post['postmeta'])) {
                    foreach ($post['postmeta'] as $meta) {
                        $key = apply_filters('import_post_meta_key', $meta['key'], $post_id, $post);
                        $value = false;

                        if ('_edit_last' == $key) {
                            $key = false;
                        }

                        if ($key) {
                            // export gets meta straight from the DB so could have a serialized string
                            if (!$value)
                                $value = maybe_unserialize($meta['value']);

                            add_post_meta($post_id, $key, $value);
                            do_action('import_post_meta', $post_id, $key, $value);
                        }
                    }
                }
            }

            unset($this->posts);
        }

        /**
         * Create new categories based on import information
         *
         * Doesn't create a new category if its slug already exists
         */
        function process_categories()
        {
            $this->categories = apply_filters('wp_import_categories', $this->categories);

            if (empty($this->categories))
                return;

            foreach ($this->categories as $cat) {
                // if the category already exists leave it alone
                $term_id = term_exists($cat['category_nicename'], 'category');
                if ($term_id) {
                    if (is_array($term_id)) $term_id = $term_id['term_id'];
                    if (isset($cat['term_id']))
                        $this->processed_terms[intval($cat['term_id'])] = (int)$term_id;
                    continue;
                }

                $category_parent = empty($cat['category_parent']) ? 0 : category_exists($cat['category_parent']);
                $category_description = isset($cat['category_description']) ? $cat['category_description'] : '';
                $catarr = array(
                    'category_nicename' => $cat['category_nicename'],
                    'category_parent' => $category_parent,
                    'cat_name' => $cat['cat_name'],
                    'category_description' => $category_description
                );
                $catarr = wp_slash($catarr);

                $id = wp_insert_category($catarr);
                if (!is_wp_error($id)) {
                    if (isset($cat['term_id']))
                        $this->processed_terms[intval($cat['term_id'])] = $id;
                } else {
                    printf(__('Failed to import category %s', 'wordpress-importer'), esc_html($cat['category_nicename']));
                    if (defined('IMPORT_DEBUG') && IMPORT_DEBUG)
                        echo ': ' . $id->get_error_message();
                    echo '<br />';
                    continue;
                }

                $this->process_termmeta($cat, $id['term_id']);
            }

            unset($this->categories);
        }

        /**
         * Create new post tags based on import information
         *
         * Doesn't create a tag if its slug already exists
         */
        function process_tags()
        {
            $this->tags = apply_filters('wp_import_tags', $this->tags);

            if (empty($this->tags))
                return;

            foreach ($this->tags as $tag) {
                // if the tag already exists leave it alone
                $term_id = term_exists($tag['tag_slug'], 'post_tag');
                if ($term_id) {
                    if (is_array($term_id)) $term_id = $term_id['term_id'];
                    if (isset($tag['term_id']))
                        $this->processed_terms[intval($tag['term_id'])] = (int)$term_id;
                    continue;
                }

                $tag = wp_slash($tag);
                $tag_desc = isset($tag['tag_description']) ? $tag['tag_description'] : '';
                $tagarr = array('slug' => $tag['tag_slug'], 'description' => $tag_desc);

                $id = wp_insert_term($tag['tag_name'], 'post_tag', $tagarr);
                if (!is_wp_error($id)) {
                    if (isset($tag['term_id']))
                        $this->processed_terms[intval($tag['term_id'])] = $id['term_id'];
                } else {
                    printf(__('Failed to import post tag %s', 'wordpress-importer'), esc_html($tag['tag_name']));
                    if (defined('IMPORT_DEBUG') && IMPORT_DEBUG)
                        echo ': ' . $id->get_error_message();
                    echo '<br />';
                    continue;
                }

                $this->process_termmeta($tag, $id['term_id']);
            }

            unset($this->tags);
        }

        /**
         * Create new terms based on import information
         *
         * Doesn't create a term its slug already exists
         */
        function process_terms()
        {
            $this->terms = apply_filters('wp_import_terms', $this->terms);

            if (empty($this->terms))
                return;

            foreach ($this->terms as $term) {
                // if the term already exists in the correct taxonomy leave it alone
                $term_id = term_exists($term['slug'], $term['term_taxonomy']);
                if ($term_id) {
                    if (is_array($term_id)) $term_id = $term_id['term_id'];
                    if (isset($term['term_id']))
                        $this->processed_terms[intval($term['term_id'])] = (int)$term_id;
                    continue;
                }

                if (empty($term['term_parent'])) {
                    $parent = 0;
                } else {
                    $parent = term_exists($term['term_parent'], $term['term_taxonomy']);
                    if (is_array($parent)) $parent = $parent['term_id'];
                }
                $term = wp_slash($term);
                $description = isset($term['term_description']) ? $term['term_description'] : '';
                $termarr = array('slug' => $term['slug'], 'description' => $description, 'parent' => intval($parent));

                $id = wp_insert_term($term['term_name'], $term['term_taxonomy'], $termarr);
                if (!is_wp_error($id)) {
                    if (isset($term['term_id']))
                        $this->processed_terms[intval($term['term_id'])] = $id['term_id'];
                } else {
                    printf(__('Failed to import %s %s', 'wordpress-importer'), esc_html($term['term_taxonomy']), esc_html($term['term_name']));
                    if (defined('IMPORT_DEBUG') && IMPORT_DEBUG)
                        echo ': ' . $id->get_error_message();
                    echo '<br />';
                    continue;
                }

                $this->process_termmeta($term, $id['term_id']);
            }

            unset($this->terms);
        }

        /**
         * Add metadata to imported term.
         *
         * @since 0.6.2
         *
         * @param array $term Term data from WXR import.
         * @param int $term_id ID of the newly created term.
         */
        protected function process_termmeta($term, $term_id)
        {
            if (!isset($term['termmeta'])) {
                $term['termmeta'] = array();
            }

            /**
             * Filters the metadata attached to an imported term.
             *
             * @since 0.6.2
             *
             * @param array $termmeta Array of term meta.
             * @param int $term_id ID of the newly created term.
             * @param array $term Term data from the WXR import.
             */
            $term['termmeta'] = apply_filters('wp_import_term_meta', $term['termmeta'], $term_id, $term);

            if (empty($term['termmeta'])) {
                return;
            }

            foreach ($term['termmeta'] as $meta) {
                /**
                 * Filters the meta key for an imported piece of term meta.
                 *
                 * @since 0.6.2
                 *
                 * @param string $meta_key Meta key.
                 * @param int $term_id ID of the newly created term.
                 * @param array $term Term data from the WXR import.
                 */
                $key = apply_filters('import_term_meta_key', $meta['key'], $term_id, $term);
                if (!$key) {
                    continue;
                }

                // Export gets meta straight from the DB so could have a serialized string
                $value = maybe_unserialize($meta['value']);

                add_term_meta($term_id, $key, $value);

                /**
                 * Fires after term meta is imported.
                 *
                 * @since 0.6.2
                 *
                 * @param int $term_id ID of the newly created term.
                 * @param string $key Meta key.
                 * @param mixed $value Meta value.
                 */
                do_action('import_term_meta', $term_id, $key, $value);
            }
        }

        /**
         * Parse a WXR file
         *
         * @param string $data Path to WXR file for parsing
         * @return WP_Error|array Information gathered from the WXR file
         */
        function parse($data)
        {
            $parser = new Foody_WhiteLabelWXRParser();
            return $parser->parse($data);
        }

        // Display import page title
        function header()
        {
            echo '<div class="wrap">';
            echo '<h2>' . __('Import Foody', 'wordpress-importer') . '</h2>';
        }

        // Close div.wrap
        function footer()
        {
            echo '</div>';
        }

        /**
         * Display introductory text and file upload form
         */
        function greet()
        {
            echo '<div class="narrow">';
            echo '<p>' . __('העלה קובץ WXR', 'wordpress-importer') . '</p>';
            echo '<p>' . __('Choose a WXR (.xml) file to upload, then click Upload file and import.', 'wordpress-importer') . '</p>';
            wp_import_upload_form('admin.php?import=wordpress&amp;step=1');
            echo '</div>';
        }

        /**
         * Decide if the given meta key maps to information we will want to import
         *
         * @param string $key The meta key to check
         * @return string|bool The key if we do want to import, false if not
         */
        function is_valid_meta_key($key)
        {
            // skip attachment metadata since we'll regenerate it from scratch
            // skip _edit_lock as not relevant for import
            if (in_array($key, array('_wp_attached_file', '_wp_attachment_metadata', '_edit_lock')))
                return false;
            return $key;
        }

        /**
         * Decide whether or not the importer is allowed to create users.
         * Default is true, can be filtered via import_allow_create_users
         *
         * @return bool True if creating users is allowed
         */
        function allow_create_users()
        {
            return apply_filters('import_allow_create_users', true);
        }

        /**
         * Decide whether or not the importer should attempt to download attachment files.
         * Default is true, can be filtered via import_allow_fetch_attachments. The choice
         * made at the import options screen must also be true, false here hides that checkbox.
         *
         * @return bool True if downloading attachments is allowed
         */
        function allow_fetch_attachments()
        {
            return apply_filters('import_allow_fetch_attachments', true);
        }

        /**
         * Decide what the maximum file size for downloaded attachments is.
         * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
         *
         * @return int Maximum attachment file size to import
         */
        function max_attachment_size()
        {
            return apply_filters('import_attachment_size_limit', 0);
        }

        /**
         * Added to http_request_timeout filter to force timeout at 60 seconds during import
         * @param $val
         * @return int 60
         */
        function bump_request_timeout($val)
        {
            return 60;
        }

        // return the difference in length between two strings
        function cmpr_strlen($a, $b)
        {
            return strlen($b) - strlen($a);
        }
    }

} // class_exists( 'WP_Importer' )
else {
    Foody_WhiteLabelLogger::error('WP Importer class not found');
}

function foody_importer_init()
{
    load_plugin_textdomain('wordpress-importer');

    /**
     * WordPress Importer object for registering the import callback
     * @global Foody_Import $wp_import
     */
    $GLOBALS['foody_import'] = new Foody_Import();
    register_importer('foody', 'Foody', __('Import <strong>terms</strong> from a WordPress export file.', 'wordpress-importer'), array($GLOBALS['foody_import'], 'dispatch'));
}

add_action('admin_init', 'foody_importer_init');
