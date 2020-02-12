<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/6/18
 * Time: 8:35 PM
 */
class Foody_HomePage
{

    private $id;

    /**
     * @var $team FoodyTeam
     * */
    private $team;

    /**
     * @var $grid FoodyGrid
     * */
    private $grid;

    private $sidebar_filter;

    private $sidebar_id = 'foody-sidebar';
    private $mobile_sidebar_id = 'foody-sidebar-mobile';

    /**
     * HomePage constructor.
     */
    public function __construct()
    {
        $this->id = get_option('page_on_front');
    }

    public function init()
    {
        $this->team = new FoodyTeam();
        $this->grid = new FoodyGrid();
        $this->sidebar_filter = new SidebarFilter();
    }

    public function featured()
    {
        if (wp_is_mobile()) {
            return;
        }

        $posts = $this->get_featured_posts();
        $title_el = wp_is_mobile() ? 'h2' : 'div';
        $this->grid->loop($posts, 2, 12, null, [], null, ['image_size' => '', 'title_el' => $title_el]);
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
        echo '<h1 class="title">הנבחרת שלנו</h1>';
        echo '<div class="team-listing row" data-count="' . $data['count'] . '" dir="rtl">';

        echo $data['content'];

        echo '</div>';

    }

    public function socials_bar()
    {

    }

    public function feed($is_mobile = false)
    {
        $query = new WP_Query();

        $foody_query = Foody_Query::get_instance();

        $args = $foody_query->get_query('homepage', [], true);

        $posts = $query->query($args);

        $posts = array_map('Foody_Post::create', $posts);

        if ((wp_is_mobile() || $is_mobile) && get_current_blog_id() != 2 && get_current_blog_id() != 3) {
            $posts = array_merge($this->get_featured_posts(), $posts);
        }

        $grid = [
            'id' => 'homepage-feed',
            'cols' => 2,
            'posts' => $posts,
            'more' => $foody_query->has_more_posts($query),
            'header' => [
                'sort' => true,
                'title' => __('ההמלצות שלנו', 'foody')
            ],
            'title_el' => 'h2'
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

    public function sidebar($container_selector)
    {
        echo "<aside class=\"sidebar sidebar-desktop col pl-0\">";

        $sidebar_name = $this->sidebar_id;
        if (wp_is_mobile() && !foody_is_tablet()) {
            $sidebar_name = $this->mobile_sidebar_id;
        }

        get_search_form();

        echo "<div class=\"sidebar-content\">";
        dynamic_sidebar($sidebar_name);
//        dynamic_sidebar('foody-social');
//        foody_dynamic_sidebar_ajax_loading('foody-social',$container_selector);
        echo "</div></aside>";
    }

    private function get_featured_posts()
    {

        $featured = get_field('featured_items', $this->id);
        $posts = [];
        if (!empty($featured)) {

            $featured = $this->get_relevant_posts($featured);

            $posts = array_map(function ($row) {

                // WP_Post
                $foody_post = Foody_Post::create($row['post']);

                if (!empty($row['image']['url'])) {
                    $foody_post->setImage($row['image']['url']);
                }

                if (!empty($row['title'])) {
                    $foody_post->setTitle($row['title']);
                }

                if (!empty($row['secondary_text'])) {
                    $foody_post->setDescription($row['secondary_text']);
                    $foody_post->description_mobile = $row['secondary_text'];
                }

                if (!empty($row['secondary_text_mobile'])) {
                    $foody_post->description_mobile = $row['secondary_text_mobile'];
                }

                if (!empty($row['link'])) {
                    $foody_post->link = $row['link']['url'];
                }

                return $foody_post;
            }, $featured);
        }

        return $posts;
    }

    /**
     * Displays promoted items groups
     * in the homepage below featured categories
     */
    public function promoted_items()
    {
        $promoted_groups = get_field('promoted_groups', $this->id);

        if (!empty($promoted_groups)) {
            foreach ($promoted_groups as $promoted_group) {
                $args = [
                    'title' => $promoted_group['title'],
                    'items' => array_map(function ($item) {
                        return [
                            'title' => $item['title'],
                            'image' => $item['desktop_image']['url'],
                            'mobile_image' => $item['mobile_image']['url'],
                            'link' => $item['link']
                        ];
                    }, $promoted_group['items'])
                ];

                foody_get_template_part(get_template_directory() . '/template-parts/content-promotions-listing.php', $args);

            }
        }
    }

    public function show_google_adx()
    {
        $show_google_adx = get_option('foody_show_google_adx');

        return $show_google_adx;
    }

    public function the_google_adx()
    {
        $show_google_adx = $this->show_google_adx();
        $google_adx_script = get_option('foody_google_adx_script');

        if ($show_google_adx && !empty($google_adx_script)) {
            echo $google_adx_script;
        }
    }

    public static function get_relevant_posts($posts)
    {
	    $datetime          = new DateTime( null, new DateTimeZone( 'Asia/Jerusalem' ) );
	    $current_date_time = $datetime->format( 'Y-m-d H:i' );

	    $response = [];
	    foreach ( $posts as $post ) {
		    $item_found         = [];
		    $last_selected_date = '0-0-0 00:00';
		    if ( isset( $post['featured_item'] ) ) {
			    foreach ( $post['featured_item'] as $featured_item ) {
				    if ( $featured_item['time_date'] <= $current_date_time ) {
					    if ( $featured_item['time_date'] >= $last_selected_date ) {
						    $item_found         = $featured_item;
						    $last_selected_date = $featured_item['time_date'];
					    }
				    }
			    }
		    }
		    array_push( $response, $item_found );
	    }

	    return $response;
    }
}