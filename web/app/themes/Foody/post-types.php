<?php 
//FAQ==========================================================================
function my_custom_post_faq()
{
    $labels = array(
        'name'               => _x('שאלות תשובות', 'post type general name'),
        'singular_name'      => _x('שאלות תשובות', 'post type singular name'),
        'add_new'            => _x('הוסף שאלה', 'book'),
        'add_new_item'       => __('הוסף שאלה חדשה'),
        'edit_item'          => __('ערוך שאלה'),
        'new_item'           => __('שאלה חדשה'),
        'all_items'          => __('כל השאלות'),
        'view_item'          => __('View'),
        'search_items'       => __('Search'),
        'not_found'          => __('לא נמצאו שאלות'),
        'not_found_in_trash' => __('לא נמצאו שאלות בפח אשפה'),
        'parent_item_colon'  => '',
        'menu_name'          => 'שאלות תשובות'
    );

    $args = array(
        'labels'        => $labels,
        'taxonomies' => array('category', 'post_tag'),
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
        'public'        => true,
        'menu_position' => 2,
        'rewrite'       => array('slug' => 'questions'), //or some other slug :)
        'hierarchical' => true,
        'can_export' => true,
        'has_archive'   => true,
        'capability_type' => 'post',
        'menu_icon'     => 'dashicons-editor-help',

    );
    register_post_type('questions', $args);
}
add_action('init', 'my_custom_post_faq');
//END FAQ==================================================================

//Recipe replacment products:===============================================

function my_custom_post_recipe_product_replacment()
{
    $labels = array(
        'name'               => _x('מצרכים חלופים למתכון', 'post type general name'),
        'singular_name'      => _x('מצרכים חלופים למתכון', 'post type singular name'),
        'add_new'            => _x('הוסף מצרך חלופי', 'book'),
        'add_new_item'       => __('הוספת מצרך חלופי חדש'),
        'edit_item'          => __('ערוך מצרך חלופי'),
        'new_item'           => __('חדש'),
        'all_items'          => __('כל המצרכים החלופיים'),
        'view_item'          => __('View'),
        'search_items'       => __('Search'),
        'not_found'          => __('לא נמצאו מצרכים חלופיים'),
        'not_found_in_trash' => __('לא נמצאו מצרכים חלופיים בפח אשפה'),
        'parent_item_colon'  => '',
        'menu_name'          => 'מצרכים חלופים למתכון'
    );

    $args = array(
        'labels'        => $labels,
       // 'taxonomies' => array('category', 'post_tag'),
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
        'public'        => true,
        'menu_position' => 2,
        'rewrite'       => array('slug' => 'replacements'), //or some other slug :)
        'hierarchical' => true,
        'can_export' => true,
        'has_archive'   => true,
        'capability_type' => 'post',
        'menu_icon'     => 'dashicons-buddicons-topics',

    );
    register_post_type('replacements', $args);
}
add_action('init', 'my_custom_post_recipe_product_replacment');






//Recipe replacment products MAKING PROCCESS:===============================================

function my_custom_post_recipe_product_replacment_Baking_Proccess()
{
    $labels = array(
        'name'               => _x('מצרכים חלופים אופן הכנה', 'post type general name'),
        'singular_name'      => _x('מצרכים חלופים אופן הכנה', 'post type singular name'),
        'add_new'            => _x('הוסף אופן הכנה', 'book'),
        'add_new_item'       => __('הוספת אופן הכנה'),
        'edit_item'          => __('ערוך '),
        'new_item'           => __('חדש'),
        'all_items'          => __('כל האייטמים'),
        'view_item'          => __('View'),
        'search_items'       => __('Search'),
        'not_found'          => __('לא נמצאו תוצאות'),
        'not_found_in_trash' => __('אין כלום בפח'),
        'parent_item_colon'  => '',
        'menu_name'          => 'אופן הכנה למוצרים חלופיים'
    );

    $args = array(
        'labels'        => $labels,
       // 'taxonomies' => array('category', 'post_tag'),
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
        'public'        => true,
        'menu_position' => 2,
        'rewrite'       => array('slug' => 'baking_proccess'), //or some other slug :)
        'hierarchical' => true,
        'can_export' => true,
        'has_archive'   => true,
        'capability_type' => 'post',
        'menu_icon'     => 'dashicons-food',

    );
    register_post_type('baking_proccess', $args);
}
add_action('init', 'my_custom_post_recipe_product_replacment_Baking_Proccess');


//=======================PULL CONTENT TYPE======================================




//Recipe replacment products MAKING PROCCESS:===============================================

function Foody_Recipe_Poll()
{
    $labels = array(
        'name'               => _x('משאל', 'post type general name'),
        'singular_name'      => _x('משאל', 'post type singular name'),
        'add_new'            => _x('הוסף משאל', 'book'),
        'add_new_item'       => __('הוספת משאל'),
        'edit_item'          => __('ערוך '),
        'new_item'           => __('חדש'),
        'all_items'          => __('כל האייטמים'),
        'view_item'          => __('View'),
        'search_items'       => __('Search'),
        'not_found'          => __('לא נמצאו תוצאות'),
        'not_found_in_trash' => __('אין כלום בפח'),
        'parent_item_colon'  => '',
        'menu_name'          => 'משאל'
        
    );

    $args = array(
        'labels'        => $labels,
       // 'taxonomies' => array('category', 'post_tag'),
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
        'public'        => true,
        'menu_position' => 2,
        'rewrite'       => array('slug' => 'poll'), //or some other slug :)
        'hierarchical' => true,
        'can_export' => true,
        'has_archive'   => true,
        'capability_type' => 'post',
        'menu_icon'     => 'dashicons-megaphone',
        'show_in_menu'=> true

    );
    register_post_type('poll', $args);
}
add_action('init', 'Foody_Recipe_Poll');

//=============POLL combain post type : ========================

function Foody_Recipe_Poll_Results()
{
    $labels = array(
        'name'               => _x('תוצאות משאל', 'post type general name'),
        'singular_name'      => _x('תוצאות משאל', 'post type singular name'),
        'add_new'            => _x('הוספת תוצאות משאל', 'book'),
        'add_new_item'       => __('הוספת תוצאות משאל'),
        'edit_item'          => __('ערוך '),
        'new_item'           => __('חדש'),
        'all_items'          => __('כל האייטמים'),
        'view_item'          => __('View'),
        'search_items'       => __('Search'),
        'not_found'          => __('לא נמצאו תוצאות'),
        'not_found_in_trash' => __('אין כלום בפח'),
        'parent_item_colon'  => '',
        'menu_name'          => 'תוצאות משאל'
        
    );

    $args = array(
        'labels'        => $labels,
       // 'taxonomies' => array('category', 'post_tag'),
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
        'public'        => true,
        'menu_position' => 4,
        'rewrite'       => array('slug' => 'poll'), //or some other slug :)
        'hierarchical' => true,
        'can_export' => true,
        'has_archive'   => true,
        'capability_type' => 'post',
        'menu_icon'     => 'dashicons-admin-settings',
        'show_in_menu'=> false

    );
    register_post_type('poll_results', $args);
}
add_action('init', 'Foody_Recipe_Poll_Results');


add_action( 'admin_menu', 'my_Poll_Result_admin_submenu' );
 

 
function my_Poll_Result_admin_submenu(){
 
    add_submenu_page(
        'edit.php?post_type=poll',                 // parent slug
        'תוצאות משאל',             // page title
        'תוצאות משאל',             // sub-menu title
        'edit_posts',                 // capability
        'edit.php?post_type=poll_results' // your menu menu slug
       
    );
}
//=====================ANSWERS FOR POLL===============================
function Foody_Recipe_Poll_Answers()
{
    $labels = array(
        'name'               => _x('תשובות למשאל', 'post type general name'),
        'singular_name'      => _x('תשובות למשאל', 'post type singular name'),
        'add_new'            => _x('הוספת תשובות משאל', 'book'),
        'add_new_item'       => __('הוספת תוצאות משאל'),
        'edit_item'          => __('ערוך '),
        'new_item'           => __('חדש'),
        'all_items'          => __('כל האייטמים'),
        'view_item'          => __('View'),
        'search_items'       => __('Search'),
        'not_found'          => __('לא נמצאו תוצאות'),
        'not_found_in_trash' => __('אין כלום בפח'),
        'parent_item_colon'  => '',
        'menu_name'          => 'תוצאות משאל'
        
    );

    $args = array(
        'labels'        => $labels,
       // 'taxonomies' => array('category', 'post_tag'),
        'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
        'public'        => true,
        'menu_position' => 2,
        'rewrite'       => array('slug' => 'poll'), //or some other slug :)
        'hierarchical' => true,
        'can_export' => true,
        'has_archive'   => true,
        'capability_type' => 'post',
        'menu_icon'     => 'dashicons-admin-settings',
        'show_in_menu'=> false

    );
    register_post_type('poll_answers', $args);
}
add_action('init', 'Foody_Recipe_Poll_Answers');


add_action( 'admin_menu', 'my_Poll_Result_admin_submenu_answers' );
 

 
function my_Poll_Result_admin_submenu_answers(){
 
    add_submenu_page(
        'edit.php?post_type=poll',                 // parent slug
        'תשובות למשאל',             // page title
        'תשובות למשאל',             // sub-menu title
        'edit_posts',                 // capability
        'edit.php?post_type=poll_answers' // your menu menu slug
       
    );
}


?>




