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

        return get_field('answer_banner', $this->get_id())['url'];
    }

    function image_or_video(){

        if ($this->our_post() != null) {
            if (have_rows('video', $this->get_id())) {
                while (have_rows('video', $this->get_id())): the_row();
                    $video_url = get_sub_field('url');

                    if ($video_url && count($parts = explode('v=', $video_url)) > 1) {

                        $query = explode('&', $parts[1]);
                        $video_id = $query[0];
                        $args = array(
                            'id' => $video_id,
                            'post_id' => $this->id
                        );
                        foody_get_template_part(get_template_directory() . '/template-parts/content-recipe-video.php', $args);
                    } else {
                        echo get_the_post_thumbnail($this->id, 'foody-main');
                        $feed_area_id = !empty($this->id) ? get_field('recipe_channel', $this->id) : get_field('recipe_channel');
                    }

                endwhile;
            } else {
                echo get_the_post_thumbnail($this->get_id(), 'foody-main');
            }
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
                    'orderby' => 'rand',
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



}
