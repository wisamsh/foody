<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/19/18
 * Time: 9:40 AM
 */
class Foody_Sidebar {

	private $show_filters;

	/**
	 * Foody_Sidebar constructor.
	 */
	public function __construct() {

		add_action( 'template_redirect', array( $this, 'handle_widgets' ) );
	}


	/**
	 * Hides the filter widget based
	 * on a custom field using 'sidebars_widgets' hook
	 *
	 * @param $all_widgets
	 *
	 * @return mixed
	 */
	function hide_widgets( $all_widgets ) {
		$queried_object     = get_queried_object();
		$this->show_filters = get_field( 'show_filters', $queried_object );
		if ( $this->show_filters === null ) {
			$this->show_filters = true;
		}


		if ( $this->show_filters === false ) {
			foreach ( $all_widgets['foody-sidebar'] as $i => $inst ) {
				//check if the id for the archives widgets exists.
				$pos = strpos( $inst, 'foody_search_filter' );

				if ( $pos !== false ) {
					//remove the archives widget by unsetting it's id
					unset( $all_widgets['foody-sidebar'][ $i ] );
				}
			}
		}

		return $all_widgets;
	}

	public function handle_widgets() {
		add_filter( 'sidebars_widgets', array( $this, 'hide_widgets' ) );
	}


	public function the_sidebar() {

	}

	public function the_social_sidebar() {
		dynamic_sidebar( 'foody-social' );
	}

}

new Foody_Sidebar();