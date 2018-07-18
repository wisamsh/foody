<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/16/18
 * Time: 4:17 PM
 */
class Foody_User
{

    const META_KEY_CHANNELS = 'favorite_channels';

    /**
     * @var WP_User current logged in user
     */
    public $user;

    /**
     * @var int current logged in user id
     */
    private $user_id;

    /**
     * Foody_User constructor.
     */
    public function __construct($user_id = false)
    {
        if (!$user_id) {
            $this->user = wp_get_current_user();
        } else {
            $this->user = get_userdata($this->user_id);
        }

        $this->user_id = $this->user->ID;

    }


    /*
     *
     * Favorite Channels
     *
     * */

    public function get_favorite_channels_ids()
    {
        return $this->get_meta(self::META_KEY_CHANNELS);

    }

    /**
     * @return array array of channels posts
     */
    public function get_favorite_channels()
    {
        $query = $this->the_favorite_channels();
        return $query->get_posts();
    }


    /**
     * @return WP_Query query prepared for loop
     */
    public function the_favorite_channels()
    {
        $channels_ids = $this->get_favorite_channels_ids();
        $query = new WP_Query(array(
            'post__in' => $channels_ids
        ));

        return $query;
    }


    /*
     *
     * Generic
     *
     * */

    private function get_user_meta_post_array($meta_key)
    {
        $channels_ids = $this->get_meta($meta_key);
        $query = new WP_Query(array(
            'post__in' => $channels_ids
        ));

        return $query->get_posts();
    }

    private function get_meta($meta_key)
    {
        return get_user_meta($this->user_id, $meta_key, true);
    }
}