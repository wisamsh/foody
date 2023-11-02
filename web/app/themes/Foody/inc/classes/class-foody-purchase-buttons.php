<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/14/19
 * Time: 1:46 PM
 */

class Foody_PurchaseButtons {


	/**
	 * @var $options
	 *
	 *
	 */
	private $options;

	private static $instance;

	/**
	 * Foody_PurchaseButtons constructor.
	 */
	private function __construct() {
		$this->load_options();
	}

	/**
	 * @return Foody_PurchaseButtons
	 */
	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new Foody_PurchaseButtons();
		}

		return self::$instance;
	}

	public static function the_button( $button, $echo = true ) {

		if ( ! empty( $button['image'] ) ) {
			$images = [
				'mobile_image' => $button['image']
			];
			if ( ! empty( $button['desktop_image'] ) ) {
				$images['image'] = $button['desktop_image'];
			}

			$content = foody_get_template_part(
				get_template_directory() . '/template-parts/common/picture.php',
				[ 'return' => true, 'images' => $images]
			);
		} else {
			$color      = $button['color'];
			$text_color = $button['title_color'];
			$title      = $button['title'];

			$content = "<button aria-label='רכישה' style='background-color: $color;color: $text_color;'>$title</button>";
		}

		if ( $echo ) {
			echo $content;
		}

		return $content;
	}

	/**
	 * @param int $post
	 *
	 * @return array purchase buttons relevant to post
	 * @throws Exception
	 */
	public function get_buttons_for_post( $post ) {
		$buttons = [];
        $args['types'] = [];
        $filters_rule_mapping = Foody_FiltersRuleMapping::get_instance();
        $rules_list = $filters_rule_mapping->getRules();

        if($rules_list !== false) {

            if (!empty($this->options)) {
                // only allow button with defined filters
                $options = array_filter($this->options, function ($option) {
                    return !empty($option['filter']);
                });

                foreach ($options as $option) {

                    $filter_id = $option['filter']->ID;

                    // $post exists in query, add
                    // button options to buttons
                    if (isset($rules_list[$filter_id]) && !empty($rules_list[$filter_id]) && in_array( $post, $rules_list[$filter_id])) {
                        $copy = array_merge_recursive([], $option);
                        unset($copy['filter']);
                        $buttons[] = $copy;
                    }
                }

            }
        }
		return $buttons;
	}

	private function load_options() {
		$options       = get_field( 'buttons', 'foody_purchase_options-new' );
		$this->options = $options;
	}
}