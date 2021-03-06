<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/23/18
 * Time: 4:42 PM
 */
class Foody_Footer
{

    public $footer_pages;
    public $footer_links;

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
        if (empty($this->footer_pages)) {
            $this->footer_pages = [];
        }

        if (empty($this->footer_links)) {
            $this->footer_links = [];
        }


        $chunk_size = 0;
        $links_count = count($this->footer_links);
        if ($links_count > 0) {
            $chunk_size = ceil($links_count / self::$MAX_MENUS);
        }

        // FEATURE allow control over the separate cols in the footer
        if ($chunk_size > 0 && !empty($this->footer_links)) {
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

            if (!empty($this->footer_links)) {
                foreach ($this->footer_links as $link_group) {
                    $this->display_menu($link_group, 'col');
                }
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


    public function newsletter($id)
    {
        $args['ID'] = $id;

        foody_get_template_part(get_template_directory() . '/template-parts/content-newsletter.php', $args);
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

    public function the_foody_israel($echo = false)
    {
        $foody = array(
            'title' => sprintf(__('Foody Israel') . ' %s', date('Y')),
            'url' => function_exists('foody_get_main_site_url') ? foody_get_main_site_url() : get_home_url(),
            'target' => '_blank'
        );

        if ($echo) {
            echo '<a href="' . $foody['url'] . '" target="' . $foody['target'] . '">' . $foody['title'] . '</a>';
        } else {
            return $foody;
        }
    }

    public function the_moveo($echo = true)
    {
        $moveo = file_get_contents(get_template_directory() . '/resources/images/moveo.svg');
        if ($echo) {
            echo '<a href="https://www.moveo.group/" target="_blank">' . $moveo . '</a>';
        }

        return $moveo;
    }

    public function display_pages_menu($menu_items)
    {

        ?>
        <ul class="menu col-4 row justify-content-between menu-pages">


            <?php

            $this->newsletter(get_option('foody_id_for_newsletter'));
            if (empty($menu_items)) {
                return;
            }
            $items = array_chunk($menu_items, count($menu_items) / 2);

            if (count($items) > 1) {

                if (get_theme_mod('foody_show_moveo_logo', true)) {
                    $items[sizeof($items)][] = $this->moveo();
                }

                if (get_current_blog_id() == 1 || (get_current_blog_id() != 1 && !get_option('foody_remove_foodys_link_footer', false))) {
                    $items[sizeof($items) + 1][] = $this->the_foody_israel();
                }
            }


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

    public function add_nagish_li_script()
    {

        $show_accessibility = get_theme_mod('foody_show_accessibility');
        // always show on main site
        if (!is_multisite() || is_main_site()) {
            $show_accessibility = true;
        }

        if ($show_accessibility) {

            ?>
            <!--   old in comment   -->
            <!-- <script async defer>
                setTimeout(() => {
                    (function (document, tag) {
                        var script = document.createElement(tag);
                        var element = document.getElementsByTagName('body')[0];
                        script.src = 'https://accessibeapp.com/api/v1/assets/js/accessibe.js';
                        script.async = true;
                        script.defer = true;
                        (typeof element === 'undefined' ? document.getElementsByTagName('html')[0] : element).appendChild(script);
                        script.onload = function () {
                            AccessiBe.init({
                                clientId: 1105,
                                clientKey: 'CTr7CLASmMyt02TnLnNs',
                                wlbl: 'Nagishly',
                                statementLink: '<?php echo get_permalink(get_page_by_title('?????????? ????????????'))?>',
                                feedbackLink: '',
                                showAllActions: false,
                                keyNavStrong: false,
                                hideMobile: true,
                                hideTrigger: true,
                                language: 'he',
                                focusInnerColor: 'var(--color__primary)',
                                focusOuterColor: '#ff7216',
                                leadColor: 'var(--color__primary)',
                                triggerColor: 'var(--color__primary)',
                                usefulLinks: {},
                            });
                        };
                    }(document, 'script'));
                });
            </script>-->
            <script>
                (function (document, tag) {
                    var script = document.createElement(tag);
                    var element = document.getElementsByTagName('body')[0];
                    script.src = 'https://acsbap.com/apps/app/assets/js/acsb.js';
                    script.async = true;
                    script.defer = true;
                    (typeof element === 'undefined' ? document.getElementsByTagName('html')[0] : element).appendChild(script);
                    script.onload = function () {
                        acsbJS.init({
                            statementLink: '',
                            feedbackLink: '',
                            footerHtml: '',
                            hideMobile: false,
                            hideTrigger: true,
                            language: 'he',
                            position: 'right',
                            leadColor: 'var(--color__primary)',
                            triggerColor: 'var(--color__primary)',
                            triggerRadius: '50%',
                            triggerPositionX: 'right',
                            triggerPositionY: 'bottom',
                            triggerIcon: 'default',
                            triggerSize: 'medium',
                            triggerOffsetX: 20,
                            triggerOffsetY: 20,
                            mobile: {
                                triggerSize: 'small',
                                triggerPositionX: 'left',
                                triggerPositionY: 'center',
                                triggerOffsetX: 0,
                                triggerOffsetY: 0,
                                triggerRadius: '0'
                            }
                        });
                    };
                }(document, 'script'));</script>
            <?php
        }

        // Custom accessibility widget
        $show_custom_accessibility = get_theme_mod('show_white_label_accessibility');
        $custom_accessibility_script = get_theme_mod('white_label_accessibility_script');
        $custom_accessibility_class = get_theme_mod('white_label_accessibility_class');

        if ($show_custom_accessibility && !empty($custom_accessibility_script)) {

            echo $custom_accessibility_script;

            if (!empty($custom_accessibility_class)) {

                ?>
                <style>
                    <?php echo $custom_accessibility_class ?>
                    {
                        visibility: hidden
                    ;
                    }
                </style>
                <?php
            }
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

    public function get_relevant_banner($banners_list)
    {
        $datetime = new DateTime(null, new DateTimeZone('Asia/Jerusalem'));
        $current_date_time = $datetime->format('Y-m-d H:i');
        $last_selected_date = '0-0-0 00:00';
        $response = [];

        foreach ($banners_list as $banner) {
            if (isset($banner['time_date'])) {
                if ($banner['time_date'] <= $current_date_time) {
                    if ($banner['time_date'] >= $last_selected_date) {
                        $response = $banner;
                        $last_selected_date = $banner['time_date'];
                    }
                }
            }
        }
        return $response;
    }
}