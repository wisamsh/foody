<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/24/19
 * Time: 7:00 PM
 */

class Foody_FiltersRuleMapping
{
    private $table_name;
    private static $instance;

    /**
     * Foody_FiltersRuleMapping constructor.
     */
    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'foody_filters_cache';
    }

    /**
     * @return Foody_FiltersRuleMapping
     * singleton method
     */
    public static function get_instance() {
        if ( self::$instance == null ) {
            self::$instance = new Foody_FiltersRuleMapping();
        }

        return self::$instance;
    }

    function addMany($values)
    {
        global $wpdb;
        $insert_query = "INSERT INTO {$this->table_name} (`filter_id`, `post_id`) VALUES %s";

        $insert_values = array_map(function ($value) {
            return sprintf("(%s, %s) ", $value['filter_id'], $value['post_id']);
        }, $values);

        $insert_query = sprintf($insert_query, implode(',', $insert_values));


        $result = $wpdb->query($insert_query);

        if ($result === false) {
            Foody_WhiteLabelLogger::error("Error inserting to foody_commercial_rule_mapping: $wpdb->last_error", $wpdb->last_result);
        }

        return $result;
    }


    public function cleanTable()
    {
        global $wpdb;

        $clear_table_query = "TRUNCATE TABLE {$this->table_name}";
        $wpdb->get_results($clear_table_query);
    }

    public function getRules(){
        global $wpdb;
        $rules_list = [];

        $get_rules_query = "SELECT filter_id, post_id FROM {$this->table_name}";
        $results = $wpdb->get_results($get_rules_query);

        foreach ($results as $result){
            if(isset($result->filter_id) && isset($result->post_id)){
                if(!isset($rules_list[$result->filter_id])){
                    $rules_list[$result->filter_id] = [];
                }
                array_push($rules_list[$result->filter_id], $result->post_id);
            }
        }

        return !empty($rules_list) ? $rules_list : false;
    }

}

function get_posts_for_filters($ID)
{
    $option_filter = get_field('filters_list', $ID);
    $args['types'] = [];
    $values_for_table = [];

    foreach ($option_filter as $list) {
        if (is_array($list)) {
            $filter = SidebarFilter::parse_search_args($list);
            foreach ($filter as $filter_type) {
                array_push($args['types'], $filter_type);
            }
        }
    }

    $context = 'purchase_buttons';
    $context_args = null;

    // Creating WP_args for search query parameter.
    $wp_args = [];
    $foody_query = Foody_Query::get_instance();
    $query_args = $foody_query->get_query('purchase_buttons', $context_args);
    $wp_args = array_merge($wp_args, $query_args);

    // set to return all the posts results
    if (isset($wp_args['posts_per_page'])) {
        $wp_args['posts_per_page'] = '-1';
    }

    // set return values to be posts ids
    $wp_args['fields'] = 'ids';

    // purchase_buttons will invoke purchase_buttons ffn
    // in class Foody_Query
    $foody_search = new Foody_Search($context, $context_args);

    $results = $foody_search->query($args, $wp_args);

    if(isset($results['found']) && $results['found'] > 0 &&  isset($results['posts']) && is_array($results['posts'])) {
        foreach ($results['posts'] as $post_id){
            array_push($values_for_table, ['filter_id' => $ID, 'post_id' => $post_id]);
        }
    }

    return $values_for_table;
}

function update_filters_cache()
{
    global $wpdb;
    $filters_rules_mapping = Foody_FiltersRuleMapping::get_instance();
    $filters_rules_list = [];

    $filters_rules_mapping->cleanTable();

    $query = "SELECT ID FROM {$wpdb->posts} WHERE post_type='foody_filter' AND post_status='publish'";
    $filters_id = [];
    $results = $wpdb->get_results($query);
    if (is_array($results)) {
        $filters_id = array_map(function ($result) {
            if (isset($result->ID) && !empty($result->ID)) {
                return $result->ID;
            }
        }, $results);
    }

    foreach ($filters_id as $id) {
        $filters_rules_list =  array_merge($filters_rules_list, get_posts_for_filters($id));
    }

    if(!empty($filters_rules_list)){
        $filters_rules_mapping->addMany($filters_rules_list);
    }
}

add_action('foody_update_filters_cache_hook', 'update_filters_cache');
