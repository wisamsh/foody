<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 14/09/20
 * Time: 9:41 PM
 */
class Foody_Rating
{
    private $table_name = 'foody_ratings';

    /**
     * Foody_Lists constructor.
     */
    public function __construct()
    {
    }


    function foody_has_rating($post_id)
    {
        $ratings = $this->get_all_ratings_by_post_id($post_id, 'rating_id');

        return is_array($ratings) ? count($ratings) > 0 : false;
    }

    function get_all_ratings_by_post_id($post_id, $column)
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_name;

        $query = "SELECT {$column} FROM {$table} where postid = " . $post_id;

        return $wpdb->get_results($query);
    }

    function foody_get_the_rating($post_id)
    {
        if ($this->foody_has_rating($post_id)) {
            return $this->foody_get_populated_ratings($post_id);
        } else {
            return $this->foody_get_empty_rating();
        }
    }

    function foody_get_empty_rating(){
        $num_of_start = 5;
        $empty_stars_prefix = 'rating/rating-empty-';
        $rating_elements ='<div class="rating-stars-container">';

        for($index = 1; $index<=$num_of_start; $index++){
            $rating_elements += '<img class="empty-star" data-index="'. $index .'" src="'.  $GLOBALS['images_dir'] . 'icons/'.$empty_stars_prefix . $index .'">';
        }

        $rating_elements += '</div>';

        return $rating_elements;
    }

    function foody_get_populated_ratings($post_id)
    {
        $ratings = $this->get_all_ratings_by_post_id($post_id, '*');
        $num_of_rates = count($ratings);
        $rated_text = $num_of_rates > 1 ? __('דירגו') : __('דירג');
        $ratings_sum = 0;

        foreach ($ratings as $rating) {
            $ratings_sum += floatval($rating->rating);
        }

        $avrage_rating = $ratings_sum / $num_of_rates;

        $rating_header = '<div class="rating-header"><span class="num-of-rates">' . $num_of_rates . ' ' . $rated_text . ' ' . '</span><span class="rating-avrage">[' . __('ציון') . ' ' . $avrage_rating . ']</span></div>';

        if (!$this->user_rated($post_id, get_current_user_id())) {
            $rating_ui = $this->get_empty_stars();
        } else {
            $rating_ui = $this->get_results_stars();
        }

        return $rating_header . $rating_ui;
    }

    function get_empty_stars(){

    }

    function get_results_stars(){

    }

    function foody_add_rating($post_id, $rating, $user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_name;

        $username = $user_id != 0 ? get_userdata($user_id)->user_nicename : 'Guest';
        $rating_ip = $this->foody_get_ip();
        $post_title = get_post($post_id)->post_title;

        /** add to coupons table */
        $wpdb->query("INSERT INTO {$table} (postid, posttitle, rating, ip, username, userid)
                VALUES('$post_id','$post_title','$rating','$rating_ip','$username','$user_id')");
    }

    private function user_rated($post_id, $user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_name;
        $ip = $this->foody_get_ip();

        $query = "SELECT rating_id FROM {$table} WHERE postid = {$post_id} AND (userid = {$user_id}  OR ip = '{$ip}')";
        $result = $wpdb->get_results($query);

        return count($result) > 0;
    }

    private function foody_get_ip()
    {
        $keys_list = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        foreach ($keys_list as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return wp_hash(esc_attr($ip));
                    }
                }
            }
        }
    }

}