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

    private $grid;

    private $sidebar_filter;

    /**
     * HomePage constructor.
     */
    public function __construct()
    {
        $this->team = new FoodyTeam();
        $this->grid = new RecipesGrid();
        $this->sidebar_filter = new SidebarFilter();
    }


    public function featured()
    {
        if (wp_is_mobile()) {
            return;
        }

        $posts = $this->get_featured_posts();


        foreach ($posts as $post) {
            $this->grid->draw(2);
        }
//        get_template_part('template-parts/content', 'featured');
    }

    public function cover_photo()
    {
        get_template_part('template-parts/content', 'cover-image');
    }

    public function categories_listing()
    {
        $num_of_categories = wp_is_mobile() ? 4 :5;
        for ($i = 0; $i < $num_of_categories; $i++) {
            get_template_part('template-parts/content', 'category-listing');
        }
    }

    public function team()
    {
        $this->team->list_authors();
    }

    public function recommended(){
        $posts = $this->get_recommended_posts();

        foreach ($posts as $post) {
            $this->grid->draw(1);
        }
    }

    public function feed()
    {
        $posts = $this->feed_query();


        foreach ($posts as $post) {
            $this->grid->draw(3);
        }

    }

    public function filter(){
        $this->sidebar_filter->tags_groups();
    }


    private function feed_query()
    {
        // TODO implement

        $posts = array();
        for ($i = 0; $i < 9; $i++) {
            $posts[] = new Recipe();
        }
        return $posts;
    }


    private function get_featured_categories(){

    }

    private function get_featured_posts(){
        $posts = array();
        for ($i = 0; $i < 2; $i++) {
            $posts[] = new Recipe();
        }
        return $posts;
    }


    private function get_recommended_posts(){
        $posts = array();
        for ($i = 0; $i < 10; $i++) {
            $posts[] = new Recipe();
        }
        return $posts;
    }

}