<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 6:23 PM
 */
class Foody_Answer extends Foody_Post
{



    function the_details()
    {
        echo '<section class="accessory-details-container">';
        bootstrap_breadcrumb();
        the_title( '<h1 class="title">', '</h1>' );
        echo '</section>';
    }


}
