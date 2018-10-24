<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/1/18
 * Time: 1:17 PM
 */
class Foody_CategoriesAccordionWalker extends Walker_Category
{
    /**
     * What the class handles.
     *
     * @see Walker::$tree_type
     * @since 3.0.0
     * @var string
     */
    var $tree_type = array('post_type', 'taxonomy', 'custom');

    /**
     * Database fields to use.
     *
     * @see Walker::$db_fields
     * @since 3.0.0
     * @todo Decouple this.
     * @var array
     */
    var $db_fields = array('parent' => 'menu_item_parent', 'id' => 'db_id');

    function __construct()
    {

        /**
         * Current top level element ID
         *
         * @var integer
         */
        $this->element_id = 1;

        /**
         * Item parent ID
         *
         * @var string
         */
        $this->parent_id = false;

    }

    /**
     * Starts the list before the elements are added.
     *
     * @see Walker::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of menu item. Used for padding.
     * @param array $args An array of arguments. @see wp_nav_menu()
     */
    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);

        $id = (false !== $this->parent_id) ? $this->parent_id : '';

        $output .= "<div id=\"$id\" class=\"panel-collapse collapse\">";
        $output .= "\n$indent<ul class=\"nav nav-pills nav-stacked\">\n";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int $depth Depth of menu item. Used for padding.
     * @param array $args An array of arguments. @see wp_nav_menu()
     */
    function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</div>\n";
    }

    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param WP_Term $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param array $args An array of arguments. @see wp_nav_menu()
     * @param int $id Current item ID.
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = $value = '';


        $children = get_term_children($item->term_id,$item->taxonomy);

        if (!isset($item->classes)) {
            $item->classes = [];
        }

        if (!empty($children)) {
            $item->classes[] = 'menu-item-has-children';
        }

        $classes = empty($item->classes) ? array() : (array)$item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        /**
         * Filter the CSS class(es) applied to a menu item's <li>.
         *
         * @since 3.0.0
         *
         * @param array $classes The CSS classes that are applied to the menu item's <li>.
         * @param object $item The current menu item.
         * @param array $args An array of arguments. @see wp_nav_menu()
         */
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        /**
         * Filter the ID applied to a menu item's <li>.
         *
         * @since 3.0.1
         *
         * @param string The ID that is applied to the menu item's <li>.
         * @param object $item The current menu item.
         * @param array $args An array of arguments. @see wp_nav_menu()
         */
        $id = $item->term_id;
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        if (0 == $depth) {

            $output .= '<div class="panel panel-default">';
            $output .= '<div class="panel-heading">';

        } else {

            $output .= $indent . '<li' . $id . $value . $class_names . '>';

        }

        $atts = array();
        $atts['title'] = !empty($item->name) ? $item->name : '';


        if (0 == $depth && in_array('menu-item-has-children', $item->classes)) {

            $atts['href'] = '#' . sanitize_title($item->name);
            $atts['data-toggle'] = 'collapse';
            $atts['data-parent'] = '#cat-' . $item->parent;

            $this->parent_id = sanitize_title($item->name);

        } else {

            $atts['href'] = get_term_link($item->term_id);

        }

        /**
         * Filter the HTML attributes applied to a menu item's <a>.
         *
         * @since 3.6.0
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
         *
         * @type string $title The title attribute.
         * @type string $target The target attribute.
         * @type string $rel The rel attribute.
         * @type string $href The href attribute.
         * }
         * @param object $item The current menu item.
         * @param array $args An array of arguments. @see wp_nav_menu()
         */
//        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = '';
        $item_output .= '<a' . $attributes . '>';
        /** This filter is documented in wp-includes/post-template.php */
        $item_output .= apply_filters('the_title', $item->name, $item->term_id);
        $item_output .= '</a>';
//        $item_output .= $args->after;

        if (0 == $depth) {

            $item_output .= '</div>';

        }

        /**
         * Filter a menu item's starting output.
         *
         * The menu item's starting output only includes $args->before, the opening <a>,
         * the menu item's title, the closing </a>, and $args->after. Currently, there is
         * no filter for modifying the opening and closing <li> for a menu item.
         *
         * @since 3.0.0
         *
         * @param string $item_output The menu item's starting HTML output.
         * @param object $item Menu item data object.
         * @param int $depth Depth of menu item. Used for padding.
         * @param array $args An array of arguments. @see wp_nav_menu()
         */
        $output .= apply_filters('walker_category_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Page data object. Not used.
     * @param int $depth Depth of page. Not Used.
     * @param array $args An array of arguments. @see wp_nav_menu()
     */
    function end_el(&$output, $item, $depth = 0, $args = array())
    {

        $output .= (0 == $depth) ? "</div>\n" : "</li>\n";

    }


}