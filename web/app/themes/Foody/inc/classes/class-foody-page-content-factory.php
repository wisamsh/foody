<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:24 AM
 */
class Foody_PageContentFactory
{


    /**
     * @var Foody_PageContentFactory
     */
    private static $instance;

    /**
     * Foody_PageContentFactory constructor.
     */
    private function __construct()
    {
    }


    public static function get_instance()
    {
        if (Foody_PageContentFactory::$instance == null) {
            Foody_PageContentFactory::$instance = new Foody_PageContentFactory;
        }

        return Foody_PageContentFactory::$instance;
    }


    /**
     * Returns the relevant class
     * for the page content
     *
     * @return Foody_ContentWithSidebar|null
     */
    public function get_page()
    {

        $page = null;

        if (is_single()) {

            global $post;
            $post_type = get_post_type();

            switch ($post_type) {
                case 'foody_recipe':
                    $page = new Foody_Recipe($post);
                    break;

                case 'foody_channel':
                    $page = new Foody_Channel($post);
                    break;

                case 'foody_playlist':
                    $page = new Foody_Playlist($post);
                    break;
                default:
                    $page = new Foody_Article($post);
                    break;
            }
        } elseif (is_author()) {
            $page = new Foody_Author();

        } elseif (is_category()) {
            $category_id = get_queried_object_id();
            $page = new Foody_Category($category_id);
        }elseif (is_search()){
            $page = new Foody_SearchPage();
        }


        return $page;
    }


}