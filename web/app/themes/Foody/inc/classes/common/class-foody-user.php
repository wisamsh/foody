<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/16/18
 * Time: 4:17 PM
 */
class Foody_User
{
    const META_KEY_FOLLOWED_AUTHORS = 'followed_authors';
    const META_KEY_FOLLOWED_CHANNELS = 'followed_channels';

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
     * @param bool|int $user_id
     */
    public function __construct($user_id = false)
    {
        if (!$user_id) {
            $this->user = wp_get_current_user();
        } else {
            $this->user = get_userdata($user_id);
        }

        $this->user_id = $this->user->ID;

    }

    /**
     * @return array
     */
    public function get_followed_authors()
    {
        global $wp_session;

        $followed_authors = $wp_session['followed_authors'];

        if (empty($followed_authors)) {
            $followed_authors = [];
        }

        return array_map(function ($author_id) {

            $user = new Foody_User($author_id);

            return [
                'image' => $user->get_image(),
                'link' => get_author_posts_url($author_id),
                'name' => $user->user->display_name,
                'id' => $author_id,
                'type' => 'followed_authors'
            ];

        }, $followed_authors);

    }

    /**
     * @return array
     */
    public function get_followed_channels()
    {
        global $wp_session;

        $followed_channels = $wp_session['followed_channels'];

        if (empty($followed_channels)) {
            $followed_channels = [];
        }

        return array_map(function ($channel_id) {


            $post = get_post($channel_id);
            $channel = new Foody_Channel($post);

            return [
                'image' => $channel->getImage('thumbnail'),
                'link' => get_permalink($channel_id),
                'name' => $channel->getTitle(),
                'id' => $channel_id,
                'type' => 'followed_channels'
            ];

        }, $followed_channels);

    }

    /**
     * @return array
     */
    public function get_followed_topics()
    {
        return array_merge(
            $this->get_followed_authors(),
            $this->get_followed_channels()
        );
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array|null|object
     */
    public function get_followed_content($offset = 0, $limit = 10, $count = false)
    {
        global $wp_session;
        global $wpdb;

        $authors = $wp_session['followed_authors'];
        $channels = $wp_session['followed_channels'];


        $query_authors = "";
        $query_channels = "";

        if (is_array($authors) && count($authors) > 0) {
            $authors = string_array_to_int($authors);
            $authors = implode(',', $authors);
            $query_authors = "post_author IN ($authors)";
        }

        if (is_array($channels) && count($channels) > 0) {
            $channels = string_array_to_int($channels);
            $channels = implode(',', $channels);
            $query_channels = "ID IN (
                SELECT  p.ID FROM wp_postmeta pm
                LEFT JOIN wp_posts p ON pm.meta_value LIKE concat('%\"', p.ID,'\"%')
                WHERE pm.post_id IN ($channels) AND meta_key IN ('related_playlists','related_recipes')
            )";
        }


        if (empty($query_authors) && empty($query_channels)) {
            $results = [];
        } else {
            $ip = str_replace('.', '', $_SERVER['REMOTE_ADDR']);
            $seed = ($ip + date('Hjn'));

            $select = '*';
            if ($count) {
                $select = 'count(*) as count ';
            }

            $query = "SELECT $select FROM wp_posts";

            $query .= " WHERE ";

            $query .= implode(' OR ', array_filter([$query_authors, $query_channels]));

            $query .= "AND post_type IN ('foody_recipe','foody_playlist')
            AND post_status = 'publish'
            ORDER BY rand($seed)
            LIMIT $offset,$limit";

            $results = $wpdb->get_results($query);
        }


        return $results;
    }

    public function get_image($size = '52')
    {

        if ($this->user->ID > 0) {
            $user_images = get_user_meta($this->user_id, 'wp_user_avatars', true);
            if (!empty($user_images) && is_array($user_images)) {
                if (!isset($user_images[$size])) {

                    $available_sizes = array_keys($user_images);
                    $available_sizes = array_filter($available_sizes, function ($key) {
                        return is_numeric($key);
                    });

                    if (!empty($available_sizes)) {
                        $size = $this->get_closest($size, $available_sizes);
                    }
                }

                $image = $user_images[$size];
            } else {
                $image = wsl_get_wp_user_custom_avatar('gravatar.com',$this->user->ID,$size,'','');

            }
        }

        if (empty($image)) {
            $image = $GLOBALS['images_dir'] . 'avatar.png';
        }

        return $image;
    }

    // TODO move to utils
    function get_closest($search, $arr)
    {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item - $search)) {
                $closest = $item;
            }
        }
        return $closest;
    }

}