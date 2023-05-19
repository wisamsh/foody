<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:32 PM
 */
class Foody_Ingredient extends Foody_Post
{

    public $amount;

    public $unit;

    public $amounts;

    public $comment;

    public $amounts_delimiter = ' <b> או </b> ';

    public $unit_taxonomy;

    public $fractions = [
        '3' => '1/3',
        '25' => '1/4',
        '5' => '1/2',
        '66' => '2/3',
        '75' => '3/4',
    ];

    public $plural_name;
	
    public $singular_name;
    public $nutrients;
    public $nutrients_conversion;
    public $recipe_id;

    private $foody_search;

    public $has_alter_link = false;

    public $recipe_substitute_ingredient;
    public $substitute_ingredient_everywhere = false;

    public $substitute_ingredients_list = [];

    private $regex_pattern_for_special_chars = "^[^\\\p{Hebrew} a-zA-Z0-9]{1,}$";
    //private $regex_pattern_for_special_chars = "/^[^1-9\\\a-z\\\u{0590}-\\\u{05fe} ]+$/i";

    /**
     * Foody_Ingredient constructor.
     *
     * @param $ingredient_post_id
     * @param $amount
     * @param $unit
     */
    public function __construct($post, $amount = null, $unit = null, $unit_taxonomy = null)
    {
        parent::__construct($post);
        $this->amount = $amount;
        $this->unit = $unit;

        $this->plural_name = get_field('plural_name', $this->id);
        $this->singular_name = $this->getTitle();
        $this->nutrients = get_field('nutrients', $this->id);
        $this->nutrients_conversion = get_field('nutrients_conversion', $this->id);

        $this->unit_taxonomy = $unit_taxonomy;

        $this->foody_search = new Foody_Search('foody_ingredient');

        $substitute_ingredients = get_field('substitute_ingredients_list', $this->id);
        if (is_array($substitute_ingredients)) {
            foreach ($substitute_ingredients as $substitute_ingredient) {
                $substitute_ingredient_title = str_replace(['-', '-', '_', '_'], '', $substitute_ingredient['substitute_ingredient']->post_title);
//                $substitute_ingredient_title = preg_replace($this->regex_pattern_for_special_chars, '', $substitute_ingredient['substitute_ingredient']->post_title);
                $this->substitute_ingredients_list[$substitute_ingredient_title] = [
                    'title' => $substitute_ingredient['substitute_ingredient']->post_title,
                    'conversion' => $substitute_ingredient['conversion'],
                    'text' => $substitute_ingredient['text'],
                    'original_ingredient_text' => isset($substitute_ingredient['original_ingredient_text']) ? $substitute_ingredient['original_ingredient_text'] : '',
                    'text_color' => $substitute_ingredient['text_color'],
                    'show_everywhere' => $substitute_ingredient['show_everywhere'],
                    'post' => $substitute_ingredient['substitute_ingredient'],
                    'filter' => $substitute_ingredient['filter'],
					
                ];
            }
        }
    }


    function __toString()
    {
        return sprintf(
            '%s %s %s',
            $this->get_the_amounts(),
            $this->getUnit(),
            $this->getTitle()
        );
    }

    public function feed()
    {
        $foody_query = Foody_Query::get_instance();

        $args = $foody_query->get_query('foody_ingredient', [$this->id], true);

        $query = new WP_Query($args);

        $posts = $query->get_posts();

        $posts = array_map('Foody_Post::create', $posts);

        $grid_args = [
            'id' => 'foody-ingredient-feed',
            'posts' => $posts,
            'more' => $foody_query->has_more_posts($query),
            'cols' => 2,
            'header' => [
                'sort' => true
            ]
        ];

        echo '<div class="container-fluid feed-container ingredient-feed-container">';
        foody_get_template_part(get_template_directory() . '/template-parts/common/foody-grid.php', $grid_args);
        echo '</div>';
    }

    public function getUnit()
    {
        return $this->unit;
    }


    public function the_featured_content($shortcode = false)
    {

    }

    public function the_sidebar_content($args = array())
    {
        dynamic_sidebar('foody-sidebar');
        dynamic_sidebar('foody-social');
    }

    public function the_amounts($echo = true, $recipe_id = false)
    {
        if ($this->amounts != null) {

            $length = count($this->amounts);

            $last = array_pop($this->amounts);

            $to_fraction = array($this, 'to_fraction');
            $get_ingredient_data_attr = array($this, 'get_ingredient_data_attr');

            $content = implode($this->amounts_delimiter, array_map(function ($amount) use ($to_fraction, $get_ingredient_data_attr) {

                $display = call_user_func($to_fraction, $amount['amount']);
                $data = call_user_func($get_ingredient_data_attr, $amount['amount'], $display);
                $data .= ' ' . foody_array_to_data_attr(['unit' => $amount['unit']]);

                if ($this->recipe_substitute_ingredient == null) {
                    foreach ($this->substitute_ingredients_list as $substitute_ingredient) {
                        if ($substitute_ingredient['show_everywhere']) {
                            $this->substitute_ingredient_everywhere = true;
                            $this->recipe_substitute_ingredient = new Foody_Ingredient($substitute_ingredient['post']);
                            break;
                        }
                    }
                }

                // has substitute ingredient
                if ($this->recipe_substitute_ingredient != null && $this->recipe_substitute_ingredient->getTitle() != '') {
                    $recipe_substitute_ingredient_title = str_replace(['-', '-', '&#8211;', '_', '_'], '', $this->recipe_substitute_ingredient->getTitle());
                    if (is_array($this->substitute_ingredients_list) && $recipe_substitute_ingredient_title != '' && isset($this->substitute_ingredients_list[$recipe_substitute_ingredient_title])) {
                        $convertion_value = $this->substitute_ingredients_list[$recipe_substitute_ingredient_title]['conversion'];
                        $get_substitute_ingredient_data_attr = array($this->recipe_substitute_ingredient, 'get_substitute_ingredient_data_attr');
                        $data .= ' ' . call_user_func($get_substitute_ingredient_data_attr, $amount['amount'] * $convertion_value, $this->string_fraction_to_decimal($display) * $convertion_value);
                        $substitute_amount = $this->change_amount_by_convertion($amount, $convertion_value);
                        $data .= ' ' . foody_array_to_substitute_data_attr(['unit' => $substitute_amount['unit']]);
                        $this->recipe_substitute_ingredient->amounts = [];
                        array_push($this->recipe_substitute_ingredient->amounts, $substitute_amount);
                    }
                }

                return
                    '<span dir="ltr" class="amount"' . $data . '>
                        ' . $display . '
                    </span>
                    <div class="extra-ingredients db">
                    <span class="unit">
                         ' . $amount['unit'] . '
                    </span>';
            }, $this->amounts));

            $unit_tax = $last['unit_tax'];

            $show_after_ingredient = get_field('show_after_ingredient', $unit_tax);

            $display = $this->to_fraction($last['amount']);

            $amount = $last['amount'];

            $title = $this->getTitle();

            $unit = $last['unit'];
            $this->amounts[] = $last;
            $ing_html = $this->get_ingredient_html($amount, $display, $unit, $title, $show_after_ingredient, $length, $recipe_id);


            if ($length > 1) {
                $content .= $this->amounts_delimiter;
            } else {
                $content .= '<div class="extra-ingredients db">';
            }

            if (!empty($content)) {
                $content .= $ing_html;
            } else {
                $content = $ing_html;
            }


        } else {
            $content = '<span class="unit">
                        ' . $this->getTitle() . '
                    </span>';
        }

        $content .= '</div>';

        if ($echo) {
            echo $content;
        }

        return $content;
    }

    public function get_ingredient_html($amount, $display, $unit, $title, $is_unit_after_title, $length, $recipe_id = false)
    {
        $has_substitute = false;

        if (!empty($this->plural_name)) {
            if (ceil($amount) > 1 || (ceil($amount) > 0 && $unit == 'ק"ג')) {
                $title = $this->plural_name;
            }
        }

        $data = $this->get_ingredient_data_attr($amount, $display);
        $data .= ' ' . foody_array_to_data_attr(['unit' => $unit]);

        if (!empty($this->nutrients) && is_array($this->nutrients)) {
            $nutrients_data = [];

            $nutrients_names = self::get_nutrients_options();


            foreach ($nutrients_names as $nutrient_name => $value) {

                $nutrients_data[$nutrient_name] = $this->get_nutrient_data_by_unit_and_amount($nutrient_name);
            }

            if ($this->recipe_substitute_ingredient == null) {
                foreach ($this->substitute_ingredients_list as $substitute_ingredient) {
                    if ($substitute_ingredient['show_everywhere']) {
                        $this->substitute_ingredient_everywhere = true;
                        $this->recipe_substitute_ingredient = new Foody_Ingredient($substitute_ingredient['post']);
                        break;
                    }
                }
            }

            if ($this->recipe_substitute_ingredient != null && $this->recipe_substitute_ingredient->getTitle() != '') {
                $recipe_substitute_ingredient_title = str_replace(['-', '-', '&#8211;', '_', '_'], '', $this->recipe_substitute_ingredient->getTitle());
                if (is_array($this->substitute_ingredients_list) && $recipe_substitute_ingredient_title != '' && isset($this->substitute_ingredients_list[$recipe_substitute_ingredient_title])) {
                    $has_substitute = true;
                    $substitute_amounts = [];
                    $convertion_value = $this->substitute_ingredients_list[$recipe_substitute_ingredient_title]['conversion'];
                    foreach ($this->amounts as $amount_item) {
                        if ($amount_item['unit'] == $unit) {
                            $substitute_amount = $amount_item;
                            $substitute_amount = $this->change_amount_by_convertion($substitute_amount, $convertion_value);
                            array_push($substitute_amounts, $substitute_amount);
                        }
                    }
                    if (!is_array($this->recipe_substitute_ingredient->amounts)) {
                        $this->recipe_substitute_ingredient->amounts = [];
                    }
                    array_push($this->recipe_substitute_ingredient->amounts, $substitute_amount);

                    foreach ($nutrients_names as $nutrient_name => $value) {
                        $substitute_nutrients_data[$nutrient_name] = $this->recipe_substitute_ingredient->get_nutrient_data_by_unit_and_amount($nutrient_name);
                    }
                }
            }

            $data .= ' ' . foody_array_to_data_attr($nutrients_data);
            if ($has_substitute) {
                $data .= ' ' . foody_array_to_substitute_data_attr(['unit' => $substitute_amount['unit']]);
                $data .= ' ' . $this->recipe_substitute_ingredient->get_substitute_ingredient_data_attr($amount * $convertion_value, $this->string_fraction_to_decimal($display) * $convertion_value);
                $data .= ' ' . foody_array_to_substitute_data_attr($substitute_nutrients_data);
                if (isset($this->recipe_substitute_ingredient->part_of_bundle)) {
                    $is_part_of_the_bandle = $this->recipe_substitute_ingredient->part_of_bundle ? 1 : 0;
                    $data .= ' ' . ' data-substitute-bundle=' . '"' . $is_part_of_the_bandle . '"';
                }
            }

        }

        $amount_el = '<span class="ingredient-container">';
        $amount_el .= ' <span dir="ltr" class="amount" ' . $data . '>
                        ' . $display . '
                    </span>';

        $amount_el .= '<span class="ingredient-data">';
        $unit_el = ' <span class="unit">
                         ' . $unit . '
                    </span>';

        $commercial_link = $this->get_sponsored_ingredient_link($recipe_id);
        $ingredient_link = get_field('ingredient_link', $this->get_id());
	
        if ($this->has_alter_link) {
            $link = $this->link['url'];
            $link_title = $this->link['title'];
            $target = $this->link['target'];
        } else if ($commercial_link) {
            $link = $commercial_link['url'];
            $link_title = $commercial_link['title'];
            $target = $commercial_link['target'];
        } else if ($ingredient_link) {
            $link = $ingredient_link['url'];
            $link_title = $ingredient_link['title'];
            $target = $ingredient_link['target'];
        } else {
            $link_title = esc_attr(sprintf('לכל המתכונים עם %s', $title));
            $link = $this->link;
            $target = '_self';

        }
        $name_el = '<span class="name"><a target="' . $target . '" title="' . $link_title . '" class="foody-u-link" href="' . $link . '">
                        ' . $title . '
                    </span></a>';

        if ($is_unit_after_title) {
            $amount_el .= $name_el;
            $amount_el .= $unit_el;
        } else {
            $amount_el .= $unit_el;
            $amount_el .= $name_el;
        }

        if ($length > 1) {
            $amount_el .= '</span></span>';
        }

        

        if ($length <= 1) {
            $amount_el .= '</span></span>';
        }

        return $amount_el;
    }

    private function get_ingredient_data_attr($amount, $display)
    {
        return foody_array_to_data_attr([
            'amount' => $amount,
            'original' => $display,
            'plural' => $this->plural_name,
            'singular' => $this->singular_name
        ]);
    }

    private function get_substitute_ingredient_data_attr($amount, $display)
    {
        return foody_array_to_substitute_data_attr([
            'amount' => $amount,
            'original' => $display,
            'plural' => $this->plural_name,
            'singular' => $this->singular_name
        ]);
    }

    private function to_fraction($dec)
    {

        $str = strval($dec);

        $fraction = explode('.', $str);
        if (isset($fraction[1])) {


            if ($fraction != null) {
                $whole = $fraction[0];
                if ($whole == '0') {
                    $whole = '';
                }

                // Use fractions only when exists
                if (!empty($this->fractions[$fraction[1]])) {
                    return $whole . ' ' . $this->fractions[$fraction[1]];
                }
            }
        }

        return $dec;
        // return Fraction::fromFloat($dec)

    }

    public function get_the_amounts()
    {
        return $this->the_amounts(false);
    }

    public function the_details()
    {
        echo '<section class="ingredient-details-container">';
        bootstrap_breadcrumb();
        the_title('<h1 class="title">', '</h1>');
        echo '</section>';
    }


    /**
     * @param $nutrient_name string
     * @param $unit WP_Term
     * @param $amount number
     *
     * @return float|int
     */
    public function get_nutrient_for_by_unit_and_amount($nutrient_name)
    {

        $value = 0;
        if (!empty($this->amounts)) {
            $unit = $this->amounts[0]['unit_tax'];
            $amount = $this->amounts[0]['amount'];

            if (!empty($this->nutrients) && is_array($this->nutrients)) {
                $nutrients = array_filter($this->nutrients, function ($nutrient) use ($nutrient_name, $unit, $amount) {

                    $valid = false;

                    if ($unit instanceof WP_Term) {

                        $valid = $nutrient['unit'] == $unit->term_id &&
                            $nutrient['nutrient'] == $nutrient_name;
                    }

                    return $valid;
                });

                if (!empty($nutrients)) {
                    $nutrient = array_shift($nutrients);
                    $value = $nutrient['value'];
                    $factor = 1;
                    if ($unit->name == 'גרם' || $unit->name == 'מ"ל') {
                        $factor = 100;
                    }

                    $amount = floatval($amount);
                    $value = floatval($value);
                    $value = ($amount / $factor) * $value;

                    $value = (float)$value;

                }
            }
        }


        return $value;
    }

    /**
     * Retreive ingredient nutritional data using nutrients_conversion table
     *
     * @param $nutrient_name string
     * @param $unit WP_Term
     * @param $amount number
     *
     * @return float|int
     */
    public function get_nutrient_data_by_unit_and_amount($nutrient_name)
    {
        $value = 0;
        $unit_name = '';

        if (!empty($this->amounts)) {
            $amounts = $this->amounts[0];
            $unit = !empty($amounts['unit']) ? $amounts['unit_tax'] : $amounts['unit'];
            $amount = $amounts['amount'];

            if ($unit instanceof WP_Term) {
                $unit_name = get_term_field('slug', $unit->term_id, 'units');
            }

            if ((!empty($this->nutrients_conversion) && is_array($this->nutrients_conversion))) {
                $nutrients = array_filter($this->nutrients_conversion, function ($nutrient) use ($nutrient_name, $unit, $amount, $unit_name) {
                    $valid = false;

                    if ($unit instanceof WP_Term) {
                        $valid = $nutrient['unit'] == $unit->term_id;
                    } else if (empty($unit)) {
                        $valid = true;
                    }

                    return $valid;
                });

            } else if (urldecode($unit_name) == 'גרם' || urldecode($unit_name) == 'מל') {
                $value = 1;
            }

            if (!empty($nutrients) || (urldecode($unit_name) == 'גרם' || urldecode($unit_name) == 'מל')) {

                if (!empty($nutrients)) {
                    $nutrient = array_shift($nutrients);
                    $value = $nutrient['grams'];
                }

                $grams = $this->get_nutrient_grams_by_unit($nutrient_name);
                $factor = 100;// per 100 Grams

                $amount = floatval($amount);
                $value = floatval($value);
                if (!empty($amounts['unit']) && ($amounts['unit'] == 'גרם' || $amounts['unit'] == 'מ"ל')) {
                    $value = 1;
                }
                $grams_value = ($value / $factor) * $grams;
                $value = $amount * $grams_value;

                $value = (float)$value;

            }
        }

        return $value;
    }

    /**
     * Retreive nutrient grams value
     *
     * @param $nutrient_name string
     * @param $unit WP_Term
     * @param $amount number
     *
     * @return float|int
     */
    public function get_nutrient_grams_by_unit($nutrient_name)
    {
        $value = 0;
        if (!empty($this->amounts)) {
            $amounts = $this->amounts[0];
            $unit = !empty($amounts['unit']) ? $amounts['unit_tax'] : $amounts['unit'];
            $amount = $amounts['amount'];

            if (!empty($this->nutrients) && is_array($this->nutrients)) {
                $nutrients = array_filter($this->nutrients, function ($nutrient) use ($nutrient_name, $unit, $amount) {

                    $valid = false;

                    if ($unit instanceof WP_Term) {

                        $unit_name = get_term_field('slug', $nutrient['unit'], 'units');

                        if (is_wp_error($unit_name) || empty($unit_name)) {
                            $unit_name = '';
                        }

                        $valid = (urldecode($unit_name) == 'גרם' ||
                                urldecode($unit_name) == 'מל') &&
                            $nutrient['nutrient'] == $nutrient_name;
                    } else if (empty($unit)) {
                        $valid = true;
                    }

                    return $valid;
                });

                if (!empty($nutrients)) {
                    $nutrient = array_shift($nutrients);
                    $value = $nutrient['value'];
                }
            }
        }


        return $value;
    }


    public static function get_nutrients_options()
    {
        $nutrients = get_field_object('field_5b62c59c35d88')['choices'];

        /** remove suger from nutrients list **/
        //unset($nutrients['sugar']);

        return $nutrients;
    }

    /**
     * @param $nutrient
     *
     * @return string
     */
    public static function get_nutrient_unit($nutrient)
    {
        $nutrients = self::get_nutrients_options();
        $gram = __('גרם');
        $m_gram = __('מ״ג');
        $nutrients['calories'] = __('קק״ל');
        $nutrients['carbohydrates'] = $gram;
        $nutrients['fats'] = $gram;
        $nutrients['protein'] = $gram;
        $nutrients['sodium'] = $m_gram;
        $nutrients['sugar'] = $gram;
        $nutrients['fibers'] = $gram;
        $nutrients['saturated_fat'] = $gram;
        $nutrients['cholesterol'] = $m_gram;
        $nutrients['calcium'] = $m_gram;
        $nutrients['iron'] = $m_gram;
        $nutrients['potassium'] = $m_gram;
        $nutrients['zinc'] = $m_gram;

        $unit = '';

        if (isset($nutrients[$nutrient])) {
            $unit = $nutrients[$nutrient];
        }

        return $unit;
    }

    function __clone()
    {

    }


    /**
     * Checks if a commercial brand
     * should be displayed in this ingredient's html
     * representation
     * @return mixed|null|void
     */
    private function get_sponsor()
    {
        $sponsor_to_return = null;
        if (!empty($sponsor = get_field('sponsor', $this->id))) {

            $filters = get_field('filters', $this->id);
            if (!empty($filters)) {

                $post = get_post($this->recipe_id);

                foreach ($filters as $filter) {
                    $filter = $filter['filter'];
                    $value = $filter['value_' . $filter['type']];

                    if ($value) {
                        switch ($filter['type']) {
                            case 'author':
                                /** @var WP_User $value */
                                if ($post->post_author == $value->ID) {
                                    $sponsor_to_return = $sponsor;
                                }
                                break;
                            case 'category':
                                /** @var WP_Term $value */
                                if (in_category($value->term_id, $post)) {
                                    $sponsor_to_return = $sponsor;
                                }
                                break;
                            case 'tag':
                                /** @var WP_Term $value */
                                if (has_tag($value->term_id, $post)) {
                                    $sponsor_to_return = $sponsor;
                                }
                                break;
                            case 'channel':
                                /** @var WP_Post $value */
                                $channel_recipes = get_field('related_recipes', $value);

                                if (!empty($channel_recipes) && is_array($channel_recipes)) {

                                    $channel_recipes = array_map(function ($post) {
                                        return $post->ID;
                                    }, $channel_recipes);

                                    if (in_array($this->recipe_id, $channel_recipes)) {
                                        $sponsor_to_return = $sponsor;
                                    }
                                }
                                break;
                        }
                    }
                }
            } else {
                $sponsor_to_return = $sponsor;
            }

        }

        return $sponsor_to_return;
    }

    /**
     *
     * @return mixed|null|void
     */
    public function the_sponsored_ingredient($echo = true, $recipe_id = false)
    {
        // Fetch rules for recipe
        $rules = Foody_CommercialRuleMapping::getByIngredientRecipe($this->recipe_id, $this->id);
        $sponsored_ingredient = '';

        if (!empty($rules)) {
            $rules = foody_get_commercial_rules($rules,  $recipe_id);
            $sponsored_ingredient = foody_print_commercial_rules($rules);
        }

        if ($echo) {
            echo $sponsored_ingredient;
        }

        return $sponsored_ingredient;
    }


    /**
     *
     * @return mixed|null|void
     */
    public function get_sponsored_ingredient_link( $recipe_id = false)
    {
        // Fetch rules for recipe
        $rules = Foody_CommercialRuleMapping::getByIngredientRecipe($this->recipe_id, $this->id);
        $link = null;

        if (!empty($rules)) {
            $rules = foody_get_commercial_rules($rules, $recipe_id);

            if (!empty($rules)) {
                $first_rule = array_shift($rules);
                $link = get_field('link', $first_rule['rule_id']);
            }
        }

        return $link;
    }

    public function get_substitute_ingredient($substitute_ingredients_details_filter, $recipe_id = false)
    {
        if ($this->recipe_substitute_ingredient != null && $this->recipe_substitute_ingredient->getTitle() != '' && !$this->substitute_ingredient_everywhere) {
            $recipe_substitute_ingredient_title =  str_replace(['-', '-', '&#8211;', '_', '_'], '', $this->recipe_substitute_ingredient->getTitle());
            $recipe_substitute_ingredient_text = isset($this->substitute_ingredients_list[$recipe_substitute_ingredient_title]) ? $this->substitute_ingredients_list[$recipe_substitute_ingredient_title]['text'] : '';
            $recipe_original_ingredient_text = isset($this->substitute_ingredients_list[$recipe_substitute_ingredient_title]) ? $this->substitute_ingredients_list[$recipe_substitute_ingredient_title]['original_ingredient_text'] : '';
            $recipe_substitute_ingredient_text_color = isset($this->substitute_ingredients_list[$recipe_substitute_ingredient_title]) ? $this->substitute_ingredients_list[$recipe_substitute_ingredient_title]['text_color'] : '';
            if (isset($this->substitute_ingredients_list[$recipe_substitute_ingredient_title])) {
                $substitute_ingredient_html = '<div class="substitute-ingredient" data-text="' . $recipe_substitute_ingredient_text . '" data-original-text="' . $recipe_original_ingredient_text . '" data-text-color="' . $recipe_substitute_ingredient_text_color . '" data-name="' . $recipe_substitute_ingredient_title . '" data-url="' . $this->recipe_substitute_ingredient->link . '">' . __('החלפה ל') . $recipe_substitute_ingredient_title . '</div>';
                return $substitute_ingredient_html;
            }
        } elseif ($this->substitute_ingredient_everywhere) {
            foreach ($this->substitute_ingredients_list as $substitute_ingredient) {
                $recipe_substitute_ingredient_text = isset($substitute_ingredient['text']) ? $substitute_ingredient['text'] : '';
                $recipe_original_ingredient_text = isset($substitute_ingredient['original_ingredient_text']) ? $substitute_ingredient['original_ingredient_text'] : '';
                $recipe_substitute_ingredient_text_color = isset($substitute_ingredient['text_color']) ? $substitute_ingredient['text_color'] : '';
                if ($substitute_ingredient['show_everywhere']) {
                    if (!$substitute_ingredient['filter']) {
                        $substitute_ingredient_html = '<div class="substitute-ingredient" data-text="' . $recipe_substitute_ingredient_text . '" data-original-text="' . $recipe_original_ingredient_text . '" data-text-color="' . $recipe_substitute_ingredient_text_color . '" data-name="' . $substitute_ingredient['title'] . '"  data-url="' . get_permalink($substitute_ingredient['post']) . '">'. __('החלפה ל') . $substitute_ingredient['title'] . '</div>';
                        return $substitute_ingredient_html;
                    } else {
                        $show_ingredient = $this->determine_substitute_display_by_filter($substitute_ingredient['filter'][0], $substitute_ingredients_details_filter, $recipe_id);
                        if ($show_ingredient) {
                            $substitute_ingredient_html = '<div class="substitute-ingredient" data-text="' . $recipe_substitute_ingredient_text . '" data-original-text="' . $recipe_original_ingredient_text . '" data-text-color="' . $recipe_substitute_ingredient_text_color . '" data-name="' . $substitute_ingredient['title'] . '"  data-url="' . get_permalink($substitute_ingredient['post']) . '">' . __('החלפה ל') . $substitute_ingredient['title'] . '</div>';
                            return $substitute_ingredient_html;
                        }
                    }
                }
            }
        }
    }

    private function determine_substitute_display_by_filter($substitute_ingredient, $substitute_ingredients_details_filter, $recipe_id = false)
    {
        $result = false;
        $exclude = $substitute_ingredient['exclude'];
        switch ($substitute_ingredient['filter_type']) {
            case 'categories':
                $category_id = $substitute_ingredient['filter_value_category'];
                if ($exclude) {
                    $result = !in_array($category_id, $substitute_ingredients_details_filter['categories']);
                } else {
                    $result = in_array($category_id, $substitute_ingredients_details_filter['categories']);
                }
                break;
            case 'techniques':
                $technique_id = $substitute_ingredient['filter_value_technique'];
                if ($exclude) {
                    $result = !in_array($technique_id, $substitute_ingredients_details_filter['techniques']);
                } else {
                    $result = in_array($technique_id, $substitute_ingredients_details_filter['techniques']);
                }
                break;
            case 'authors':
                $author_id = $substitute_ingredient['filter_value_author'];
                if ($exclude) {
                    $result = $author_id != $substitute_ingredients_details_filter['author'];
                } else {
                    $result = $author_id == $substitute_ingredients_details_filter['author'];
                }
                break;
            case 'feed':
                $feed_area_id = $substitute_ingredient['filter_value_feed_area'];
                $recipe_feed_area_connection = $recipe_id ? get_field('recipe_channel', $recipe_id) : get_field('recipe_channel');
                $recipe_referer = isset($_GET['referer']) ? $_GET['referer'] : false;
                $recipe_referer = $recipe_referer ? $recipe_referer : $recipe_feed_area_connection;

                if ($exclude) {
                    $result = ((!isset($_GET['referer']) && !$recipe_feed_area_connection) || ((isset($_GET['referer']) || $recipe_feed_area_connection) && $feed_area_id != $recipe_referer));
                } else {
                    $result = ((isset($_GET['referer'] ) || $recipe_feed_area_connection) && $feed_area_id == $recipe_feed_area_connection);
                }
                break;
        }
        return $result;
    }

    public function change_amount_by_convertion($amount_object, $convertion_value)
    {
        if ($amount_object['amount'] > 1) {
            // original is plural
            if ($amount_object['amount'] * $convertion_value <= 1) {
                $single = isset($amount_object['unit_tax']->name) ? $amount_object['unit_tax']->name : '';
                $amount_object['unit'] = $single && $single != '' ? $single : $amount_object['unit'];
            }
        } else {
            if ($amount_object['amount'] * $convertion_value > 1) {
                $plural = get_field('plural_name', $amount_object['unit_tax']);
                $amount_object['unit'] = $plural && $plural != '' ? $plural : $amount_object['unit'];
            }
        }

        $amount_object['amount'] = strval($amount_object['amount'] * $convertion_value);
        return $amount_object;

    }

    private function string_fraction_to_decimal($string)
    {

            return 1;


    }
	
	
	
		
	
	
	
	
	
	
	

}