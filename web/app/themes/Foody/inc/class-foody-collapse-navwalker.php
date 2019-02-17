<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 12/10/18
 * Time: 6:13 PM
 */
class Foody_BootstrapCollapseNavwalker extends Walker_Nav_Menu
{
    public static $BS_MAX_DEPTH = 2;
    public static $BS_DROPDOWN_MANUAL_DEPTH = 1;

    /**
     * @since       1.0.0
     * @access      public
     * @var type    bool
     */
    private $dropdown = false;

    private $current_dropdown = '';

    /**
     * Starts the list before the elements are added.
     *
     * @since       1.0.0
     *
     * @see Walker::start_lvl()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of menu item. Used for padding.
     * @param stdClass $args An object of wp_nav_menu() arguments.
     */
    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        if ($depth >= Foody_BootstrapCollapseNavwalker::$BS_DROPDOWN_MANUAL_DEPTH) {

            $output .= $n . str_repeat($t, $depth) . '<ul class="dropdown-menu-innner" role="menu" id="' . $this->current_dropdown . '" >' . $n;

        } else {
            $this->dropdown = true;
            $output .= $n . str_repeat($t, $depth) . $this->get_collapse_start($this->current_dropdown, '') . $n;
        }
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @since       1.0.0
     *
     * @see Walker::end_lvl()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of menu item. Used for padding.
     * @param stdClass $args An object of wp_nav_menu() arguments.
     */
    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        if ($depth >= Foody_BootstrapCollapseNavwalker::$BS_DROPDOWN_MANUAL_DEPTH) {
            $output .= $n . str_repeat($t, $depth) . '</ul>' . $n;
        } else {
            $this->dropdown = false;
            $output .= $n . str_repeat($t, $depth) . $this->get_end_collapse() . $n;
        }
    }

    /**
     * Starts the element output.
     *
     * @since 1.0.0
     *
     * @see Walker::start_el()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param WP_Post $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param stdClass $args An object of wp_nav_menu() arguments.
     * @param int $id Current item ID.
     */
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        $indent = str_repeat($t, $depth);

        if (0 === strcasecmp($item->attr_title, 'divider') && $this->dropdown) {
            $output .= $indent . '<div class="dropdown-divider"></div>' . $n;
            return;
        } elseif (0 === strcasecmp($item->title, 'divider') && $this->dropdown) {
            $output .= $indent . '<div class="dropdown-divider"></div>' . $n;
            return;
        }

        $classes = empty($item->classes) ? array() : (array)$item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $classes[] = 'nav-item';

        if ($args->walker->has_children) {
            $classes[] = 'dropdown';
            $classes[] = 'parent';
        }

        if (0 < $depth) {
            $classes[] = 'dropdown-menu';
            $classes[] = 'parent';
        }

        /**
         * Filters the arguments for a single nav menu item.
         *
         * @since 1.2.0
         *
         * @param stdClass $args An object of wp_nav_menu() arguments.
         * @param WP_Post $item Menu item data object.
         * @param int $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

        /**
         * Filters the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post $item The current menu item.
         * @param stdClass $args An object of wp_nav_menu() arguments.
         * @param int $depth Depth of menu item. Used for padding.
         */
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post $item The current menu item.
         * @param stdClass $args An object of wp_nav_menu() arguments.
         * @param int $depth Depth of menu item. Used for padding.
         */
        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        if (!$this->dropdown) {
            $output .= $indent . '<li' . $id . $class_names . '>' . $n . $indent . $t;
        }

        $item_output = $args->before;

        /** @noinspection PhpUnusedLocalVariableInspection */
        /**
         * The link(s) the will be rendered
         * into the item.
         * Concatenated with $item_output.
         * currently this var is always set so
         * this declaration is redundant, but I
         * keep it here for readability purposes.
         * @var string $item_links
         */
        $item_links = $this->build_menu_item_link($item, $args, $depth);


        $item_output .= $item_links;
        $item_output .= $args->after;

        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string $item_output The menu item's starting HTML output.
         * @param WP_Post $item Menu item data object.
         * @param int $depth Depth of menu item. Used for padding.
         * @param stdClass $args An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }


    private function build_menu_item_link($item, $args, $depth)
    {
        $item_output = '';
        $atts = array();
        $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
        $atts['href'] = !empty($item->url) ? $item->url : '';

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         * @type string $title Title attribute.
         * @type string $target Target attribute.
         * @type string $rel The rel attribute.
         * @type string $href The href attribute.
         * }
         * @param WP_Post $item The current menu item.
         * @param stdClass $args An object of wp_nav_menu() arguments.
         * @param int $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        if ($args->walker->has_children) {
//                $atts['data-toggle'] = 'dropdown';
//                $atts['aria-haspopup'] = 'true';
//                $atts['aria-expanded'] = 'false';
        }

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters('the_title', $item->title, $item->ID);

        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string $title The menu item's title.
         * @param WP_Post $item The current menu item.
         * @param stdClass $args An object of wp_nav_menu() arguments.
         * @param int $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_classes = array('nav-link');

        if (isset($item->link_classes)) {
            $item_classes = array_merge($item_classes, $item->link_classes);
        }

        if ($args->walker->has_children) {
//            $item_classes[] = 'dropdown-toggle';
        }

        // menu depth > 0,
        // meaning a dropdown should
        // be rendered
        if (0 < $depth) {
//                $item_classes = array_diff($item_classes, ['nav-link']);
            $item_classes[] = 'dropdown-item';
        }


        if ($args->walker->has_children) {
            $item_output .= '<div class="toggle-wrap">';
        }

        $item_output .= '<a class="' . implode(' ', $item_classes) . '" ' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        if ($args->walker->has_children) {
            $this->current_dropdown = uniqid();
            $item_output .= '<a href="#' . $this->current_dropdown . '" data-toggle="collapse" class="d-inline d-lg-none sub-menu-toggle collapsed" aria-expanded="false" aria-controls="#' . $this->current_dropdown . '">';
            $item_output .= '<i class="icon-side-arrow"></i>';
            $item_output .= '</a>';
        }
        $item_output .= '</a>';

        if ($args->walker->has_children) {
            $item_output .= '</div>';
        }

        return $item_output;
    }

    /**
     * Ends the element output, if needed.
     *
     * @since 1.0.0
     *
     * @see Walker::end_el()
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param WP_Post $item Page data object. Not used.
     * @param int $depth Depth of page. Not Used.
     * @param stdClass $args An object of wp_nav_menu() arguments.
     */
    public function end_el(&$output, $item, $depth = 0, $args = array())
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }

        $output .= $this->dropdown ? '' : str_repeat($t, $depth) . '</li>' . $n;
    }

    /**
     * Menu Fallback
     *
     * @since 1.0.0
     *
     * @param array $args passed from the wp_nav_menu function.
     */
    public static function fallback($args)
    {
        if (current_user_can('edit_theme_options')) {

            $defaults = array(
                'container' => 'div',
                'container_id' => false,
                'container_class' => false,
                'menu_class' => 'menu',
                'menu_id' => false,
            );
            $args = wp_parse_args($args, $defaults);
            if (!empty($args['container'])) {
                echo sprintf('<%s id="%s" class="%s">', $args['container'], $args['container_id'], $args['container_class']);
            }
            echo sprintf('<ul id="%s" class="%s">', $args['container_id'], $args['container_class']) .
                '<li class="nav-item">' .
                '<a href="' . admin_url('nav-menus.php') . '" class="nav-link">' . __('Add a menu') . '</a>' .
                '</li></ul>';
            if (!empty($args['container'])) {
                echo sprintf('</%s>', $args['container']);
            }
        }
    }


    function get_collapse_start($id, $title, $title_classes = '', $collapse_classes = '')
    {
        return "
        <div id=\"accordion-" . $id . "\" role=\"tablist\" class=\"foody-accordion\">
            <div class=\"foody-accordion-content\">
                <div class=\"foody-accordion-title\" role=\"tab\" id=\"heading-" . $id . "\">
                    <h5 class=\"mb-0\">
                        <a class=\" " . foody_el_classes($title_classes) . "\" data-toggle=\"collapse\" href=\"#" . $id . "\"
                       aria-expanded=\"false\"
                       aria-controls=\"" . $id . "\" role=\"button\">
                        " . $title . "
                        </a>
                        <i class=\"icon-side-arrow arrow\" data-toggle=\"collapse\" aria-controls=\"" . $id . "\"></i>
                    </h5>
                </div>
            <div id=\"" . $id . "\" class=\"collapse " . foody_el_classes($collapse_classes) . "\" role=\"tabpanel\"
                 aria-labelledby=\"heading-" . $id . "\">
                <div class=\"card-body\">";
    }

    function get_end_collapse()
    {
        return "</div>
            </div>
        </div>
    </div>";
    }

}