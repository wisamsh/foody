<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/15/19
 * Time: 1:44 PM
 */

/** @noinspection PhpUnusedParameterInspection */
define('WXR_VERSION', '1.2');
/**
 * Generates the WXR export file for download.
 *
 * Default behavior is to export all content, however, note that post content will only
 * be exported for post types with the `can_export` argument enabled. Any posts with the
 * 'auto-draft' status will be skipped.
 *
 * @since 2.1.0
 *
 *
 * @param $newBlogId int newly create blog to import content into
 * @throws Exception
 */
function export_import_foody_wp($newBlogId)
{
    global $wpdb;

    // Add more types if relevant
    $post_types = [
        'foody_ingredient'
    ];

    $esses = array_fill(0, count($post_types), '%s');
    $where = $wpdb->prepare("{$wpdb->posts}.post_type IN (" . implode(',', $esses) . ')', $post_types);
    $join = '';
    // Grab a snapshot of post IDs, just in case it changes during the export.
    /** @noinspection SqlResolve */
    $post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} $join WHERE $where");
    /*
     * Get the requested terms ready
     */
    $cats = $tags = $terms = array();
    $categories = (array)get_categories(array('get' => 'all'));
    $tags = (array)get_tags(array('get' => 'all'));
    $custom_taxonomies = get_taxonomies(array('_builtin' => false));
    $custom_terms = (array)get_terms($custom_taxonomies, array('get' => 'all'));

    // Put categories in order with no child going before its parent.
    while ($cat = array_shift($categories)) {
        if ($cat->parent == 0 || isset($cats[$cat->parent]))
            $cats[$cat->term_id] = $cat;
        else
            $categories[] = $cat;
    }

    // Put terms in order with no child going before its parent.
    while ($t = array_shift($custom_terms)) {
        if ($t->parent == 0 || isset($terms[$t->parent]))
            $terms[$t->term_id] = $t;
        else
            $custom_terms[] = $t;
    }

    unset($categories, $custom_taxonomies, $custom_terms);

    add_filter('foody_export_skip_post', 'foody_skip_post_import', 10, 3);

    /**
     * @param $skip boolean
     * @param $post WP_Post
     * @param $destination_blog_id int
     * @return boolean
     */
    function foody_skip_post_import($skip, $post, $destination_blog_id)
    {
        $type = $post->post_type;

        $unique_title_post_types = [
            'foody_ingredient'
        ];

        // if this post type has unique titles
        // check if it already exists in destination blog
        if (in_array($type, $unique_title_post_types)) {

            switch_to_blog($destination_blog_id);
            $post_in_dest = get_page_by_title($post->post_title, OBJECT, $type);

            restore_current_blog();
            // post found by source post title, skip.
            if (($post_in_dest instanceof WP_Post) === true && $post_in_dest->ID > 0) {
                $skip = true;
                Foody_WhiteLabelLogger::info("skipping $type, id: {$post->ID}");
            }
        }

        return $skip;
    }

    /**
     * Wrap given string in XML CDATA tag.
     *
     * @since 2.1.0
     *
     * @param string $str String to wrap in XML CDATA tag.
     * @return string
     */
    function foody_cdata($str)
    {
        if (!empty($str)){
            if (!seems_utf8($str)) {
                $str = utf8_encode($str);
            }
            // $str = ent2ncr(esc_html($str));

            $str = '<![CDATA[' . str_replace(']]>', ']]]]><![CDATA[>', $str) . ']]>';
        }
        return $str;
    }

    /**
     * Return the URL of the site
     *
     * @since 2.5.0
     *
     * @return string Site URL.
     */
    function foody_site_url()
    {
        // Multisite: the base URL.
        if (is_multisite())
            return network_home_url();
        // WordPress (single site): the blog URL.
        else
            return get_bloginfo_rss('url');
    }

    /**
     * Output a cat_name XML tag from a given category object
     *
     * @since 2.1.0
     *
     * @param object $category Category Object
     */
    function foody_cat_name($category)
    {
        if (empty($category->name))
            return;

        echo '<wp:cat_name>' . foody_cdata($category->name) . "</wp:cat_name>\n";
    }

    /**
     * Output a category_description XML tag from a given category object
     *
     * @since 2.1.0
     *
     * @param object $category Category Object
     */
    function foody_category_description($category)
    {
        if (empty($category->description))
            return;

        echo '<wp:category_description>' . foody_cdata($category->description) . "</wp:category_description>\n";
    }

    /**
     * Output a tag_name XML tag from a given tag object
     *
     * @since 2.3.0
     *
     * @param object $tag Tag Object
     */
    function foody_tag_name($tag)
    {
        if (empty($tag->name))
            return;

        echo '<wp:tag_name>' . foody_cdata($tag->name) . "</wp:tag_name>\n";
    }

    /**
     * Output a tag_description XML tag from a given tag object
     *
     * @since 2.3.0
     *
     * @param object $tag Tag Object
     */
    function foody_tag_description($tag)
    {
        if (empty($tag->description))
            return;

        echo '<wp:tag_description>' . foody_cdata($tag->description) . "</wp:tag_description>\n";
    }

    /**
     * Output list of taxonomy terms, in XML tag format, associated with a post
     *
     * @since 2.3.0
     */
    function foody_post_taxonomy()
    {
        $post = get_post();

        $taxonomies = get_object_taxonomies($post->post_type);
        if (empty($taxonomies))
            return;
        $terms = wp_get_object_terms($post->ID, $taxonomies);

        if (!is_wp_error($terms)) {
            foreach ((array)$terms as $term) {
                echo "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . foody_cdata($term->name) . "</category>\n";
            }
        }
    }

    /**
     * Output a term_name XML tag from a given term object
     *
     * @since 2.9.0
     *
     * @param object $term Term Object
     */
    function foody_term_name($term)
    {
        if (empty($term) || empty($term->name))
            return;

        echo '<wp:term_name>' . foody_cdata($term->name) . "</wp:term_name>\n";
    }

    /**
     * Output a term_description XML tag from a given term object
     *
     * @since 2.9.0
     *
     * @param object $term Term Object
     */
    function foody_term_description($term)
    {
        if (empty($term->description))
            return;

        echo "\t\t<wp:term_description>" . foody_cdata($term->description) . "</wp:term_description>\n";
    }

    /**
     * Output term meta XML tags for a given term object.
     *
     * @since 4.6.0
     *
     * @param WP_Term $term Term object.
     */
    function foody_term_meta($term)
    {
        global $wpdb;

        $term_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->termmeta WHERE term_id = %d", $term->term_id));

        $source_term = new stdClass();
        $source_term->meta_key = 'source_term';
        $source_term->meta_value = $term->term_id;
        $term_meta[] = $source_term;

        foreach ($term_meta as $meta) {
            /**
             * Filters whether to selectively skip term meta used for WXR exports.
             *
             * Returning a truthy value to the filter will skip the current meta
             * object from being exported.
             *
             * @since 4.6.0
             *
             * @param bool $skip Whether to skip the current piece of term meta. Default false.
             * @param string $meta_key Current meta key.
             * @param object $meta Current meta object.
             */
            if (!apply_filters('foody_export_skip_termmeta', false, $meta->meta_key, $meta)) {
                $meta_key = foody_cdata($meta->meta_key);
                $meta_value = foody_cdata($meta->meta_value);
                echo "\t\t<wp:termmeta>\n\t\t\t<wp:meta_key>$meta_key</wp:meta_key>\n\t\t\t<wp:meta_value>$meta_value</wp:meta_value>\n\t\t</wp:termmeta>\n";
            }
        }
    }

    function get_wxr_head()
    {
        echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
        ?>
        <?php the_generator('export'); ?>
        <!--suppress HtmlUnknownTag -->
        <rss version="2.0"
        xmlns:excerpt="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/excerpt/"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:wfw="http://wellformedweb.org/CommentAPI/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:wp="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/"
        >
        <?php
    }

    function get_wxr_channel_head()
    {
        echo '<channel>';
        ?>
        <title><?php bloginfo_rss('name'); ?></title>
        <!--suppress HtmlExtraClosingTag -->
        <link><?php bloginfo_rss('url'); ?></link>
        <description><?php bloginfo_rss('description'); ?></description>
        <pubDate><?php echo date('D, d M Y H:i:s +0000'); ?></pubDate>
        <language><?php bloginfo_rss('language'); ?></language>
        <wp:wxr_version><?php echo WXR_VERSION; ?></wp:wxr_version>
        <wp:base_site_url><?php echo foody_site_url(); ?></wp:base_site_url>
        <wp:base_blog_url><?php bloginfo_rss('url'); ?></wp:base_blog_url>
        <?php
    }

    /**
* @param $wpdb wpdb
*/
    function foody_export_post(){

        global $wpdb;
        $post = get_post();
        $is_sticky = is_sticky($post->ID) ? 1 : 0;
        ob_start();
        ?>
                    <item>
                            <title>
                                <?php
                                /** This filter is documented in wp-includes/feed.php */
                                echo apply_filters('the_title_rss', $post->post_title);
                                ?>
                            </title>
                            <!--suppress HtmlExtraClosingTag -->
                            <link><?php the_permalink_rss() ?></link>
                            <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                            <dc:creator><?php echo foody_cdata(get_the_author_meta('login')); ?></dc:creator>
                            <guid isPermaLink="false"><?php the_guid(); ?></guid>
                            <description></description>
                            <content:encoded>
                                <?php
                                /**
                                 * Filters the post content used for WXR exports.
                                 *
                                 * @since 2.5.0
                                 *
                                 * @param string $post_content Content of the current post.
                                 */
                                echo foody_cdata(apply_filters('the_content_export', $post->post_content));
                                ?>
                            </content:encoded>
                            <excerpt:encoded>
                                <?php
                                /**
                                 * Filters the post excerpt used for WXR exports.
                                 *
                                 * @since 2.6.0
                                 *
                                 * @param string $post_excerpt Excerpt for the current post.
                                 */
                                echo foody_cdata(apply_filters('the_excerpt_export', $post->post_excerpt));
                                ?>
                            </excerpt:encoded>
                            <wp:post_id><?php echo intval($post->ID); ?></wp:post_id>
                            <wp:post_date><?php echo foody_cdata($post->post_date); ?></wp:post_date>
                            <wp:post_date_gmt><?php echo foody_cdata($post->post_date_gmt); ?></wp:post_date_gmt>
                            <wp:comment_status><?php echo foody_cdata($post->comment_status); ?></wp:comment_status>
                            <wp:ping_status><?php echo foody_cdata($post->ping_status); ?></wp:ping_status>
                            <wp:post_name><?php echo foody_cdata($post->post_name); ?></wp:post_name>
                            <wp:status><?php echo foody_cdata($post->post_status); ?></wp:status>
                            <wp:post_parent><?php echo intval($post->post_parent); ?></wp:post_parent>
                            <wp:menu_order><?php echo intval($post->menu_order); ?></wp:menu_order>
                            <wp:post_type><?php echo foody_cdata($post->post_type); ?></wp:post_type>
                            <wp:post_password><?php echo foody_cdata($post->post_password); ?></wp:post_password>
                            <wp:is_sticky><?php echo intval($is_sticky); ?></wp:is_sticky>
                            <?php foody_post_taxonomy(); ?>
                            <?php
                            // get all post meta from db
                            $post_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d", $post->ID));
//                            if (!is_array($post_meta)) {
//                                $post_meta = [];
//                            }
                            // add meta for source post in main site
//                            $source_post_meta = new stdClass();
//                            $source_post_meta->meta_key = 'source_post';
//                            $source_post_meta->meta_value = $post->ID;
//                            $post_meta[] = $source_post_meta;
                            foreach ($post_meta as $meta) :
                                /**
                                 * Filters whether to selectively skip post meta used for WXR exports.
                                 *
                                 * Returning a truthy value to the filter will skip the current meta
                                 * object from being exported.
                                 *
                                 * @since 3.3.0
                                 *
                                 * @param bool $skip Whether to skip the current post meta. Default false.
                                 * @param string $meta_key Current meta key.
                                 * @param object $meta Current meta object.
                                 */
                                if (apply_filters('foody_export_skip_postmeta', false, $meta->meta_key, $meta))
                                    continue;
                                ?>
                                <wp:postmeta>
                                    <wp:meta_key><?php echo foody_cdata($meta->meta_key); ?></wp:meta_key>
                                    <wp:meta_value><?php echo foody_cdata($meta->meta_value); ?></wp:meta_value>
                                </wp:postmeta>
                            <?php endforeach; ?>

                        </item>
        <?php

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }


    /**
     * @param $newBlogId
     * @param $cats
     * @param $tags
     * @param $terms
     * @param $post_ids
     * @param $wpdb wpdb
     * @return string
     */
    function write_foody_wxr($newBlogId, $cats, $tags, $terms, $post_ids, $wpdb)
    {
        $date = str_replace(' ', '-', date('d.m.Y H:i:s'));
        $file_name = plugin_dir_path(__FILE__) . "/exports/foody-wl-export-blog-$newBlogId-{$date}.xml";
        $fh = fopen($file_name, 'a') or die("can't open file");

        ob_start();
        get_wxr_head();
        $content = ob_get_contents();
        ob_end_clean();
        fwrite($fh, $content);

        ob_start();
        get_wxr_channel_head();
        $content = ob_get_contents();
        ob_end_clean();
        fwrite($fh, $content);

        // categories
        ob_start();
        ?>
        <?php foreach ($cats as $c) : ?>
        <wp:category>
            <wp:term_id><?php echo intval($c->term_id); ?></wp:term_id>
            <wp:category_nicename><?php echo foody_cdata($c->slug); ?></wp:category_nicename>
            <wp:category_parent><?php echo foody_cdata($c->parent ? $cats[$c->parent]->slug : ''); ?></wp:category_parent>
            <?php foody_cat_name($c);
            foody_category_description($c);
            foody_term_meta($c); ?>
        </wp:category>
    <?php endforeach;
        unset($cats); ?>
        <?php
        $categories_content = ob_get_contents();
        ob_end_clean();
        fwrite($fh, $categories_content);

        // tags
        ob_start();
        ?>
        <?php foreach ($tags as $t) : ?>
        <wp:tag>
            <wp:term_id><?php echo intval($t->term_id); ?></wp:term_id>
            <wp:tag_slug><?php echo foody_cdata($t->slug); ?></wp:tag_slug>
            <?php foody_tag_name($t);
            foody_tag_description($t);
            foody_term_meta($t); ?>
        </wp:tag>
    <?php endforeach;
        unset($tags); ?>
        <?php
        $tags_content = ob_get_contents();
        ob_end_clean();
        fwrite($fh, $tags_content);

        // custom taxonomies
        ob_start();
        ?>
        <?php foreach ($terms as $t) : ?>
        <wp:term>
            <wp:term_id><?php echo foody_cdata($t->term_id); ?></wp:term_id>
            <wp:term_taxonomy><?php echo foody_cdata($t->taxonomy); ?></wp:term_taxonomy>
            <wp:term_slug><?php echo foody_cdata($t->slug); ?></wp:term_slug>
            <wp:term_parent><?php echo foody_cdata($t->parent ? $terms[$t->parent]->slug : ''); ?></wp:term_parent>
            <?php foody_term_name($t);
            foody_term_description($t);
            foody_term_meta($t); ?>
        </wp:term>
    <?php endforeach;
        unset($terms); ?>
        <?php
        $terms_content = ob_get_contents();
        ob_end_clean();
        fwrite($fh, $terms_content);

        // posts

        ?>
        <?php if ($post_ids) {

        try {
            /**
             * @global WP_Query $wp_query
             */
            global $wp_query;

            // Fake being in the loop.
            $wp_query->in_the_loop = true;

            try {
                // Fetch 20 posts at a time rather than loading the entire table into memory.
                while ($next_posts = array_splice($post_ids, 0, 20)) {
                    $where = 'WHERE ID IN (' . join(',', $next_posts) . ')';
                    $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");

                    // Begin Loop.
                    foreach ($posts as $post) {
                        setup_postdata($post);
                        // skip post if filter returns true
//                    if (apply_filters('foody_export_skip_post', false, $post, $newBlogId)) {
//                        continue;
//                    }

                        $content = foody_export_post();
                        fwrite($fh, $content);
                    }
                }
            } catch (Exception $e) {
                Foody_WhiteLabelLogger::error($e->getMessage(), ['error' => $e]);
            }
            unset($post_ids);
        } catch (Exception $e) {
            Foody_WhiteLabelLogger::error($e->getMessage(), ['error' => $e]);
        }

    }

        // footer
        $footer = '</channel></rss>';
        fwrite($fh, $footer);

        // close file handler
        fclose($fh);

        return $file_name;
    }


    $export_file = write_foody_wxr($newBlogId, $cats, $tags, $terms, $post_ids, $wpdb);


    /**
     * if data is not empty switch to
     * the new blog and import
     */
    if (!empty($export_file)) {
        try {
            switch_to_blog($newBlogId);

            $importer = new Foody_Import();
            $importer->import($export_file);

            restore_current_blog();
        } catch (Exception $e) {
            Foody_WhiteLabelLogger::info("error exporting to $export_file", ['error' => $e]);
        }

    } else {
        Foody_WhiteLabelLogger::error('invalid data to import');
        throw new Exception('invalid data to import');
    }
}
