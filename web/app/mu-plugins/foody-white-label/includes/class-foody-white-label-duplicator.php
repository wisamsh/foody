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
     * Duplicates a post & its meta and returns the new duplicated Post ID
     * @param  WP_Post $old_post The Post you want to clone
     * @param $blogId int blog id to copy the post into
     * @return int The duplicated Post ID
     */
    private static function duplicate($old_post, $blogId)
    {
        $post = array(
            'post_title' => $old_post->post_title,
            'post_status' => 'draft',
            'post_type' => $old_post->post_type,
            'post_author' => 1,
            'post_content' => $old_post->post_content
        );

        // get source post meta before switching
        // to destination blog
        $meta_data = get_post_custom($old_post->ID);


        switch_to_blog($blogId);

        // add post to destination blog
        $new_post_id = wp_insert_post($post);
        if (!is_wp_error($new_post_id)) {
            // copy post metadata
            foreach ($meta_data as $key => $values) {
                foreach ($values as $value) {
                    add_post_meta($new_post_id, $key, $value);
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