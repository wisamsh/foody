<?php
/*
 Template Name: Sitemap Page
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/22/19
 * Time: 4:13 PM
 */
//echo do_shortcode('[simple-sitemap-group type="foody_recipe" render"tab"]');
//$types = [
//    'post',
//    'foody_recipe',
//    'foody_channel',
//];
//
//foreach ($types as $type) {
//    echo do_shortcode("[simple-sitemap-group type='$type' visibility='public' hide-empty='1' ]");
//}

?>
<!--<section class="sitemap">-->
<!---->
<!---->
<!--    <h2 id="authors">-->
<!--        --><?php //echo __('יוצרים') ?>
<!--    </h2>-->
<!---->
<!--    <ul>-->
<!--        --><?php //wp_list_authors(array('exclude_admin' => true)); ?>
<!--    </ul>-->
<!---->
<!---->
<!--    <h2 id="pages">-->
<!--        --><?php //echo __('עמודים') ?>
<!--    </h2>-->
<!---->
<!---->
<!--    <ul>-->
<!--        --><?php //// Add pages you'd like to exclude in the exclude here
//        wp_list_pages(array('exclude' => '',
//                'title_li' => '',
//            )
//        );
//        ?>
<!--    </ul>-->
<!---->
<!---->
<!--    <h2 id="posts">-->
<!--        --><?php //echo __('כתבות') ?>
<!--    </h2>-->
<!---->
<!---->
<!--    <ul>-->
<!--        --><?php
//        // Add categories you'd like to exclude in the exclude here
//        $cats = get_categories('exclude=');
//        foreach ($cats as $cat) {
//            echo "<li><h3><a href='" . get_term_link($cat->term_id) . "'>" . $cat->cat_name . "</a></h3>";
//            echo "<ul>";
//            query_posts('posts_per_page=-1&cat=' . $cat->cat_ID);
//            while (have_posts()) {
//                the_post();
//                $category = get_the_category();
//                // Only display a post link once, even if it's in multiple categories
//                if ($category[0]->cat_ID == $cat->cat_ID) {
//                    echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
//                }
//            }
//            echo "</ul>";
//            echo "</li>";
//        }
//        ?>
<!--    </ul>-->
<!---->
<!--    --><?php
//    $post_types = get_post_types(array('public' => true));
//    $categories = get_categories(array(
//        'child_of' => 0,
//        'orderby' => 'name',
//        'order' => 'ASC',
//        'hide_empty' => 0,
//        'taxonomy' => 'category', //change this to any taxonomy
//    ));
//
//    $hidden_post_types = array(
//        'post',
//        'page',
//        'attachment',
//        'foody_ingredient',
//        'foody_accessory',
//        'foody_technique',
//        'footab_sites',
//        'footab_rsss',
//        'footab_rsssposts'
//    );
//
//    foreach ($post_types as $post_type) {
//
//        if (in_array($post_type, $hidden_post_types))
//            continue;
//
//        $pt = get_post_type_object($post_type);
//
//
//        echo '<h2>' . $pt->labels->name . '</h2>';
//        categories_post_type_display($post_type, $categories);
////        echo '<ul>';
////        query_posts('post_type=' . $post_type . '&posts_per_page=-1&post_status=publish');
////        while (have_posts()) {
////            the_post();
////            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
////        }
////
////        echo '</ul>';
//    }
//
//    ?>
<!---->
<!--</section>-->
<!---->
<!--<style>-->
<!--    .sitemap ul {-->
<!--        padding-right: 20px;-->
<!--        list-style-type: none;-->
<!--    }-->
<!---->
<!--    .sitemap a:visited, .sitemap a {-->
<!--        color: #4a4a4a-->
<!--    }-->
<!---->
<!--    .sitemap h3 {-->
<!--        font-size: 17px;-->
<!--        font-weight: normal;-->
<!--    }-->
<!--</style>-->

<?php
function categories_post_type_display($post_type, $categories)
{

    foreach ($categories as $tax) :
        // List posts by the terms for a custom taxonomy of any post type
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $tax->slug
                )
            )
        );
        if (get_posts($args)) :
            ?>
            <h2><?php echo $tax->name; ?></h2>
            <ul>
                <?php
                $posts = get_posts($args);
                foreach ($posts as $p) :

                    ?>
                    <li><a href="<?php echo get_permalink($p); ?>"><?php echo $p->post_title; ?></a></li>
                <?php endforeach; ?>
                <?php
                $sub_categories = get_categories("hide_empty=0&child_of={$tax->term_id}");
                if (!empty($sub_categories)) {
                    categories_post_type_display($post_type, $sub_categories);
                }
                ?>

            </ul>
        <?php
        endif;
    endforeach;
}

?>

