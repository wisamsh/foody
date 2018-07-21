<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 6:23 PM
 */
class Foody_Recipe extends Foody_Post
{

    private $duration;

    private $video;

    private $sponsership;

    private $overview;

    private $ingredients_groups;

    private $ingredients_count;

    private $number_of_dishes;

    private $debug = true;


    /**
     * Recipe constructor.
     */
    public function __construct(WP_Post $post = null)
    {
        parent::__construct($post);

        if ($post != null) {

            $this->init();

            $this->duration = $this->video['duration'];

        } else {
            $this->duration = '2.45';
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

    /**
     * @return string
     */
    public function getDuration(): string
    {
        return $this->duration ?? '';
    }

    /**
     * @param string $duration
     */
    public function setDuration(string $duration)
    {
        $this->duration = $duration;
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

        if (array_not_empty($notes)) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-recipe-notes.php', $notes);
        }

    }

    public function the_rating()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/content-rating.php'
        );
    }

    public function the_nutrition()
    {
        $nutritions = array();
        while (have_rows('nutritions')): the_row();


            while (have_rows('nutrition')): the_row();

                $value = get_sub_field('value');
                $name = get_sub_field('name');
                $positive_negative = get_sub_field('positive');

                $nutrition = array(
                    'name' => $name,
                    'value' => $value,
                    'positive_negative' => $positive_negative
                );
                $nutritions[] = $nutrition;

            endwhile;

        endwhile;
        if (array_not_empty($nutritions)) {
            $nutritions = array_chunk($nutritions, ceil(count($nutritions) / 3));

            foody_get_template_part(
                get_template_directory() . '/template-parts/content-nutritions.php',
                $nutritions
            );

        }

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

    public function the_tags()
    {

        $tags = wp_get_post_tags($this->post->ID);

        foody_get_template_part(get_template_directory() . '/template-parts/content-tags.php', $tags);
    }

    public function how_i_did()
    {
        $template = '/comments-how-i-did.php';

        if (wp_is_mobile()) {
            $template = '/comments-how-i-did-mobile.php';
        }

        comments_template(
            $template
        );
    }

    public function comments()
    {
        $template = '';
        if (wp_is_mobile()) {
            $template = '/comments-mobile.php';
        }
        comments_template($template);
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

    public function the_featured_content()
    {
        $this->the_video_box();
    }

    public function the_sidebar_content($args = array())
    {

        $playlists_args = array(
            'title' => 'פלייליסט',
            'selector' => 'related_playlists',
            'content_classes' => 'related-playlists',
            'template_args_func' => function ($item) {
                return array(
                    'count' => 20 // TODO
                );
            }
        );

        $this->related_content($playlists_args);

        $recipes_args = array(
            'title' => 'מתכונים נוספים',
            'selector' => 'related_recipes',
            'content_classes' => 'related-recipes',
            'template_args_func' => function ($recipe) {
                $foody_recipe = new Foody_Recipe($recipe);
                return array(
                    'duration' => $foody_recipe->getDuration()
                );
            }
        );

        $this->related_content($recipes_args);

    }

    public function the_mobile_sidebar_content()
    {
        $playlists_args = array(
            'title' => 'מתכונים נוספים',
            'selector' => 'related_playlists',
            'content_classes' => 'related-playlists',
            'template_args_func' => function ($item) {
                return array(
                    'count' => 20 // TODO
                );
            }
        );

        $this->related_content($playlists_args);

        $recipes_args = array(
            'title' => '',
            'selector' => 'related_recipes',
            'content_classes' => 'related-recipes',
            'template_args_func' => function ($recipe) {
                $foody_recipe = new Foody_Recipe($recipe);
                return array(
                    'duration' => $foody_recipe->getDuration()
                );
            }
        );

        $this->related_content($recipes_args);

    }

    /*
     * Private
     * */


    private function init()
    {
        $this->init_video();
        $this->init_ingredients();
        $this->init_overview();
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

    private function init_video()
    {
        while (have_rows('video',$this->post->ID)): the_row();
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
        while (have_rows('overview',$this->post->ID)): the_row();
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

        while (have_rows('ingredients',$this->post->ID)): the_row();

            $this->number_of_dishes = get_sub_field('number_of_dishes');
            $current_group = 0;
            while (have_rows('ingredients_groups')): the_row();

                $this->ingredients_groups[] = array(
                    'title' => get_sub_field('title'),
                    'ingredients' => array(),
                    'free_text_ingredients' => array()
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

                while (have_rows('free_text_ingredients')): the_row();
                    $ingredient = get_sub_field('ingredient');
                    $this->ingredients_groups[$current_group]['free_text_ingredients'][] = $ingredient;
                endwhile;

                $current_group++;


            endwhile;
        endwhile;
    }

    private function posts_bullets($array, $title)
    {
        $list = '<h2 class="title">' . $title . '</h2><ul>%s</ul>';

        $items = array();

        if (array_not_empty($array)) {
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


    }

    private function related_content($related_content_args)
    {
        /** @var WP_Post[] $playlists */
        $related_content = posts_to_array($related_content_args['selector'],$this->post->ID);

        if (!empty($related_content)) {
            $template_args = array(
                'items' => array(),
                'type' => get_post_type($related_content[0]),
                'content_classes' => $related_content_args['content_classes'],
                'title' => $related_content_args['title']
            );


            $template_args['items'] = array_map(function (WP_Post $item) use ($related_content_args) {

                $default_template_args = array(
                    'title' => $item->post_title,
                    'id' => $item->ID,
                    'image' => get_the_post_thumbnail_url($item,'140'),
                    'author' => array(
                        'name' => get_the_author_meta('user_nicename', $item->post_author),
                        'link' => get_author_posts_url($item->post_author)
                    ),
                    'view_count' => view_count_display(foody_get_post_views($item->ID))
                );


                $default_template_args = array_merge($default_template_args, call_user_func($related_content_args['template_args_func'], $item));


                return $default_template_args;

            }, $related_content);


            foody_get_template_part(
                get_template_directory() . '/template-parts/content-related-content.php',
                $template_args
            );
        }

    }

}