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
        $this->grid->loop($posts, 2, 12, null, [], null, ['image_size' => '']);
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
        Foody_Social::socials_bar();
    }

    public function feed()
    {
        $query = new WP_Query();

        $args = $this->foody_query->get_query('homepage', [], true);

        $posts = $query->query($args);

        $posts = array_map('Foody_Post::create', $posts);

        if (wp_is_mobile()) {
            $posts = array_merge($this->get_featured_posts(), $posts);
        }

        $grid = [
            'id' => 'homepage-feed',
            'cols' => 2,
            'posts' => $posts,
            'more' => $this->foody_query->has_more_posts($query),
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

    public function feed_query()
    {
        $query = new WP_Query();

        $args = $this->foody_query->get_query('homepage', [], true);

        $posts = $query->query($args);

        $posts = array_map('Foody_Post::create', $posts);

        if (wp_is_mobile()) {
            $posts = array_merge($this->get_featured_posts(), $posts);
        }

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

        echo "<div class=\"sidebar-content\">";
        dynamic_sidebar($sidebar_name);
        dynamic_sidebar('foody-social');
        echo "</div></aside>";
    }

    private function get_featured_posts()
    {

        $featured = get_field('featured_items');
        $posts = [];
        if (!empty($featured)) {

            $posts = array_map(function ($row) {

                // WP_Post
                $foody_post = Foody_Post::create($row['post']);

                if (!empty($row['image']['url'])) {
                    $foody_post->setImage($row['image']['url']);
                }

                if (!empty($row['secondary_text'])) {
                    $foody_post->setDescription($row['secondary_text']);
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
        $promoted_groups = get_field('promoted_groups');

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

    public function the_approvals_popup()
    {
        $approved_marketing = Foody_User::user_has_meta('marketing');
        $approved_e_book = Foody_User::user_has_meta('e_book');
        $registration_page = get_page_by_title('הרשמה');
        $show = get_field('show', $registration_page);

        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $referer = strtolower($referer);
        $registration = get_permalink(get_page_by_title('הרשמה'));
        $registration = add_query_arg('registered', 1, $registration);
        $registration = strtolower($registration);
        if ((!$approved_marketing || (!$approved_e_book && $show)) && ($referer == $registration)) {
            $modal_args = [
                'id' => 'approvals-modal',
                'title' => '',
                'hide_buttons' => true,
                'body' => foody_get_template_part(get_template_directory() . '/template-parts/content_approvals_popup.php', ['return' => true])
            ];

            foody_get_template_part(get_template_directory() . '/template-parts/common/modal.php', $modal_args);
        }
    }
}