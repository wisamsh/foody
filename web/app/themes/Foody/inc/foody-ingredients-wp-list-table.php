<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Ingredient_List_Table extends WP_List_Table
{

    protected $ingredient_id = -1;
    protected $recipes_count = 0;

    /** Class constructor */
    public function __construct($id)
    {

        parent::__construct([
            'singular' => __('Recipe'), //singular name of the listed records
            'plural' => __('Recipes'), //plural name of the listed records
            'ajax' => false //should this table support ajax?

        ]);

        $this->ingredient_id = $id;
    }

    public function get_recipes()
    {
        global $wpdb;

        $query = "SELECT * FROM $wpdb->postmeta as postmeta 
JOIN $wpdb->posts as posts
where posts.ID = postmeta.post_id 
	AND meta_key like 'ingredients_ingredients_groups_%_ingredients_%_ingredient'
	AND meta_value = $this->ingredient_id
    AND post_status = 'publish'";

        $posts = $wpdb->get_results($query);

        foreach ($posts as $single_post) {
            $recipe_id = $single_post->ID;
            $recipe_link = '<a class="recipe-list-item" href="' . get_edit_post_link($recipe_id) . '">';
            $categories_array = (get_the_terms($recipe_id, 'category')) ? get_the_terms($recipe_id, 'category') : [];
            $tags_array = (get_the_tags($recipe_id)) ? get_the_tags($recipe_id) : [];

            $posts_list[$recipe_id] = array(
                'ID' => $recipe_id,
                'כותרת' => $recipe_link . $single_post->post_title . '</a>',
                'מאת' => get_user_by('ID', $single_post->post_author)->data->display_name,
                'קטגוריות' => implode(",", array_map(function ($cat) {
                    return $cat->name;
                }, $categories_array)),
                'תגיות' => implode(",", array_map(function ($tag) {
                    return $tag->name;
                }, $tags_array)),
                'תאריך' => $single_post->post_date,
            );
        }

        $this->recipes_count = count($posts_list);
        return $posts_list;
    }

    public function record_count()
    {
        return $this->recipes_count;
    }

    /** Text displayed when no Taxis data is available */
    public function no_items()
    {
        _e('No recipes available.');
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'כותרת':
            case 'מאת':
            case 'קטגוריות':
            case 'תגיות':
            case 'תאריך':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'כותרת' => __('כותרת'),
            'מאת' => __('מאת'),
            'קטגוריות' => __('קטגוריות'),
            'תגיות' => __('תגיות'),
            'תאריך' => __('תאריך'),
        ];

        return $columns;
    }

    function usort_reorder($a, $b)
    {
        // If no sort, default to title
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'כותרת';
        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'כותרת' => array('כותרת', true),
            'מאת' => array('מאת', false),
            'קטגוריות' => array('קטגוריות', false),
            'תגיות' => array('תגיות', false),
            'תאריך' => array('תאריך', false),
        );

        return $sortable_columns;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {

        $columns = $this->get_columns();

        $hidden = array();

        $sortable = $this->get_sortable_columns();

        // $this->_column_headers = array($columns, $hidden, $sortable);
        $recipes_list = $this->get_recipes();
        usort($recipes_list, array(&$this, 'usort_reorder'));

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = $this->record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);

        $recipes_list = array_slice($recipes_list, (($current_page - 1) * $per_page), $per_page);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $recipes_list;
    }

    public function process_bulk_action()
    {
    }
}


