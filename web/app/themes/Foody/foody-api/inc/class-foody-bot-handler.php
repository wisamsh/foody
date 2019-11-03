<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/30/19
 * Time: 5:05 PM
 */

namespace FoodyAPI;


use Foody_Ingredient;
use Foody_Recipe;
use WP_Post;
use WP_Query;
use WP_Term_Query;

class Foody_BotHandler
{
    public static $MAX_RECIPES = 100;

    private $helperArr = [];

    private $mainCategoriesList = ["בשר" => "בשר", "מתכוני עוף" => "מתכוני עוף", "דגים" => "דגים", "אורז" => "אורז", "פסטה" => "פסטה"];

    private $authorsPriority = ["מערכת Foody" => 1, "ישראל אהרוני" => 2, "קרין גורן" => 3, "משה שגב" => 4, "רחלי קרוט" => 5, "נטלי לוין" => 6, "עז תלם" => 7, "יונית צוקרמן" => 8, "אלון שבו" => 9, "אפרת ליכטנשטט" => 10, "אור בן אוליאל" => 11, "רותם ליברזון" => 12,
        "שרית נובק" => 13, "עדי קלינגהופר" => 14, "אולגה טוכשר" => 15, "תמרה אהרוני" => 16, "אושר אידלמן" => 17, "זהר לוסטיגר-בשן" => 18, "רות אופק" => 19, "אילן פנחס" => 20, "אינס ינאי" => 21, "שר פיטנס" => 22, "אודי ואושר" => 23, "עידית נרקיס כ\"ץ" => 24, "יהודית מורחיים" => 25,
        "בת חן דיאמנט" => 26, "סימה ביטון" => 27, "לידר שקד" => 28, "אבי לוי" => 29, "שי-לי ליפא" => 30, "מאיר אדוני" => 31, "הילה אלפרט" => 32, "איילת הירשמן" => 33, "מיקי שמו" => 34, "ירון קסטנבוים" => 35, "איילת לטוביץ" => 36, "גלי ברמן" => 37,
        "ליה שומרון פינדר" => 38, "חן קורן" => 39, "תמר שילון" => 40, "Ninja Foodi" => 41, "תמר שורצברד" => 42, "גיל גוטקין" => 43];

    /**
     * Foody_BotHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $params array
     *
     * @return array
     */
    public function query($params)
    {
        $posts = $this->getPosts($params, 0);

        if (count($posts) > 7) {
            array_splice($posts, 7);
        }

        return $this->extractAttributesFromRecipes($posts, $params);
    }

    public function getResults($params)
    {
        $this->helperArr = [];
        $index = 0;

        $posts = $this->getPosts($params, 0);

        $postsBeforeRecipe = $posts;

        $posts = array_map([$this, 'mapPostToResponse'], $posts);

        foreach ($posts as $post) {
            $this->helperArr[$post['title']] = $postsBeforeRecipe[$index]->ID;
            $index++;
        }

        $posts = $this->sortResults($posts);

        if (count($posts) > 7) {
            array_splice($posts, 7);
        }

        return $posts;
    }


    /**
     * @param $post WP_Post
     *
     * @return array
     */
    private function mapPostToResponse($post)
    {
        //$recipe = new Foody_Recipe($post);
        //$recipe->init();
        $overview = get_field('overview', $post->ID);

        $difficulty_level = $overview['difficulty_level'];

        $short_link_with_utm = wp_get_shortlink($post->ID) . '&utm_source=Foody%20Bot&utm_medium=Bot&utm_campaign=Bot';

        $item = [
            'title' => $post->post_title,
            'url' => $short_link_with_utm,
            'image' => get_the_post_thumbnail_url($post),
            'author' => get_the_author_meta('display_name', $post->post_author),
            'ingredients' => $this->getPostIngredients($post),
            'techniques' => $this->getPostRepeaterTitles('techniques', $post),
            'accessories' => $this->getPostRepeaterTitles('accessories', $post),
            'tags' => wp_get_post_terms($post->ID, 'post_tag', ['fields' => 'names']),
            'categories' => wp_get_post_terms($post->ID, 'category', ['fields' => 'names']),
            'time' => $this->get_recipe_time($overview['total_time']),
            'difficulty_level' => $difficulty_level,
        ];


        return $item;

    }


    /**
     * @param $post WP_Post
     *
     * @return array
     */
    private function getPostIngredients($post)
    {

        $ingredients_groups = get_field('ingredients', $post->ID);

        $ingredients = [];

        foreach ($ingredients_groups['ingredients_groups'] as $ingredients_group) {

            if (is_array($ingredients_group['ingredients'])) {
                $ingredients_ids = array_map(function ($row) {
                    return $row['ingredient'];
                }, $ingredients_group['ingredients']);

                if (is_array($ingredients_ids)) {

                    $ingredients = array_merge($ingredients, array_map('get_the_title', $ingredients_ids));
                }
            }

        }

        return $ingredients;
    }

    /**
     * @param $selector string
     * @param $post WP_Post
     *
     * @return array
     */
    private function getPostRepeaterTitles($selector, $post)
    {

        $items = [];
        $posts = get_field($selector, $post->ID);
        if (!empty($posts)) {
            $posts = $posts[$selector];
            if ($posts) {
                $items = array_map('get_the_title', $posts);
            }
        }

        return $items;
    }

    private function getPosts($params, $counter, $sortedArray = [] , $recursiveArgs =[])
    {
        if( $counter == 0 ) {
            $args = $this->parseQueryArgs($params);
        }
        else{
            $args = $recursiveArgs;
        }

        $posts = [];

        if (!empty($args)) {

            $args['suppress_filters'] = false;

            add_filter('posts_where', array($this, 'botPostsWhere'), 11, 2);

            $posts = get_posts($args);

            remove_filter('posts_where', array($this, 'botPostsWhere'), 11);

            /*
             * no result and have more then one ingredient
             */
            if (empty($posts) && $counter < 5 && count($params['ingredients']) > 1) {
                if ($counter === 0) {
                    $sortedArray = $this->sortIngredientsByPopularity($params['ingredients']);
                }

                reset($sortedArray);
                $toRemove = key($sortedArray);
                array_shift($sortedArray);

                $indexToRemove = array_search($toRemove, $params['ingredients']);

                array_splice($params['ingredients'], $indexToRemove, 1);
                $posts = $this->getPosts($params, ++$counter, $sortedArray, $args);
            }
        }

        return $posts;
    }

    /**
     * @param $params array
     *
     * @return array|null
     */
    private function parseQueryArgs($params)
    {
        $defaults = [
            'ingredients' => [],
            'tags' => [],
            'categories' => [],
            'accessories' => [],
            'techniques' => [],
            'authors' => [],
            'level' => '',
            'time' => [],
        ];

        $params = wp_parse_args($params, $defaults);

//        if (isset($this->mainCategoriesList[$params['ingredients'][0]])) {
//            $category = array_shift($params['ingredients']);
//            array_push($params['categories'], $category);
//        }

        $args = null;

        if (!empty(array_filter($params))) {
            $args = [
                'post_type' => 'foody_recipe',
                'post_status' => 'publish',
                'posts_per_page' => self::$MAX_RECIPES,
                'meta_query' => [
                    'relation' => 'AND'
                ]
            ];

            if (!empty($params['ingredients'])) {
                $ingredients = $this->findPostsByTitle($params['ingredients'], 'foody_ingredient');
                if (count($ingredients) != count($params['ingredients'])) {
                    $ingredients[] = 'INVALID';
                }
                $args['has_wildcard_key'] = true;
                foreach ($ingredients as $index => $ingredient) {
                    $args['meta_query'][] = [
                        'key' => 'ingredients_ingredients_groups_$_ingredients_$_ingredient',
                        'compare' => '=',
                        'value' => $ingredient,
                        'type' => 'NUMERIC'
                    ];
                }
            }

            if (!empty($params['tags'])) {
                $tags = $this->getTermsByNames($params['tags'], 'post_tag');
                if (count($tags) != count($params['tags'])) {
                    $tags[] = 'INVALID';
                }
                $args['tag__and'] = $tags;
            }

            if (!empty($params['categories'])) {
                $categories = $this->getTermsByNames($params['categories'], 'category');
                if (count($categories) != count($params['categories'])) {
                    $categories[] = 'INVALID';
                }
                $args['category__and'] = $categories;
            }

            if (!empty($params['accessories'])) {
                $accessories = $this->findPostsByTitle($params['accessories'], 'foody_accessory');
                if (count($accessories) != count($params['accessories'])) {
                    $accessories[] = 'INVALID';
                }
                foreach ($accessories as $accessory) {
                    $args['meta_query'][] = [
                        'key' => 'accessories_accessories',
                        'compare' => 'LIKE',
                        'value' => '"' . $accessory . '"'
                    ];
                }
            }

            if (!empty($params['techniques'])) {
                $techniques = $this->findPostsByTitle($params['techniques'], 'foody_technique');
                if (count($techniques) != count($params['techniques'])) {
                    $techniques[] = 'INVALID';
                }
                foreach ($techniques as $technique) {
                    $args['meta_query'][] = [
                        'key' => 'techniques_techniques',
                        'compare' => 'LIKE',
                        'value' => '"' . $technique . '"'
                    ];
                }
            }

            if (!empty($params['limitations'])) {
                $limitations = $this->getTermsByNames($params['limitations'], 'limitations');
                if (count($limitations) != count($params['limitations'])) {
                    $limitations[] = 'INVALID';
                }
                foreach ($limitations as $limitation) {
                    $args['meta_query'][] = [
                        'key' => 'limitations',
                        'compare' => 'LIKE',
                        'value' => '"' . $limitation . '"'
                    ];
                }
            }

            if (!empty($params['authors'])) {
                // find authors by names
                $authors = $this->findAuthorsByDisplayName($params['authors']);
                if (count($authors) != count($params['authors'])) {
                    $authors[] = 'INVALID';
                }
                $args['author__in'] = $authors;
            }

            if (!empty($params['level'])) {
                $level_settings = get_field_object('field_5b34fe4c9c7eb');
                $level = array_search($params['level'], $level_settings['choices']);
                $args['meta_query'][] = [
                    'key' => 'overview_difficulty_level',
                    'compare' => '=',
                    'value' => $level
                ];
            }

            if (!empty(($time = $params['time'])) && (!empty($time['min']) || !empty($time['max']))) {

                $time_meta_query = [

                ];

                if (!empty($time['min'])) {
                    $time_meta_query[] = [
                        'key' => 'overview_preparation_time_time',
                        'compare' => '>=',
                        'value' => intval($time['min']),
                        'type' => 'NUMERIC'
                    ];
                }

                if (!empty($time['max'])) {
                    $time_meta_query[] = [
                        'key' => 'overview_preparation_time_time',
                        'compare' => '<=',
                        'value' => intval($time['max']),
                        'type' => 'NUMERIC'
                    ];
                }

                if (!empty($time_meta_query)) {
                    $time_meta_query['relation'] = 'AND';
                    $args['meta_query'][] = $time_meta_query;
                }
            }

//            $args['meta_query'][] = [
//                'level_sort' => [
//                    'key' => 'overview_difficulty_level',
//                    'compare' => 'EXISTS'
//                ]
//            ];
//
//
//            $args['orderby'] = [
//                'level_sort'=>'ASC'
//            ];
//
//            $args['order']='';

        }

        return $args;
    }

    private function extractAttributesFromRecipes(array $posts, $params)
    {
        $foody_posts = array_map('Foody_Post::create', $posts);
        $foody_recipes = array_filter($foody_posts, function ($post) {
            return $post instanceof Foody_Recipe;
        });

        /** @var Foody_Recipe $foody_recipe */
        foreach ($foody_recipes as $foody_recipe) {
            $foody_recipe->init();
        }

        $attributes = [
            'tags' => [],
            'categories' => [],
            'ingredients' => [],
            'accessories' => [],
            'techniques' => [],
            'limitations' => [],
        ];

        /** @var Foody_Recipe $foody_recipe */
        foreach ($foody_recipes as $foody_recipe) {

            // ingredients
            $ingredients_titles = [];
            foreach ($foody_recipe->ingredients_groups as $ingredients_group) {
                $group_ingredients_titles = array_map(function ($ingredient) {
                    /** @var Foody_Ingredient $ingredient */
                    return $ingredient->getTitle();
                }, $ingredients_group['ingredients']);

                $ingredients_titles = array_merge($ingredients_titles, $group_ingredients_titles);
            }

            $ingredients_titles = array_diff($ingredients_titles, $params['ingredients']);
            $attributes['ingredients'] = $ingredients_titles;


            // tags and categories
            $tags = wp_get_post_tags($foody_recipe->id, ['fields' => 'names']);
            $categories = wp_get_post_categories($foody_recipe->id, ['fields' => 'names']);
            //$tags = array_merge($tags, $categories);
            if (!empty($tags)) {
                $attributes['tags'] = array_merge($attributes['tags'], array_diff($tags, $params['tags']));
            }
            if (!empty($categories)) {
                $attributes['categories'] = array_merge($attributes['categories'], array_diff($categories, $params['categories']));
            }


            $repeaters = [
                'accessories' => [
                    'selector' => 'accessories_accessories',
                    'mapper' => 'get_the_title'
                ],
                'techniques' => [
                    'selector' => 'techniques_techniques',
                    'mapper' => 'get_the_title'
                ],
                'limitations' => [
                    'selector' => 'limitations',
                    'mapper' => function ($limitation) {
                        return get_term_field('name', $limitation);
                    }
                ],
            ];

            foreach ($repeaters as $name => $args) {
                $accessories = get_field($args['selector'], $foody_recipe->id);
                if (is_array($accessories)) {
                    $accessories = array_map($args['mapper'], $accessories);
                    $attributes[$name] = array_merge($attributes[$name], array_diff($accessories, $params[$name]));
                }
            }
        }

        foreach ($attributes as &$attribute) {
            $attribute = array_unique($attribute);
            $attribute = array_values($attribute);
        }

        return $attributes;
    }

    public function findPostsByTitle($titles, string $post_type)
    {
        global $wpdb;
        $titles_in = implode(', ', array_fill(0, count($titles), '%s'));
        $query = "SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' and post_title in ($titles_in) and post_type = '$post_type'";
        $query = $wpdb->prepare($query, $titles);
        $results = $wpdb->get_results($query);
        $results = array_map(function ($post) {
            return $post->ID;
        }, $results);

        return $results;
    }

    private function getTermsByNames($names, string $taxonomy)
    {
        $args = [
            'name' => $names,
            'taxonomy' => $taxonomy,
            'fields' => 'ids'
        ];

        $query = new WP_Term_Query($args);

        return $query->get_terms();
    }


    /**
     * @param $where string
     * @param $query WP_Query
     *
     * @return mixed
     */
    public function botPostsWhere($where, $query)
    {
        if ($query->get('has_wildcard_key')) {
            $where = str_replace(
                "meta_key = 'ingredients_ingredients_groups_\$_ingredients_\$_ingredient",
                "meta_key LIKE 'ingredients_ingredients_groups_%_ingredients_%_ingredient",
                $where
            );
        }

        return $where;
    }

    private function findAuthorsByDisplayName($authors)
    {
        global $wpdb;
        $names = implode(', ', array_fill(0, count($authors), '%s'));
        $query = "SELECT ID FROM {$wpdb->users} WHERE display_name in ($names)";
        $query = $wpdb->prepare($query, $authors);

        $results = $wpdb->get_results($query);
        $results = array_map(function ($post) {
            return $post->ID;
        }, $results);

        return $results;
    }


    private function sortIngredientsByPopularity($ingredients)
    {
        global $wpdb;
        $results = [];
        foreach ($ingredients as $ingredient) {

            $query = "SELECT count(meta_value) as count 
            FROM {$wpdb->postmeta} where meta_key like  'ingredients_ingredients_groups_%_ingredients_%_ingredient'  
            AND meta_value = (SELECT ID FROM {$wpdb->posts} where post_title = '$ingredient' and post_status = 'publish' AND post_type = 'foody_ingredient')
            group by meta_value";

            $toPush = $wpdb->get_results($query);
            if (is_array($toPush) && !empty($toPush)) {
                $results[$ingredient] = $toPush[0]->count;
            }
        }
        asort($results);
        return $results;
    }

    private function sortResults($results)
    {
        usort($results, array($this, 'sortByLevel'));
        return $results;
    }

    private function sortByLevel($a, $b)
    {
        $aIngredientsCount = count($a['ingredients']);
        $bIngredientsCount = count($b['ingredients']);

        if ($aIngredientsCount === $bIngredientsCount) {

            $aNumber = $this->getLevelNumericRepresentation($a['difficulty_level']);
            $bNumber = $this->getLevelNumericRepresentation($b['difficulty_level']);

            if ($aNumber === $bNumber) {
                $aTime = get_field('overview', $this->helperArr[$a['title']])['total_time'];
                $aTime = $this->getTimeAsMinutes($aTime['time'], $aTime['time_unit']);

                $bTime = get_field('overview', $this->helperArr[$b['title']])['total_time'];
                $bTime = $this->getTimeAsMinutes($bTime['time'], $bTime['time_unit']);
                if ($aTime === $bTime) {
                    $aAuthor = $this->authorsPriority[$a['author']];
                    $bAuthor = $this->authorsPriority[$b['author']];

                    if ($aAuthor === $bAuthor) return 0;
                    return ($aAuthor < $bAuthor) ? -1 : 1;
                }
                return ($aTime < $bTime) ? -1 : 1;
            }
            return ($aNumber < $bNumber) ? -1 : 1;
        }
        return ($aIngredientsCount < $bIngredientsCount) ? -1 : 1;
    }

    private function getTimeAsMinutes($time, $unit)
    {

        switch ($unit) {
            case 'minutes':
                return $time;
                break;
            case 'hours':
                return $time * 60;
                break;
            case 'days':
                return $time * 60 * 24;
                break;
            default:
                return $time;
        }

    }

    private function getLevelNumericRepresentation($case)
    {
        $result = 0;
        switch ($case) {
            case "בסיסי":
                $result = 1;
                break;
            case "בינונית":
                $result = 2;
                break;
            case "קשה":
                $result = 3;
                break;
            case "קשה מאוד":
                $result = 4;
                break;
        }
        return $result;
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

}