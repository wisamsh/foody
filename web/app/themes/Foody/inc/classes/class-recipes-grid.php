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

    private $is_in_loop = false;
    private $current_item = 0;

    private $items_for_type = [
        'Foody_Recipe' => ''
    ];


    /**
     * RecipesGrid constructor.
     */
    public function __construct()
    {

    }


    /**
     * @param $post Foody_Post
     * @param $col_num
     * @param int $col_num_mobile
     * @param bool $echo
     * @return string the item html
     * @throws Error if 12 is not divided by col_num
     */
    public function draw($post, $col_num, $col_num_mobile = 12, $echo = true, $type = 'recipe')
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


        $container_start = '<div class="' . $class . ' ' . $type . '-item-container" data-sort="' . $post->getTitle() . '" data-order="' . $this->current_item . '">';
        $container_end = '</div>';

        $item_content = $container_start;

        $item_content .= foody_get_template_part(
            get_template_directory() . '/template-parts/content-' . $type . '-list-item.php',
            [
                'post' => $post,
                'return' => true
            ]
        );

        $item_content .= $container_end;

        if ($echo) {
            echo $item_content;
        }
        return $item_content;

    }

    public function grid_debug($items_count, $col_num)
    {

        for ($i = 0; $i < $items_count; $i++) {
            $this->draw(new Foody_Recipe(), $col_num);
        }
    }

    /**
     * @param $posts Foody_Post[]
     * @param $cols
     * @param bool $echo
     * @param string $type
     * @return string
     */
    public function loop($posts, $cols, $echo = true, $type = null)
    {
        $items = '';
        $this->is_in_loop = true;
        $reset_type = false;
        foreach ($posts as $post) {

            if (is_null($type)) {
                $reset_type = true;
                $type = $post->post->post_type;
                $type = str_replace('foody_', '', $type);

                if ($type == 'post') {
                    $type = 'article';
                }
            }

            $items .= $this->draw($post, $cols, 12, $echo, $type);
            $this->current_item++;
            if ($reset_type) {
                $reset_type = false;
                $type = null;
            }

        }
        $this->current_item = 0;
        $this->is_in_loop = false;
        return $items;
    }

}