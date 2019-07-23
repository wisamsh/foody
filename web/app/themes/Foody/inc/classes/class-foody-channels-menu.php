<?php
/**
 * Created by PhpStorm.
 * User: omri
 * Date: 07/07/2018
 * Time: 16:35
 */

class Foody_Channels_Menu {


	private $debug;

	/**
	 * Foody_Channels_Menu constructor.
	 */
	public function __construct() {

	}


	public function the_menu( $echo = true ) {

		// get menu items by location
		$menu_items = foody_get_menu_by_location( "channels-menu" );

		// insert logic and items manipulation here
		$items_for_display = array();

		foreach ( $menu_items as $menu_item ) {

			$image = get_the_post_thumbnail_url( $menu_item->object_id );

			$items_for_display[] = array(
				'title' => $menu_item->title,
				'link'  => $menu_item->url,
				'image' => $image
			);
		}

		$template_args = array(
			'return' => ! $echo,
			'items'  => $items_for_display
		);

		// send items to template and display

		return foody_get_template_part( get_template_directory() . '/template-parts/channels-menu.php', $template_args );
	}

	public function get_the_menu() {
		return $this->the_menu( false );
	}

	/**
	 * @return mixed
	 */
	public function getDebug() {
		return $this->debug;
	}

	/**
	 * @param mixed $debug
	 */
	public function setDebug( $debug ) {
		$this->debug = $debug;
	}

}