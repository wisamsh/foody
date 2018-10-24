<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/23/18
 * Time: 4:42 PM
 */
class Foody_Footer
{
    // TODO remove debug
    private $debug = false;

    private static $MAX_MENUS = 10;

    /**
     * Foody_Footer constructor.
     */
    public function __construct()
    {
        if (wp_is_mobile()) {
            self::$MAX_MENUS = 2;
        }
    }


    public function menu()
    {
        $footer_pages = wp_get_nav_menu_items('footer-pages');

        $footer_pages[] = array(
            'title' => sprintf(__('Foody Israel') . ' %s', date('Y'))
        );

        $footer_links = wp_get_nav_menu_items('footer-links');

        if ($this->debug) {
            $footer_links = array_merge($footer_links, $this->dummy_links(40));
        }


        $chunk_size = 0;
        $links_count = count($footer_links);
        if ($links_count > 0) {
            $chunk_size = ceil($links_count / self::$MAX_MENUS);
        }

        // FEATURE allow control over the separate cols in the footer
        if ($chunk_size > 0) {
            $footer_links = array_chunk($footer_links, $chunk_size);
        }

        if (!wp_is_mobile()) {
            $this->display_menu($footer_pages);
        }


        while (count($footer_links) > self::$MAX_MENUS) {
            array_pop($footer_links);
        }

        foreach ($footer_links as $link_group) {

            $this->display_menu($link_group);
        }
    }


    private function display_menu($menu_items)
    {
        echo '<ul class="menu">';

        foreach ($menu_items as $link) {

            $url = is_object($link) ? $link->url : (isset($link['url']) ? $link['url'] : '');
            $title = is_object($link) ? $link->title : $link['title'];

            echo '<li class="menu-item"><a href="' . $url . '">' . $title . '</a></li>';
        }
        echo '</ul>';
    }


    private function dummy_links($num = 40)
    {
        $links = array();

        $dumdum = 'ךלג  שדךל שדךכ יךשדכ';

        for ($i = 0; $i < $num; $i++) {

            $title = esc_html(substr($dumdum, 0, rand(0, strlen($dumdum) - 1)));
            if (empty($title)) {
                $title = 'asfasf';
            }
            $links[] = array(
                'url' => '',
                'title' => $title
            );
        }

        return $links;
    }
}