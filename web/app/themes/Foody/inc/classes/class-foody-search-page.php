<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/26/18
 * Time: 11:37 AM
 */
class Foody_SearchPage implements Foody_ContentWithSidebar
{

    /**
     * Foody_SearchPage constructor.
     */
    public function __construct()
    {

    }


    function the_featured_content()
    {
        // TODO: Implement the_featured_content() method.
    }

    function the_sidebar_content()
    {
        dynamic_sidebar('foody-sidebar');
    }

    function the_details()
    {
        foody_get_template_part(get_template_directory() . '/template-parts/search-details.php');
    }

    function the_content($page)
    {

      foody_get_template_part(get_template_directory().'/template-parts/search-results.php');
    }

    function getId()
    {
        // TODO: Implement getId() method.
    }
}