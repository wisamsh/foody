<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:41 PM
 */
class RecipesGrid
{


    /**
     * RecipesGrid constructor.
     */
    public function __construct()
    {

    }


    public function draw($col_num, $col_num_mobile = 12)
    {
        if ($col_num == 0 || 12 % $col_num != 0) {
            throw new Error("RecipesGrid:  invalid col_num");
        }


        $class = 'col-sm-' . 12 / $col_num;

        $mobile_class = ' col-' . $col_num_mobile;

        $class .= $mobile_class;

        $container_start = '<div class="' . $class . '">';
        $container_end = '</div>';

        echo $container_start;

        get_template_part('template-parts/content', 'recipe-list-item');

        echo $container_end;

    }

}