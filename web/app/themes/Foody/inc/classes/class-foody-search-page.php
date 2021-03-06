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

    private $latest_search = '';

    private $latest_search_results = [];

    private $latest_search_query = [];

    /**
     * Foody_SearchPage constructor.
     */
    public function __construct()
    {
        $this->foody_query = Foody_Query::get_instance();
        function search_filter(WP_Query $query)
        {
            if ($query->is_search || (!empty($_POST) && (isset($_POST['action']) && $_POST['action'] == 'foody_filter'))) {
                $query->set('post_type', ['foody_feed_channel', 'foody_recipe', 'foody_playlist', 'post']);
            }

            return $query;
        }

        add_filter('pre_get_posts', 'search_filter');
    }


    function the_featured_content()
    {

    }

    public function sidebar()
    {
        dynamic_sidebar('foody-sidebar');
    }

    function the_sidebar_content()
    {
        $this->sidebar();
        dynamic_sidebar('foody-social');
    }

    function the_details()
    {
        foody_get_template_part(get_template_directory() . '/template-parts/search-details.php');
    }

    function the_content($page)
    {
        foody_get_template_part(get_template_directory() . '/template-parts/search-results.php', ['search' => $this]);

        // mobile filter
        if ( get_current_blog_id() == 1 ){
            foody_get_template_part(get_template_directory() . '/template-parts/common/mobile-filter.php', [
                'sidebar' => array($this, 'sidebar'),
                'wrap' => true
            ]);
        } else {
            return '';
        }

    }

    public function has_results()
    {
        return $this->the_results(true);
    }

    public function the_results($for_has_results = false)
    {

        $args = $this->foody_query->get_query('search', [], true);
        if(!$for_has_results && is_array($args) && isset($args['s']) && $args['s'] == $this->latest_search){
            $posts = $this->latest_search_results;
            $query = $this->latest_search_query;

            // init latest_search and latest_search_results
            $this->latest_search = '';
            $this->latest_search_results = [];
            $this->latest_search_query = [];
        }
        else {
            $query = new WP_Query($args);

            global $wp_query;

            // use wordpress query to
            // ensure proper escaping and decoding
            // of special characters
            $query->set('s', $wp_query->get('s'));

            $posts = $query->get_posts();

            if ($for_has_results) {
                $this->latest_search = is_array($args) && isset($args['s']) ? $args['s'] : '';
                $this->latest_search_results = $posts;
                $this->latest_search_query = $query;
                if (is_array($posts) && count($posts) < 1) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        $foody_posts = array_map('Foody_Post::create', $posts);

        $grid = [
            'id' => 'search-results',
            'cols' => 2,
            'posts' => $foody_posts,
            'more' => $this->foody_query->has_more_posts($query),
            'header' => [
                'sort' => true,
                'title' => ''
            ]
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