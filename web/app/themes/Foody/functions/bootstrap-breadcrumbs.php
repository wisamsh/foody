<?php
/**
 * Display the breadcrumb
 */
function bootstrap_breadcrumb($parent_id = null)
{

//    bcn_display($return = false, $linked = true, $reverse = false);
//    return;

    wp_reset_query();
    global $post;

    if (!is_front_page() && !is_home()) {
        //open up breadcrumbs list
        echo '<ol class="breadcrumb">';

        //display home link
        echo '<li><a href="';
        echo get_option('home');
        echo '">';
        echo get_the_title(get_option('page_on_front'));
        echo "</a></li>";


        if (is_single() && !is_category() && !is_page()) {

            $category = get_the_category();
            $category_name_and_link = get_primary_category($category);
            $category_id = $category[0]->ID;
            $category_name = $category[0]->cat_name;
            if ($category_name_and_link != null){
                $category_id = $category_name_and_link['id'];
                $category_name = $category_name_and_link['name'];
            }

            $subCategory = new Foody_Category($category_id,$category);
            $subCategoryParentId = $subCategory->parent($category_name);
            echo '<li><a href="' . get_permalink($subCategoryParentId) . '">' . get_the_title($subCategoryParentId) . '</a></li>';
            echo '<li class="active"><a href="' . get_category_link($category_id) . '">' . $category_name . '</a></li>';
        }
        if (is_category()) {
            if ($parent_id) {
                echo '<li><a href="' . get_permalink($parent_id) . '">' . get_the_title($parent_id) . '</a></li>';
                echo '<li class="active">' . single_cat_title(null, false) . '</li>';

            }

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
            echo '<li class="active">' . __('Search', 'WordPress') . '</li>';
        } elseif (is_404()) {
            echo '<li class="active">404</li>';
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