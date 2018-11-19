<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 7:17 PM
 */
abstract class Foody_Post implements Foody_ContentWithSidebar
{

    public $id;

    private $posted_on;

    protected $image;

    private $description;

    private $description_mobile;

    private $title;

    private $author_image;

    private $author_name;

    private $view_count;

    public $body;

    public $link;

    public $favorite = false;

    public static $MAX__RELATED_ITEMS = 3;


    public $post;

    protected $stub_images = array();

    /**
     * FoodyPost constructor.
     * @param WP_Post $post
     */
    public function __construct(WP_Post $post = null)
    {

        $this->stub_images = array(
            'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/Nimrod_Genisher_0272.jpg',
            'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/Nimrod_Genisher_0321.jpg',
            'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/Nimrod_Genisher_0032.jpg'
        );

        if ($post != null) {
            $this->post = $post;
            $this->id = $post->ID;
            global $wp_session;
            if (isset($wp_session['favorites']) && is_array($wp_session['favorites']) && in_array($this->id, $wp_session['favorites'])) {
                $this->favorite = true;
            }
            $this->image = get_the_post_thumbnail_url($this->id, 'list-item');
            $this->posted_on = foody_posted_on(false, $post);
            $this->description = !empty($post->post_excerpt) ? get_the_excerpt($this->id) : null;
            $this->description_mobile = get_field('mobile_caption', $this->id);
            $this->title = get_the_title($post->ID);
            $this->view_count = view_count_display(foody_get_post_views($this->id), 0);

            $post_author_id = get_post_field('post_author', $this->id);


            $user_avatars = get_the_author_meta('wp_user_avatars', $post_author_id);

            if (is_null($user_avatars) || empty($user_avatars) || !isset($user_avatars['90'])) {
                $this->author_image = get_avatar_url($this->post->post_author, ['size' => 96]);
            } else {
                $this->author_image = $user_avatars['90'];
            }

            $author = get_user_by('ID', $post->post_author);

            if ($author) {
                $this->author_name = get_user_by('ID', $post->post_author)->display_name;
            } else {
                $this->author_name = '';
            }


            $this->body = apply_filters('the_content', $post->post_content);
            $this->link = get_permalink($this->id);

        } else {
            $k = array_rand($this->stub_images);
            $v = $this->stub_images[$k];
            $this->image = $v;// $GLOBALS['images_dir'] . 'food.jpg';
            $this->posted_on = date('d.m.y');
            $this->description = 'המנה המושלמת לאירוח, קלה ולעולם לא מאכזבת. הטעם המושלם של תפוחי אדמה בתנור עם טוויסט מיוחד.';
            $this->title = 'סירות תפוחי אדמה אפויות';
            $this->view_count = view_count_display(13454, 1);
            $this->author_image = 'http://' . $_SERVER['HTTP_HOST'] . '/app/uploads/2018/05/avatar_user_2_1527527183-250x250.jpg';// $GLOBALS['images_dir'] . 'matan.jpg';
            $this->author_name = "ישראל אהרוני";
            $this->link = get_permalink();
        }
    }

    /**
     * @return string
     */
    public function getAuthorImage(): string
    {
        if ($this->author_image == null) {
            $this->author_image = '';
        }
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
     * @return mixed
     */
    public function getPostedOn()
    {
        return $this->posted_on;
    }

    /**
     * @param mixed $posted_on
     */
    public function setPostedOn($posted_on)
    {
        $this->posted_on = $posted_on;
    }

    /**
     * @return mixed
     */
    public function getImage($size = null)
    {
        $image = $this->image;
        if ($size != null) {
            $image = get_the_post_thumbnail_url($this->getId(), $size);
        }

        return $image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        if (wp_is_mobile()) {
            return $this->description_mobile;
        }
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function get_primary_category()
    {

        $primary = get_post_meta($this->post->ID, '_yoast_wpseo_primary_category', true);

        if (!$primary || !is_numeric($primary) || intval($primary) <= 0) {
            /** @var WP_Term[] $categories */
            $categories = wp_get_post_categories($this->id, ['fields' => 'all_with_object_id']);
            if (!is_wp_error($categories)) {
                if (is_array($categories)) {

                    $categories = array_filter($categories, function ($category) {
                        return $category instanceof WP_Term;
                    });

                    if (count($categories) > 0) {
                        $first = $categories[0];
                        $primary = $first->term_id;
                    }

                }
            }


        }

        return $primary;
    }

    public function the_featured_content()
    {
        the_post_thumbnail('foody-main');
    }

    public function the_sidebar_content()
    {
        $this->the_sidebar_related_content('מתכונים נוספים', 'פלייליסטים קשורים');
    }

    private function the_sidebar_related_content($recipes_title, $playlist_title)
    {
        if (!isset($args['hide_playlists']) || $args['hide_playlists'] == false) {
            $playlists_args = array(
                'title' => $playlist_title,
                'selector' => 'related_playlists',
                'content_classes' => 'related-playlists',
                'template_args_func' => function (Foody_Playlist $item) {
                    return array(
                        'count' => $item->num_of_recipes
                    );
                }
            );

            $playlists = $this->get_related_content_by_categories_and_custom('foody_playlist', 'related_playlists');

            $this->related_content($playlists_args, $playlists);
        }

        if (!isset($args['hide_recipes']) || $args['hide_recipes'] == false) {
            $recipes = $this->get_related_content_by_categories_and_custom('foody_recipe', 'related_recipes');

            $recipes_args = array(
                'title' => $recipes_title,
                'selector' => 'related_recipes',
                'content_classes' => 'related-recipes',
                'template_args_func' => function ($recipe) {
                    $foody_recipe = $recipe;
                    if (!$foody_recipe instanceof Foody_Recipe) {

                        $foody_recipe = new Foody_Recipe($recipe);
                    }
                    return array(
                        'duration' => $foody_recipe->getDuration(),
                        'has_video' => $foody_recipe->has_video
                    );
                }
            );

            $this->related_content($recipes_args, $recipes);
        }

    }

    private function related_content($related_content_args, $posts = null)
    {
        /** @var WP_Post[] $playlists */
        $related_content = posts_to_array($related_content_args['selector'], $this->post->ID);
        if ($posts != null) {
            $related_content = $posts;
        }

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
                    'image' => get_the_post_thumbnail_url($item, 'list-item'),
                    'author' => array(
                        'name' => get_the_author_meta('display_name', $item->post_author),
                        'link' => get_author_posts_url($item->post_author)
                    ),
                    'view_count' => view_count_display(foody_get_post_views($item->ID))
                );

                $foody_item = self::create($item);
                $default_template_args = array_merge($default_template_args, call_user_func($related_content_args['template_args_func'], $foody_item));


                return $default_template_args;

            }, $related_content);


            foody_get_template_part(
                get_template_directory() . '/template-parts/content-related-content.php',
                $template_args
            );
        }

    }

    private function get_related_content_by_categories_and_custom($post_type, $selector)
    {
        $posts = [];
        $related = get_field($selector, $this->id);
        if (!empty($related)) {
            $posts = $related;
        }

        $items_to_fetch = self::$MAX__RELATED_ITEMS - count($posts);


        if ($items_to_fetch < 0) {
            $items_to_fetch = 0;
        }

        if ($items_to_fetch > 0) {
            $posts_to_exclude = array_map(function ($post) {
                return $post->ID;
            }, $posts);

            $posts_to_exclude[] = $this->id;

            $categories = [
                $this->get_primary_category()
            ];
            if (!is_wp_error($categories)) {

                $query = new WP_Query([
                    'post_type' => $post_type,
                    'category__and' => $categories,
                    'posts_per_page' => $items_to_fetch,
                    'post_status' => 'publish',
                    'post__not_in' => $posts_to_exclude,
                    'orderby' => 'rand',
                ]);

                $posts = array_merge($posts, $query->get_posts());
            }
        }

        return $posts;
    }

    public function the_mobile_sidebar_content()
    {
        $this->the_sidebar_related_content('', 'מתכונים נוספים');
    }

    public abstract function the_details();

    function the_content($page)
    {
        $type = get_post_type();


        $template = "/template-parts/single-$type.php";

        foody_get_template_part(
            get_template_directory() . $template,
            [
                'page' => $page
            ]
        );
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Factory method for creating
     * Foody_Post objects based
     * on the WP_Post::post_type field.
     *
     * @param stdClass|WP_Post $post
     * @return Foody_Post
     */
    public static function create($post)
    {
        if ($post instanceof stdClass) {
            $post = new WP_Post($post);
        }

        $type = $post->post_type;

        switch ($type) {
            case 'foody_recipe':
                $foody_post = new Foody_Recipe($post);
                break;
            case 'foody_playlist':
                $foody_post = new Foody_Playlist($post);
                break;
            default:
                $foody_post = new Foody_Article($post);
                break;
        }

        return $foody_post;
    }

    public function get_author_link()
    {
        return get_author_posts_url($this->post->post_author);
    }

    public function has_rating()
    {
        return false;
    }

    public function js_vars()
    {
        return [
            'ID' => $this->post->ID,
            'type' => $this->post->post_type,
            'title' => $this->title
        ];
    }

    public function featured_content_classes()
    {
        return [];
    }


    public function get_label()
    {
        $label = get_field('recipe_label', $this->post->ID);

        if (empty($label)) {
            $label = '';
        }

        return $label;
    }

}