<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/13/18
 * Time: 11:15 AM
 */
class Foody_CategoriesAccordionWidget extends Foody_Widget {
	public static $foody_widget_id = 'categories_accordion_widget';

	const CSS_CLASSES = 'sidebar-section categories-accordion-widget';

	/**
	 * To create the example widget all four methods will be
	 * nested inside this single instance of the WP_Widget class.
	 **/

	public function __construct() {
		$widget_options = array(
			'classname'   => self::CSS_CLASSES,
			'description' => 'This is an Example Widget',
		);
		parent::__construct( self::$foody_widget_id, 'Categories Accordion', $widget_options );
	}

	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
        <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
               name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
        </p><?php
	}

	protected function display( $args, $instance ) {
		echo $this->categories_tree_to_accordion( $args );
	}


	protected function get_css_classes() {
		return self::CSS_CLASSES;
	}


	/**
	 *
	 * @return string|null
	 */
	private function categories_tree_to_accordion( $args ) {

		$categories = get_categories( [
			'hide_empty'   => false,
			'hierarchical' => true,
			'parent'       => 0,
			'number'       => 15
		] );

		$accordion_args = array(
			// TODO use title from widget
			'title'         => __( 'קטגוריות', 'foody' ),
			'id'            => 'categories-widget-accordion',
			'return'        => true,
			'content'       => '',
			'title_classes' => 'main-title category-title',
			'title_icon'    => 'icon-categories',
			'start_state'   => $args['name'] == 'Foody Sidebar' ? 'show' : 'hide'
		);

		foreach ( $categories as $category ) {

			$accordion_args['content'] .= $this->category_accordion( $category );
		}

		return foody_get_template_part(
			get_template_directory() . '/template-parts/common/accordion.php',
			$accordion_args
		);
	}


	/**
	 * @param WP_Term $category
	 *
	 * @return bool|string
	 */
	private function category_accordion( $category ) {

		$children = get_categories( [
			'parent'       => $category->term_id,
			'hide_empty'   => false,
			'hierarchical' => true,
			'depth'        => 1,
			'number'       => 5
		] );


		$accordion_args = array(
			'title'         => $category->name,
			'id'            => $category->term_id,
			'content'       => '',
			'return'        => true,
			'title_classes' => 'category-title',
			'start_state'   => 'hideeee'
		);


		if ( ! empty( $children ) ) {
			$accordion_args['content'] .= $this->get_accordion_item( $category );
			/** @var WP_Term $child */
			foreach ( $children as $child ) {

				$accordion_args['content'] .= $this->category_accordion( $child );
			}

			return foody_get_template_part(
				get_template_directory() . '/template-parts/common/accordion.php',
				$accordion_args
			);

		} else {

			return $this->get_accordion_item( $category );
		}
	}

	private function get_accordion_item( $category, $classes = '' ) {
		$link = get_term_link( $category->term_id );

		return "<div class='category-accordion-item $classes'><a href='$link'>{$category->name}</a></div>";
	}
}