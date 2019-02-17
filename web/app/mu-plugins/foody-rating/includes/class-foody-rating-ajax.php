<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/10/18
 * Time: 4:01 PM
 */
class Foody_Rating_Ajax
{

    private $table;


    /**
     * Foody_Rating_Ajax constructor.
     */
    public function __construct()
    {
        global $wpdb;

        $this->table = $wpdb->prefix . 'foody_rating';

        $this->register_ajax();
    }


    private function register_ajax()
    {
        add_action('wp_ajax_foody_rating', array($this, 'ajax_add_rating'));
        add_action('wp_ajax_foody_get_rating', array($this, 'ajax_get_rating'));
    }

    public function ajax_add_rating()
    {
        $logged_in = is_user_logged_in();

        if ($logged_in) {


            $required = [
                'post_type',
                'post_id',
                'value'
            ];

            if ($this->validate_post_required($required)) {

                $post_id = $_POST['post_id'];
                $post_type = $_POST['post_type'];
                $value = $_POST['value'];

                $user_id = get_current_user_id();

                $result = $this->update_rating($user_id, $post_type, $post_id, $value);

                if (!$result) {
                    $error = 'db update failed';
                    $code = 500;
                }


            } else {
                $error = 'bad request';
            }


        } else {
            $error = 'please log in';
            $code = 401;
        }

        if (isset($error)) {
            $code = isset($code) ? $code : 400;
            wp_send_json_error(['message' => $error], $code);
        } else {
            wp_send_json_success();
        }
    }

    public function ajax_get_rating(){


        if(isset($_POST['post_id'])){
            $post_id = $_POST['post_id'];

            $rating = get_post_rating($post_id);


            wp_send_json_success(['rating'=>$rating]);

        }else{
            wp_send_json_error(['message'=>'post id is required'],400);
        }
    }

    private function update_rating($user_id, $post_type, $post_id, $value)
    {
        if (!is_numeric($value)) {
            $result = false;
        } else {
            $value = doubleval($value);
            global $wpdb;

            $data = compact('user_id', 'post_type', 'post_id', 'value');

            $result = $wpdb->replace($this->table, $data);
        }


        return $result;
    }

    private function validate_post_required($vars)
    {
        $valid = true;

        foreach ($vars as $var) {
            if (!isset($_POST[$var])) {
                $valid = false;
                break;
            }
        }

        return $valid;
    }

    public static function get_result($column, $results, $def = '')
    {
        $result = $def;
        if (!is_null($results) && !empty($results)) {
            $result = $results[0]->$column;
        }

        return $result;
    }
}

new Foody_Rating_Ajax();

function get_rating_by_user_and_post($post_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'foody_rating';
    $rating = 0;

    $user_id = get_current_user_id();
    if ($user_id) {
        $query = "
            SELECT value FROM $table where user_id = $user_id AND post_id = $post_id
        ";

        $results = $wpdb->get_results($query);
        $rating = Foody_Rating_Ajax::get_result('value', $results, $rating);
    }


    return $rating;
}

function get_post_rating($post_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'foody_rating';
    $query = "SELECT AVG(value) AS rating FROM $table WHERE post_id = $post_id ";
    $results = $wpdb->get_results($query);
    $rating = 0;
    if ($results && !empty($results)) {
        $rating = $results[0]->rating;
    }
    return $rating;
}