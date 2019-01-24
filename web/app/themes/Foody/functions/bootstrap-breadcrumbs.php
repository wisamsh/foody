<?php
/**
 * Display the breadcrumb
 */
function bootstrap_breadcrumb($parent_id = null, $path = null)
{

//    bcn_display($return = false, $linked = true, $reverse = false);
//    return;

    wp_reset_query();
    global $post;

    if ($path != null && !empty($path)) {

        echo '<ol class="breadcrumb">';
        //display home link
        home_item();


        for ($i = 0; $i < count($path); $i++) {

            $link = $path[$i];
            if ($i != count($path) - 1) {
                echo '<li><a href="' . $link['href'] . '">' . $link['title'] . '</a></li>';
            } else {
                echo '<li class="active">' . $link['title'] . '</li>';
            }
        }

        echo '</ol>';

    } elseif (!is_front_page() && !is_home()) {
        //open up breadcrumbs list
        echo '<ol class="breadcrumb">';

        //display home link
        home_item();

        if (is_single() && !is_category() && !is_page()) {

            $foody_post = Foody_Post::create(get_post());
            if(!empty($foody_post)){
                $cat= $foody_post->get_primary_category();
                if(!empty($cat))
                $category = new Foody_Category($cat);
                $term = $category->term;
                if(!is_wp_error($term)){
                    echo '<li><a href="' .get_term_link($term->term_id) . '">' . $term->name . '</a></li>';
                }
            }

            echo '<li class="active"><a>' . get_the_title() . '</a></li>';


        }
        if (is_category()) {

            $category = new Foody_Category(get_queried_object_id());
            $parents = $category->get_tree();

            array_pop($parents);

            ?>

            <li>
                <?php $categories_page = get_page_by_path('קטגוריות') ?>
                <a href="<?php echo get_permalink($categories_page) ?>">
                    <?php echo get_the_title($categories_page) ?>
                </a>
            </li>

            <?php

            foreach ($parents as $parent) {

                ?>

                <li>
                    <a href="<?php echo get_term_link($parent, 'category') ?>">
                        <?php echo get_term($parent)->name ?>
                    </a>
                </li>

                <?php
            }

            echo '<li class="active">' . single_cat_title(null, false) . '</li>';

//            echo '<li><a href="' . get_permalink($parent_id) . '">' . get_the_title($parent_id) . '</a></li>';
//            echo '<li class="active">' . single_cat_title(null, false) . '</li>';

        } elseif (is_page() && $post->post_parent) {
            $home = get_post(get_option('page_on_front'));
            for ($i = count($post->ancestors) - 1; $i >= 0; $i--) {
                if (($home->ID) != ($post->ancestors[$i])) {
                    echo '<li><a href="';
                    echo get_permalink($post->ancestors[$i]);
                    echo '">';
                    echo get_the_title($post->ancestors[$i]);
                    echo "</a></li>";
                }
            }
            echo '<li class="active">' . get_the_title($post->ID) . '</li>';
        } elseif (is_page()) {
            echo '<li class="active">' . get_the_title($post->ID) . '</li>';
        } elseif (is_search()) {
            echo '<li class="active">' . __('חיפוש', 'WordPress') . '</li>';
        } elseif (is_404()) {
            echo '<li class="active">404</li>';
        } elseif (is_tag()) {
            echo '<li class="active">'. get_term(get_queried_object_id())->name .'</li>';
        }
        echo '</ol>';
    }
}


function get_primary_category($category)
{
    $category_name_and_link = null;
    if (class_exists('WPSEO_Primary_Term')) {
        // Show the post's 'Primary' category, if this Yoast feature is available, & one is set
        $wpseo_primary_term = new WPSEO_Primary_Term('category', get_the_id());
        $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
        $term = get_term($wpseo_primary_term);
        if (is_wp_error($term)) {
            // Default to first category (not Yoast) if an error is returned
            $category_display = $category[0]->name;
            $category_id = $category[0]->term_id;
        } else {
            // Yoast Primary category
            $category_display = $term->name;
            $category_id = $term->term_id;
        }

        $category_name_and_link = array(
            'name' => $category_display,
            'id' => $category_id
        );
    }

    return $category_name_and_link;
}

function home_item()
{
    echo '<li><a href="';
    echo get_option('home');
    echo '">';
    echo get_the_title(get_option('page_on_front'));
    echo "</a></li>";
}
