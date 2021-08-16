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

    function our_id(){
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

    function image_or_video(){
        if (have_rows('video', $this->our_id())) {

                $video_url = get_sub_field('url',$this->our_id());

                if ($video_url && count($parts = explode('v=', $video_url)) > 1) {

                    $query = explode('&', $parts[1]);
                    $video_id = $query[0];

                    $video_element = '<div class="item"><span id="video" style="display: none;" data-video-id="' . $video_id . '"></span><div class="video-overlay"></div><div class="video-container no-print"></div></div>';
                    $video_image = get_sub_field('image');

                    return 'wow';
                }

        } else {
           return 'image';
        }

    }

    private function get_related_content_by_categories_and_custom($post_type, $selector, $args = array())
    {
        $posts = [];
        $related = get_field($selector, $this->our_id());
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

            $posts_to_exclude[] = $this->our_id();

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

        $primary = get_post_meta($this->our_id(), '_yoast_wpseo_primary_category', true);

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
        $similar_contents = get_field($content_group, $this->our_id());
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
        foody_get_template_part(get_template_directory() . '/template-parts/content-similar-content-listing.php', $args);
    }

}
