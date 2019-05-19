<?php

/**
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 4/21/19
 * Time: 8:35 PM
 */
class Foody_WhiteLabel_HomePage
{

    private $sidebar_id = 'foody-sidebar';
    private $mobile_sidebar_id = 'foody-sidebar-mobile';
    private $foody_search;
    private $blocks_drawer;

    /**
     * HomePage constructor.
     */
    public function __construct()
    {
        $this->foody_search = new Foody_Search('white_label_homepage');
        $this->blocks_drawer = new Foody_Blocks($this->foody_search);
    }

    public function cover_photo()
    {
        $type = get_field('top_cover_type');
        if (isset($type)) {
            if ($type == 'html') {
                if (wp_is_mobile()) {
                    the_field('top_cover_mobile_html');
                } else {
                    the_field('top_cover_desktop_html');
                }
            } else if ($type == 'image') {
                if (wp_is_mobile()) {
                    $image = get_field('top_cover_mobile_image');
                } else {
                    $image = get_field('top_cover_desktop_image');
                }
                $image_link = get_field('top_cover_link');
                $args = [
                    'image' => $image,
                    'link' => $image_link
                ];
                foody_get_template_part(get_template_directory() . '/template-parts/content-cover-image.php', $args);
            }
        }
    }

    public function the_brands()
    {
        $brands = get_field('brands');
        $brands_title = get_field('brands_title');
        if (!empty($brands)) {
            foody_get_template_part(get_template_directory() . '/template-parts/white-label/content-brands-slider.php', [
                'brands' => $brands,
                'brands_title' => $brands_title
            ]);
        }
    }

    public function sidebar()
    {
        echo "<aside class=\"sidebar col pl-0\">";

        $sidebar_name = $this->sidebar_id;
        if (wp_is_mobile() && !foody_is_tablet()) {
            $sidebar_name = $this->mobile_sidebar_id;
        }

        get_search_form();

        echo "<div class=\"sidebar-content\">";
        dynamic_sidebar($sidebar_name);
        dynamic_sidebar('foody-social');
        echo "</div></aside>";
    }

    public function blocks()
    {
        $blocks = get_field('white_label_blocks');

        if (!empty($blocks)) {

            foreach ($blocks as $block) {
                $type = $block['type'];

                if (!empty($type)) {
                    $this->blocks_drawer->validate_block($block);

                    $block_fn = "draw_{$type}_block";
                    if (method_exists($this->blocks_drawer, $block_fn)) {
                        $block_options = call_user_func([$this->blocks_drawer, $block_fn], $block);
                        if (!empty($block_options) && !empty($block_options['content'])) {
                            $this->blocks_drawer->wrap_block($block_options);
                        }
                    }
                }
            }
        }
    }

}