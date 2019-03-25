<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/25/19
 * Time: 7:16 PM
 */

class Foody_Feed_Filter extends Foody_Post
{

    private $foody_search;

    /**
     * Foody_Feed_Filter constructor.
     */
    public function __construct($post)
    {
        parent::__construct($post);
        $this->foody_search = new Foody_Search('foody_filter',[$this->id]);
    }

    public function the_details()
    {
        echo '<section class="filter-details-container">';
        bootstrap_breadcrumb();
        the_title('<h1 class="title">', '</h1>');
        echo '</section>';
    }

    public function the_sidebar_content($args = array())
    {
        dynamic_sidebar('foody-sidebar');
        dynamic_sidebar('foody-social');
    }

    public function feed()
    {
        $foody_query = Foody_Query::get_instance();

        $args = $foody_query->get_query('foody_filter', [$this->id], true);

        $query = new WP_Query($args);

        $posts = $query->get_posts();

        $posts = array_map('Foody_Post::create', $posts);

        $grid_args = [
            'id' => 'foody-filter-feed',
            'posts' => $posts,
            'more' => $foody_query->has_more_posts($query),
            'cols' => 2,
            'header' => [
                'sort' => true
            ]
        ];

        echo '<div class="container-fluid feed-container filter-feed-container">';
        foody_get_template_part(get_template_directory() . '/template-parts/common/foody-grid.php', $grid_args);
        echo '</div>';
    }

}