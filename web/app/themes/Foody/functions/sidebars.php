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
			'id'            => 'foody-sidebar',
			'name'          => 'Foody Sidbar',
		),array(
			'id'            => 'homepage-categories',
			'name'          => 'Homepage Categories',
		)
	);

	foreach ( $sidebars as $sidebar ) {
		register_sidebar( array(
			'id'            => $sidebar['id'],
			'name'          => $sidebar['name'],
		) );
	}

}
add_action( 'widgets_init', 'foody_sidebars' );