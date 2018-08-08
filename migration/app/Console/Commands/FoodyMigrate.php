<?php

namespace App\Console\Commands;

use App\WordPressApi;
use Illuminate\Console\Command;
use Jenssegers\Mongodb\Connection as MongoDBConnection;
use League\Flysystem\Exception;
use WP_Query;


class FoodyMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foody {action?} {--db-ingredients} {--recipes} {--accessories} {--techniques} {--ingredients} {--units} {--only-taxonomy} {--without-taxonomy} {--start=} {--end=} {--single=} {--startID=} {--endID=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Begin the migration of Foody recipes database';

    public $originDB;
    public $wp;


    public $debug = false;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->originDB = new MongoDBConnection([
            'driver' => 'mongodb',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', 27017),
            'database' => 'Foody',
            'username' => '',
            'password' => ''
        ]);

        $this->wp = new WordPressApi();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {

        if ($this->argument('action') == 'migrate') {

            $this->processMigration();

            $this->info("\nFinished processing migration.\n");

        } elseif ($this->argument('action') == 'migrate-full') {
            $this->processFullMigration();
        } else {
            $this->info("\n\n\n                Foody migration tool.\n\nFor usage instructions see the repository readme.md");
        }

        $this->info("\nCommand finished.\n\n");

    }

    /**
     *
     *  Run migration according to options
     *
     */
    private function processMigration()
    {
        if ($this->option('units')) {
            $this->processUnits();
        }

        if ($this->option('ingredients')) {
            $this->processIngredients();
        }

        if ($this->option('recipes')) {
            $this->processRecipes();
        }

        if ($this->option('accessories')) {

            $query = $this->originDB->collection('otherdatamodels');

            $data = $query->get()->first();

            $accessories = $data['Accessories'];
            $this->processData(
                'foody_accessory', $accessories
            );
        }

        if ($this->option('techniques')) {

            $query = $this->originDB->collection('otherdatamodels');

            $data = $query->get()->first();

            $techniques = $data['CoockingTechnics'];
            $this->processData(
                'foody_technique', $techniques
            );
        }

        if ($this->option('db-ingredients')) {
            $this->processDBIngredients();
        }
    }

    /**
     *
     *  Run migration with all dependencies
     *
     */
    private function processFullMigration()
    {
        // units
        $this->processUnits();

        // ingredients from nutrients values
        $this->processIngredients();

        // accessories
        $query = $this->originDB->collection('otherdatamodels');

        $data = $query->get()->first();

        $accessories = $data['Accessories'];
        $this->processData(
            'foody_accessory', $accessories
        );

        // techniques
        $query = $this->originDB->collection('otherdatamodels');

        $data = $query->get()->first();

        $techniques = $data['CoockingTechnics'];
        $this->processData(
            'foody_technique', $techniques
        );

        // ingredients from Mongo
        $this->processDBIngredients();

//        $this->debug = true;

        // recipes
        $this->processRecipes();


    }

    public function processTaxonomy()
    {
        $categories = $this->extractValuesFromRecipes(function ($category) {
            return $category != null;
        }, 'General.Category');


        $this->insertTerms('category', $categories);
    }

    public function processRecipes()
    {

        $this->info("\n\nConverting and importing recipes...\n\n");

        $query = $this->originDB->collection('recipemodels')->where('status', '=', 'active');

        $recipes = $query->get()->toArray();


        if ($this->debug) {

            $debug_recipe = null;

            foreach ($recipes as $recipe) {
                if (count($recipe['RecipeIngredients']) > 1) {
                    $debug_recipe = $recipe;
                    break;
                }
            }
            if ($debug_recipe == null) {
                $debug_recipe = $recipes[0];
            }
            $recipes = array_slice($recipes, 300, 1);
        }

        $bar = $this->output->createProgressBar(count($recipes));


        foreach ($recipes as $recipe) {

            if ($this->debug && false) {
                echo json_encode($recipe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            $title = trim($recipe['Name']);
            $post_name = sanitize_title_with_dashes($title, null, 'save');

            // categories


            $categories = $recipe['General']['Category'];

            if ($categories) {
                $categories = array_map(function ($category) {
                    $id = null;
                    if ($category && !empty($category)) {
                        $term = get_term_by('name', $category, 'category');
                        if ($term && !is_wp_error($term)) {
                            $id = $term->term_id;
                        }
                    }
                    return $id;

                }, $categories);

                $categories = array_filter($categories, function ($category) {
                    return $category != null && is_numeric($category);
                });
            } else {
                $categories = [];
            }

            // post title
            $title = trim($recipe['Name']);

            $post = [
                'post_title' => $title,
                'post_name' => $post_name,
                'post_type' => 'foody_recipe',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_content' => $this->get_content($recipe['HowTo']),
                'post_excerpt' => $recipe['General']['SubTextDesktop'],
                'post_category' => $categories
            ];


            // insert basic post data and get post id

            $post_id = wp_insert_post($post, true);

            if (is_wp_error($post_id)) {
                $this->error('Error inserting recipe post: ' . $post_id->get_error_message());
                continue;
            }

            echo "post $post_id added";


            // set post template
            update_post_meta($post_id, '_wp_page_template', 'page-templates/content-with-sidebar.php');

            // featured image

            if (isset($recipe['General']['Image']) && !empty($recipe['General']['Image'])) {
                $attachment_id = $this->upload_image($post_id, $recipe['General']['Image'], $title);

                if (!is_wp_error($attachment_id)) {
                    if (!is_null($attachment_id)) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                } else {
                    $this->error($attachment_id->get_error_message());
                }
            }

            // ===== start custom fields ===== //

            // accessories

            $accessories = $recipe['Other']['Accessories'];

            $accessories = $this->get_posts_by_names($accessories, 'foody_accessory');

            update_field('accessories_accessories', $accessories, $post_id);

            // techniques

            $techniques = $recipe['Other']['CoockingTechnics'];

            $techniques = $this->get_posts_by_names($techniques, 'foody_technique');

            update_field('techniques_techniques', $techniques, $post_id);

            // intro

            $intro = "<p>" . $recipe['General']['Introduction'] . "</p>";

            update_field('preview', $intro, $post_id);

            // mobile caption

            $mobile_caption = $recipe['General']['SubTextMobile'];

            update_field('mobile_caption', $mobile_caption, $post_id);

            // cooking time

            $cooking_time = $recipe['WorkTime'];

            $total_time = $recipe['GeneralTime'];

            update_field('overview_preparation_time_time',
                intval($cooking_time['Amount']), $post_id);
            update_field('overview_preparation_time_time_unit',
                $this->get_time_unit($cooking_time['Type']), $post_id);


            update_field('overview_total_time_time',
                intval($total_time['Amount']), $post_id);
            update_field('overview_total_time_time_unit',
                $this->get_time_unit($total_time['Type']), $post_id);

            $difficulty = $this->get_difficulty($recipe['General']['Difficulty']);

            update_field('overview_difficulty_level', $difficulty, $post_id);

            $original_id = $recipe['_id'];

            update_field('original_recipe', "http://foody-prod.moveodevelop.com/admin/CreateOrEditRecipe?recipeId=$original_id", $post_id);


            // add ingredients groups

            $ingredients_groups = $recipe['RecipeIngredients'];

            $number_of_dishes = $recipe['General']['AmountDinners'];
            if (!is_numeric($number_of_dishes)) {
                $number_of_dishes = 1;
            }

            $number_of_dishes = intval($number_of_dishes);
            $amount_for = 'כמות מנות';

            update_field('ingredients_amount_for', $amount_for, $post_id);
            update_field('ingredients_number_of_dishes', $number_of_dishes, $post_id);

            foreach ($ingredients_groups as $ingredients_group) {

                $title = $ingredients_group['Name'];

                if (empty($title) || $title == 'כללי' || is_null($title)) {
                    $title = '';
                }

                $ingredients = $ingredients_group['Ingredients'];

                $_this = $this;

                // convert db ingredients into repeater rows
                $ingredients = array_map(function ($ingredient) use ($_this, $post_id) {

                    $ingredient_post = $_this->get_ingredient_by_name($ingredient['Ingredient']);
                    if (is_null($ingredient_post)) {
                        return new \WP_Error(null, 'no ingredient');
                    }


                    $unit = $_this->get_unit_by_name($ingredient['AmountTypes']);

                    if (is_wp_error($unit) || is_null($unit)) {
                        echo 'no unit for value ' . $ingredient['AmountTypes'];
                        return new \WP_Error(null, 'no unit for value ' . $ingredient['AmountTypes']);
                    }

                    $amount = $ingredient['Amount'];

                    $amount = $_this->parse_amount($amount);

                    $ingredient_row = [
                        'amounts' => [
                            [
                                'amount' => $amount,
                                'unit' => ($unit instanceof \WP_Term) ? $unit->term_id : ''
                            ]
                        ],
                        'ingredient' => $ingredient_post->ID
                    ];


//                    if (!empty($ingredient['AlternativeIngredient'])) {
//                        $alt_ingredient_post = $this->get_ingredient_by_name($ingredient['AlternativeIngredient']);
//
//                        if (!is_null($alt_ingredient_post)) {
//                            $alt_amount = $this->parse_amount($ingredient['AlternativeAmountType']);
//                            $alt_unit = $this->get_unit_by_name($ingredient['AlternativeAmountType']);
//
//                            if (!is_wp_error($alt_unit) && !is_null($alt_unit)) {
//                                $ingredient_row['amounts'][] = [
//                                    'amount' => $alt_amount,
//                                    'unit' => $alt_unit->term_id,
//                                ];
//                            }
//                        }
//                    }

                    return $ingredient_row;

                }, $ingredients);

                // filter nulls
                $ingredients = array_filter($ingredients, function ($ingredient) {
                    return !is_null($ingredient) && !is_wp_error($ingredient);
                });


                if (count($ingredients) > 0) {

                    $group_row = [
                        'title' => $title,
                        'ingredients' => $ingredients
                    ];


                    $existing = get_field('field_5b0584cf8c831', $post_id);

                    if ($existing && isset($existing['ingredients_groups'])) {
                        $existing['ingredients_groups'][] = $group_row;
                        update_field('field_5b0584cf8c831', $existing, $post_id);
                    } else {
                        update_field(
                            'field_5b0584cf8c831',
                            [
                                'ingredients_groups' => [
                                    $group_row
                                ]
                            ],
                            $post_id
                        );
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
    }

    public function processIngredients()
    {
        $this->info("\n\nConverting and importing ingredients...\n\n");

        $nutrients = $this->getNutritions();


        if ($this->debug) {
            $nutrients = [
                array_shift($nutrients)
            ];
        }

        $bar = $this->output->createProgressBar(count($nutrients));

        foreach ($nutrients as $nutrient_group) {

            $nutrient = $nutrient_group[0];

            $post_name = sanitize_title_with_dashes($nutrient['ingredient'], null, 'save');
            $post = [
                'post_type' => 'foody_ingredient',
                'post_title' => trim($nutrient['ingredient']),
                'post_status' => 'publish',
                'post_name' => $post_name,
                'post_author' => 1,
                'comment_status' => 'closed'
            ];

            $post_id = wp_insert_post($post);

            foreach ($nutrient_group as $item) {
                foreach ($item['nutrients'] as $name => $nutrient_val) {
                    $row = [
                        'value' => $nutrient_val,
                        'nutrient' => $name
                    ];

                    if (isset($item['unit'])) {
                        $row['unit'] = $item['unit'];
                    }

                    add_row('nutrients', $row, $post_id);
                }
            }

            $bar->advance();
        }

        $bar->finish();
    }

    public function processDBIngredients()
    {
        $ingredients = json_decode(file_get_contents(base_path('data/ingredients.json')));
        $ingredients = array_map('trim', $ingredients);
        $ingredients = array_unique($ingredients);
        $this->processData('foody_ingredient', $ingredients);
    }

    public function processUnits()
    {
        $units = json_decode(file_get_contents(base_path('data/units.json')));
        $units = array_map('trim', $units);
        $units = array_unique($units);
        $this->insertTerms('units', $units);
        return $units;
    }

    /**
     * Generic method to add posts
     * of different types from an array
     * of strings (titles)
     * @param string $type a valid post_type
     * @param array $data array of posts titles
     */
    public function processData($type, $data)
    {
        $this->info("\n\nConverting and importing $type...\n\n");


        $bar = $this->output->createProgressBar(count($data));

        foreach ($data as $item) {

            $title = trim($item);

            $post = [
                'post_type' => $type,
                'post_title' => $title,
                'post_name' => sanitize_title_with_dashes($title),
                'post_status' => 'publish',
                'comments_status' => 'closed'
            ];

            $result = wp_insert_post($post);

            if (is_wp_error($result)) {
                echo "error saving $type: " . $result->get_error_message();
            }

            $bar->advance();
        }

        $bar->finish();
    }

    private function getNutritions()
    {
        $nutritions = json_decode(file_get_contents(base_path('data/nutrients.json')), true);

        $nutritions = array_filter($nutritions, function ($nutrition) {
            return $nutrition != null && isset($nutrition['רכיב']) && $nutrition['רכיב'] != '';
        });


        $nutritions = array_map(function ($nutrition) {

            $unit = get_term_by('name', trim($nutrition['יחידת מידה']), 'units');
            $ret_val = [
                'nutrients' => [
                    'calories' => $this->defaultVal($nutrition['קלוריות']),
                    'fats' => $this->defaultVal($nutrition['שומן']),
                    'sodium' => $this->defaultVal($nutrition['נתרן']),
                    'carbohydrates' => $this->defaultVal($nutrition['פחמימות']),
                    'sugar' => $this->defaultVal($nutrition['סוכר']),
                    'protein' => $this->defaultVal($nutrition['חלבון'])
                ],
                'ingredient' => $nutrition['רכיב']
            ];

            if (!is_wp_error($unit) && !is_null($unit) && is_object($unit)) {
                $ret_val['unit'] = $unit->term_id;
            }

            return $ret_val;

        }, $nutritions);


        $nutritions = $this->_group_by($nutritions, 'ingredient');

        return $nutritions;
    }

    private function insertTerms($termType, $terms)
    {

        $this->info("\n\nConverting and importing terms of type $termType...\n\n");

        $bar = $this->output->createProgressBar(count($terms));


        foreach ($terms as $term) {
            $source = wp_insert_term($term, $termType);
            if (is_wp_error($source)) {
                $this->error($source->get_error_message());
            } else {
                $source_id = $source['term_id'];
                add_term_meta($source_id, 'active', true);
            }
            $bar->advance();
        }

        $bar->finish();
    }

    private function extractValuesFromRecipes($mapper_func, $select, $uniqe_key = null)
    {
        $query = $this->originDB->collection('recipemodels')->select([$select]);

        $values = $query->get();

        $arr = $values->toArray();

        $arr = array_map($mapper_func, $arr);

        $arr = array_flatten($arr);

        $arr = array_filter($arr, function ($value) {
            return $value != null;
        });

        if ($uniqe_key != null) {
            $arr = $this->uniqueAssocArray($arr, $uniqe_key);
        } else {
            $arr = array_unique($arr);
        }

        return $arr;
    }

    function uniqueAssocArray($array, $uniqueKey)
    {
        if (!is_array($array)) {
            return array();
        }
        $uniqueKeys = array();
//        foreach ($array as $key => $item) {
//            $groupBy = $item[$uniqueKey];
//            if (isset($uniqueKeys[$groupBy])) {
//                //compare $item with $uniqueKeys[$groupBy] and decide if you
//                //want to use the new item
//                $replace = ...
//        } else {
//                $replace = true;
//            }
//            if ($replace) $uniqueKeys[$groupBy] = $item;
//        }
        return $uniqueKeys;
    }

    private function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }

    private function defaultVal($value, $default = 0)
    {
        if (empty($value)) {
            $value = $default;
        }

        return $value;
    }

    public function get_ingredient_by_name($name)
    {

        $wp_query = new WP_Query([
            'name' => sanitize_title_for_query(trim($name)),
            'post_type' => 'foody_ingredient',
            'post_status' => 'publish',
            'numberposts' => 1
        ]);


        $ingredient_post = $wp_query->get_posts();

        if (count($ingredient_post) == 0 || !isset($ingredient_post[0])) {
            $ingredient_post = null;
        }

        if (is_countable($ingredient_post) && count($ingredient_post) > 0 && ($ingredient_post[0] instanceof \WP_Post)) {
            $ingredient_post = $ingredient_post[0];
        }

        return $ingredient_post;
    }

    public function get_unit_by_name($name)
    {
        if (empty($name) || $name == 'ללא מידה') {
            return '';
        }

        $unit = get_term_by('name', trim($name), 'units');

        if (is_wp_error($unit) || is_null($unit) || $unit === false) {
            return NULL;
        }


        return $unit;
    }

    public function parse_amount($amount)
    {
        if (empty($amount)) {
            $amount = 1;
        } else {
            if (is_numeric($amount)) {
                $amount = intval($amount);
            } elseif ($amount === 'NaN') {
                $amount = 1;
            } else {
                try {
                    $amount = preg_replace('/[\p{Hebrew}]/', '', $amount);
                    $amount = preg_replace('/-/', '', $amount);
                    $amount = trim($amount);
                    if (preg_match('/[1-9]{1,} [1-9]{1,}\/[1-9]/', $amount)) {
                        $fractions = explode(' ', $amount);

                        $fraction = $fractions[1];
                        switch ($fraction) {
                            case '1/2':
                                $fraction_num = 0.5;
                                break;

                            case '1/4':
                                $fraction_num = 0.25;
                                break;
                            case '1/3':
                                $fraction_num = 0.3;
                                break;
                            case '1/8':
                                $fraction_num = 0.125;
                                break;

                            default:
                                $fraction_num = 0;
                        }

                        $amount = intval($fractions[0]) + $fraction_num;
                    }
                } catch (Exception $exception) {
                    $amount = 1;
                }

            }
        }


        return $amount;

    }

    private function get_posts_by_names($names, $type)
    {
        if ($names == null || !is_array($names) || count($names) == 0) {
            return [];
        }

        $args = [
            'post_name__in' => array_map(function ($name) {
                return trim($name);
            }, $names),
            'post_type' => $type
        ];

        $query = new WP_Query($args);
        $posts = $query->get_posts();
        if (count($posts) != count($names)) {
            $this->error('posts count does not match names count');
        }
        return array_map(function ($post) {
            return $post->ID;
        }, $posts);
    }

    private function upload_image($postID, $url, $alt = "")
    {

        if (!$postID || !is_numeric($postID)) {
            return new \WP_Error("invalid post id: $postID");
        }

        try {
            $wp_path = base_path("../web/wp");
            require_once("$wp_path/wp-admin/includes/image.php");
            require_once("$wp_path/wp-admin/includes/file.php");
            require_once("$wp_path/wp-admin/includes/media.php");

//            $tmp = download_url($url);

            $tmp = wp_tempnam('', base_path('tmp/'));

            $ext = pathinfo($url, PATHINFO_EXTENSION);

            $tmp = str_replace('.tmp', '.' . $ext, $tmp);

            $file = file_put_contents($tmp, file_get_contents($url));


            $id = null;
            if ($file) {
                $desc = $alt;
                $file_array = array();

                // Set variables for storage
                // fix file filename for query strings
                preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
                $file_array['name'] = basename($matches[0]);
                $file_array['name'] = pathinfo($file_array['name'], PATHINFO_FILENAME) . '.' . pathinfo($file_array['name'], PATHINFO_EXTENSION);
                $file_array['tmp_name'] = $tmp;
                $file_array['name'] = $tmp;

                // If error storing temporarily, unlink
                if (is_wp_error($tmp)) {
                    @unlink($file_array['tmp_name']);
                    $file_array['tmp_name'] = '';
                }

                // do the validation and storage stuff
                $id = media_handle_sideload($file_array, $postID, $desc);

                // If error storing permanently, unlink
                if (is_wp_error($id)) {
                    @unlink($file_array['tmp_name']);
                    return $id;
                }
                @unlink($file_array['tmp_name']);
            }

            return $id;
        } catch (Exception $e) {
            return new \WP_Error($e->getMessage());
        }
    }

    private function get_time_unit($unit)
    {
        $time_unit = '';
        switch ($unit) {
            case 'דקות':
                $time_unit = 'minutes';
                break;
            case 'שעות':
                $time_unit = 'hours';
                break;
            case 'ימים':
                $time_unit = 'days';
                break;
        }

        return $time_unit;
    }

    private function get_difficulty($db_difficulty)
    {
        $difficulty = 1;

        if (is_array($db_difficulty)) {
            if (isset($db_difficulty[0])) {
                $db_difficulty = $db_difficulty[0];
            } else {
                return $difficulty;
            }
        }

        $difficulty = 1;

        switch ($db_difficulty) {
            case 'קל':
                $difficulty = 2;
                break;

            case 'בינוני':
                $difficulty = 3;
                break;
        }

        return $difficulty;
    }

    public function info($string, $verbosity = null)
    {
        $string = "\n$string\n";
        parent::info($string, $verbosity);
    }


    public function error($string, $verbosity = null)
    {
        file_put_contents(base_path('logs/error.log'), $string . PHP_EOL, FILE_APPEND);

        parent::error($string, $verbosity);
    }

    private function get_content($content_array)
    {

        $title = '<h2>אופן הכנה</h2>';
        $start_el = '<ol>';

        $content = implode('', array_map(function ($instruction) {
            return "<li>$instruction</li>";
        }, $content_array));

        $end_el = '</ol>';

        return $title . $start_el . $content . $end_el;
    }


}