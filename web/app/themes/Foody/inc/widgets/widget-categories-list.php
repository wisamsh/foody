<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/28/18
 * Time: 10:42 AM
 */
class foody_Categories_List_Widget extends Foody_Widget {


	public static $foody_widget_id = 'categories_list_widget';

	public const CSS_CLASSES = 'categories-list-widget';

	/**
	 * To create the example widget all four methods will be
	 * nested inside this single instance of the WP_Widget class.
	 **/

	public function __construct() {
		$widget_options = array(
			'classname'   => self::CSS_CLASSES,
			'description' => 'This is an Example Widget',
		);
		parent::__construct( self::$foody_widget_id, 'Categories List', $widget_options );
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {

		$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
        <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
               name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
        </p><?php
	}

    protected function display($args, $instance)
    {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $title = '<h3 class="title">' . $title . '</h3>';
        echo $title;
        $categories = get_field( 'categories', 'widget_' . $this->id );

        $num_of_categories = wp_is_mobile() ? 4 : 5;
        $args              = array(
            'hide_empty' => 0
        );
        $categories_count  = sizeof( get_categories( $args ) ) - $num_of_categories;
        echo '<div class="categories-listing d-flex flex-row" data-count="' . $categories_count . '">';

        $count = 0;
        foreach ( $categories as $category ) {
            if ( $count == $num_of_categories ) {
                break;
            }
            foody_get_template_part( get_template_directory() . '/template-parts/content-category-listing.php', array(
                'name'  => $category->name,
                'image' => get_field( 'image', $category->taxonomy . '_' . $category->term_id )
            ) );

            $count ++;
        }
        echo '</div>';
    }

    protected function get_css_classes()
    {
        return self::CSS_CLASSES;
    }
}

?>