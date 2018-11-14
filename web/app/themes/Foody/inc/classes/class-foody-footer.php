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

    private static $MAX_MENUS = 4;

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


        ?>

        <section class="row">

            <?php

            if (!wp_is_mobile()) {
                $this->display_pages_menu($footer_pages);
            }


            while (count($footer_links) > self::$MAX_MENUS) {
                array_pop($footer_links);
            }


            foreach ($footer_links as $link_group) {


                $this->display_menu($link_group, 'col');
            }

            ?>
        </section>
        <?php

    }


    private function display_menu($menu_items, $classes = '')
    {
        echo '<ul class="menu ' . $classes . '">';

        $this->display_menu_items($menu_items);

        echo '</ul>';
    }

    private function display_pages_menu($menu_items)
    {

        ?>
        <ul class="menu col-4 row justify-content-between menu-pages">
            <h4>
                <?php echo __('אל תפספסו את המתכונים החמים!', 'foody'); ?>
            </h4>

            <section class="newsletter">
                <form class="row justify-content-between" method="post">
                    <div class="input-container col-7">
                        <input type="email" placeholder="<?php echo __('הכנס כתובת מייל', 'foody') ?>">

                    </div>
                    <button type="submit" class="col-3 offset-1">
                        <?php echo __('הרשמה', 'foody') ?>
                    </button>
                </form>
            </section>

            <?php

            $items = array_chunk($menu_items, count($menu_items) / 2);


            $items[1][] = array(
                'title' => sprintf(__('Foody Israel') . ' %s', date('Y'))
            );

            $moveo = file_get_contents(get_template_directory() . '/resources/images/moveo.svg');

            $items[0][] = array(
                'title' => $moveo, // '<img src="' . $GLOBALS['images_dir'] . 'moveo.svg' . '">',
                'url' => 'https://moveo.group',
                'target' => '_blank'
            );


            foreach ($items as $item) {
                echo '<ul class="menu pages-menu col-6">';

                $this->display_menu_items($item);

                echo '</ul>';
            }
            ?>
        </ul>

        <?php
    }

    private function display_menu_items($menu_items)
    {
        foreach ($menu_items as $link) {

            $url = is_object($link) ? $link->url : (isset($link['url']) ? $link['url'] : '');
            $title = is_object($link) ? $link->title : $link['title'];
            $target = !is_object($link) && isset($link['target']) ? $link['target'] : '';

            $this->display_menu_item($url, $title, $target);
        }

    }


    private function display_menu_item($url, $title, $target = '')
    {
        ?>
        <li class="menu-item"><a <?php if ($target): ?> target="<?php echo $target ?>" <?php endif; ?>
                    href="<?php echo $url ?>"><?php echo $title ?></a></li>

        <?php
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