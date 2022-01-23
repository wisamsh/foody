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

    public $overview;

    public $ingredients_groups;

    private $ingredients_count;

    public $number_of_dishes;

    public $substitute_all_button;

    private $calories_per_dish;

    public $rating;


    /**
     * Recipe constructor.
     *
     * @param WP_Post|null $post
     * @param boolean $load_content
     */
    public function __construct(WP_Post $post = null, $load_content = true)
    {
        parent::__construct($post,  $load_content);
        $this->init_video();
        $this->duration = $this->video['duration'];

        $substitute_text = get_field('substitute_text');
        $restore_text = get_field('restore_text');

        if ($substitute_text && $restore_text) {
            $this->substitute_all_button = [
                'substitute' => $substitute_text,
                'restore' => $restore_text
            ];
        }

        $this->rating = new Foody_Rating();
    }


    public function before_content()
    {
        $cover_image = get_field('cover_image');
        $mobile_image = get_field('mobile_cover_image');
        $feed_area_id = get_field('recipe_channel');

        if (isset($_GET['referer']) || $feed_area_id) {
            $referer_post = isset($_GET['referer']) ? $_GET['referer'] : $feed_area_id;
            if (!empty($referer_post)) {
                $cover_image = get_field('cover_image', $referer_post);
                $mobile_image = get_field('mobile_cover_image', $referer_post);
            }
        }

        if (!empty($cover_image)) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-cover-image.php', [
                'image' => $cover_image,
                'mobile_image' => $mobile_image
            ]);
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
                [
                    'overview' => $this->overview,
                    'recipe' => $this
                ]
            );
        }
    }

    public function the_ingredients()
    {
        $recipe_id = $this->getId();
        $categories = wp_get_post_categories($recipe_id);
        $techniques = $this->the_techniques(false);
        if ($techniques != false) {
            $techniques = array_map(function ($technique_post) {
                return $technique_post->ID;
            }, $this->the_techniques(false));
        } else {
            $techniques = [];
        }

        $author = get_post_field('post_author', $recipe_id);

        $substitute_ingredients_filters = ['categories' => $categories, 'techniques' => $techniques, 'author' => $author];

        foody_get_template_part(
            get_template_directory() . '/template-parts/content-recipe-ingredients.php',
            [
                'groups' => array_merge([], $this->get_the_ingredients_groups()),
                'substitute_ingredients_filters' => $substitute_ingredients_filters,
                'recipe_id' => $recipe_id
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

    public function the_notes($print = false)
    {
        $notes = null;
        $title = null;

        while (have_rows('notes', $this->post->ID)) : the_row();
            $notes = get_sub_field('notes');
            $title = get_sub_field('title');
        endwhile;

        $template_args = [
            'notes' => $notes,
            'title' => $title,
            'print' => $print
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
            $title = __('ערכים תזונתיים לפי מנה אחת');
        }

        if (!empty($this->nutrients)) {

            //            $nutrients = array_chunk($this->nutrients, ceil(count($this->nutrients) / 3));

            foody_get_template_part(
                get_template_directory() . '/template-parts/content-nutritions.php',
                [
                    'nutritions' => $this->nutrients,
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

        if (!empty($sponsor) && get_class($sponsor) == 'WP_Term') {

            $sponsor_name = isset($sponsor->name) ? $sponsor->name : '';
            $sponsor_taxonomy = isset($sponsor->taxonomy) ? $sponsor->taxonomy : '';
            $sponsor_term_id = isset($sponsor->term_id) ? $sponsor->term_id : '';
            $sponsor_link = get_field('link', $sponsor_taxonomy . '_' . $sponsor_term_id);

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

            echo '<a class="sizes-and-weights" href="' . $link . '" target="' . $target . '">' . $link_text . '</a>';
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

        while (have_rows('accessories', $this->post->ID)) : the_row();
            $posts = get_sub_field('accessories');
            $title = get_sub_field('title');
        endwhile;

        //		if ( empty( $posts ) ) {
        //			$posts = foody_get_serialized_field_by_meta( 'accessories_accessories', $this->id );
        //		}

        $this->posts_bullets($posts, $title);
    }

    public function the_techniques($print = true)
    {
        $posts = [];
        $title = '';

        while (have_rows('techniques', $this->post->ID)) : the_row();
            $posts = get_sub_field('techniques');
            $title = get_sub_field('title');
        endwhile;

        //		if ( empty( $posts ) ) {
        //			$posts = foody_get_serialized_field_by_meta( 'techniques_techniques', $this->id );
        //		}

        if ($print) {
            $this->posts_bullets($posts, $title);
        } else {
            return $posts;
        }
    }

    public function how_i_did()
    {
        $template = '/comments-how-i-did.php';

        if (wp_is_mobile()) {
            $template = '/comments-how-i-did-mobile.php';
        }

        comments_template(
            $template,
            true
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
        $content = get_field('preview', $this->post->ID, true);

        //        if (!empty($content)) {
        //            $content = foody_normalize_content($content, true);
        //        }
        if (get_field('add_more_preview', $this->post->ID) === true) {
            $content = '<div class="foody-content">' . $content . '</div>';
        } else {
            $content = '<div class="foody-content show-read-more">' . $content . '</div>';
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


    public function init()
    {
        $this->init_ingredients();
        $this->init_nutrients();
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

    private function init_overview()
    {
        $overview = get_field('overview', $this->post->ID);

        $difficulty_level = $overview['difficulty_level'];

        if ($this->has_nutrients()) {
            $this->calories_per_dish = round($this->get_calories());
        }


        $this->overview = array(
            'ingredients_count' => ['text' => $this->ingredients_count, 'icon' => 'ingedients@3x.png', 'icon-desktop' => 'ingedients@2x.png?ver=1.2'],
            'time' => [
                'preparation_time' => ['text' => $this->get_recipe_time($overview['preparation_time'], true, true), 'icon' => 'clock@3x.png', 'icon-desktop' => 'clock@2x.png?ver=1.2'],
                'total_time' => ['text' => $this->get_recipe_time($overview['total_time'], true, true), 'icon' => null]
            ],
            'calories_per_dish' => ['text' => $this->calories_per_dish, 'icon' => 'kcal@3x.png?ver=1.2', 'icon-desktop' => 'kcal@2x.png?ver=1.2'],
            'difficulty_level' => ['text' => $difficulty_level, 'icon' => null],
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

    private function get_recipe_time($time_field, $local = true, $shortText = false)
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
            if (!$shortText) {
                $unit = 'דקות';
            } else {
                $unit = "דק׳";
            }
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
            while (have_rows('ingredients', $this->post->ID)) : the_row();

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
                while (have_rows('ingredients_groups', $this->post->ID)) : the_row();

                    $this->ingredients_groups[] = array(
                        'title' => get_sub_field('title'),
                        'ingredients' => array(),
                        'free_text_ingredients' => array()
                    );


                    while (have_rows('ingredients', $this->post->ID)) : the_row();

                        $ingredient_post = get_sub_field('ingredient');
                        $this->ingredients_count++;


                        $amounts = [];

                        while (have_rows('amounts', $this->post->ID)) : the_row();

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

                        $substitute_ingredient_field = get_sub_field('recipe_substitute_ingredient');

                        if ($ingredient_post && $ingredient_post instanceof WP_Post) {
                            $ingredient = new Foody_Ingredient($ingredient_post);

                            $ingredient->recipe_id = $this->post->ID;
                            $ingredient->comment = get_sub_field('comment');
                            $alter_link = get_sub_field('alter_link');
                            if (!empty($alter_link)) {
                                $ingredient->has_alter_link = true;
                                $ingredient->link = get_sub_field('alter_link');
                            }
                            $ingredient->amounts = $amounts;
                            $ingredient->recipe_substitute_ingredient = $substitute_ingredient_field ? new Foody_Ingredient($substitute_ingredient_field) : null;
                            if ($ingredient->recipe_substitute_ingredient !== null) {
                                $ingredient->recipe_substitute_ingredient->part_of_bundle = get_sub_field('part_of_bundle');
                            }

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
                if (!is_numeric($item)) {
                    $post_id = $item->ID;
                } else {
                    $post_id = $item;
                }

                $sponsors = $this->get_accessory_commercial($post_id);
                if (empty($sponsors)) {
                    $sponsors = '';
                }
                $items[] = '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a>' . $sponsors . '</li>';
            }

            echo sprintf($list, implode('', $items));
        }
    }

    private function get_accessory_commercial($accessory_id)
    {
        // Fetch rules for recipe
        $rules = Foody_CommercialRuleMapping::getByIngredientRecipe($this->post->ID, $accessory_id);
        $sponsored_ingredient = '';

        if (!empty($rules)) {
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
                <!-- <span class="rating-digits" id="lowest-rating">1</span>-->
                <?php echo do_shortcode('[ratings]')
                ?>
                <!-- <span class="rating-digits" id="highest-rating">5</span>-->
            </section>
        <?php
        }
    }

    public function ratings_new()
    {
        global $post;
        ?>
        <section class="ratings-wrapper <?php echo $this->rating->foody_has_rating($post->ID) ? '' : 'empty' ?>">
            <?php
            echo $this->rating->foody_get_the_rating($post->ID);
            ?>
        </section>
<?php
    }


    public function get_jsonld_nutrients()
    {
        $json = [
            "@type" => "NutritionInformation",
            'calories' => isset($this->nutrients[0]) ? $this->nutrients[0]['value'] : '',
            'carbohydrateContent' => isset($this->nutrients[1]) ? $this->nutrients[0]['value'] : '',
            'fatContent' => isset($this->nutrients[2]) ? $this->nutrients[2]['value'] : '',
            'sodiumContent' => isset($this->nutrients[3]) ? $this->nutrients[3]['value'] : '',
            'proteinContent' => isset($this->nutrients[4]) ? $this->nutrients[4]['value'] : '',
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
        $feed_area_id = get_field('recipe_channel');
        if (isset($_GET['referer']) || $feed_area_id) {
            $recipe_referer = isset($_GET['referer']) ? $_GET['referer'] : $feed_area_id;
            $show_sugar = get_field('enable_sugar', $recipe_referer);
            if ($show_sugar) {
                $excluded_nutrients = [
                    //			'fibers',
                    //			'saturated_fat',
                    //			'cholesterol',
                    //			'calcium',
                    //			'iron',
                    //			'potassium',
                    //			'zinc',
                    //          'sugar'
                ];
            }
        }

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

    function get_all_ratings_by_post_id($post_id, $column)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'foody_ratings';

        $query = "SELECT {$column} FROM {$table} where postid = " . $post_id;

        return $wpdb->get_results($query);
    }

    public function get_jsonld_aggregateRating($id)
    {
        $ratings = $this->get_all_ratings_by_post_id($id, '*');
        $num_of_rates = count($ratings);
        $ratings_sum = 0;

        foreach ($ratings as $rating) {
            $ratings_sum += floatval($rating->rating);
        }
        if ($num_of_rates == 0) {
            $average_rating = 0;
        } else {
            $average_rating = $ratings_sum / $num_of_rates;
        }

        // round to full int or half
        $average_rating = round($average_rating * 2) / 2;


        if ($average_rating === "" || $num_of_rates === "") {
            return false;
        }

        $json = [
            "@type" => "AggregateRating",
            "ratingValue" => $average_rating,
            "reviewCount" => $num_of_rates
        ];

        return json_encode($json);
    }

    public function get_images_gallery_repeater()
    {
        $images_for_slider = get_field('images_gallery_repeater', $this->id);
        if (is_array($images_for_slider) && $images_for_slider) {
            $item = [];
            foreach ($images_for_slider as $image) {
                $item[] = $image['image']['url'];
            }

            return stripslashes(json_encode($item));
        } else {
            return '"' . $this->getImage() . '"';
        }
    }

    public function get_tags_names()
    {
        return array_map(function ($tag) {
            return str_replace(['״', '"'], '', $tag->name);
        }, wp_get_post_tags($this->get_id()));
    }

    public function get_jsonld_video()
    {
        if ($this->video['url'] != null) {
            $time = explode(':', $this->video['duration']);
            $secs = $time[0] * 60 + $time[1];
            $duration = $this->duration8601($secs);
            $json = [
                "@type" => "VideoObject",
                "contentUrl" => $this->video['url'],
                "duration" => ltrim($duration, $duration[0])
            ];

            return json_encode($json, JSON_UNESCAPED_SLASHES);
        }
        return "";
    }

    public function get_system_tip()
    {
        $tip_group = get_field('system_tip_group', $this->get_id());
        $content_type = isset($tip_group['content_type']) ? $tip_group['content_type'] : false;
        if ($content_type) {
            $content_class = $content_type == 'טקסט' ? 'text-content' : 'image-content';
            $tip_title = isset($tip_group['title']) ? $tip_group['title'] : _('טיפ מערכת');
            $tip_title_element = '<div class="title-container"><img src="' . $GLOBALS['images_dir'] . 'icons/tip.svg' . '" alt="tip"><h2 class="title">' . $tip_group['title'] . '</h2></div>';

            if ($content_type == 'טקסט') {
                $tip_text = isset($tip_group['text']) ? $tip_group['text'] : '';
                $tip_content = '<p class="tip-text">' . $tip_text . '</p>';
            } else {
                $tip_image = isset($tip_group['image']) && isset($tip_group['image']['url']) ? $tip_group['image']['url'] : '';
                $tip_content = '<img class="tip-image" src="' . $tip_image . '">';
            }

            if (isset($tip_group['link']) && is_array($tip_group['link']) && isset($tip_group['link']['url']) && !empty($tip_group['link']['url'])) {
                echo '<a class="tip-link" href="' . $tip_group['link']['url'] . '" target="' . $tip_group['link']['target'] . '"><div class="system-tip ' . $content_class . '">' . $tip_title_element . $tip_content . '</div></a>';
            } else {
                echo '<div class="system-tip ' . $content_class . '">' . $tip_title_element . $tip_content . '</div>';
            }
        }
    }

    public function get_similar_content($similar_contents)
    {
        $not_in_random = [];
        array_push($not_in_random, $this->get_id());
        $counter = 0;
        $title_of_section = isset($similar_contents['title']) && !empty($similar_contents['title']) ? $similar_contents['title'] : __('מתכונים נוספים שכדאי לכם לנסות');
        $args = ['title' => $title_of_section, 'items' => []];

        if (isset($similar_contents['similar_content']) && $similar_contents['similar_content']) {
            foreach ($similar_contents['similar_content'] as $content) {
                if ($content['post'] != false) {
                    array_push($not_in_random, $content['post']->ID);
                    $current_post = Foody_Post::create($content['post']);
                }
                if (!empty($current_post)) {
                    $title = $current_post->getTitle();
                    $image = $current_post->getImage();
                    $link = $current_post->link;
                    $current_post = false;
                } else {
                    if ($content['category'] != false) {
                        $title = get_cat_name($content['category']);
                        $image = $content['image']['url'];
                        $link = get_category_link($content['category']);
                    } else {
                        $title = $content['title'];
                        $image = $content['image']['url'];
                        $link = $content['manual'];
                    }
                }
                $args_to_push = [
                    'title' => $title,
                    'image' => $image,
                    'link' => $link
                ];

                array_push($args['items'], $args_to_push);
                $counter++;
            }
        }

        if ($counter < 4) {
            $query_args = array(
                'post_type' => 'foody_recipe',
                'posts_per_page' => (4 - $counter),
                'order' => 'DESC',
                'post__not_in' => $not_in_random,
                'meta_query' => [
                    [
                        'key' => '_yoast_wpseo_primary_category',
                        'compare' => 'IN',
                        'value' => $this->get_primary_category(),
                        'type' => 'NUMERIC'
                    ]
                ]
            );

            $the_query = new WP_Query($query_args);
            foreach ($the_query->posts as $post) {
                $current_post = Foody_Post::create($post, false);
                $args_to_push = [
                    'title' => $current_post->getTitle(),
                    'image' => $current_post->getImage(),
                    'link' => $current_post->link
                ];
                array_push($not_in_random, $post->ID);
                array_push($args['items'], $args_to_push);
            }
            $post_added = count($the_query->posts);
            if ((4 - $counter) != $post_added) {
                $post_added += $counter;
                $query_args = array(
                    'post_type' => 'foody_recipe',
                    'posts_per_page' => (4 - $post_added),
                    'order' => 'ASC',
                    'orderby' => 'rand',
                    'post__not_in' => $not_in_random
                );

                $the_query = new WP_Query($query_args);
                foreach ($the_query->posts as $post) {
                    $current_post = Foody_Post::create($post);
                    $args_to_push = [
                        'title' => $current_post->getTitle(),
                        'image' => $current_post->getImage(),
                        'link' => $current_post->link
                    ];

                    array_push($args['items'], $args_to_push);
                }
            }
        }
        foody_get_template_part(get_template_directory() . '/template-parts/content-similar-content-listing.php', $args);
    }

    function the_promotion_area($promotion_area_group)
    {
        $enable_background = isset($promotion_area_group['enable_background']) ? $promotion_area_group['enable_background'] : false;
        $background_color = isset($promotion_area_group['background_color']) && $enable_background != false ? $promotion_area_group['background_color'] : false;
        $promotion_text = $promotion_area_group['text'];
        $has_link = isset($promotion_area_group['link']) && is_array($promotion_area_group['link']);
        $promotion_link = $has_link && isset($promotion_area_group['link']['url']) ? $promotion_area_group['link']['url'] : false;

        if ($enable_background != false && $background_color != false) {
            $promotion_area_element = '<p class="promotion-text" style="background-color: ' . $background_color . '">' . $promotion_text . '</p>';
        } else {
            $promotion_area_element = '<p class="promotion-text">' . $promotion_text . '</p>';
        }
        if ($promotion_link) {
            $link_target = $has_link && isset($promotion_area_group['link']['target']) ? $promotion_area_group['link']['target'] : '';
            $promotion_area_element = '<a class="promotion-link" href="' . $promotion_area_group['link']['url'] . '" target="' . $link_target . '" >' . $promotion_area_element . '</a>';
        }

        echo $promotion_area_element;
    }

    function get_calories()
    {
        $calories_per_dish = 0;
        foreach ($this->nutrients as $nutrient) {
            if (is_array($nutrient) && isset($nutrient['name']) && isset($nutrient['valuePerDish'])) {
                if ($nutrient['name'] === __('קלוריות')) {
                    $calories_per_dish = $nutrient['valuePerDish'];
                }
            }
        }

        return $calories_per_dish;
    }

    function get_comments_rating_preps_component($number_of_preps)
    {
        $num_of_comments = count(get_comments(array('type' => 'comment', 'post_id' => $this->id)));
        $rating = '<div class="rating">' . $this->rating->foody_get_the_rating($this->id, true) . '</div>';

        $number_of_preps = $this->get_number_of_approved_preps() + intval($number_of_preps);

        $preps_element_title = '<div class="preparations-share-title">' . __('כבר הכנתם?') . '</div>';
        $preps_element_link = '<a href="#how-i-did" class="preparation-share-link">' . __('שתפו אותנו') . '</a>';
        $preps_elements = '<div class="preparations-share" data-numOfPreps="' . $number_of_preps . '">' . $preps_element_title . $preps_element_link . '</div>';

        $comments_element_title = '<div class="comments-title">' . __('רוצים להגיב?') . '</div>';
        $comments_element_link = '<a href="#comments" class="comments-link">' . __('לחצו כאן') . '</a>';
        $comments_elements = '<div class="comments-link-container" data-numOfComments="' . $num_of_comments . '">' . $comments_element_title . $comments_element_link . '</div>';

        echo $preps_elements . $rating . $comments_elements;
    }

    function get_number_of_approved_preps()
    {
        $preps_comments = get_comments(array('type' => 'how_i_did', 'post_id' => $this->id));

        $approved_comments = array_filter($preps_comments, function ($preps_comments) {
            return $this->filter_comments($preps_comments);
        });

        $preps_made = get_post_meta($this->id, 'num_of_preps');
        $preps_made = is_array($preps_made) && !empty($preps_made) ? intval($preps_made[0]) : 0;

        return count($approved_comments) + $preps_made;
    }

    function filter_comments($comment)
    {
        $author = get_user_by('email', $comment->comment_author_email);

        return $comment->comment_approved || (!$comment->comment_approved && $author->ID == get_current_user_id());
    }

    function get_relevant_content()
    {
        $content_in_steps = get_field('recipe_steps', $this->id);
        if (is_array($content_in_steps)) {
            if (isset($content_in_steps['enable_recipe_by_steps']) && $content_in_steps['enable_recipe_by_steps'] && isset($content_in_steps['steps'])) {
                echo '<div class="content-steps-container"><h2 class="steps-title">' . __('אופן ההכנה') . '</h2> ' . $this->get_content_as_steps($content_in_steps['steps']) . '</div>';
                return;
            }
        }

        //WISAM : Tiktok video 
        if (get_field("tiktok_video", get_the_ID())) {
            echo get_field("tiktok_video", get_the_ID());
        }

        $content_body = $this->body;
        echo '<div class="content-container no-print">' . $content_body . '</div>';
        //        echo '<div class="content-container print-mobile">' . $content_body . '</div>';
        $print_body = apply_filters('foody_print_version_for_content', $content_body);
        echo '<div class="content-container print-desktop print"><div class="content-and-notes print">' . $print_body['content'] . $this->get_notes() . '</div><div class="content-images">' . $print_body['figures'] . '</div></div>';
    }

    function get_notes()
    {
        $notes = null;
        $title = null;
        $notes_element = '';

        while (have_rows('notes', $this->post->ID)) : the_row();
            $notes = get_sub_field('notes');
            $title = get_sub_field('title');
        endwhile;

        if (is_array($notes)) {
            $notes_element = '<section class="recipe-notes box print"><div class="title-with-line"><h2 class="title">' . $title . '</h2><hr class="title-line"></div><ul class="notes" title="הערות">';
            foreach ($notes as $note) :
                $notes_element .= '<li class="note">' . $note["note"] . '</li>';
            endforeach;
            $notes_element .= '</ul></section>';
        }

        return $notes_element;
    }

    function get_content_as_steps($steps)
    {
        $slider = '<div class="slider recipe-content-steps justify-content-between">';
        $counter = 1;
        foreach ($steps as $step) {
            $image_content = '';
            $image_text = '';
            $image_credit = '';

            if ($counter === 1) {
                $item = '<div class="step first-step">';
            } elseif (count($steps) === $counter) {
                $item = '<div class="step last-step">';
            } else {
                $item = '<div class="step">';
            }
            $title = '<div class="step-text">' . $counter++ . '. ' . $step['text'] . '</div>';

            if (is_array($step['image'])) {
                $image_content = "<img class='desktop-image' src='{$step['image']['url']}' alt='{$step['image']['alt']}' />";
            }

            if (is_array($step['image_mobile'])) {
                $image_content .= "<img class='mobile-image' src='{$step['image_mobile']['url']}' alt='{$step['image_mobile']['alt']}' />";
            }

            if (!empty($step['image_text'])) {
                $image_text = '<span class="image-text">' . $step['image_text'] . '.' . '</span>';
            }

            if (!empty($step['image_text'])) {
                $image_credit = '<span class="image-credit"><span class="image-credit-prefix">' . __(' צילום: ') . '</span>' . $step['image_credit'] . '</span>';
            }

            $item .= $title . $image_content . '<div class="image-description">' . $image_text . $image_credit . '</div>';

            $item .= '</div>';

            $slider .= $item;
        }
        $slider .= '</div>';
        return $slider;
    }

    function is_content_by_steps()
    {
        return get_field('recipe_steps_enable_recipe_by_steps', $this->id);
    }

    function get_take_me_to_recipe_btn()
    {
        $btn_image = get_field('take_to_recipe_btn');

        $btn_image_url = isset($btn_image['url']) ? $btn_image['url'] : false;
        $btn_image_alt =  isset($btn_image['alt']) ? $btn_image['alt'] : '';

        if ($btn_image_url) {
            echo "<a class='take_to_recipe_link' href='#recipe-ingredients'><img class='take-me-to-recipe' src='" . $btn_image_url . "' alt='" . $btn_image_alt . "'></a>";
        }
    }

    function the_print_overview()
    {
        $overview_data = [
            'ingredients_count' => ['title' => __('מרכיבים'), 'data' => isset($this->overview) && isset($this->overview['ingredients_count']) ? $this->overview['ingredients_count']['text'] : 0],
            'preparation_time' => ['title' => __('זמן הכנה'), 'data' => isset($this->overview) && isset($this->overview['time']['preparation_time']['text']) ? $this->overview['time']['preparation_time']['text'] : 0],
            'total_time' => ['title' => __('זמן כולל'), 'data' => isset($this->overview) && isset($this->overview['time']['total_time']['text']) ? $this->overview['time']['total_time']['text'] : 0],
            'calories_per_dish' =>  ['title' => __('קלוריות'), 'data' => isset($this->overview) && isset($this->overview['calories_per_dish']) ? $this->overview['calories_per_dish']['text'] : 0],
            'dishes_amount' => ['title' => __('כמות מנות'), 'data' => $this->getNumberOfDishes()]
        ];

        $table_element = '<table class="overview-table print"><tr>';
        foreach ($overview_data as $key => $value) {
            $table_element .= '<th><div class="cell-title ' . $key . '">' . $value['title'] . '</div><div class="cell-value ' . $key . '">' . $value['data'] . '</div> </th>';
        }
        $table_element .= '</tr></table>';

        return $table_element;
    }

    function the_print_rating()
    {
        return $this->rating->foody_get_the_rating($this->id, true, true);
    }
}
