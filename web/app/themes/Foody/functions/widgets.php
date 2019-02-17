<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/28/18
 * Time: 10:51 AM
 */


function foody_widgets() {


	$widgets = array(
		'foody_Categories_List_Widget',
		'foody_Search_Filter',
        'Foody_CategoriesAccordionWidget'
	);

	foreach ( $widgets as $widget ) {
		register_widget( $widget );
	}
}

add_action( 'widgets_init', 'foody_widgets' );