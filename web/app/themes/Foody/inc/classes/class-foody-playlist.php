<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/20/18
 * Time: 8:52 PM
 */
class Foody_Playlist extends Foody_Post
{

    /**
     * @var Foody_Recipe[].
     *
     */
    public $recipes;

    public $num_of_recipes;

    private $debug = false;

    /**
     * Foody_Playlist constructor.
     * @param WP_Post $post
     */
    public function __construct($post)
    {
        parent::__construct($post);

        $this->init();
    }


    public function the_featured_content()
    {
        $current_recipe = $this->get_current_recipe();

        $current_recipe->the_featured_content();
    }

    public function the_sidebar_content()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-playlist-recipes.php',
            [
                'playlist' => $this,
                'recipes' => $this->recipes,
                'title' => $this->get_playlist_title()
            ]
        );

        $current_recipe = $this->get_current_recipe();
        if (!is_null($current_recipe)) {
            $current_recipe->the_sidebar_content(['hide_playlists' => true]);
        }
    }


    /**
     * @return null|Foody_Recipe
     */
    public function get_current_recipe()
    {
        $recipe = null;
        if ($this->recipes != null) {
            $index = $this->get_current_recipe_index();
            $recipe = $this->recipes[$index];
        }

        return $recipe;
    }


    public function get_current_recipe_index()
    {
        if (!is_null(get_query_var('recipe', null))) {
            $recipe_name = get_query_var('recipe');
            $index = array_search($recipe_name, array_map(function ($recipe) use ($recipe_name) {
                return urldecode($recipe->post->post_name);
            }, $this->recipes));
        } else {
            $index = 0;
        }
        return $index;
    }

    public function next()
    {
        $link = '';
        $next_index = $this->get_current_recipe_index() + 1;
        if ($next_index < count($this->recipes)) {
            $recipe = $this->recipes[$next_index];
            $link = $this->get_playlist_recipe_link($recipe);
        }


        return $link;
    }

    public function prev()
    {
        $link = '';
        $prev_index = $this->get_current_recipe_index() - 1;
        if ($prev_index >= 0) {
            $recipe = $this->recipes[$prev_index];
            $link = $link = $this->get_playlist_recipe_link($recipe);
        }

        return $link;

    }

    public function getTitle()
    {
        if (is_single('foody_playlist')){
            $title = parent::getTitle();
            $current_recipe = $this->get_current_recipe();
            if (!is_null($current_recipe)) {
                $title = $current_recipe->getTitle();
            }
        }else{
            $title = parent::getTitle();
        }

        return $title;
    }

    public function getDescription()
    {
        $description = parent::getDescription();
        $current_recipe = $this->get_current_recipe();
        if (!is_null($current_recipe)) {
            $description = $current_recipe->getDescription();
        }

        return $description;
    }

    public function getId(): int
    {
        $id = parent::getId();
        if (($current_recipe = $this->get_current_recipe()) != null) {
            $id = $current_recipe->getId();
        }

        return $id;
    }


    private function init()
    {
        $this->load_recipes();
    }

    private function load_recipes()
    {

        $recipes_posts = posts_to_array('recipes', $this->post->ID, 'Foody_Recipe');
        if (is_null($recipes_posts) || empty($recipes_posts)) {
            if ($this->debug) {
                throw new Exception('no recipes for this playlist');
            }
            $this->recipes = [];
            $this->num_of_recipes = 0;
        } else {
            $this->recipes = $recipes_posts;
            $this->num_of_recipes = count($this->recipes);
        }
    }

    public function the_details()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-playlist-details.php',
            [
                'playlist' => $this
            ]
        );
    }

    public function get_playlist_title()
    {
        return parent::getTitle();
    }

    public function the_mobile_sidebar_content()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-playlist-recipes.php',
            [
                'playlist' => $this,
                'recipes' => $this->recipes,
                'title' => $this->get_playlist_title(),
                'hide_title' => true
            ]
        );

    }

    public function get_playlist_recipe_link($recipe)
    {
        $link = add_query_arg('recipe', $recipe->post->post_name, get_permalink());
        return $link;
    }

    public function js_vars()
    {
        $current_recipe = $this->get_current_recipe();

        $vars = parent::js_vars();

        if (!empty($current_recipe)) {
            $vars['currentRecipe'] = $current_recipe->js_vars();
        }

        return $vars;
    }

    public function featured_content_classes()
    {
        $current_recipe = $this->get_current_recipe();
        if(!empty($current_recipe)){
            return $current_recipe->featured_content_classes();
        }
        return parent::featured_content_classes(); // TODO: Change the autogenerated stub
    }
}