<?php
class Custom_Menu_Structure {
    public $menu_location;
    public $menu_tree;

    // Constructor to initialize the menu display
    public function __construct($menu_location) {
        $this->menu_location = $menu_location;
        $this->menu_tree = $this->get_menu_tree();
    }

    // Get menu tree structure
    public function get_menu_tree() {
        $locations = get_nav_menu_locations(); // Get all registered menu locations

        if (isset($locations[$this->menu_location])) {
            $menu_id = $locations[$this->menu_location]; // Get the menu ID from the location
            $menu_items = wp_get_nav_menu_items($menu_id); // Get raw menu items

            // Build the menu tree structure
            return $this->build_menu_tree($menu_items);
        } else {
            return [];
        }
    }

    /**
     * Recursive function to build the menu tree
     * 
     * @param array $menu_items Raw menu items from wp_get_nav_menu_items()
     * @param int $parent_id Parent ID to build hierarchy (default is 0 for top-level items)
     * @return array Menu tree structure
     */
    private function build_menu_tree($menu_items, $parent_id = 0) {
        $tree = array();
        foreach ($menu_items as $menu_item) {
            if ($menu_item->menu_item_parent == $parent_id) {
                // Recursively get child items
                $children = $this->build_menu_tree($menu_items, $menu_item->ID);
                if (!empty($children)) {
                    $menu_item->children = $children;
                }
                $tree[] = $menu_item;
            }
        }
        return $tree;
    }

    /**
     * Display the menu tree recursively
     * 
     * @param array $menu_tree Nested menu structure
     * @param string $ul_class Custom class for the <ul> (optional)
     * @param string $nested_ul_class Custom class for nested <ul>
     */
    public function display_menu_tree($menu_tree = null, $ul_class = '', $nested_ul_class = 'nested-ul') {
        if (is_null($menu_tree)) {
            $menu_tree = $this->menu_tree; // Use the built tree if none provided
        }

        if (!empty($menu_tree)) {
            // Check if this is a nested <ul> (add $nested_ul_class for nested lists)
            $rtn = '<ul class="' . esc_attr($ul_class) . '"><div class="saparetor"></div>';

            foreach ($menu_tree as $menu_item) {
                $rtn .= '<li>';
                $rtn .= '<a href="' . esc_url($menu_item->url) . '">' . esc_html($menu_item->title) . '</a>';

                // Check if the item has children and display them
                if (isset($menu_item->children) && !empty($menu_item->children)) {
                    // Add custom class to the nested <ul>
                   // $rtn .= '<ul class="' . esc_attr($nested_ul_class) . '">';
                    $rtn .= $this->display_menu_tree($menu_item->children,  $nested_ul_class); // Recursive call
                   // $rtn .= '</ul>';
                }

                $rtn .= '</li>';
            }

            $rtn .= '</ul>';
        }
        return $rtn;
    }
}//END CLASS
?>
