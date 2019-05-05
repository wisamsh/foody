<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/28/18
 * Time: 10:42 AM
 */
class foody_Product_Widget extends Foody_Widget {


	public static $foody_widget_id = 'foody-product_widget';

	const CSS_CLASSES = 'foody-product-widget';

	/**
	 * To create the example widget all four methods will be
	 * nested inside this single instance of the WP_Widget class.
	 **/

	public function __construct() {
		$widget_options = array(
			'classname'   => self::CSS_CLASSES,
			'description' => 'Sidebar product widget',
		);
		parent::__construct( self::$foody_widget_id, 'Product', $widget_options );
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">כותרת:</label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
        </p>
		<?php
	}

	protected function display( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$image = get_field( 'image', 'widget_' . $this->id );
		$link  = get_field( 'link', 'widget_' . $this->id );

		foody_get_template_part( get_template_directory() . '/template-parts/white-label/content-foody-product.php',
			[
				'product' =>
					[
						'title' => $title,
						'image' => $image,
						'link'  => $link
					],
				'widget'  => true
			]
		);
	}

	protected function get_css_classes() {
		return self::CSS_CLASSES;
	}
}

?>