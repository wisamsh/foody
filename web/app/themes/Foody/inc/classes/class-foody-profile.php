<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/10/18
 * Time: 4:59 PM
 */
class Foody_Profile
{

    private $sidebar_filter;

    private $foody_user;

    private $grid;

    private $form_classes = ['profile-form'];

    /**
     * Foody_Profile constructor.
     */
    public function __construct()
    {
        $this->sidebar_filter = new SidebarFilter();
        $this->foody_user = new Foody_User();
        $this->grid = new FoodyGrid();
    }

    /**
     * Displays the sidebar
     */
    public function sidebar()
    {
        dynamic_sidebar('foody-sidebar');
    }

    public function get_image()
    {
        return $this->foody_user->get_image('90');
    }

    public function get_name()
    {
        $first = $this->foody_user->user->first_name;
        $last = $this->foody_user->user->last_name;
        return sprintf('%s %s', $first, $last);
    }

    public function get_email()
    {
        return $this->foody_user->user->user_email;
    }

    /**
     * Displays a grid of items the
     * user added to the favorites list
     */
    public function my_favorites()
    {
        global $wp_session;

        $favorites = $this->foody_user->get_favorites();

        if (!empty($favorites)) {



            echo '<h2 class="title">ספר המתכונים שלי</h2>';


            $grid_args = [
                'id' => 'my-recipes-grid',
                'posts' => $favorites,
                'more' => false,
                'cols' => 1
            ];

            foody_get_template_part(get_template_directory() . '/template-parts/common/foody-grid.php', $grid_args);

        } else {
            foody_get_template_part(get_template_directory() . '/template-parts/content-no-recipes.php');
        }

    }

    /**
     * Displays content related to
     * followed channels or authors
     */
    public function my_topics_content()
    {
        ?>
        <h2 class="title">
            <?php echo __('מתכונים מערוצים', 'foody') ?>
        </h2>
        <?php

        $posts = $this->foody_user->get_followed_content();

        $posts = array_map('Foody_Post::create', $posts);


        $grid_args = [
            'id' => 'my-channels-grid',
            'posts' => $posts,
            'more' => false,
            'cols' => 1
        ];

        foody_get_template_part(get_template_directory() . '/template-parts/common/foody-grid.php', $grid_args);


    }

    /**
     * Displays a list of followed
     * channels and authors.
     */
    public function my_followed_topics()
    {
        $list = $this->foody_user->get_followed_topics();

        if (!is_null($list) && !empty($list)) {
            foody_get_template_part(
                get_template_directory() . '/template-parts/content-user-managed-list.php',
                $list
            );
        } else {
            foody_get_template_part(
                get_template_directory() . '/template-parts/content-no-followed-topics.php'
            );
        }


    }

    public function favorites_tab()
    {
        global $wp_session;

        $count = empty($wp_session['favorites']) ? 0 : count($wp_session['favorites']);


        echo '<span>' . sprintf("המתכונים שלי (%s)", $count) . '</span>';
    }

    public function channels_tab()
    {
        $results = $this->foody_user->get_followed_content(0, 12, true);
        $count = 0;
        if (isset($results[0]) && isset($results[0]->count)) {
            $count = $results[0]->count;
        }


        echo '<span>' . sprintf('מתכונים מערוצים (%s)', $count) . '</span>';
    }

    public function the_content()
    {


        $followed = $this->foody_user->get_followed_content();
        $followed = array_map('Foody_Post::create', $followed);
        $results = $this->foody_user->get_followed_content(0, 12, true);
        $count = 0;
        if (isset($results[0]) && isset($results[0]->count)) {
            $count = $results[0]->count;
        }


        global $wp_session;

        $favorite_count = 0;
        if (isset($wp_session['favorites']) && !empty($wp_session['favorites']) && count($favorite_posts = $wp_session['favorites']) > 0) {
            $recipes = [];
            $favorite_posts = $wp_session['favorites'];

            foreach ($favorite_posts as $favorite_post) {
                $post = get_post($favorite_post);
                $recipes[] = Foody_Post::create($post);
            }

            $favorite_count = count($favorite_posts);
            $recipes_content = $this->get_posts_grid(
                $recipes,
                'my-recipes',
                'ספר המתכונים שלי'
            );
        } else {
            $recipes_content = '';
        }

        $tabs = [
            [
                'title' => sprintf("המתכונים שלי (%s)", $favorite_count),
                'target' => 'my-recipes-tab-pane',
                'content' => $recipes_content,
                'classes' => 'show active',
                'link_classes' => 'active'
            ],
            [
                'title' => sprintf('מתכונים מערוצים (%s)', $count),
                'target' => 'playlists-tab-pane',
                'content' =>
                    $this->get_posts_grid(
                        $followed,
                        'my-channels',
                        'מתכונים מערוצים'
                    )
            ]
        ];

        foody_get_template_part(get_template_directory() . '/template-parts/common/foody-tabs.php', $tabs);
    }

    private function get_posts_grid($posts, $id, $title)
    {

        $grid = [
            'id' => $id,
            'cols' => 2,
            'posts' => $posts,
            'classes' => [
                $id
            ],
            'more' => false,
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

    public function the_user_details_form()
    {
        foody_get_template_part(get_template_directory() . '/template-parts/content-profile-edit.php', [
            'form_classes' => $this->form_classes
        ]);
    }

    public function the_password_change_form()
    {
        foody_get_template_part(get_template_directory() . '/template-parts/content-password-change.php', [
            'form_classes' => $this->form_classes
        ]);
    }
}