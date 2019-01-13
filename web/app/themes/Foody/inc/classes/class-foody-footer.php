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
    public $footer_pages;
    public $footer_links;
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

        $this->footer_pages = wp_get_nav_menu_items('footer-pages');
        $this->footer_links = wp_get_nav_menu_items('footer-links');
    }


    public function menu()
    {


        if ($this->debug) {
            $this->footer_links = array_merge($this->ooter_links, $this->dummy_links(40));
        }


        $chunk_size = 0;
        $links_count = count($this->footer_links);
        if ($links_count > 0) {
            $chunk_size = ceil($links_count / self::$MAX_MENUS);
        }

        // FEATURE allow control over the separate cols in the footer
        if ($chunk_size > 0) {
            $this->footer_links = array_chunk($this->footer_links, $chunk_size);
        }


        ?>

        <section class="row">

            <?php

            if (!wp_is_mobile()) {
                $this->display_pages_menu($this->footer_pages);
            }


            while (count($this->footer_links) > self::$MAX_MENUS) {
                array_pop($footer_links);
            }


            foreach ($this->footer_links as $link_group) {


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


    public function newsletter()
    {

        ?>

        <h4 class="newsletter-title">
            <?php echo __('אל תפספסו את המתכונים החמים!', 'foody'); ?>
        </h4>

        <section class="newsletter">
            <form class="row justify-content-between" method="post">
                <div class="input-container col-9 col-lg-7">
                    <input type="email" placeholder="<?php echo __('הכנס כתובת מייל', 'foody') ?>">

                </div>
                <button type="submit" class="col-3 col-lg-3 offset-lg-1">
                    <?php echo __('הרשמה', 'foody') ?>
                </button>
            </form>
        </section>

        <?php
    }

    public function moveo()
    {
        $moveo = $this->the_moveo(false);

        return array(
            'title' => $moveo,
            'url' => 'https://moveo.group',
            'target' => '_blank',
            'classes' => 'moveo'
        );
    }


    public function the_moveo($echo = true){
        $moveo = file_get_contents(get_template_directory() . '/resources/images/moveo.svg');
        if ($echo){
            echo $moveo;
        }

        return $moveo;
    }

    public function display_pages_menu($menu_items)
    {

        ?>
        <ul class="menu col-4 row justify-content-between menu-pages">


            <?php

            $this->newsletter();

            $items = array_chunk($menu_items, count($menu_items) / 2);


            $items[1][] = array(
                'title' => sprintf(__('Foody Israel') . ' %s', date('Y'))
            );

            $items[0][] = $this->moveo();


            foreach ($items as $item) {
                echo '<ul class="menu pages-menu col-6">';

                $this->display_menu_items($item);

                echo '</ul>';
            }
            ?>
        </ul>

        <?php
    }

    public function display_menu_items($menu_items)
    {
        foreach ($menu_items as $link) {

            $url = is_object($link) ? $link->url : (isset($link['url']) ? $link['url'] : '');
            $title = is_object($link) ? $link->title : $link['title'];
            $target = !is_object($link) && isset($link['target']) ? $link['target'] : '';
            $classes = !is_object($link) && isset($link['classes']) ? $link['classes'] : '';

            $this->display_menu_item($url, $title, $target, $classes);
        }

    }


    private function display_menu_item($url, $title, $target = '', $classes = '')
    {
        ?>
        <li class="menu-item <?php echo $classes ?>">
            <a <?php if ($target): ?> target="<?php echo $target ?>" <?php endif; ?>
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