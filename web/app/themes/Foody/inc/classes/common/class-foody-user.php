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

    public $favorites;

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

        $this->favorites = get_user_meta($this->user_id, 'favorites', true);


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
                'image' => get_the_post_thumbnail($channel->id),
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
     * Get an array of Foody_Post objects
     * for the user's favorites posts
     * @return Foody_Post[]
     */
    public function get_favorites()
    {
        $user_favorites = [];

        if (!empty($this->favorites)) {

            $posts = array_map('get_post', $this->favorites);
            $posts = array_filter($posts, function ($post) {
                $valid = false;

                if (!empty($post)) {
                    $valid = true;
                }

                return $valid;
            });

            usort($posts, function ($post_a, $post_b) {
                return strtotime($post_b->post_date_gmt) - strtotime($post_a->post_date_gmt);
            });

            $user_favorites = array_map('Foody_Post::create', $posts);
        }

        return $user_favorites;

    }

    /**
     * @param int $offset
     * @param int $limit
     * @param bool $count
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
                if (isset($user_images[$size])) {
                    $image = $user_images[$size];
                    if (!empty($image)) {
                        $image = "<img class='avatar' src='$image' >";
                    }
                } elseif (isset($user_images['full'])) {
                    $image = $user_images['full'];
                    if (!empty($image)) {
                        $image = "<img class='avatar' src='$image' >";
                    }
                }
            } else {
                $image = wsl_get_wp_user_custom_avatar('gravatar.com', $this->user->ID, $size, '', '');
            }
        }

        if (empty($image) || 'gravatar.com' == $image) {
            $image = $GLOBALS['images_dir'] . 'avatar.svg';
            $image = "<img class='avatar default-avatar' src='$image' >";
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


    public static function is_user_subscriber()
    {

        $is_subscriber = false;

        global $user;
        if (!empty($user) && $user->ID > 0) {
            if (user_can($user, 'subscriber')) {
                $is_subscriber = true;
            } elseif (isset($user->roles) && is_array($user->roles)) {
                if (in_array('foody_fut_user', $user->roles) || in_array('subscriber', $user->roles)) {
                    $is_subscriber = true;
                }
            }
        }
        
        return $is_subscriber;
    }

    public static function is_current_user_social()
    {
        $is_social_user = false;

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $social = get_user_meta($user_id, 'wsl_current_provider', true);
            $is_social_user = !empty($social);
        }

        return $is_social_user;
    }

    public static function has_user_seen_approvals()
    {
        $has_user_seen_approvals = false;

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $seen_approvals = get_user_meta($user_id, 'seen_approvals', true);
            $has_user_seen_approvals = !empty($seen_approvals);
        }

        return $has_user_seen_approvals;
    }

    public static function is_first_login()
    {

        $is_first_login = false;
        if (is_user_logged_in()) {
            $login_amount = get_user_meta(get_current_user_id(), 'login_amount', true);
            $is_first_login = $login_amount <= 1;
        }
        return $is_first_login;
    }

    public static function user_has_meta($meta_key)
    {
        $value = false;
        if (is_user_logged_in()) {
            $value = get_user_meta(get_current_user_id(), $meta_key, true) == true;
        }
        return $value;
    }
}