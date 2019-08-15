<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/1/18
 * Time: 2:27 PM
 */
class Foody_Author implements Foody_ContentWithSidebar, Foody_Topic
{

    public $debug = false;

    private $author;

    private $grid;

    private $foody_query;

    /**
     * Foody_Author constructor.
     */
    public function __construct()
    {
        $this->author = get_user_by('slug', get_query_var('author_name'));
        $this->grid = new FoodyGrid();
        $this->foody_query = Foody_Query::get_instance();
    }


    function the_featured_content()
    {
        $image = get_field('cover_image', "user_{$this->author->ID}");

        ?>
        <img src=" <?php echo $image['url'] ?> " alt="<?php echo $this->author->display_name ?>">

        <?php
    }

    function the_sidebar_content()
    {
        $this->sidebar();
        dynamic_sidebar('foody-social');
    }

    public function sidebar()
    {
        dynamic_sidebar('foody-sidebar');
    }

    function the_details()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-topic-details.php',
            [
                'topic' => $this
            ]
        );
    }

    function the_content($page)
    {


        $recipes = $this->get_author_content('foody_recipe');
        $playlists = $this->get_author_content('foody_playlist');

        $tabs = [
            [
                'title' => sprintf(__('מתכונים (%s)'), $recipes['count']),
                'target' => 'recipes-tab-pane',
                'content' =>
                    $this->get_posts_grid(
                        $recipes['posts'],
                        'recipe',
                        $recipes['more']
                    ),
                'classes' => 'show active',
                'link_classes' => 'active'
            ],
//            [
//                'title' => sprintf(__('פלייליסטים (%s)'), $playlists['count']),
//                'target' => 'playlists-tab-pane',
//                'content' =>
//                    $this->get_posts_grid(
//                        $playlists['posts'],
//                        'playlist',
//                        $playlists['more']
//                    )
//            ]
        ];

        foody_get_template_part(get_template_directory() . '/template-parts/common/foody-tabs.php', $tabs);

        // mobile filter
        foody_get_template_part(get_template_directory() . '/template-parts/common/mobile-filter.php', [
            'sidebar' => array($this, 'sidebar'),
            'wrap' => true
        ]);

        if (get_field('more_recipes', "user_{$page->getId()}") != "") {
            ?>
            <section class="no-print col-12">
                <?php $this->load_more_recipes(); ?>
            </section>
        <?php }
    }

    function getId()
    {
        return $this->author->ID;
    }

    private function get_author_content($type)
    {
        $args = $this->foody_query->get_query('author', [
            $this->getId(),
            $type
        ], true);

        $post_types = [
            $type,
            'post'
        ];

        $count = count_user_posts($this->author->ID, $post_types);


        $query = new WP_Query($args);
        $posts = $query->get_posts();
        $foody_posts = array_map('Foody_Post::create', $posts);

        return [
            'posts' => $foody_posts,
            'count' => $count,
            'more' => $this->foody_query->has_more_posts($query)
        ];

    }

    private function get_posts_grid($posts, $type, $more)
    {
        $id = "author-$type-feed";

        $grid = [
            'id' => $id,
            'cols' => 2,
            'posts' => $posts,
            'classes' => [
                "author-$type-grid"
            ],
            'more' => $more,
            'header' => [
                'sort' => true
            ],
            'return' => true
        ];

        return foody_get_template_part(
            get_template_directory() . '/template-parts/common/foody-grid.php',
            $grid
        );
    }

    // Foody_Topic

    function get_type()
    {
        return 'authors';
    }

    function get_id()
    {
        return $this->getId();
    }

    function topic_image()
    {
        return get_avatar_url($this->getId(), ['size' => 96]);
    }

    function topic_title()
    {
        return $this->author->display_name;
    }

    function get_followers_count()
    {
        $query = new WP_User_Query([
            'meta_query' => [
                [
                    [
                        'key' => 'followed_authors',
                        'value' => '"' . $this->author->ID . '"',
                        'compare' => 'LIKE'
                    ]
                ]
            ],
            'meta_key' => 'followed_authors',
            'count_total' => true
        ]);

        $total = $query->get_total();

        return view_count_display($total, 0, null, '%s עוקבים');
    }

    function get_description()
    {
        return $this->author->description;
    }

    function get_breadcrumbs_path()
    {
        $team_link = get_permalink(18);

        return [
            [
                'href' => $team_link,
                'title' => get_post_field('post_title', 18)
            ],
            [
                'title' => $this->topic_title()
            ]
        ];
    }

    function load_more_recipes()
    {
        $recipes = ($this->get_author_content('foody_recipe'))['posts'];
        $recipes_ids = array_map(function ($recipe) {
            return $recipe->get_id();
        }, $recipes);

        $main_categories = $this->get_main_categories($recipes);


        $args = array(
            'post_type' => 'foody_recipe',
            'posts_per_page' => 4,
            'order' => 'ASC',
            'orderby' => 'rand',
            'post__not_in' => $recipes_ids,
            'meta_query' => [
                [
                    'key' => '_yoast_wpseo_primary_category',
                    'compare' => 'IN',
                    'value' => $main_categories,
                    'type' => 'NUMERIC'
                ]
            ],
        );

        $the_query = new WP_Query($args);
        $posts_for_grid = array_map('Foody_Post::create', $the_query->posts);

        $grid = [
            'id' => 'recipe-feed',
            'cols' => 2,
            'posts' => $posts_for_grid,
            'more' => '',
            'header' => [
                'sort' => false,
                'title' => __('מתכונים דומים', 'foody')
            ],
            'title_el' => 'h2'
        ];


        foody_get_template_part(
            get_template_directory() . '/template-parts/common/foody-grid.php',
            $grid
        );
    }

    private function get_main_categories($recipes_list)
    {
        $primary_categories_associative_list = [];
        $primary_categories_list = [];
        foreach ($recipes_list as $recipe) {
            $primary_category = $recipe->get_primary_category_name();
            if (!array_key_exists($primary_category, $primary_categories_associative_list)) {
                $primary_categories_associative_list[$primary_category] = $primary_category;
                array_push($primary_categories_list, $recipe->get_primary_category());
            }
        }
        return $primary_categories_list;
    }
}