<?php
/**
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 2019-04-24
 * Time: 14:14
 */


add_action('wpmu_new_blog', 'foody_set_permalink_structure', 10);
function foody_set_permalink_structure($blog_id)
{
    switch_to_blog($blog_id);
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    $wp_rewrite->flush_rules();
    restore_current_blog();
}

function foody_is_registration_open() {
	if ( is_multisite() ) {
		return get_option( 'nsur_join_site_enabled' );
	} else {
		return get_option( 'users_can_register' );
	}
}