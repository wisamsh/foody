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
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Foody_WhiteLabelDuplicator();
        }
        return self::$instance;
    }

    public static function whiteLabelCreate($newBlogId)
    {
        // TODO change output to logger
        try {
            $output = export_import_foody_wp($newBlogId);
            if (empty($output)) {
                Foody_WhiteLabelLogger::error('empty export data', ['blog' => $newBlogId]);
            }
            if (class_exists('Foody_Import') && Foody_Import::isDebug()) {
                // TODO remove
                var_dump($output);
            }
        } catch (Exception $e) {
            if (class_exists('Foody_Import') && Foody_Import::isDebug()) {
                echo $e->getMessage();
                Foody_WhiteLabelLogger::error($e->getMessage(), ['blog' => $newBlogId, 'location' => get_class()]);
            }
        }
    }


    public static function duplicateCategory($categoryId, $blogId)
    {
        $args = self::getArgs([
            'cat' => $categoryId,
        ]);

        self::duplicateByQuery($args, $blogId);
    }


    private static function duplicateByQuery($args, $blogId)
    {
        $query = new WP_Query($args);
        $posts = $query->get_posts();

        $success = 0;
        $failed = [];

        foreach ($posts as $post) {
            $result = self::duplicate($post, $blogId);
            if (is_wp_error($result)) {
                $failed[] = $result;
            } else {
                $success++;
            }
        }

    }


    /**
     * @param $source_post WP_Post
     * @param $postType string
     */
    private static function getPostDataForType($source_post, $postType)
    {
        $data = [
            'post_title' => $source_post->post_title,
            'post_status' => 'draft',
            'post_type' => $source_post->post_type,
            'post_author' => 1,
            'post_content' => $source_post->post_content
        ];
    }


    /**
     * Duplicates a post & its meta and returns the new duplicated Post ID
     * @param  WP_Post $old_post The Post you want to clone
     * @param $blogId int blog id to copy the post into
     * @param bool $with_media if true - copies post thumbnail
     * @return int The duplicated Post ID
     */
    private static function duplicate($old_post, $blogId, $with_media = false)
    {
        $post = array(
            'post_title' => $old_post->post_title,
            'post_status' => 'draft',
            'post_type' => $old_post->post_type,
            'post_author' => 1,
            'post_content' => $old_post->post_content
        );

        if ($with_media) {
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


        switch_to_blog($blogId);

        $meta_data_to_exclude = [
            ''
        ];

        $meta_data_to_include = [
            'ingredients',
            'preview',
            'tags',
            'video',
            'notes',
            '[categories]',
            '[accessories]',
            '[techniques]',
        ];

        // add post to destination blog
        $new_post_id = wp_insert_post($post);
        if (!is_wp_error($new_post_id)) {
            // copy post metadata
            foreach ($meta_data as $key => $values) {
                foreach ($values as $value) {
                    add_post_meta($new_post_id, $key, $value);
                }
            }

            if (!empty($image_url)) {
                // Add Featured Image to Post
                $upload_dir = wp_upload_dir(); // Set upload folder

                global $wp_version;
                add_filter('block_local_requests', '__return_false');
                $image_data = wp_remote_get($image_url,
                    [
                        'timeout' => 10, 'sslverify' => false,
                        'httpversion' => '1.0',
                        'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
                    ]
                ); // Get image data
                $filename = basename($image_url); // Create image file name

                // Check folder permission and define file location
                if (wp_mkdir_p($upload_dir['path'])) {
                    $file = $upload_dir['path'] . '/' . $filename;
                } else {
                    $file = $upload_dir['basedir'] . '/' . $filename;
                }

                // Create the image  file on the server
                $result = file_put_contents($file, $image_data);

                if ($result !== false) {
                    // Check image file type
                    $wp_file_type = wp_check_filetype($filename, null);

                    // Set attachment data
                    $attachment = array(
                        'post_mime_type' => $wp_file_type['type'],
                        'post_title' => sanitize_file_name($filename),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    // Create the attachment
                    $attach_id = wp_insert_attachment($attachment, $file, $new_post_id);

                    // Include image.php
                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    // Define attachment metadata
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file);

                    // Assign metadata to attachment
                    wp_update_attachment_metadata($attach_id, $attach_data);

                    // And finally assign featured image to post
                    set_post_thumbnail($new_post_id, $attach_id);
                }
            }
        }

        // switch back to main site
        switch_to_blog(BLOG_ID_CURRENT_SITE);
        return $new_post_id;
    }

    private static function getArgs($args = [])
    {
        $default_args = [
            'post_status' => 'publish',
            'post_type' => ['foody_recipe', 'post', 'foody_playlist'],
            'posts_per_page' => -1
        ];

        return array_merge($default_args, $args);
    }
}