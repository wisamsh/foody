<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:41 PM
 */
class RecipesGrid
{

    const NONE = 1000;

    /**
     * RecipesGrid constructor.
     */
    public function __construct()
    {

    }


    public function draw($post, $col_num, $col_num_mobile = 12)
    {
        if ($col_num == 0) {
            $col_num = self::NONE;
        } elseif (12 % $col_num != 0) {
            throw new Error("RecipesGrid:  invalid col_num $col_num");
        }

        $class = '';
        if ($col_num != self::NONE) {
            $class = 'col-sm-' . 12 / $col_num;

            $mobile_class = ' col-' . $col_num_mobile;

            $class .= $mobile_class;
        }


        $container_start = '<div class="' . $class . ' recipe-item-container">';
        $container_end = '</div>';

        echo $container_start;

        foody_get_template_part(get_template_directory() . '/template-parts/content-recipe-list-item.php', array('post' => $post));

        echo $container_end;

    }

    public function grid_debug($items_count, $col_num)
    {

        for ($i = 0; $i < $items_count; $i++) {
            $this->draw(new Foody_Recipe(), $col_num);
        }
    }

}