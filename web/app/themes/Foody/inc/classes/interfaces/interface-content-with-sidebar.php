<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/1/18
 * Time: 2:21 PM
 */
interface Foody_ContentWithSidebar
{
    function the_featured_content();

    function the_sidebar_content();

    function the_details();

    function the_content($page);

    function getId();
}