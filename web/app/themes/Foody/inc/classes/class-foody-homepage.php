<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/6/18
 * Time: 8:35 PM
 */
class Foody_HomePage
{

    private $team;

    private $grid;

    private $sidebar_filter;

    private $sidebar_id = 'foody-sidebar';
    private $mobile_sidebar_id = 'foody-sidebar-mobile';
    private $foody_query;

    /**
     * HomePage constructor.
     */
    public function __construct()
    {
        $this->team = new FoodyTeam();
        $this->grid = new FoodyGrid();
        $this->sidebar_filter = new SidebarFilter();
        $this->foody_query = Foody_Query::get_instance();
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
        $query = new WP_Query();

        $args = $this->foody_query->get_query('homepage', [], true);

        $posts = $query->query($args);

        $posts = array_map('Foody_Post::create', $posts);

//        for ($i=0;$i<100;$i++){
//            $posts[]= $posts[0];
//        }

        $grid = [
            'id' => 'homepage-feed',
            'cols' => 3,
            'posts' => $posts,
            'more' => $this->foody_query->has_more_posts($query),
            'header' => [
                'sort' => true,
                'title' => __('ההמלצות שלנו', 'foody')
            ]
        ];

        foody_get_template_part(
            get_template_directory() . '/template-parts/common/foody-grid.php',
            $grid
        );

    }

    public function filter()
    {
        dynamic_sidebar('foody-sidebar');
    }

    public function feed_query()
    {
        $query = new WP_Query();

        $args = $this->foody_query->get_query('homepage', [], true);

        $posts = $query->query($args);

        $posts = array_map('Foody_Post::create', $posts);


        // debug a lot of feed items
        // make sure to disable when not
        // in dev
        //$posts = array_fill(count($posts)-1,18,$posts[0]);

        return $posts;
    }

    public function sidebar()
    {
        echo "<aside class=\"sidebar col pl-0\">";

        $sidebar_name = $this->sidebar_id;
        if (wp_is_mobile() && !foody_is_tablet()) {
            $sidebar_name = $this->mobile_sidebar_id;
        }

        get_search_form();

//        echo "<input name=\"search\" type=\"text\" class=\"search d-none d-lg-block\" title=\"search\" placeholder=\"חיפוש מתכון…\">";


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