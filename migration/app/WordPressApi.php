<?php
/**
 * Created by PhpStorm.
 * User: liore
 * Date: 06/02/17
 * Time: 11:46
 */

namespace App;


class WordPressApi
{
    public function __construct()
    {

        $this->loadWordPress();
    }

    /**
     *
     */
    public function loadWordPress()
    {


        require_once base_path('../web/wp/wp-load.php');
        require_once base_path('../web/wp/wp-admin/includes/taxonomy.php');

        date_default_timezone_set('Europe/London');


        switch_theme('Foody');

    }

    /**
     * @param $post
     * @return int|\WP_Error
     */
    public function insertPost($post)
    {

        $id = wp_insert_post($post['post_data'], true);
        if ($id > 0) {
            foreach ($post['meta_data'] as $key => $value) {
                add_post_meta($id, $key, $value);
            }

        }
        return $id;
    }

    public function getLastPost()
    {


        $args = array(
            'numberposts' => 1,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'include' => '',
            'exclude' => '',
            'meta_key' => '',
            'meta_value' => '',
            'post_type' => 'post',
            'post_status' => 'draft, publish, future, pending, private',
            'suppress_filters' => true
        );

        return wp_get_recent_posts($args, ARRAY_A);
    }

}