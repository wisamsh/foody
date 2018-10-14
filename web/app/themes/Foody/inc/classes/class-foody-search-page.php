<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/26/18
 * Time: 11:37 AM
 */
class Foody_SearchPage implements Foody_ContentWithSidebar
{

    private $foody_query;

    /**
     * Foody_SearchPage constructor.
     */
    public function __construct()
    {
        $this->foody_query = Foody_Query::get_instance();
    }


    function the_featured_content()
    {

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
        foody_get_template_part(get_template_directory() . '/template-parts/search-results.php', ['search' => $this]);
    }

    public function the_results()
    {
        $args = $this->foody_query->get_query('search',[],true);

        $query = new WP_Query($args);

        $posts = $query->get_posts();

        $foody_posts = array_map('Foody_Post::create', $posts);

        $grid = [
            'id' => 'search-results',
            'cols' => 3,
            'posts' => $foody_posts,
            'more' => $this->foody_query->has_more_posts($query)
        ];

        foody_get_template_part(
            get_template_directory() . '/template-parts/common/foody-grid.php',
            $grid
        );
    }

    public function no_results()
    {
        foody_get_template_part(get_template_directory() . '/template-parts/no-results.php');
    }

    function getId()
    {
        return 0;
    }
}