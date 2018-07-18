<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/9/18
 * Time: 7:45 PM
 */
class Foody_Categories
{


    /**
     * Foody_Categories constructor.
     */
    public function __construct()
    {
    }


    public function display()
    {
        $cards_per_row = get_field('cards_per_row');
        if ($cards_per_row <= 0) {
            $cards_per_row = 4;
        }

        $container = '<div class="card-columns card-columns-4  gutter-10">';
        echo $container;

        if (have_rows('categories')) {
            while (have_rows('categories')): the_row();

                $sub_categories = array();

                if (have_rows('sub_categories')) {
                    while (have_rows('sub_categories')): the_row();
                        $sub_categories[] = array(
                            'title' => get_sub_field('title'),
                            'categories' => array_map(function ($cat_id) {
                                return array(
                                    'title' => get_cat_name($cat_id),
                                    'link' => get_category_link($cat_id)
                                );
                            }, get_sub_field('category'))
                        );
                    endwhile;
                }

                $main_category_id = get_sub_field('category');
                $main_title = get_sub_field('title');
                if(!$main_title){
                    $main_title = get_cat_name($main_category_id);
                }

                $args = array(
                    'id' => $main_category_id,
                    'categories' => $sub_categories,
                    'title' => $main_title,
                    'subtitle' => get_sub_field('subtitle'),
                    'cards_per_row' => $cards_per_row
                );

                foody_get_template_part(get_template_directory() . '/template-parts/content-top-level-category-card.php', $args);
                foody_get_template_part(get_template_directory() . '/template-parts/content-top-level-category-card.php', $args);
                foody_get_template_part(get_template_directory() . '/template-parts/content-top-level-category-card.php', $args);
                foody_get_template_part(get_template_directory() . '/template-parts/content-top-level-category-card.php', $args);
                foody_get_template_part(get_template_directory() . '/template-parts/content-top-level-category-card.php', $args);


            endwhile;
        }

        $container_end = '</div>';
        echo $container_end;

    }

}