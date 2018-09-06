<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/25/18
 * Time: 3:33 PM
 */
class Foody_PostFactory
{


    /**
     * Foody_PostFactory constructor.
     */
    public function __construct()
    {

    }


    /**
     * @param WP_Post $post
     */
    public static function get_post($post){
        switch ($post->post_type){
            case 'foody_recipe':
                return new Foody_Recipe($post);
                break;

            default:
                return new Foody_Article($post);
                break;

        }
    }
}