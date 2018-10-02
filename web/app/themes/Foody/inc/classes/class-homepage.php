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

    private $sidebar_id = 'foody-sidebar';
    private $mobile_sidebar_id = 'foody-sidebar-mobile';

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
            $this->grid->draw($post, 2);
        }
    }

    public function cover_photo()
    {
        get_template_part('template-parts/content', 'cover-image');
    }

    public function categories_listing()
    {
        dynamic_sidebar('homepage-categories');
    }

    public function team()
    {

        $data = $this->team->list_authors();
        echo '<h3 class="title">הנבחרת שלנו</h3>';
        echo '<div class="team-listing row" data-count="' . $data['count'] . '" dir="rtl">';

        echo $data['content'];

        echo '</div>';

    }

    public function socials_bar()
    {
        Foody_Social::socials_bar();
    }

    public function feed()
    {
        $posts = $this->feed_query();

        foreach ($posts as $post) {
            $this->grid->draw($post, 3);
        }

    }

    public function filter()
    {
        dynamic_sidebar('foody-sidebar');
    }

    private function feed_query()
    {
        // TODO handle post types
        $query = new WP_Query();

        $args = array(
            'post_type' => array('foody_recipe'),
            'number' => get_option('posts_per_page')
        );

        $posts = $query->query($args);

        $posts = array_map(function ($post) {
            return new Foody_Recipe($post);
        }, $posts);

        return $posts;
    }


    public function sidebar()
    {
        echo "<aside class=\"sidebar col pl-0\">";

        $sidebar_name = $this->sidebar_id;
        if (wp_is_mobile()) {
            $sidebar_name = $this->mobile_sidebar_id;
        } else {
            echo "<input name=\"search\" type=\"text\" class=\"search\" title=\"search\" placeholder=\"חיפוש מתכון…\">";
        }

        echo "<div class=\"sidebar-content\">";
        dynamic_sidebar($sidebar_name);
        echo "</div></aside>";
    }


    private function get_featured_categories()
    {
        // TODO
    }

    private function get_featured_posts()
    {
        $posts = array_map(function ($post) {
            return new Foody_Recipe($post);
        }, posts_to_array('featured_content'));

        return $posts;
    }
}