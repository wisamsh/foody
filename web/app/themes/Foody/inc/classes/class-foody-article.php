<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/8/18
 * Time: 11:32 AM
 */
class Foody_Article extends Foody_Post implements Foody_ContentWithSidebar
{

    public function the_featured_content()
    {
        parent::the_featured_content();
    }

    public function the_sidebar_content()
    {
        parent::the_sidebar_content();
    }

    public function the_details()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-recipe-details.php',
            [
                'page' => $this,
                'show_favorite' => false
            ]
        );
    }
}