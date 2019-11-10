<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/27/18
 * Time: 7:49 PM
 */


function foody_sidebars() {

	$sidebars = array(
		array(
			'id'   => 'foody-sidebar',
			'name' => 'Foody Sidebar',
		),
		array(
			'id'   => 'foody-sidebar-mobile',
			'name' => 'Foody Mobile Sidebar',
		),
		array(
			'id'   => 'homepage-categories',
			'name' => 'Homepage Categories',
		),
		array(
			'id'   => 'foody-social',
			'name' => 'פודי Social',
		),
        array(
            'id'   => 'foody-social-mobile',
            'name' => 'פודי Social mobile',
        )
	);

	foreach ( $sidebars as $sidebar ) {
		register_sidebar( array(
			'id'   => $sidebar['id'],
			'name' => $sidebar['name'],
		) );
	}

}

add_action( 'widgets_init', 'foody_sidebars' );


function foody_register_menu() {
	register_nav_menu( 'categories', __( 'תפריט קטגוריות' ) );
}

add_action( 'after_setup_theme', 'foody_register_menu' );
