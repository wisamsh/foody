<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 6:23 PM
 */
class Foody_Answer extends Foody_Post
{

    public static $MAX__RELATED_ITEMS = 3;
    public $answer_has_video;

    public function __construct(WP_Post $post = null, $load_content = true)
    {
        parent::__construct($post,  $load_content);

        $this->post = $post;
    }

    function the_details()
    {
        echo '<section class="accessory-details-container">';
        bootstrap_breadcrumb();
        the_title( '<h1 class="title">', '</h1>' );
        echo '</section>';
    }

    function our_post(){
        global $post;
        return $post;
    }

    function get_id(){
        return $this->our_post()->ID;
    }

    function our_author(){
        $author = get_user_by('ID', $this->our_post()->post_author);
        return $author;
    }

    function our_description(){
        $description = $this->our_post()->post_content;
        return $description;
    }

    function has_banner(){

    }

    function our_banner() {
        if ( wp_is_mobile() ) {
            return get_field('answer_banner_mobile', $this->get_id())['url'];
        } else {
            return get_field('answer_banner', $this->get_id())['url'];
        }

    }

    function image_or_video(){

        if ($this->our_post() != null) {


                while (have_rows('video', $this->get_id())): the_row();
                    $video_url = get_sub_field('url');

                    if ($video_url && count($parts = explode('v=', $video_url)) > 1) {
                        $answer_has_video = true;
                        $query = explode('&', $parts[1]);
                        $video_id = $query[0];
                        $args = array(
                            'id' => $video_id,
                            'post_id' => $this->get_id()
                        );
                        foody_get_template_part(get_template_directory() . '/template-parts/content-recipe-video.php', $args);
                    } else {
                        $answer_has_video = false;
                        echo get_the_post_thumbnail($this->get_id(), 'foody-main');
                    }

                endwhile;
             
        }

    }

    private function get_related_content_by_categories_and_custom($post_type, $selector, $args = array())
    {
        $posts = [];
        $related = get_field($selector, $this->get_id());
        if (!empty($related) && is_array($related)) {
            $posts = $related;
        }

        $posts = array_filter($posts, function ($post) {
            return $post instanceof WP_Post && $post->post_status === 'publish';
        });

        $items_to_fetch = self::$MAX__RELATED_ITEMS - count($posts);


        if ($items_to_fetch < 0) {
            $items_to_fetch = 0;
        }

        if ($items_to_fetch > 0) {
            $posts_to_exclude = array_map(function ($post) {
                return $post->ID;
            }, $posts);

            $posts_to_exclude[] = $this->get_id();

            if (isset($args['exclude']) && is_array($args['exclude'])) {
                $posts_to_exclude = array_merge($posts_to_exclude, $args['exclude']);
            }

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
                    'orderby' => 'publish_date',
                ]);

                $posts = array_merge($posts, $query->get_posts());
            }
        }

        return $posts;
    }

    function related_questions(){

    }

    function related_recipes(){

    }

    public function get_primary_category($post_id = false)
    {
        if (!$post_id) {
            $post_id = $this->id;
        }

        $primary = get_post_meta($this->get_id(), '_yoast_wpseo_primary_category', true);

        if (!$primary || !is_numeric($primary) || intval($primary) <= 0) {
            /** @var WP_Term[] $categories */
            $categories = wp_get_post_categories($post_id, ['fields' => 'all_with_object_id']);
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

    public function get_similar_content($post_type, $content_group )
    {
        $similar_contents = get_field($content_group, $this->get_id());
        $not_in_random = [];
        array_push($not_in_random, $this->get_id());
        $counter = 0;
        if ( $post_type === 'foody_answer') {
            $title_of_section = isset($similar_contents['title']) && !empty($similar_contents['title']) ? $similar_contents['title'] : __('שאלות קשורות');
        }
        if ( $post_type === 'foody_recipe' ) {
            $title_of_section = isset($similar_contents['title']) && !empty($similar_contents['title']) ? $similar_contents['title'] : __('מתכונים נוספים שכדאי לכם לנסות');
        }

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
                'post_type' => $post_type,
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
                    'post_type' => $post_type,
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
        if ( $post_type === 'foody_recipe' ) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-similar-content-listing.php', $args);
        }
        if ( $post_type === 'foody_answer' ) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-similar-content-faq.php', $args);
        }
    }


    public function the_categories_answer()
    {
        echo '<h2 class="title">' . __('קטגוריות') . '</h2>';
        echo get_the_category_list('', '', $this->get_id());
    }

    public function the_accessories_answer()
    {
        $posts = [];
        $title = '';

        while (have_rows('accessories', $this->get_id())): the_row();
            $posts = get_sub_field('accessories');
            $title = get_sub_field('title');
        endwhile;


        $this->posts_bullets_answer($posts, $title);
    }

    public function the_techniques_answer($print = true)
    {
        $posts = [];
        $title = '';

        while (have_rows('techniques', $this->get_id())): the_row();
            $posts = get_sub_field('techniques');
            $title = get_sub_field('title');
        endwhile;

//		if ( empty( $posts ) ) {
//			$posts = foody_get_serialized_field_by_meta( 'techniques_techniques', $this->id );
//		}

        if ($print) {
            $this->posts_bullets_answer($posts, $title);
        } else {
            return $posts;
        }
    }

    public function the_tags_answer()
    {
        if ($this->has_tags_answer()) {
            $tags = wp_get_post_tags($this->get_id());
            foody_get_template_part(get_template_directory() . '/template-parts/content-tags.php', $tags);
        }
    }

    public function has_tags_answer()
    {
        $has_tags = false;
        $tags = wp_get_post_tags($this->get_id());
        if (!is_wp_error($tags) && count($tags) > 0) {
            $has_tags = true;
        }

        return $has_tags;
    }

    private function posts_bullets_answer($array, $title)
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


                $items[] = '<li><a href="' . get_permalink($post_id) . '">' . get_the_title($post_id) . '</a>' . '</li>';
            }

            echo sprintf($list, implode('', $items));
        }
    }


    public function the_sidebar_content($args = array())
    {
        $this->the_sidebar_related_content('מתכונים נוספים', 'פלייליסטים קשורים', $args = array('hide_playlists' => true));
        dynamic_sidebar('foody-social');
    }

    private function the_sidebar_related_content($recipes_title, $playlist_title, $args = array())
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

            $playlists = $this->get_related_content_by_categories_and_custom('foody_playlist', 'related_playlists', $args);

            $this->related_content($playlists_args, $playlists);
        }

        if (!isset($args['hide_recipes']) || $args['hide_recipes'] == false) {
            $recipes = $this->get_related_content_by_categories_and_custom('foody_recipe', 'related_recipes', $args);

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
        $related_content = posts_to_array($related_content_args['selector'], $this->get_id());
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



}
