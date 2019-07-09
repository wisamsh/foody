<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 6:23 PM
 */
class Foody_Recipe extends Foody_Post
{

    public $ingredients_title;

    public $amount_for;

    public $nutrients;

    private $overview;

    private $ingredients_groups;

    private $ingredients_count;

    public $number_of_dishes;


    /**
     * Recipe constructor.
     *
     * @param WP_Post|null $post
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
        $slices = $this->get_pan_slices();
        if ($slices != 0) {
            return $slices;
        } else {
            return $this->number_of_dishes;
        }
    }

    /**
     * @param mixed $number_of_dishes
     */
    public function setNumberOfDishes($number_of_dishes)
    {
        $this->number_of_dishes = $number_of_dishes;
    }

    /**
     * @param string $duration
     */
    public function setDuration(string $duration)
    {
        $this->duration = $duration;
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

        foody_get_template_part(
            get_template_directory() . '/template-parts/content-recipe-ingredients.php',
            [
                'groups' => array_merge([], $this->get_the_ingredients_groups())
            ]
        );

    }

    /**
     * A copy of $ingredients_groups
     * @return array
     */
    public function get_the_ingredients_groups()
    {
        return array_map(function ($group) {

            return [
                'title' => $group['title'],
                'ingredients' => array_map(function ($ingredient) {
                    /** @var Foody_Ingredient $ingredient */
                    return clone $ingredient;
                }, $group['ingredients'])
            ];

        }, $this->ingredients_groups);
    }

    public function the_notes()
    {
        $notes = null;
        $title = null;

        while (have_rows('notes', $this->post->ID)): the_row();
            $notes = get_sub_field('notes');
            $title = get_sub_field('title');
        endwhile;

        $template_args = [
            'notes' => $notes,
            'title' => $title
        ];

        if (array_not_empty($notes)) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-recipe-notes.php', $template_args);
        }

    }

    public function the_rating()
    {
        $args = [
            'value' => get_rating_by_user_and_post($this->id)
        ];

        foody_get_template_part(
            get_template_directory() . '/template-parts/content-rating.php',
            $args
        );
    }

    public function the_nutrition()
    {

        $title = get_field('nutritions_title', $this->post->ID);

        if (empty($title)) {
            $title = __('ערכים תזונתיים');
        }

        if (!empty($this->nutrients)) {
            $nutrients = array_chunk($this->nutrients, ceil(count($this->nutrients) / 3));

            foody_get_template_part(
                get_template_directory() . '/template-parts/content-nutritions.php',
                [
                    'nutritions' => $nutrients,
                    'title' => $title,
                    'dishes_amount' => $this->getNumberOfDishes(),
                    'dishes_title' => $this->the_amount_title()
                ]
            );

        }
    }

    public function the_sponsor()
    {

        /** @var WP_Term $sponsor */
        $sponsor = get_field('sponsor', $this->post->ID);
        $sponsor_text = get_field('sponsor_text', $this->post->ID);

        if (!empty($sponsor)) {

            $sponsor_name = $sponsor->name;
            $sponsor_link = get_field('link', $sponsor->taxonomy . '_' . $sponsor->term_id);

            foody_get_template_part(
                get_template_directory() . '/template-parts/content-recipe-sponsor.php',
                [
                    'sponsor_name' => $sponsor_name,
                    'sponsor_link' => $sponsor_link,
                    'sponsor_text' => $sponsor_text,
                ]
            );

        }
    }

    public function the_conversion_table_link()
    {
        $show = get_option('foody_conversion_table_link_show', false);
        if ($show) {
            $link = get_option('foody_conversion_table_link', false);
            $target = get_option('foody_conversion_table_link_target', false) ? '_blank' : '_self';
            $link_text = get_option('foody_conversion_table_link_text', false);

            echo '<a href="' . $link . '" target="' . $target . '">' . $link_text . '</a>';
        } else {
            echo '';
        }
    }

    public function the_categories()
    {
        echo '<h2 class="title">' . __('קטגוריות') . '</h2>';
        echo get_the_category_list('', '', $this->getId());
    }

    public function the_amount_title()
    {
        if ($this->get_pan_slices() != 0) {
            return ['plural' => 'פרוסות', 'singular' => 'פרוסה'];
        } else {
            return ['plural' => 'מנות', 'singular' => 'מנה'];
        }
    }

    public function the_accessories()
    {
        $posts = [];
        $title = '';

        while (have_rows('accessories', $this->post->ID)): the_row();
            $posts = get_sub_field('accessories');
            $title = get_sub_field('title');
        endwhile;


        $this->posts_bullets($posts, $title);
    }

    public function the_techniques()
    {
        $posts = [];
        $title = '';

        while (have_rows('techniques', $this->post->ID)): the_row();
            $posts = get_sub_field('techniques');
            $title = get_sub_field('title');
        endwhile;

        $this->posts_bullets($posts, $title);
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


    public function to_json_schema()
    {
        // TODO
        /** @noinspection PhpUnusedLocalVariableInspection */
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

//    public function the_featured_content()
//    {
//        $this->the_video_box();
//    }

    public function the_sidebar_content($args = array())
    {
        parent::the_sidebar_content($args);
    }

    public function preview()
    {
        $content = get_field('preview', $this->post->ID, false);

        if (!empty($content)) {
            $content = foody_normalize_content($content);
        }

        echo $content;
    }

    public function the_ingredients_title()
    {
        return $this->ingredients_title;
    }

    /*
     * Private
     * */


    private function init()
    {
        $this->init_video();
        $this->init_ingredients();
        $this->init_overview();
        $this->init_nutrients();
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

    private function init_overview()
    {
        $overview = get_field('overview', $this->post->ID);

        $difficulty_level = $overview['difficulty_level'];

        $this->overview = array(
            'preparation_time' => $this->get_recipe_time($overview['preparation_time']),
            'total_time' => $this->get_recipe_time($overview['total_time']),
            'difficulty_level' => $difficulty_level,
            'ingredients_count' => $this->ingredients_count
        );

    }

    public function duration8601($second)
    {
        $h = intval($second / 3600);
        $m = intval(($second - $h * 3600) / 60);
        $s = $second - ($h * 3600 + $m * 60);
        $ret = 'PT';
        if ($h) {
            $ret .= $h . 'H';
        }
        if ($m) {
            $ret .= $m . 'M';
        }
        if ((!$h && !$m) || $s) {
            $ret .= $s . 'S';
        }

        return $ret;
    }

    public function time_to_iso8601_duration($time_field)
    {
        $str = '';
        $overview = get_field('overview', $this->id);

        $field = $overview[$time_field];
        if (!isset($field['unit'])) {
            $field['unit'] = 'minutes';
        }
        $m = $this->unit_to_minutes($field['unit'], $field['time']);
        if (is_numeric($m)) {
            $s = $m * 60;
            $str = $this->duration8601($s);
        }

        return $str;
    }

    private function get_recipe_time($time_field, $local = true)
    {
        if (!$local) {
            global $locale;
            $locale = 'en_US';
        }
        if (!isset($time_field['time'])) {
            $time_field['time'] = 0;
        }
        if (!isset($time_field['time_unit'])) {
            $time_field['time_unit'] = 'minutes';
        }
        $time = $time_field['time'];
        $unit = trim($time_field['time_unit']);

        $converted_time = $this->unit_to_minutes($unit, $time);
        $times = $this->convert_to_hours_minutes($converted_time);

        $recipe_time = '';

        if (!empty($times['days'])) {
            $unit = 'days';
            $singular = 'day';
            $recipe_time = sprintf(_n("%s $singular", "%s $unit", trim($times['days'])), $this->format_recipe_time($local, intval($times['days'])));
        }

        if (!empty($times['hours'])) {
            $unit = 'hours';
            $singular = 'hour';

            if (!empty($recipe_time)) {
                $recipe_time .= ', ';
            }

            $hours_str = sprintf(_n("%s $singular", "%s $unit", trim($times['hours'])), $this->format_recipe_time($local, intval($times['hours'])));
            if (strcmp($hours_str, 'שעה 1') == 0) {
                $hours_str = 'שעה';
            } elseif (strcmp($hours_str, '2 שעות') == 0) {
                $hours_str = 'שעתיים';
            }
            $recipe_time .= $hours_str;
        }

        if (!empty($times['minutes'])) {
            $unit = 'דקות';
            $singular = 'דקה';
            if (!$local) {
                $unit = 'Minutes';
                $singular = 'Minute';
            }

            $minutes_time = sprintf(_n("%s $singular", "%s $unit", trim($times['minutes'])), $this->format_recipe_time($local, intval($times['minutes'])));
            if (!empty($recipe_time)) {
                $recipe_time .= __(' ו-');
            }
            $recipe_time .= $minutes_time;

            global $locale;
            $locale = 'iw_IL';
        }

        return $recipe_time;
    }

    private function format_recipe_time($local, $time)
    {
        $retval = number_format_i18n($time);
        if (!$local) {
            $retval = number_format($time);
        }

        return $retval;
    }

    private function unit_to_minutes($unit, $time)
    {
        $new_time = $time;
        if (mb_strtolower($unit) == 'hours') {
            $new_time = $new_time * 60;
        } elseif (mb_strtolower($unit) == 'days') {
            $new_time = $new_time * 60 * 24;
        }

        return $new_time;
    }

    /**
     * @param int $time time in minutes
     *
     * @return array|string
     */
    private function convert_to_hours_minutes($time)
    {
        if (!is_numeric($time)) {
            return '';
        }

        $time = intval($time);
        if ($time < 1) {
            return '';
        }
        $hours = floor($time / 60);
        $minutes = (int)($time % 60);


        $days = 0;
        if ($hours > 24) {
            $days = (int)($hours / 24);
            $hours = (int)($hours % 24);
        }


        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'days' => $days
        ];
    }

    private function init_ingredients()
    {
        $this->ingredients_groups = array();
        $this->ingredients_count = 0;

        if (have_rows('ingredients', $this->post->ID)) {
            while (have_rows('ingredients', $this->post->ID)): the_row();

                $this->number_of_dishes = get_sub_field('number_of_dishes');
                if (empty($this->number_of_dishes)) {
                    $this->number_of_dishes = 1;
                }

                // Pan slices
                $this->number_of_dishes = get_sub_field('number_of_dishes');
                if (empty($this->number_of_dishes)) {
                    $this->number_of_dishes = 1;
                }
                $this->amount_for = get_sub_field('amount_for');
                $this->ingredients_title = get_sub_field('title');
                $current_group = 0;
                while (have_rows('ingredients_groups', $this->post->ID)): the_row();

                    $this->ingredients_groups[] = array(
                        'title' => get_sub_field('title'),
                        'ingredients' => array(),
                        'free_text_ingredients' => array()
                    );


                    while (have_rows('ingredients', $this->post->ID)): the_row();

                        $ingredient_post = get_sub_field('ingredient');
                        $this->ingredients_count++;


                        $amounts = [];

                        while (have_rows('amounts', $this->post->ID)): the_row();

                            $unit_field = get_sub_field('unit');
                            $unit = get_term($unit_field, 'units');
                            $amount = get_sub_field('amount');
                            if (!is_wp_error($unit) && $unit instanceof WP_Term) {

                                $unit_name = $unit->name;

                                if ($amount > 1) {
                                    $plural = get_field('plural_name', $unit);
                                    if ($plural != null) {
                                        $unit_name = get_field('plural_name', $unit);
                                    }
                                }
                            } else {
                                $unit_name = '';
                            }


                            $amounts[] = [
                                'amount' => $amount,
                                'unit' => $unit_name,
                                'unit_tax' => $unit
                            ];

                        endwhile;

                        if ($ingredient_post && $ingredient_post instanceof WP_Post) {
                            $ingredient = new Foody_Ingredient($ingredient_post);

                            $ingredient->recipe_id = $this->post->ID;
                            $ingredient->comment = get_sub_field('comment');

                            $ingredient->amounts = $amounts;

                            $this->ingredients_groups[$current_group]['ingredients'][] = $ingredient;
                        }

                    endwhile;

                    $current_group++;


                endwhile;
            endwhile;
        }
    }

    private function posts_bullets($array, $title)
    {
        $list = '<h2 class="title">' . $title . '</h2><ul>%s</ul>';

        $items = array();

        if (array_not_empty($array)) {
            foreach ($array as $item) {
                $post_id = $item->ID;
                $sponsors = $this->get_accessory_commercial($post_id);
                if (empty($sponsors)) {
                    $sponsors = '';
                }
	            $items[] = '<li><a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a>' . $sponsors . '</li>';
            }

            echo sprintf($list, implode('', $items));
        }
    }

    private function get_accessory_commercial($accessory_id) {
	    // Fetch rules for recipe
	    $rules = Foody_CommercialRuleMapping::getByIngredientRecipe( $this->post->ID, $accessory_id );
	    $sponsored_ingredient = '';

	    if ( ! empty( $rules ) ) {
		    $sponsored_ingredient = foody_print_commercial_rules($rules);
	    }

	    return $sponsored_ingredient;
    }

    public function the_details()
    {
        foody_get_template_part(
            get_template_directory() . '/template-parts/_content-recipe-details.php',
            [
                'page' => $this
            ]
        );
    }

    public function has_rating()
    {
        return true;
    }

    public function featured_content_classes()
    {
        $classes = parent::featured_content_classes();
        if ($this->has_video) {
            $classes[] = 'video-featured-content';
        }

        return $classes;
    }

    public function has_notes()
    {
        return have_rows('notes', $this->post->ID) && count(get_field('notes', $this->id)) > 0;
    }

    public function has_nutrients()
    {
        $show_nutrients = get_option('foody_show_ingredients_conversion');
        // TODO change check to also check for nutrients availability

        return $show_nutrients;
    }

    public function calculator()
    {
        $pan_conversion = get_field('ingredients_use_pan_conversion', $this->id);


        if ($pan_conversion) {

            $pan = get_field('ingredients_pan', $this->id);
            if ($pan) {
                $pan = get_term($pan, 'pans');
                $conversions = get_field('conversions', $pan);
                $slices = get_field('slices', $pan);

                if (empty($conversions)) {
                    $conversions = [];
                }

                foody_get_template_part(
                    get_template_directory() . '/template-parts/content-recipe-calculator-pans.php',
                    [
                        'pan' => $pan,
                        'conversions' => $conversions,
                        'slices' => $slices
                    ]
                );
            }

        } else {
            foody_get_template_part(
                get_template_directory() . '/template-parts/content-recipe-calculator-dishes.php',
                ['recipe' => $this]
            );
        }
    }

    public function get_pan_slices()
    {
        $slices = 0;

        $pan = get_field('ingredients_pan', $this->id);
        if ($pan) {
            $pan = get_term($pan, 'pans');
            $slices = get_field('slices', $pan);
        }

        return $slices;
    }

    public static function ratings()
    {
        global $post;
        if (function_exists('the_ratings') && !empty($post)) {
            ?>
            <section class="ratings-wrapper">
                <?php echo do_shortcode('[ratings]') ?>
            </section>
            <?php
        }
    }


    public function get_jsonld_nutrients()
    {
        $json = [
            "@type" => "NutritionInformation",
            'calories' => isset($this->nutrients['calories']) ? $this->nutrients['calories'] : '',
            'carbohydrateContent' => isset($this->nutrients['carbohydrates']) ? $this->nutrients['carbohydrates'] : '',
            'fatContent' => isset($this->nutrients['fats']) ? $this->nutrients['fats'] : '',
            'proteinContent' => isset($this->nutrients['protein']) ? $this->nutrients['protein'] : '',
            'sodiumContent' => isset($this->nutrients['sodium']) ? $this->nutrients['sodium'] : '',
        ];

        return json_encode($json);
    }

    private function init_nutrients()
    {
        $nutrients = array();


        $excluded_nutrients = [
//			'fibers',
//			'saturated_fat',
//			'cholesterol',
//			'calcium',
//			'iron',
//			'potassium',
//			'zinc',
            'sugar'
        ];

        foreach (Foody_Ingredient::get_nutrients_options() as $nutrients_name => $nutrients_title) {

            if (!in_array($nutrients_name, $excluded_nutrients)) {
                $item = ['name' => $nutrients_title, 'value' => 0, 'valuePerDish' => 0];
                foreach ($this->ingredients_groups as $group) {

                    foreach ($group['ingredients'] as $ingredient) {

                        /** @var Foody_Ingredient $ingredient */
                        $value = $ingredient->get_nutrient_data_by_unit_and_amount($nutrients_name);
                        $item['value'] = $item['value'] + $value;
                    }

                }
                $item['data_name'] = $nutrients_name;
                $item['unit'] = Foody_Ingredient::get_nutrient_unit($nutrients_name);
                $decimals = 1;
                if ($nutrients_name == 'protein') {
                    $decimals = 1;
                }
                $item['valuePerDish'] = $item['value'] / $this->getNumberOfDishes();
                $item['valuePerDish'] = number_format($item['valuePerDish'], $decimals, '.', '');
                $item['value'] = number_format($item['value'], $decimals, '.', '');
                $nutrients[] = $item;
            }

        }

        $this->nutrients = $nutrients;

    }

    public function get_ingredients_jsonld()
    {
        $text = [];
        if (!empty($this->ingredients_groups)) {
            foreach ($this->ingredients_groups as $ingredients_group) {
                foreach ($ingredients_group['ingredients'] as $ingredient) {
                    if ($ingredient instanceof Foody_Ingredient) {

                        $ingredient_amounts_html = $ingredient->the_amounts(false);

                        if (!empty($ingredient_amounts_html) && !is_wp_error($ingredient_amounts_html)) {
                            $ingredient_amounts = strip_tags($ingredient_amounts_html);
                            $ingredient_amounts = preg_replace('/\s+/', ' ', $ingredient_amounts);
                            $ingredient_amounts = trim($ingredient_amounts);

                            $text[] = $ingredient_amounts;
                        }
                    }
                }
            }
        }

        return json_encode($text, JSON_UNESCAPED_UNICODE);
    }


}