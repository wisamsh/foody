<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/16/18
 * Time: 4:58 PM
 */
class Foody_Channel extends Foody_Post implements Foody_Topic, Foody_ContentWithSidebar
{
    private $grid;
    private $foody_query;

    public function __construct(WP_Post $post = null)
    {
        parent::__construct($post);
        $this->grid = new FoodyGrid();
        $this->foody_query = Foody_Query::get_instance();
    }

    private function get_channel_content($selector, $class)
    {

        $posts = posts_to_array($selector, $this->getId(), $class);

        $count = count($posts);

        return [
            'posts' => $posts,
            'count' => $count
        ];

    }

    private function get_posts_grid($posts, $type, $title = '')
    {
        $id = "channel-$type-feed";

        $grid = [
            'id' => $id,
            'cols' => 2,
            'posts' => $posts,
            'classes' => [
                "channel-$type-grid"
            ],
            'more' => true,
            'header' => [
                'sort' => true,
                'title' => $title
            ],
            'return' => true
        ];

        return foody_get_template_part(
            get_template_directory() . '/template-parts/common/foody-grid.php',
            $grid
        );
    }

    public function the_featured_content()
    {
        $image = get_field('cover_image', $this->getId());

        ?>
        <img src=" <?php echo $image['url'] ?> " alt="<?php echo $this->getTitle() ?>">

        <?php
    }

    function the_sidebar_content($args = array())
    {
        $this->sidebar();
        dynamic_sidebar('foody-social');
    }

    public function sidebar()
    {
        dynamic_sidebar('foody-sidebar');
    }

    public function the_details()
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
        $recipes = $this->get_channel_content('related_recipes', Foody_Recipe::class);
        $playlists = $this->get_channel_content('related_playlists', Foody_Playlist::class);

        $query = new WP_Query();

        $args = $this->foody_query->get_query('channel', [
            $this->id
        ]);

        $posts = $query->query($args);


        $posts = array_map('Foody_Post::create', $posts);

        $tabs = [
            [
                'title' => sprintf(__('מתכונים (%s)'), $recipes['count']),
                'target' => 'recipes-tab-pane',
                'content' =>
                    $this->get_posts_grid(
                        $posts,
                        'recipe',
                        __('')
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
//                        'playlist'
//                    )
//            ]
        ];

        foody_get_template_part(get_template_directory() . '/template-parts/common/foody-tabs.php', $tabs);

        // mobile filter
        foody_get_template_part(get_template_directory() . '/template-parts/common/mobile-filter.php', [
            'sidebar' => array($this, 'sidebar'),
            'wrap' => true
        ]);

    }

    // Foody_Topic

    function topic_image()
    {
        return $this->getImage();
    }

    function topic_title()
    {
        return $this->getTitle();
    }

    function get_followers_count()
    {
        $query = new WP_User_Query([
            'meta_query' => [
                [
                    [
                        'key' => 'followed_channels',
                        'value' => '"' . $this->getId() . '"',
                        'compare' => 'LIKE'
                    ]
                ]
            ],
            'meta_key' => 'followed_channels',
            'count_total' => true
        ]);

        $total = $query->get_total();
        return view_count_display($total, 0, null, '%s עוקבים');
    }

    function get_description()
    {
        $this->getDescription();
    }

    function get_type()
    {
        return 'channels';
    }

    function get_id()
    {
        return $this->getId();
    }


    function get_breadcrumbs_path()
    {
        return [
            ['title' => $this->getTitle()]
        ];
    }
}