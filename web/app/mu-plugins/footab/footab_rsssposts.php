<?php
// Register Custom Post Type
function Footab_Posts_Rss_CPT() {

    $labels = array(
        'name'                  => _x( 'FooTab Posts Rsss', 'Post Type General Name', 'footab' ),
        'singular_name'         => _x( 'FooTab Posts Rss', 'Post Type Singular Name', 'footab' ),
        'menu_name'             => __( 'FooTab Posts Rsss', 'footab' ),
        'name_admin_bar'        => __( 'FooTab Posts Rss', 'footab' ),
        'archives'              => __( 'FooTab Posts Rss Archives', 'footab' ),
        'attributes'            => __( 'FooTab Posts Rss Attributes', 'footab' ),
        'parent_item_colon'     => __( 'Parent FooTab Posts Rss:', 'footab' ),
        'all_items'             => __( 'All FooTab Posts Rsss', 'footab' ),
        'add_new_item'          => __( 'Add New FooTab Posts Rss', 'footab' ),
        'add_new'               => __( 'Add New', 'footab' ),
        'new_item'              => __( 'New FooTab Posts Rss', 'footab' ),
        'edit_item'             => __( 'Edit FooTab Posts Rss', 'footab' ),
        'update_item'           => __( 'Update FooTab Posts Rss', 'footab' ),
        'view_item'             => __( 'View FooTab Posts Rss', 'footab' ),
        'view_items'            => __( 'View FooTab Posts Rsss', 'footab' ),
        'search_items'          => __( 'Search FooTab Posts Rss', 'footab' ),
        'not_found'             => __( 'Not found', 'footab' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'footab' ),
        'featured_image'        => __( 'Featured Image', 'footab' ),
        'set_featured_image'    => __( 'Set featured image', 'footab' ),
        'remove_featured_image' => __( 'Remove featured image', 'footab' ),
        'use_featured_image'    => __( 'Use as featured image', 'footab' ),
        'insert_into_item'      => __( 'Insert into FooTab Posts Rss', 'footab' ),
        'uploaded_to_this_item' => __( 'Uploaded to this FooTab Posts Rss', 'footab' ),
        'items_list'            => __( 'FooTab Posts Rsss list', 'footab' ),
        'items_list_navigation' => __( 'FooTab Posts Rsss list navigation', 'footab' ),
        'filter_items_list'     => __( 'Filter FooTab Posts Rsss list', 'footab' ),
    );
    $args = array(
        'label'                 => __( 'FooTab Posts Rss', 'footab' ),
        'description'           => __( 'FooTab Posts Rss Description', 'footab' ),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'taxonomies'            => array( ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 10,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'footab_rsssposts', $args );

}
add_action( 'init', 'Footab_Posts_Rss_CPT', 0 );


add_action( 'cmb2_init', 'footabrssposts_add_metabox' );
function footabrssposts_add_metabox() {

    $prefix = '_footab_';

    $cmb = new_cmb2_box( array(
        'id'           => $prefix . 'footab_posts_rss_options',
        'title'        => __( 'Foody Taboola Posts Rss Options', 'footab' ),
        'object_types' => array( 'footab_rsssposts' ),
        'context'      => 'normal',
        'priority'     => 'high',
    ) );

    $group_field_id = $cmb->add_field( array(
        'id'          =>  $prefix . 'posts_list',
        'type'        => 'group',
        'description' => __( 'add feeds to the aggregator', 'cmb2' ),
        // 'repeatable'  => false, // use false if you want non-repeatable group
        'options'     => array(
            'group_title'   => __( 'Feed {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
            'add_button'    => __( 'Add Another feed', 'cmb2' ),
            'remove_button' => __( 'Remove feed', 'cmb2' ),
            'sortable'      => true, // beta
            // 'closed'     => true, // true to have the groups closed by default
        ),
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name' => 'Post Name',
        'id'   => $prefix . 'post_title',
        'type' => 'text',
        // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
    ) );

    $cmb->add_group_field( $group_field_id, array(
        'name' => 'Post Link',
        'id'   => $prefix . 'post_link',
        'type' => 'text',
        // 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
    ) );


}

add_action( 'save_post', 'footab_delete_rss_cache' );

function footab_delete_rss_cache($post_id) {
    $prefix = '_footab_';
    delete_post_meta($post_id, $prefix.'posts_list_cache');
}