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

    /**
     * Foody_Author constructor.
     */
    public function __construct()
    {
        $this->author = get_user_by('slug', get_query_var('author_name'));
        $this->grid = new RecipesGrid();
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


        $recipes = $this->get_author_content('foody_recipe', Foody_Recipe::class);
        $playlists = $this->get_author_content('foody_playlist', Foody_Playlist::class);

        $tabs = [
            [
                'title' => sprintf(__('מתכונים (%s)'), $recipes['count']),
                'target' => 'recipes-tab-pane',
                'content' =>
                    $this->get_posts_grid(
                        $recipes['posts'],
                        'recipe'
                    ),
                'classes' => 'show active',
                'link_classes' => 'active'
            ],
            [
                'title' => sprintf(__('פלייליסטים (%s)'), $playlists['count']),
                'target' => 'playlists-tab-pane',
                'content' =>
                    $this->get_posts_grid(
                        $playlists['posts'],
                        'playlist'
                    )
            ]
        ];

        foody_get_template_part(get_template_directory() . '/template-parts/common/foody-tabs.php', $tabs);
    }

    function getId()
    {
        return $this->author->ID;
    }

    private function get_author_content($type, $class)
    {

        $args = [
            'post_type' => $type,
            'posts_per_page' => 9,
            'author' => $this->author->ID
        ];

        $count = count_user_posts($this->author->ID, $type);

        $query = new WP_Query($args);
        $posts = $query->get_posts();
        $foody_posts = array_map(function ($post) use ($class) {
            return new $class($post);
        }, $posts);

        return [
            'posts' => $foody_posts,
            'count' => $count
        ];

    }

    private function get_posts_grid($posts, $type)
    {
        if ($this->debug && !empty($posts)) {
            for ($i = 0; $i < 6; $i++)
                $posts[] = $posts[0];
        }
        return $this->grid->loop($posts, 3, false, $type);
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
}