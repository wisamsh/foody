<?php

namespace App\Console\Commands;

use App\WordPressApi;
use Illuminate\Console\Command;
use Jenssegers\Mongodb\Connection as MongoDBConnection;
use League\Flysystem\Exception;
use WP_Query;
use WP_User;


class FoodyMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foody {action?} {--taxonomy} {--categories} {--users} {--limitations} {--db-ingredients} {--recipes} {--accessories} {--techniques} {--ingredients} {--units} {--pans}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Begin the migration of Foody recipes database';

    public $originDB;
    public $wp;


    public $debug = false;

    private $authors = [
        "אבי לוי" => [
            'username' => 'אבי לוי',
            'email' => 'avi_levy@foody.co.il'
        ],
        "ישראל אהרוני" => [
            'username' => 'ישראל אהרוני',
            'email' => 'israel_aharoni@foody.co.il'
        ],
        "איילת הירשמן" => [
            'username' => 'איילת הירשמן',
            'email' => 'ayelet_hirshman@foody.co.il'
        ],
        "קרין גורן" => [
            'username' => 'קרין גורן',
            'email' => 'karin_goren@foody.co.il'
        ],
        "שי-לי ליפא" => [
            'username' => 'שי-לי ליפא',
            'email' => 'shai_li_lifa@foody.co.il'
        ],
        "הילה אלפרט ומאיר אדוני" => [
            'username' => 'הילה אלפרט ומאיר אדוני',
            'email' => 'alpert_adoni@foody.co.il'
        ],
        "משה שגב" => [
            'username' => 'משה שגב',
            'email' => 'moshe_segev@foody.co.il'
        ],
        "ירון קסטנבאום" => [
            'username' => 'ירון קסטנבאום',
            'email' => 'yaron_kastenbaum@foody.co.il'
        ],
        "מיקי שמו" => [
            'username' => 'מיקי שמו',
            'email' => 'miki_shemo@foody.co.il'
        ],
        "אודי ואושר" => [
            'username' => 'אודי ואושר',
            'email' => 'ori_osher@foody.co.il'
        ],
        "תמרה אהרוני" => [
            'username' => 'תמרה אהרוני',
            'email' => 'tamara_aharoni@foody.co.il'
        ],
        "רחלי קרוט" => [
            'username' => 'רחלי קרוט',
            'email' => 'reheli_krut@foody.co.il'
        ],
        "אלון שבו" => [
            'username' => 'אלון שבו',
            'email' => 'alon_shebo@foody.co.il'
        ],
        "עז תלם" => [
            'username' => 'עז תלם',
            'email' => 'oz_telem@foody.co.il'
        ],
        "אושר אידלמן" => [
            'username' => 'אושר אידלמן',
            'email' => 'osher_idelman@foody.co.il'
        ],
        "אינס ינאי" => [
            'username' => 'אינס ינאי',
            'email' => 'ines_yanay@foody.co.il'
        ],
        "נטלי לוין" => [
            'username' => 'נטלי לוין',
            'email' => 'nataly_levin@foody.co.il'
        ],
        "ניקי ב" => [
            'username' => 'ניקי ב',
            'email' => 'nicky_b@foody.co.il'
        ],
        "אולגה טוכשר" => [
            'username' => 'אולגה טוכשר',
            'email' => 'olga_tuscher@foody.co.il'
        ],
        "יונית צוקרמן" => [
            'username' => 'יונית צוקרמן',
            'email' => 'yonit_tzukerman@foody.co.il'
        ],
        "רות אופק" => [
            'username' => 'רות אופק',
            'email' => 'ruth_ofek@foody.co.il'
        ],
        "רותם ליברזון" => [
            'username' => 'רותם ליברזון',
            'email' => 'rotem_lieberzon@foody.co.il'
        ],
        "אפרת ליכטנשטט" => [
            'username' => 'אפרת ליכטנשטט',
            'email' => 'efrat_lichtenstat@foody.co.il'
        ],
        "שר פיטנס" => [
            'username' => 'שר פיטנס',
            'email' => 'sher_fitness@foody.co.il'
        ],
        "Foody" => [
            'email' => 'system@foody.co.il',
            'username' => 'Foody'
        ]
    ];

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
        if ($this->option('taxonomy')) {
            $this->processTaxonomy();
        } else {
            if ($this->option('units')) {
                $this->processUnits();
            }

            if ($this->option('pans')) {
                $this->processPans();
            }
            if ($this->option('limitations')) {
                $this->processLimitations();
            }

            if ($this->option('categories')) {
                $this->processTaxonomy();
            }
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

        if ($this->option('users')) {
            $this->processUsers();
        }
    }

    /**
     *
     *  Run migration with all dependencies
     *
     */
    private function processFullMigration()
    {
        // authors
        $this->processUsers();

        // all taxonomies
        $this->processTaxonomy();

        // ingredients from nutrients values
        $this->processIngredients();

        // accessories posts
        $query = $this->originDB->collection('otherdatamodels');

        $data = $query->get()->first();

        $accessories = $data['Accessories'];
        $this->processData(
            'foody_accessory', $accessories
        );

        // techniques posts
        $query = $this->originDB->collection('otherdatamodels');

        $data = $query->get()->first();

        $techniques = $data['CoockingTechnics'];
        $this->processData(
            'foody_technique', $techniques
        );

        // ingredients from Mongo
        $this->processDBIngredients();

        // recipes
        $this->processRecipes();
    }


    /**
     * Insert all taxonomy type into WordPress
     */
    public function processTaxonomy()
    {
        $this->processUnits();
        $this->processPans();
        $this->processLimitations();
        $this->processCategories();
    }


    /**
     * Insert recipe posts with all custom fields
     */
    public function processRecipes()
    {

        $this->info("\n\nConverting and importing recipes...\n\n");

        $query = $this->originDB->collection('recipemodels')->where('status', '=', 'active');

        $recipes = $query->get()->toArray();

        $debug_recipe = array_first($recipes, function ($recipe) {
            return isset($recipe['General']['Templates']) && !empty($recipe['General']['Templates']);
        });

        if ($this->debug) {
            if ($debug_recipe == null) {
                $debug_recipe = $recipes[0];
            }

            $recipes = [
                $debug_recipe
            ];
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
            $meal_types = [];
            if (isset($recipe['General']['MealType'])) {

                if (is_array($recipe['General']['MealType']) && count($recipe['General']['MealType']) > 0) {
                    $meal_types = $recipe['General']['MealType'];
                }
            }
            if (!$categories) {
                $categories = [];
            }

            $categories = array_merge($categories, $meal_types);
            $categories = array_unique($categories);

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

            // post title
            $title = trim($recipe['Name']);

            if (!isset($recipe['Author']) || !isset($this->authors[$recipe['Author']])) {
                $author = $this->authors['Foody'];
            } else {
                $author = $this->authors[$recipe['Author']];
            }

            $author_email = $author['email'];


            $user = get_user_by('email', $author_email);
            $author_id = 1;
            if ($user) {
                $author_id = $user->ID;
            }

            $post = [
                'post_title' => $title,
                'post_name' => $post_name,
                'post_type' => 'foody_recipe',
                'post_status' => 'draft',
                'post_author' => $author_id,
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

            // sponsors
            if (isset($recipe['Other']['Sponsers'])) {
                $sponsors = $recipe['Other']['Sponsers'];

                if (is_array($sponsors) && count($sponsors) > 0) {

                    $sponsors = array_filter($sponsors, function ($sponsor) {
                        return !is_null($sponsor) && !empty($sponsor);
                    });

                    $sponsors = array_map('trim', $sponsors);

                    $this->update_plain_repeater('sponsors', 'sponsor', $sponsors, $post_id);
                }
            }

            // optional sponsors
            if (isset($recipe['Other']['OpcionalSponsers'])) {
                $optional_sponsors = $recipe['Other']['OpcionalSponsers'];

                if (is_array($optional_sponsors) && count($optional_sponsors) > 0) {

                    $optional_sponsors = array_filter($optional_sponsors, function ($sponsor) {
                        return !is_null($sponsor) && !empty($sponsor);
                    });

                    $optional_sponsors = array_map('trim', $optional_sponsors);

                    $this->update_plain_repeater('optional_sponsors', 'optional_sponsor', $optional_sponsors, $post_id);
                }
            }
            // tv shows
            if (isset($recipe['General']['TVOrigin'])) {
                $tv_shows = $recipe['General']['TVOrigin'];

                if (!empty($tv_shows)) {

                    $tv_shows = [
                        $tv_shows
                    ];

                    $this->update_plain_repeater('tv_shows', 'tv_show', $tv_shows, $post_id);
                }

            }

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


            if (
                isset($recipe['General']['Limitations']) &&
                is_array($recipe['General']['Limitations']) &&
                count($recipe['General']['Limitations']) > 0
            ) {
                $limitations = $recipe['General']['Limitations'];

                $limitations = array_map(function ($limitation) {
                    $limitation = trim($limitation);

                    $term = get_term_by('name', $limitation, 'limitations');
                    $term_id = null;
                    if ($term && !is_wp_error($term)) {
                        $term_id = $term->term_id;
                    }
                    return $term_id;
                }, $limitations);

                $limitations = array_filter($limitations, function ($limitation) {
                    return !is_null($limitation) && is_numeric($limitation);
                });

                update_field('limitations', $limitations, $post_id);
            }

            // add ingredients groups

            $ingredients_groups = $recipe['RecipeIngredients'];

            $number_of_dishes = $recipe['General']['AmountDinners'];
            if (!is_numeric($number_of_dishes)) {
                $number_of_dishes = 1;
            }


            // pans
            if (isset($recipe['General']['Templates']) && !empty($recipe['General']['Templates'])) {
                $pan = trim($recipe['General']['Templates']);
                $term = get_term_by('name', $pan, 'pans');
                if ($term && !is_wp_error($term)) {
                    update_field('ingredients_pan', $term->term_id, $post_id);
                    update_field('ingredients_use_pan_conversion', 1, $post_id);
                }
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


    /**
     * Insert ingredient posts from nutrients data
     */
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

    /**
     * Insert ingredient posts from MongoDB
     */
    public function processDBIngredients()
    {
        $ingredients = json_decode(file_get_contents(base_path('data/ingredients.json')));
        $ingredients = array_map('trim', $ingredients);
        $ingredients = array_unique($ingredients);
        $this->processData('foody_ingredient', $ingredients);
    }

    /**
     * Insert users with 'author' role
     */
    public function processUsers()
    {

        $this->info("\n\nConverting and importing recipes...\n\n");

        $users = $this->authors;

        $bar = $this->output->createProgressBar(count($users));

        $users_log = '';


        foreach ($users as $key => $user) {

            $email = $user['email'];
            $username = trim($email);
            if (null == username_exists($username)) {
                $password = wp_generate_password(12, false);
                $user_id = wp_create_user($username, $password, $email);

                if (is_wp_error($user_id)) {
                    $this->error('Error creating user: ' . $user_id->get_error_message());
                } else {

                    $u = new WP_User($user_id);

                    // Remove role
                    $u->remove_role('subscriber');

                    // Add role
                    $u->add_role('author');


                    $user_data = array_merge(
                        [
                            'ID' => $user_id,
                            'nickname' => $user['username']
                        ],
                        $this->get_first_and_last_name($user['username']));

                    wp_update_user($user_data);


                    $users_log .= "Created user with email: $email, password: $password\n";
                }
            } else {
                $this->info("username $username already exists");
            }
            $bar->advance();
        }

        $bar->finish();

        file_put_contents(base_path('logs/users.log'), $users_log, FILE_APPEND);
    }


    /**
     *
     */
    public function processUnits()
    {
        $this->insertTerms('units', 'units');
    }

    /**
     *
     */
    public function processPans()
    {
        $this->insertTerms('pans', 'pans');
    }

    /**
     *
     */
    public function processLimitations()
    {
        $this->insertTerms('limitations', 'limitations');
    }

    /**
     *
     */
    public function processCategories()
    {
        $this->insertTerms('category', 'categories');
    }

    /**
     * @param $termType string valid taxonomy type
     * @param $source_file string file name to read data from
     */
    private function insertTerms($termType, $source_file)
    {
        $terms = json_decode(file_get_contents(base_path("data/$source_file.json")));
        $terms = array_map('trim', $terms);
        $terms = array_unique($terms);

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

    /**
     * @param $user string full name
     * @return array containing first and last name
     */
    private function get_first_and_last_name($user)
    {
        $parts = explode(' ', $user);

        $pivot = 2;

        if (count($parts) > $pivot) {
            $parts = [
                implode(' ', array_slice($parts, 0, $pivot)),
                implode(' ', array_slice($parts, $pivot))
            ];
        } elseif (count($parts) == 1) {
            $parts = [
                $parts[0],
                $parts[0]
            ];
        }

        return [
            'first_name' => $parts[0],
            'last_name' => $parts[1]
        ];
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

    /**
     * Get nutritional values from json
     *
     * @return array|mixed|object
     */
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

    /**
     * Groups an associative arrat by key
     * @param $array array
     * @param $key string
     * @return mixed[][]
     */
    private function _group_by($array, $key)
    {
        $return = array();
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }

    /**
     * Get array key val or default val
     * @param $value
     * @param int $default
     * @return int
     */
    private function defaultVal($value, $default = 0)
    {
        if (empty($value)) {
            $value = $default;
        }

        return $value;
    }

    /** get a @link WP_Post object by name
     * @param $name
     * @return \WP_Post
     */
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

    /**
     * Get a @link WP_Term object by term name
     * @param $name string unit term name
     * @return array|false|null|string|\WP_Term
     */
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


    /**
     * Handle fractional representation of
     * ingredients amounts
     * @param $amount string
     * @return int|mixed|string
     */
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

    /**
     * Map strings to posts ids
     * @param $names string[] post names
     * @param $type string post type
     * @return int[] posts ids
     */
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

    /**
     * Upload an image to WP's media library
     * from a url and attach it to the post
     * @param $postID
     * @param $url
     * @param string $alt
     * @return int|null|object|\WP_Error
     */
    private function upload_image($postID, $url, $alt = "")
    {

        if (!$postID || !is_numeric($postID)) {
            return new \WP_Error("invalid post id: $postID");
        }

        try {
            $wp_path = base_path("../web/wp");
            /** @noinspection PhpIncludeInspection */
            require_once("$wp_path/wp-admin/includes/image.php");
            /** @noinspection PhpIncludeInspection */
            require_once("$wp_path/wp-admin/includes/file.php");
            /** @noinspection PhpIncludeInspection */
            require_once("$wp_path/wp-admin/includes/media.php");

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

    /** Maps time units from MongoDB to ACF
     * @param $unit string
     * @return string
     */
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

    /**
     * Maps difficulty levels from MongoDB to ACF
     * @param $db_difficulty string old db representation of a recipe's difficulty
     * @return int value to be used with ACF's dropdown field
     */
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

    /**
     * Converts an array of strings to an html ol
     * element
     *
     * @param $content_array string[]
     * @return string html string for the_content
     */
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

    /**
     * Updates a simple one text field repeater
     * for a given post
     *
     * @param $selector string repeater field name
     * @param $row_selector string row field name. a repeater with a single text field is supported
     * @param $rows string[] array of items to insert
     * @param $post_id int post id
     */
    private function update_plain_repeater($selector, $row_selector, $rows, $post_id)
    {
        $rows = array_map(function ($row) use ($row_selector) {
            $ret = [];
            $ret[$row_selector] = $row;
            return $ret;
        }, $rows);

        update_field($selector, $rows, $post_id);
    }

    /**
     * @param string $string
     * @param null $verbosity
     */
    public function info($string, $verbosity = null)
    {
        $string = "\n$string\n";
        parent::info($string, $verbosity);
    }


    /**
     * @param string $string
     * @param null $verbosity
     */
    public function error($string, $verbosity = null)
    {
        file_put_contents(base_path('logs/error.log'), $string . PHP_EOL, FILE_APPEND);

        parent::error($string, $verbosity);
    }


}