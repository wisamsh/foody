<?php
// Register Custom Post Type
function Footab_Sites_CPT() {

    $labels = array(
        'name'                  => _x( 'FooTab Sites', 'Post Type General Name', 'footab' ),
        'singular_name'         => _x( 'FooTab Site', 'Post Type Singular Name', 'footab' ),
        'menu_name'             => __( 'FooTab Sites', 'footab' ),
        'name_admin_bar'        => __( 'FooTab Site', 'footab' ),
        'archives'              => __( 'FooTab Site Archives', 'footab' ),
        'attributes'            => __( 'FooTab Site Attributes', 'footab' ),
        'parent_item_colon'     => __( 'Parent FooTab Site:', 'footab' ),
        'all_items'             => __( 'All FooTab Sites', 'footab' ),
        'add_new_item'          => __( 'Add New FooTab Site', 'footab' ),
        'add_new'               => __( 'Add New', 'footab' ),
        'new_item'              => __( 'New FooTab Site', 'footab' ),
        'edit_item'             => __( 'Edit FooTab Site', 'footab' ),
        'update_item'           => __( 'Update FooTab Site', 'footab' ),
        'view_item'             => __( 'View FooTab Site', 'footab' ),
        'view_items'            => __( 'View FooTab Sites', 'footab' ),
        'search_items'          => __( 'Search FooTab Site', 'footab' ),
        'not_found'             => __( 'Not found', 'footab' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'footab' ),
        'featured_image'        => __( 'Featured Image', 'footab' ),
        'set_featured_image'    => __( 'Set featured image', 'footab' ),
        'remove_featured_image' => __( 'Remove featured image', 'footab' ),
        'use_featured_image'    => __( 'Use as featured image', 'footab' ),
        'insert_into_item'      => __( 'Insert into FooTab Site', 'footab' ),
        'uploaded_to_this_item' => __( 'Uploaded to this FooTab Site', 'footab' ),
        'items_list'            => __( 'FooTab Sites list', 'footab' ),
        'items_list_navigation' => __( 'FooTab Sites list navigation', 'footab' ),
        'filter_items_list'     => __( 'Filter FooTab Sites list', 'footab' ),
    );
    $args = array(
        'label'                 => __( 'FooTab Site', 'footab' ),
        'description'           => __( 'FooTab Site Description', 'footab' ),
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
    register_post_type( 'footab_sites', $args );

}
add_action( 'init', 'Footab_Sites_CPT', 0 );


add_action( 'cmb2_init', 'footab_add_metabox' );
function footab_add_metabox() {

    $prefix = '_footab_';

    $cmb = new_cmb2_box( array(
        'id'           => $prefix . 'footab_options',
        'title'        => __( 'Foody Taboola Options', 'footab' ),
        'object_types' => array( 'footab_sites' ),
        'context'      => 'normal',
        'priority'     => 'high',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Show Taboola Module', 'footab' ),
        'id' => $prefix . 'show_module',
        'type' => 'checkbox',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Test Mode', 'footab' ),
        'id' => $prefix . 'test_mode',
        'type' => 'checkbox',
    ) );

    $cmb->add_field( array(
        'name' => __( 'the top image', 'footab' ),
        'id' => $prefix . 'top_image',
        'type' => 'file',
    ) );

    $cmb->add_field( array(
        'name' => __( 'the top text', 'footab' ),
        'id' => $prefix . 'top_text',
        'type' => 'text',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Taboola Code Head', 'footab' ),
        'id' => $prefix . 'taboola_code_head',
        'type' => 'textarea_code',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Taboola Code', 'footab' ),
        'id' => $prefix . 'taboola_code',
        'type' => 'textarea_code',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Taboola Code Footer', 'footab' ),
        'id' => $prefix . 'taboola_code_footer',
        'type' => 'textarea_code',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Analytics ID', 'footab' ),
        'id' => $prefix . 'analitics_id',
        'type' => 'text',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Border color', 'footab' ),
        'id' => $prefix . 'border_color',
        'type' => 'colorpicker',
    ) );

    $cmb->add_field( array(
        'name' => __( 'Border location', 'footab' ),
        'id' => $prefix . 'border_location',
        'type' => 'multicheck_inline',
        'options' => array(
            'up' => __( 'up', 'footab' ),
            'right' => __( 'right', 'footab' ),
            'down' => __( 'down', 'footab' ),
            'left' => __( 'left', 'footab' ),
        ),
    ) );

}

add_filter( 'single_template', 'override_single_template' );
function override_single_template( $single_template ){
    global $post;

    $file = dirname(__FILE__) .'/templates/single-'. $post->post_type .'.php';

    if( file_exists( $file ) ) $single_template = $file;

    return $single_template;
}