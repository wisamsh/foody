<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 6:23 PM
 */
class Foody_Recipe extends Foody_Post
{

    private $author_image;

    private $author_name;

    private $view_count;

    private $duration;

    private $video;

    private $sponsership;

    private $overview;

    private $ingredients_groups;

    private $ingredients_count;

    private $number_of_dishes;


    private $debug = true;

    public $body;


    private $custom_fields = array(
        'video' => '',
        'duration' => ''
    );


    /**
     * Recipe constructor.
     */
    public function __construct(WP_Post $post = null)
    {
        parent::__construct($post);

        if ($post != null) {

            $this->init();

            $this->duration = $this->video['duration'];

            $this->view_count = view_count_display(foody_get_post_views(get_the_ID()), 0);

            $this->author_image = get_the_author_meta('wp_user_avatars', get_the_author_meta('ID'))['90'];
            $this->author_name = foody_posted_by(false);// get_the_author_meta('display_name', get_the_author_meta('ID'));

            $this->body = apply_filters('the_content', get_the_content());

        } else {
            $this->duration = 2.45;
            $this->view_count = view_count_display(13454, 1);

            $this->author_image = 'http://localhost:8000/app/uploads/2018/05/avatar_user_2_1527527183-250x250.jpg';// $GLOBALS['images_dir'] . 'matan.jpg';
            $this->author_name = "ישראל אהרוני";
        }


    }

    /**
     * @return mixed
     */
    public function getNumberOfDishes()
    {
        return $this->number_of_dishes;
    }

    /**
     * @param mixed $number_of_dishes
     */
    public function setNumberOfDishes($number_of_dishes)
    {
        $this->number_of_dishes = $number_of_dishes;
    }


    private function init()
    {
        $this->init_video();
        $this->init_ingredients();
        $this->init_overview();
    }

    /**
     * @return string
     */
    public function getAuthorImage(): string
    {
        return $this->author_image;
    }

    /**
     * @param string $author_image
     */
    public function setAuthorImage(string $author_image)
    {
        $this->author_image = $author_image;
    }

    /**
     * @return string
     */
    public function getViewCount(): string
    {
        return $this->view_count;
    }

    /**
     * @param string $view_count
     */
    public function setViewCount(string $view_count)
    {
        $this->view_count = $view_count;
    }

    /**
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     */
    public function setDuration(float $duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->author_name;
    }

    /**
     * @param mixed $author_name
     */
    public function setAuthorName($author_name)
    {
        $this->author_name = $author_name;
    }


    public function the_video_box()
    {
        if ($this->post != null) {

            while (have_rows('video')): the_row();
                $video_url = get_sub_field('url');
                $parts = explode('v=', $video_url);
                $query = explode('&', $parts[1]);
                $video_id = $query[0];
                $args = array(
                    'id' => $video_id
                );

                foody_get_template_part(get_template_directory() . '/template-parts/content-recipe-video.php', $args);
            endwhile;


        }
    }


    public function the_overview()
    {
        if ($this->post != null) {

            foody_get_template_part(
                get_template_directory() . '/template-parts/content-recipe-overview.php',
                $this->overview
            );
        }
    }

    public function the_ingredients()
    {
        if ($this->post != null) {

            foody_get_template_part(
                get_template_directory() . '/template-parts/content-recipe-ingredients.php',
                $this->ingredients_groups
            );
        }
    }

    public function the_notes()
    {
        $notes = get_field('notes');

        foody_get_template_part(get_template_directory() . '/template-parts/content-recipe-notes.php', $notes);
    }

    public function the_rating()
    {

    }

    public function the_nutrition()
    {

    }

    public function the_accessories()
    {
        $posts = get_field('accessories');
        $title = 'אביזרים';
        $this->posts_bullets($posts, $title);
    }

    public function the_techniques()
    {
        $posts = get_field('techniques');
        $title = 'טכניקות';
        $this->posts_bullets($posts, $title);
    }


    private function posts_bullets($array, $title)
    {
        $list = '<h2 class="title">' . $title . '</h2><ul>%s</ul>';

        $items = array();

        foreach ($array as $item) {
            $post_id = $item->ID;
            $items[] = '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';

            if ($this->debug) {
                $items[] = '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';
                $items[] = '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';
                $items[] = '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';
                $items[] = '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a></li>';
            }
        }

        echo sprintf($list, implode('', $items));
    }


    public function the_tags()
    {

        $tags = wp_get_post_tags($this->post->ID);

        foody_get_template_part(get_template_directory() . '/template-parts/content-tags.php', $tags);
    }


    private function init_video()
    {
        while (have_rows('video')): the_row();
            $video_url = get_sub_field('url');
            $parts = explode('v=', $video_url);
            $query = explode('&', $parts[1]);
            $video_id = $query[0];


            $this->video = array(
                'id' => $video_id,
                'url' => $video_url,
                'duration' => get_sub_field('duration')
            );
        endwhile;
    }

    private function init_overview()
    {
        while (have_rows('overview')): the_row();
            $preparation_time = get_sub_field('preparation_time');
            $total_time = get_sub_field('total_time');
            $difficulty_level = get_sub_field('difficulty_level');

            $this->overview = array(
                'preparation_time' => $preparation_time,
                'total_time' => $total_time,
                'difficulty_level' => $difficulty_level,
                'ingredients_count' => $this->ingredients_count
            );
        endwhile;
    }

    private function init_ingredients()
    {
        $this->ingredients_groups = array();
        $this->ingredients_count = 0;

        while (have_rows('ingredients')): the_row();

            $this->number_of_dishes = get_sub_field('number_of_dishes');
            $current_group = 0;
            while (have_rows('ingredients_groups')): the_row();

                $this->ingredients_groups[] = array(
                    'title' => get_sub_field('title'),
                    'ingredients' => array()
                );


                while (have_rows('ingredients')): the_row();

                    $ingredient_post = get_sub_field('ingredient');
                    $this->ingredients_count++;

                    $ingredient = new Foody_Ingredient($ingredient_post, get_sub_field('amount'), get_sub_field('unit'));

                    $this->ingredients_groups[$current_group]['ingredients'][] = $ingredient;
                    if ($this->debug) {
                        for ($i = 0; $i < 3; $i++) {
                            $this->ingredients_groups[$current_group]['ingredients'][] = $ingredient;
                        }
                    }

                endwhile;

                $current_group++;


            endwhile;
        endwhile;
    }


    public function to_json_schema()
    {

        $schema = array(
            "@context" => "http://schema.org/",
            "@type" => "Recipe",
            'name' => $this->getTitle(),
            'image' => array(
                $this->getImage()
            ),
            'author' => array(
                "@type" => "Person",
                'name' => $this->getAuthorName()
            ),
            "datePublished" => get_the_date('Y-m-d'),
            'description' => $this->getDescription(),
            'totalTime' => $this->overview['total_time'],
            'keywords' => '',
            'recipeYield' => sprintf('%s מנות', $this->number_of_dishes),
            'recipeCategory' => $this->get_primary_category(),
            'recipeIngredient' => $this->ingredients_to_string()
        );
    }

    private function ingredients_to_string()
    {

        $ingredients = [];

        foreach ($this->ingredients_groups as $ingredients_group) {
            $ingredients = array_merge($ingredients_group['ingredients'], $ingredients);
        }

        return array_map(function (Foody_Ingredient $ing) {
            return $ing->__toString();
        }, $ingredients);
    }

    public function the_featured_content()
    {
        $this->the_video_box();
    }
}