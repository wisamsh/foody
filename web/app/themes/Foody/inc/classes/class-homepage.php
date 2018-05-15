<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/6/18
 * Time: 8:35 PM
 */
class HomePage
{

    private $team;

    /**
     * HomePage constructor.
     */
    public function __construct()
    {
        $this->team = new FoodyTeam();
    }


    public function featured()
    {
        if (wp_is_mobile()) {
            return;
        }

        get_template_part('template-parts/content', 'featured');
    }

    public function cover_photo()
    {
        get_template_part('template-parts/content', 'cover-image');
    }

    public function categories_listing()
    {
        for ($i = 0; $i < 5; $i++) {
            get_template_part('template-parts/content', 'category-listing');
        }
    }

    public function team()
    {
         $this->team->list_authors();
    }

}