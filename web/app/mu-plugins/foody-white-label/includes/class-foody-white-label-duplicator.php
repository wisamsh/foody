<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/14/19
 * Time: 12:32 PM
 */


class Foody_WhiteLabelDuplicator
{
    /**
     * @var $instance Foody_WhiteLabelDuplicator
     */
    private static $instance;

    /**
     * Foody_WhiteLabelDuplicator constructor.
     */
    private function __construct()
    {
        require_once PLUGIN_DIR . 'foody-importer/functions.php';
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Foody_WhiteLabelDuplicator();
        }
        return self::$instance;
    }

    /**
     * Called when a new blog is created.
     * Duplicates core data to the new site.
     * @param $newBlogId
     */
    public static function whiteLabelCreate($newBlogId)
    {
        Foody_WhiteLabelLogger::info('start export import');
        try {
            export_import_foody_wp($newBlogId);
        } catch (Exception $e) {
            Foody_WhiteLabelLogger::error($e->getMessage(), ['blog' => $newBlogId, 'location' => get_class()]);
        }
    }


    /**
     * Duplicate posts by category
     * @param $categoryId
     * @param $blogId
     * @param array $duplicationArgs
     * @return array
     */
    public static function duplicateCategory($categoryId, $blogId, $duplicationArgs = [])
    {
        $args = self::getArgs([
            'cat' => $categoryId,
        ]);

        return self::duplicateByQuery($args, $blogId, $duplicationArgs);
    }

    /**
     * Duplicate author's posts
     * @param $authorId
     * @param $blogId
     * @param array $duplicationArgs
     * @return array
     */
    public static function duplicateAuthor($authorId, $blogId, $duplicationArgs = [])
    {
        $args = self::getArgs([
            'author' => $authorId
        ]);

        return self::duplicateByQuery($args, $blogId, $duplicationArgs);
    }


    /**
     * Duplicate posts by tag
     * @param $tagId
     * @param $blogId
     * @param array $duplicationArgs
     * @return array
     */
    public static function duplicateTag($tagId, $blogId, $duplicationArgs = [])
    {
        $args = self::getArgs([
            'tag_id' => $tagId,
        ]);

        return self::duplicateByQuery($args, $blogId, $duplicationArgs);
    }


    /**
     * @noinspection PhpDocMissingThrowsInspection
     * Duplicate posts by wp_query args
     * @param $args array
     * @param $blogId int
     * @param $duplicationArgs array
     * @return array
     */
    private static function duplicateByQuery($args, $blogId, $duplicationArgs = [])
    {
        $query = new WP_Query($args);
        $posts = $query->get_posts();

        $success = 0;
        $failed = [];

        foreach ($posts as $post) {
            try {
                $result = self::duplicate($post, $blogId, $duplicationArgs);
                if (is_wp_error($result)) {
                    $failed[] = $result;
                    Foody_WhiteLabelLogger::error($result->get_error_message(), $result);
                } else {
                    $success++;
                }
            } catch (Exception $e) {
                /** @noinspection PhpUnhandledExceptionInspection */
                Foody_WhiteLabelLogger::exception($e);
            }

        }

        return [
            'success' => $success,
            'failed' => $failed
        ];
    }

    /**
     * Duplicates a post & its meta and returns the new duplicated Post ID
     * @param  WP_Post $old_post The Post you want to clone
     * @param $blogId int blog id to copy the post into
     * @param array $duplicationArgs
     * @return int|WP_Error The duplicated Post ID
     * @throws Exception
     */
    public static function duplicate($old_post, $blogId, $duplicationArgs = [])
    {
        remove_action("wp_insert_post", 'foody_auto_sync_post');
        $defaultArgs = ['with_media' => true];
        if (!is_array($duplicationArgs)) {
            Foody_WhiteLabelLogger::warning("invalid duplication args", $duplicationArgs);
            $duplicationArgs = [];
        }
        $duplicationArgsWithDefaults = array_merge($defaultArgs, $duplicationArgs);

        $post = array(
            'post_title' => $old_post->post_title,
            'post_status' => 'draft',
            'post_type' => $old_post->post_type,
            'post_author' => 1,
            'post_content' => $old_post->post_content,
            'post_excerpt' => $old_post->post_excerpt,
        );

        switch_to_blog($blogId);

        $post_in_blog = get_page_by_title($old_post->post_title, OBJECT, $old_post->post_type);
        switch_to_blog(get_main_site_id());

        $exists = ($post_in_blog instanceof WP_Post);

        // if this post already exists add it's
        // ID to post data so wp_insert_post handle this op
        // as an update
        if ($exists) {
            return 0;
        }

        if ($duplicationArgsWithDefaults['with_media']) {
            $post_thumbnail_id = get_post_thumbnail_id($old_post->ID);
            if (!empty($post_thumbnail_id)) {
                $image_url = wp_get_attachment_image_src($post_thumbnail_id, 'full');
                if (!empty($image_url)) {
                    $image_url = $image_url[0];
                }
            }
        }

        // get source post meta before switching
        // to destination blog
        $meta_data = get_post_custom($old_post->ID);

        $categories = wp_get_post_categories($old_post->ID, ['hide_empty' => false]);

        $categories = array_map(function ($category) {
            if (is_numeric($category)) {
                $category = get_term($category);
            }
            return $category;
        }, $categories);

        switch_to_blog($blogId);

        $copy_techniques = isset($duplicationArgsWithDefaults['copy_techniques']) && $duplicationArgsWithDefaults['copy_techniques'];
        $copy_accessories = isset($duplicationArgsWithDefaults['copy_accessories']) && $duplicationArgsWithDefaults['copy_accessories'];


        // add post to destination blog
        $new_post_id = wp_insert_post($post);

        if (!is_wp_error($new_post_id)) {
            // copy post metadata
            foreach ($meta_data as $key => $values) {

                if (preg_match('/techniques/', $key)) {
                    if (!$copy_techniques) {
                        continue;
                    }
                } elseif (preg_match('/accessories/', $key)) {
                    if (!$copy_accessories) {
                        continue;
                    }
                }

                foreach ($values as $value) {
                    $value = apply_filters('foody_import_post_meta_value', $old_post->ID, $key, $value, $blogId);

                    if (has_filter("foody_import_post_meta_{$key}")) {
                        $value = apply_filters("foody_import_post_meta_{$key}", $old_post->ID, $value, $blogId);
                    }

                    update_post_meta($new_post_id, $key, $value);
                }
            }

            if (!empty($image_url)) {
                $attach_id = self::upload_image($old_post->ID, $image_url);
                if (!empty($attach_id) && is_numeric($attach_id)) {
                    // And finally assign featured image to post
                    set_post_thumbnail($new_post_id, $attach_id);
                }
            }

            $copy_categories = isset($duplicationArgsWithDefaults['copy_categories']) && $duplicationArgsWithDefaults['copy_categories'];

            if ($copy_categories) {
                $destination_categories = self::getDestinationCategories($categories);
                wp_set_post_categories($new_post_id, $destination_categories);
            }

            if (!empty($duplicationArgsWithDefaults['index'])) {
                update_post_meta($new_post_id, 'foody_index', 1);
            }

            switch_to_blog(get_main_site_id());
            Foody_WhiteLabelPostMapping::add($old_post->ID, $new_post_id, $blogId);
            switch_to_blog($blogId);
        } else {
            Foody_WhiteLabelLogger::error(__CLASS__ . "::duplicate: error inserting post", ['error' => $new_post_id]);
        }


        // switch back to main site
        switch_to_blog(get_main_site_id());
        add_action("wp_insert_post", 'foody_auto_sync_post', 10, 2);
        return $new_post_id;
    }

    /**
     * @param $term_object WP_Term
     * @param $blogId
     */
    public static function duplicateTerm($term_object, $blogId)
    {
        $slug = $term_object->slug;
        $tax = $term_object->taxonomy;
        $term_meta = get_term_meta($term_object->term_id);
        remove_action('edit_term', 'foody_auto_sync_term', 10);
        $blogId = (int)$blogId;
        switch_to_blog($blogId);
        // if the term already exists in the correct taxonomy leave it alone
        $term_id = term_exists($slug, $tax);
        if ($term_id) {
            self::process_term_meta($term_id['term_id'], $term_object->term_id, $term_object->taxonomy, $term_meta, $blogId);
            switch_to_blog(get_main_site_id());
            return;
        }

        if (empty($term_object->parent)) {
            $parent = 0;
        } else {
            $parent = term_exists($term_object->parent, $term_object->taxonomy);
            if (is_array($parent)) $parent = $parent['term_id'];
        }

//        $term = wp_slash($term);
        $description = isset($term_object->description) ? $term_object->description : '';
        $termarr = array('slug' => $term_object->slug, 'description' => $description, 'parent' => intval($parent));

        $id = wp_insert_term($term_object->name, $term_object->taxonomy, $termarr);
        if (!is_wp_error($id)) {
            self::process_term_meta($id['term_id'], $term_object->term_id, $term_object->taxonomy, $term_meta, $blogId);
        }

        switch_to_blog(get_main_site_id());
        add_action('edit_term', 'foody_auto_sync_term', 0, 3);
    }

    /**
     * @param $term_id
     * @param $tax
     * @param $meta array
     * @param $blog_id
     */
    public static function process_term_meta($term_id, $old_term_id, $tax, $meta, $blog_id)
    {
        foreach ($meta as $key => $value) {

            if (is_array($value)) {
                $value = $value[0];
            }

            // post_id for acf
            $post_id = "{$tax}_{$old_term_id}";
            $value = maybe_unserialize($value);
            $value = apply_filters('foody_import_post_meta_value', $post_id, $key, $value, $blog_id);
            update_term_meta($term_id, $key, $value);
        }
    }


    /**
     * Upload an image to WP's media library
     * from a url and attach it to the post
     * @param $postID
     * @param $url
     * @return int|null|object|\WP_Error
     * @throws Exception when env is local
     */
    public static function upload_image($postID, $url)
    {
        try {
            $wp_path = ABSPATH;
            /** @noinspection PhpIncludeInspection */
            require_once("{$wp_path}wp-admin/includes/image.php");
            /** @noinspection PhpIncludeInspection */
            require_once("{$wp_path}wp-admin/includes/file.php");
            /** @noinspection PhpIncludeInspection */
            require_once("{$wp_path}wp-admin/includes/media.php");

            wp_mkdir_p(PLUGIN_DIR . '/tmp');
            $tmp = wp_tempnam('', PLUGIN_DIR . '/tmp/');

            $ext = pathinfo($url, PATHINFO_EXTENSION);

            $tmp = str_replace('.tmp', '.' . $ext, $tmp);

            $file = file_put_contents($tmp, foody_get($url));


            $id = null;
            if ($file) {
                $desc = '';
                $file_array = array();

                // Set variables for storage
                // fix file filename for query strings
                preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
                $file_array['name'] = basename($matches[0]);
                $file_array['name'] = pathinfo($file_array['name'], PATHINFO_FILENAME) . '.' . pathinfo($file_array['name'], PATHINFO_EXTENSION);
                $file_array['tmp_name'] = $tmp;
                $file_array['name'] = $tmp;

                // If error storing temporarily, unlink
                if (is_wp_error($tmp)) {
                    @unlink($file_array['tmp_name']);
                    $file_array['tmp_name'] = '';
                }

                // do the validation and storage stuff
                if (!is_null($postID)) {
                    $id = media_handle_sideload($file_array, $postID, $desc);
                } else {
                    $wp_file = wp_handle_sideload($file_array, ['test_form' => false]);
                    if (isset($wp_file['error'])) {
                        $id = new WP_Error('upload_error', $wp_file['error'], ['file' => $file_array]);
                    } else {
                        $attachment_url = $wp_file['url'];
                        $type = $wp_file['type'];
                        $wp_file = $wp_file['file'];
                        $title = preg_replace('/\\.[^.]+$/', '', basename($wp_file));
                        $parent = 0;
                        $attachment = array('post_mime_type' => $type, 'guid' => $attachment_url, 'post_parent' => $parent, 'post_title' => $title, 'post_content' => '');
                        $id = wp_insert_attachment($attachment, $wp_file, $parent);
                    }
                }

                // If error storing permanently, unlink
                if (is_wp_error($id)) {
                    @unlink($file_array['tmp_name']);
                    return $id;
                }
                @unlink($file_array['tmp_name']);

                $image_meta = array(
                    'ID' => $id,            // Specify the image (ID) to be updated
                );

                wp_update_post($image_meta);
            }

            return $id;
        } catch (Exception $e) {
            Foody_WhiteLabelLogger::exception($e);
            return new \WP_Error($e->getMessage());
        }
    }

    /**
     * Get default wp_query args for post duplication
     * @param array $args
     * @return array
     */
    private static function getArgs($args = [])
    {
        $default_args = [
            'post_status' => 'publish',
            'post_type' => ['foody_recipe', 'post', 'foody_playlist'],
            'posts_per_page' => -1
        ];

        return array_merge($default_args, $args);
    }


    /**
     * This method is called in the context of a blog (after switch_to_blog() is called)
     *
     * @param $categories WP_Term[]
     * @return array|int|WP_Error
     */
    private static function getDestinationCategories($categories)
    {

        $source_categories_names = array_map(function ($category) {
            return $category->name;
        }, $categories);

        $destination_categories = get_terms(['name' => $source_categories_names, 'hide_empty' => false, 'fields' => 'ids']);

        return $destination_categories;
    }
}