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

    function foody_get_the_rating($post_id, $part_of_component = false)
    {
        if ($this->foody_has_rating($post_id)) {
            return $this->foody_get_populated_ratings($post_id);
        } else {
            return $this->foody_get_empty_rating($part_of_component);
        }
    }

    function foody_get_empty_rating($part_of_component = false){
        $header_text = $part_of_component ? __('דרגו את המתכון') : __('דרגו');
        $rating_header = '<div class="rating-header">'. $header_text .'</div>';
        $rating_stars = $this->foody_get_empty_stars();

        return $rating_header.$rating_stars;
    }

    function foody_get_empty_stars(){
        $num_of_start = 5;
        $empty_stars_prefix = 'rating/rating-empty-';
        $rating_elements ='<div class="rating-stars-container">';

        for($index = 1; $index<=$num_of_start; $index++){
            $rating_elements .= '<img class="empty-star" data-index="'. $index .'" src="'.  $GLOBALS['images_dir'] . 'icons/'.$empty_stars_prefix . $index .'.png">';
        }

        $rating_elements .= '</div>';

        return $rating_elements;
    }

    function foody_get_populated_ratings($post_id, $with_header = true)
    {
        $ratings = $this->get_all_ratings_by_post_id($post_id, '*');
        $num_of_rates = count($ratings);
        $rated_text = $num_of_rates > 1 ? __('דירגו') : __('דירג');
        $ratings_sum = 0;

        foreach ($ratings as $rating) {
            $ratings_sum += floatval($rating->rating);
        }

        $average_rating = $ratings_sum / $num_of_rates;

        // round to full int or half
        $average_rating = round($average_rating*2)/2;

        $rating_header = '<div class="rating-header"><span class="num-of-rates">' . $num_of_rates . ' ' . $rated_text . ' ' . '</span><span class="rating-avrage">[' . __('ציון') . ' ' . $average_rating . ']</span></div>';

        if (!$this->user_rated($post_id, get_current_user_id())) {
            $rating_ui = $this->foody_get_empty_stars();
        } else {
            $rating_ui = $this->get_results_stars($average_rating);
        }

        if($with_header){
            $rating_res = $rating_header.$rating_ui;
        }
        else{
            $rating_res = $rating_ui;
        }

        return $rating_res;
    }

    function get_results_stars($average_rating){
        $num_of_stars = 5;
        $rating_elements ='<div class="rating-stars-container">';
        $is_average_decimal = foody_rating_is_decimal($average_rating);
        $added_half = false;

        for($index = 1; $index<=$num_of_stars; $index++){
            $class = $index <= $average_rating ? 'full-star' : 'star';
            if($is_average_decimal && !$added_half && $index  > $average_rating){
                $image_path = 'icons/rating/half-star' ;
                $rating_elements .= '<img class="' . $class .'" data-index="'. $index .'" src="'.  $GLOBALS['images_dir'] . $image_path .'.png">';
                $added_half = true;
            } else {
                $image_path = $index <= $average_rating ? 'icons/rating/rating-full-' : 'icons/rating/rating-empty-';
                $rating_elements .= '<img class="' . $class .'" data-index="'. $index .'" src="'.  $GLOBALS['images_dir'] . $image_path . $index .'.png">';
            }
        }

        $rating_elements .= '</div>';

        return $rating_elements;
    }


    function user_rated($post_id, $user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . $this->table_name;
        $ip = $this->foody_get_ip();

        $query = "SELECT rating_id FROM {$table} WHERE postid = {$post_id} AND (userid = {$user_id} AND ip = '{$ip}')";
        $result = $wpdb->get_results($query);

        return count($result) > 0;
    }

    function foody_get_ip()
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

function foody_add_rating()
{
    global $wpdb;
    $rating_obj = new Foody_Rating();
    $table_name = 'foody_ratings';
    $table = $wpdb->prefix . $table_name;
    $post_id = $_POST['postID'];
    $rating = $_POST['rating'];
    $user_id = get_current_user_id();

    $username = $user_id != 0 ? get_userdata($user_id)->user_nicename : 'Guest';
    $rating_ip = $rating_obj->foody_get_ip();
    $post_title = addslashes(get_post($post_id)->post_title);

    /** add to coupons table */
    $wpdb->query("INSERT INTO {$table} (postid, posttitle, rating, ip, username, userid)
                VALUES('$post_id','$post_title','$rating','$rating_ip','$username','$user_id')");

    $calc_rating = ['details' => $rating_obj->foody_get_the_rating($post_id, false), 'component' => $rating_obj->foody_get_the_rating($post_id, true)];

    wp_send_json_success($calc_rating);
}
add_action('wp_ajax_nopriv_foody_add_rating', 'foody_add_rating');
add_action('wp_ajax_foody_add_rating', 'foody_add_rating');

function foody_rating_is_decimal( $val )
{
    return is_numeric( $val ) && floor( $val ) != $val;
}