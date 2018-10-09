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


    /**
     * Foody_Profile constructor.
     */
    public function __construct()
    {
        $this->sidebar_filter = new SidebarFilter();
        $this->foody_user = new Foody_User();
        $this->grid = new RecipesGrid();
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

        if (isset($wp_session['favorites']) && !empty($wp_session['favorites']) && count($favorite_posts = $wp_session['favorites']) > 0) {
            $posts = [];
            foreach ($favorite_posts as $favorite_post) {
                $posts[] = Foody_PostFactory::get_post(get_post($favorite_post));
            }

            echo '<h2 class="title">ספר המתכונים שלי</h2>';

            echo '<section class="my-recipes-grid">';

            $this->grid->loop($posts, 2);

            echo '</section>';
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
        <section class="my-channels-grid">
        <?php

        $posts = $this->foody_user->get_followed_content();

        $posts = array_map('Foody_Post::create', $posts);

        $data_attrs = [

        ];


        $this->grid->loop($posts, 2, true, null, $data_attrs);

        echo '</section>';

    }

    /**
     * Displays a list of followed
     * channels and authors.
     */
    public function my_followed_topics()
    {
        $list = $this->foody_user->get_followed_topics();

        if (!is_null($list) && !empty($list) && false){
            foody_get_template_part(
                get_template_directory() . '/template-parts/content-user-managed-list.php',
                $list
            );
        }else{
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
        $results = $this->foody_user->get_followed_content(0, 10, true);
        $count = 0;
        if (isset($results[0]) && isset($results[0]->count)) {
            $count = $results[0]->count;
        }


        echo '<span>' . sprintf('מתכונים מערוצים (%s)', $count) . '</span>';
    }
}