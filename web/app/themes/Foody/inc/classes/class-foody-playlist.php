<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/20/18
 * Time: 8:52 PM
 */
class Foody_Playlist extends Foody_Post
{


    private $recipes;

    public $num_of_recipes;

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
        $current_playlist_index = get_query_var('playlist_index', 0);
        if ($this->recipes != null) {

            if ($current_playlist_index > count($this->recipes) - 1) {
                $current_playlist_index = count($this->recipes) - 1;
            }
            $recipe = $this->recipes[$current_playlist_index];
        }

        return $recipe;
    }

    public function getTitle()
    {
        $title = parent::getTitle();
        $current_recipe = $this->get_current_recipe();
        if (!is_null($current_recipe)) {
            $title = $current_recipe->getTitle();
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
            throw new Exception('no recipes for this recipe');
        }

        $this->recipes = $recipes_posts;
        $this->num_of_recipes = count($this->recipes);
    }

    public function the_details()
    {
        // TODO: Implement the_details() method.
        if (!wp_is_mobile()) {
            $current_recipe = $this->get_current_recipe();
            $current_recipe->the_details();
        } else {
            foody_get_template_part(
                get_template_directory() . '/template-parts/content-playlist-details.php',
                [
                    'playlist' => $this
                ]
            );
        }
    }

    public function get_playlist_title()
    {
        return parent::getTitle();
    }
}