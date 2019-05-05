<?php
/**
 * This file is responsible for creating
 * 'must use' pages in wp.
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/7/19
 * Time: 7:52 PM
 */

$page_definitions = array(
    'השלמת-רישום' => array(
        'title' => __('השלמת רישום', 'foody'),
        'content' => '[foody-approvals redirect="true"]',
        'template' => 'content-with-images'
    ),
    'קטגוריות' => array(
        'title' => __('קטגוריות', 'foody'),
        'content' => '',
        'template' => 'categories'
    ),
    'הרשמה' => array(
        'title' => __('הרשמה', 'foody'),
        'content' => '[foody-register]',
        'template' => 'content-with-images'
    ),
    'התחברות' => array(
        'title' => __('התחברות', 'foody'),
        'content' => '[foody-login]',
        'template' => 'content-with-images'
    ),
    'צור-קשר' => array(
        'title' => __('צור קשר', 'foody'),
        'content' => '[contact-form-7 id="2842" title="צור קשר"]',
        'template' => 'centered-content'
    ),
    'פרופיל-אישי' => array(
        'title' => __('פרופיל אישי', 'foody'),
        'content' => '',
        'template' => 'profile'
    ),
);

foreach ($page_definitions as $slug => $page) {
    // Check that the page doesn't exist already
    $query = new WP_Query('pagename=' . $slug);
    if (!$query->have_posts()) {
        // Add the page using the data from the array above
        $id = wp_insert_post(
            array(
                'post_content' => $page['content'],
                'post_name' => $slug,
                'post_title' => $page['title'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'ping_status' => 'closed',
                'comment_status' => 'closed',
            )
        );

        if (!is_wp_error($id)) {
            if (!empty($page['template'])) {
                update_post_meta($id, '_wp_page_template', "page-templates/{$page['template']}.php");
            }
        }
    }
}