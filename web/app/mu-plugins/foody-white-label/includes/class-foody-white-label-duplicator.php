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
     * @return array
     */
    public static function duplicateCategory($categoryId, $blogId)
    {
        $args = self::getArgs([
            'cat' => $categoryId,
        ]);

        $duplicationArgs = [];

        return self::duplicateByQuery($args, $blogId, $duplicationArgs);
    }

    /**
     * Duplicate author's posts
     * @param $authorId
     * @param $blogId
     * @return array
     */
    public static function duplicateAuthor($authorId, $blogId)
    {
        $args = self::getArgs([
            'author' => $authorId,
        ]);

        return self::duplicateByQuery($args, $blogId);
    }


    /**
     * Duplicate posts by tag
     * @param $tagId
     * @param $blogId
     * @return array
     */
    public static function duplicateTag($tagId, $blogId)
    {
        $args = self::getArgs([
            'tag' => $tagId,
        ]);

        return self::duplicateByQuery($args, $blogId);
    }


    /**
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
            $result = self::duplicate($post, $blogId, $duplicationArgs);
            if (is_wp_error($result)) {
                $failed[] = $result;
            } else {
                $success++;
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
     * @return int The duplicated Post ID
     */
    private static function duplicate($old_post, $blogId, $duplicationArgs = [])
    {
        $defaultArgs = ['with_media' => true];
        $duplicationArgs = array_merge($defaultArgs, $duplicationArgs);

        $post = array(
            'post_title' => $old_post->post_title,
            'post_status' => 'draft',
            'post_type' => $old_post->post_type,
            'post_author' => 1,
            'post_content' => $old_post->post_content
        );

        if ($duplicationArgs['with_media']) {
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

        $categories = wp_get_post_categories($old_post->ID);

        switch_to_blog($blogId);

        $new_post_id = 0;
        if (!post_exists($old_post->post_title)) {
            $copy_techniques = isset($duplicationArgs['copy_techniques']) && $duplicationArgs['copy_techniques'];
            $copy_accessories = isset($duplicationArgs['copy_accessories']) && $duplicationArgs['copy_accessories'];


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
                        add_post_meta($new_post_id, $key, $value);
                    }
                }

                if (!empty($image_url)) {
                    $attach_id = self::upload_image($old_post->ID,$image_url);
                    if (!empty($attach_id) && is_numeric($attach_id)){
                        // And finally assign featured image to post
                        set_post_thumbnail($new_post_id, $attach_id);
                    }
                }

                $copy_categories = isset($duplicationArgs['copy_categories']) && $duplicationArgs['copy_categories'];

                if ($copy_categories) {
                    $destination_categories = self::getDestinationCategories($categories);
                    wp_set_post_categories($new_post_id, $destination_categories);
                }

                Foody_WhiteLabelPostMapping::add($old_post->ID, $blogId);
            } else {
                Foody_WhiteLabelLogger::error(__CLASS__ . "::duplicate: error inserting post", ['error' => $new_post_id]);
            }
        } else {
            Foody_WhiteLabelLogger::info("post type {$old_post->post_type} with id {$old_post->ID} already exists");
        }

        // switch back to main site
        restore_current_blog();
        return $new_post_id;
    }


    /**
     * Upload an image to WP's media library
     * from a url and attach it to the post
     * @param $postID
     * @param $url
     * @param string $alt
     * @return int|null|object|\WP_Error
     */
    private static function upload_image($postID, $url)
    {

        if (!$postID || !is_numeric($postID)) {
            return new \WP_Error("invalid post id: $postID");
        }

        try {
            $wp_path = ABSPATH;
            /** @noinspection PhpIncludeInspection */
            require_once("{$wp_path}wp-admin/includes/image.php");
            /** @noinspection PhpIncludeInspection */
            require_once("{$wp_path}wp-admin/includes/file.php");
            /** @noinspection PhpIncludeInspection */
            require_once("{$wp_path}wp-admin/includes/media.php");

            wp_mkdir_p(PLUGIN_DIR . '/tpm');
            $tmp = wp_tempnam('', PLUGIN_DIR . '/tpm');

            $ext = pathinfo($url, PATHINFO_EXTENSION);

            $tmp = str_replace('.tmp', '.' . $ext, $tmp);

            $file = file_put_contents($tmp, file_get_contents($url));


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
                $id = media_handle_sideload($file_array, $postID, $desc);


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

        $destination_categories = get_terms(['name' => $source_categories_names]);

        return $destination_categories;
    }
}