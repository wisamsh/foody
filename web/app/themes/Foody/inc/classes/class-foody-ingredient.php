<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/28/18
 * Time: 7:32 PM
 */
class Foody_Ingredient extends Foody_Post {

	public $amount;

	public $unit;

	public $amounts;

	public $comment;

	public $amounts_delimiter = ' <b> או </b> ';

	public $unit_taxonomy;

	public $fractions = [
		'3'  => '1/3',
		'25' => '1/4',
		'5'  => '1/2',
		'66' => '2/3',
		'75' => '3/4',
	];

	public $plural_name;

	public $singular_name;
	public $nutrients;
	public $nutrients_conversion;
	public $recipe_id;

	private $foody_search;

	public $has_alter_link = false;

	/**
	 * Foody_Ingredient constructor.
	 *
	 * @param $ingredient_post_id
	 * @param $amount
	 * @param $unit
	 */
	public function __construct( $post, $amount = null, $unit = null, $unit_taxonomy = null ) {
		parent::__construct( $post );
		$this->amount = $amount;
		$this->unit   = $unit;

		$this->plural_name          = get_field( 'plural_name', $this->id );
		$this->singular_name        = $this->getTitle();
		$this->nutrients            = get_field( 'nutrients', $this->id );
		$this->nutrients_conversion = get_field( 'nutrients_conversion', $this->id );

		$this->unit_taxonomy = $unit_taxonomy;

		$this->foody_search = new Foody_Search( 'foody_ingredient' );
	}


	function __toString() {
		return sprintf(
			'%s %s %s',
			$this->get_the_amounts(),
			$this->getUnit(),
			$this->getTitle()
		);
	}

	public function feed() {
		$foody_query = Foody_Query::get_instance();

		$args = $foody_query->get_query( 'foody_ingredient', [ $this->id ], true );

		$query = new WP_Query( $args );

		$posts = $query->get_posts();

		$posts = array_map( 'Foody_Post::create', $posts );

		$grid_args = [
			'id'     => 'foody-ingredient-feed',
			'posts'  => $posts,
			'more'   => $foody_query->has_more_posts( $query ),
			'cols'   => 2,
			'header' => [
				'sort' => true
			]
		];

		echo '<div class="container-fluid feed-container ingredient-feed-container">';
		foody_get_template_part( get_template_directory() . '/template-parts/common/foody-grid.php', $grid_args );
		echo '</div>';
	}

	public function getUnit() {
		return $this->unit;
	}


	public function the_featured_content() {

	}

	public function the_sidebar_content( $args = array() ) {
		dynamic_sidebar( 'foody-sidebar' );
		dynamic_sidebar( 'foody-social' );
	}

	public function the_amounts( $echo = true ) {
		if ( $this->amounts != null ) {

			$length = count( $this->amounts );

			$last = array_pop( $this->amounts );

			$to_fraction              = array( $this, 'to_fraction' );
			$get_ingredient_data_attr = array( $this, 'get_ingredient_data_attr' );

			$content = implode( $this->amounts_delimiter, array_map( function ( $amount ) use ( $to_fraction, $get_ingredient_data_attr ) {

				$display = call_user_func( $to_fraction, $amount['amount'] );
				$data    = call_user_func( $get_ingredient_data_attr, $amount['amount'], $display );

				return
					'<span dir="ltr" class="amount"' . $data . '>
                        ' . $display . '
                    </span>
                    <div class="extra-ingredients">
                    <span class="unit">
                         ' . $amount['unit'] . '
                    </span>';
			}, $this->amounts ) );

			$unit_tax = $last['unit_tax'];

			$show_after_ingredient = get_field( 'show_after_ingredient', $unit_tax );

			$display = $this->to_fraction( $last['amount'] );

			$amount = $last['amount'];

			$title = $this->getTitle();

			$unit            = $last['unit'];
			$this->amounts[] = $last;
			$ing_html        = $this->get_ingredient_html( $amount, $display, $unit, $title, $show_after_ingredient, $length );


			if ( $length > 1 ) {
				$content .= $this->amounts_delimiter;
			} else {
				$content .= '<div class="extra-ingredients">';
			}

			if ( ! empty( $content ) ) {
				$content .= $ing_html;
			} else {
				$content = $ing_html;
			}


		} else {
			$content = '<span class="unit">
                        ' . $this->getTitle() . '
                    </span>';
		}

		$content .= '</div>';

		if ( $echo ) {
			echo $content;
		}

		return $content;
	}

	public function get_ingredient_html( $amount, $display, $unit, $title, $is_unit_after_title, $length ) {

		if ( ! empty( $this->plural_name ) ) {
			if ( ceil( $amount ) > 1 || ( ceil( $amount ) > 0 && $unit == 'ק"ג' ) ) {
				$title = $this->plural_name;
			}
		}

		$data = $this->get_ingredient_data_attr( $amount, $display );
		$data .= ' ' . foody_array_to_data_attr( [ 'unit' => $unit ] );

		if ( ! empty( $this->nutrients ) && is_array( $this->nutrients ) ) {
			$nutrients_data = [];

			$nutrients_names = self::get_nutrients_options();


			foreach ( $nutrients_names as $nutrient_name => $value ) {

				$nutrients_data[ $nutrient_name ] = $this->get_nutrient_data_by_unit_and_amount( $nutrient_name );
			}

			$data .= ' ' . foody_array_to_data_attr( $nutrients_data );
		}

		$amount_el = '<span class="ingredient-container">';
		$amount_el .= ' <span dir="ltr" class="amount" ' . $data . '>
                        ' . $display . '
                    </span>';

		$amount_el .= '<span class="ingredient-data">';
		$unit_el   = ' <span class="unit">
                         ' . $unit . '
                    </span>';

		if ( $this->has_alter_link ) {
			$link       = $this->link['url'];
			$link_title = $this->link['title'];
			$target     = $this->link['target'];
		} else {
			$link_title = esc_attr( sprintf( 'לכל המתכונים עם %s', $title ) );
			$link       = $this->link;
			$target     = '_self';

		}
		$name_el = '<span class="name"><a target="' . $target . '" title="' . $link_title . '" class="foody-u-link" href="' . $link . '">
                        ' . $title . '
                    </span></a>';

		if ( $is_unit_after_title ) {
			$amount_el .= $name_el;
			$amount_el .= $unit_el;
		} else {
			$amount_el .= $unit_el;
			$amount_el .= $name_el;
		}

		if ( $length > 1 ) {
			$amount_el .= '</span></span>';
		}

		// Add ingredient comment
		if ( ! empty( $this->comment ) ) {
			$amount_el .= '<div class="comment">' . $this->comment . '</div>';
		}

		if ( $length <= 1 ) {
			$amount_el .= '</span></span>';
		}

		return $amount_el;
	}

	private function get_ingredient_data_attr( $amount, $display ) {
		return foody_array_to_data_attr( [
			'amount'   => $amount,
			'original' => $display,
			'plural'   => $this->plural_name,
			'singular' => $this->singular_name
		] );
	}

	private function to_fraction( $dec ) {

		$str = strval( $dec );

		$fraction = explode( '.', $str );
		if ( isset( $fraction[1] ) ) {


			if ( $fraction != null ) {
				$whole = $fraction[0];
				if ( $whole == '0' ) {
					$whole = '';
				}

				// Use fractions only when exists
				if ( ! empty( $this->fractions[ $fraction[1] ] ) ) {
					return $whole . ' ' . $this->fractions[ $fraction[1] ];
				}
			}
		}

		return $dec;
		// return Fraction::fromFloat($dec)

	}

	public function get_the_amounts() {
		return $this->the_amounts( false );
	}

	public function the_details() {
		echo '<section class="ingredient-details-container">';
		bootstrap_breadcrumb();
		the_title( '<h1 class="title">', '</h1>' );
		echo '</section>';
	}


	/**
	 * @param $nutrient_name string
	 * @param $unit WP_Term
	 * @param $amount number
	 *
	 * @return float|int
	 */
	public function get_nutrient_for_by_unit_and_amount( $nutrient_name ) {

		$value = 0;
		if ( ! empty( $this->amounts ) ) {
			$unit   = $this->amounts[0]['unit_tax'];
			$amount = $this->amounts[0]['amount'];

			if ( ! empty( $this->nutrients ) && is_array( $this->nutrients ) ) {
				$nutrients = array_filter( $this->nutrients, function ( $nutrient ) use ( $nutrient_name, $unit, $amount ) {

					$valid = false;

					if ( $unit instanceof WP_Term ) {

						$valid = $nutrient['unit'] == $unit->term_id &&
						         $nutrient['nutrient'] == $nutrient_name;
					}

					return $valid;
				} );

				if ( ! empty( $nutrients ) ) {
					$nutrient = array_shift( $nutrients );
					$value    = $nutrient['value'];
					$factor   = 1;
					if ( $unit->name == 'גרם' || $unit->name == 'מ"ל' ) {
						$factor = 100;
					}

					$amount = floatval( $amount );
					$value  = floatval( $value );
					$value  = ( $amount / $factor ) * $value;

					$value = (float) $value;

				}
			}
		}


		return $value;
	}

	/**
	 * Retreive ingredient nutritional data using nutrients_conversion table
	 *
	 * @param $nutrient_name string
	 * @param $unit WP_Term
	 * @param $amount number
	 *
	 * @return float|int
	 */
	public function get_nutrient_data_by_unit_and_amount( $nutrient_name ) {
		$value     = 0;
		$unit_name = '';

		if ( ! empty( $this->amounts ) ) {
			$amounts = $this->amounts[0];
			$unit    = ! empty( $amounts['unit'] ) ? $amounts['unit_tax'] : $amounts['unit'];
			$amount  = $amounts['amount'];

			if ( $unit instanceof WP_Term ) {
				$unit_name = get_term_field( 'slug', $unit->term_id, 'units' );
			}

			if ( ( ! empty( $this->nutrients_conversion ) && is_array( $this->nutrients_conversion ) ) ) {
				$nutrients = array_filter( $this->nutrients_conversion, function ( $nutrient ) use ( $nutrient_name, $unit, $amount, $unit_name ) {
					$valid = false;

					if ( $unit instanceof WP_Term ) {
						$valid = $nutrient['unit'] == $unit->term_id;
					} else if ( empty( $unit ) ) {
						$valid = true;
					}

					return $valid;
				} );

			} else if ( urldecode( $unit_name ) == 'גרם' || urldecode( $unit_name ) == 'מל' ) {
				$value = 1;
			}

			if ( ! empty( $nutrients ) || ( urldecode( $unit_name ) == 'גרם' || urldecode( $unit_name ) == 'מל' ) ) {

				if ( ! empty( $nutrients ) ) {
					$nutrient = array_shift( $nutrients );
					$value    = $nutrient['grams'];
				}

				$grams  = $this->get_nutrient_grams_by_unit( $nutrient_name );
				$factor = 100;// per 100 Grams

				$amount = floatval( $amount );
				$value  = floatval( $value );
				if ( ! empty( $amounts['unit'] ) && ( $amounts['unit'] == 'גרם' || $amounts['unit'] == 'מ"ל' ) ) {
					$value = 1;
				}
				$grams_value = ( $value / $factor ) * $grams;
				$value       = $amount * $grams_value;

				$value = (float) $value;

			}
		}

		return $value;
	}

	/**
	 * Retreive nutrient grams value
	 *
	 * @param $nutrient_name string
	 * @param $unit WP_Term
	 * @param $amount number
	 *
	 * @return float|int
	 */
	public function get_nutrient_grams_by_unit( $nutrient_name ) {
		$value = 0;
		if ( ! empty( $this->amounts ) ) {
			$amounts = $this->amounts[0];
			$unit    = ! empty( $amounts['unit'] ) ? $amounts['unit_tax'] : $amounts['unit'];
			$amount  = $amounts['amount'];

			if ( ! empty( $this->nutrients ) && is_array( $this->nutrients ) ) {
				$nutrients = array_filter( $this->nutrients, function ( $nutrient ) use ( $nutrient_name, $unit, $amount ) {

					$valid = false;

					if ( $unit instanceof WP_Term ) {

						$unit_name = get_term_field( 'slug', $nutrient['unit'], 'units' );

						if ( is_wp_error( $unit_name ) || empty( $unit_name ) ) {
							$unit_name = '';
						}

						$valid = ( urldecode( $unit_name ) == 'גרם' ||
						           urldecode( $unit_name ) == 'מל' ) &&
						         $nutrient['nutrient'] == $nutrient_name;
					} else if ( empty( $unit ) ) {
						$valid = true;
					}

					return $valid;
				} );

				if ( ! empty( $nutrients ) ) {
					$nutrient = array_shift( $nutrients );
					$value    = $nutrient['value'];
				}
			}
		}


		return $value;
	}


	public static function get_nutrients_options() {
		$nutrients = get_field_object( 'field_5b62c59c35d88' )['choices'];
		unset( $nutrients['sugar'] );

		return $nutrients;
	}

	/**
	 * @param $nutrient
	 *
	 * @return string
	 */
	public static function get_nutrient_unit( $nutrient ) {
		$nutrients                  = self::get_nutrients_options();
		$gram                       = __( 'גרם' );
		$m_gram                     = __( 'מ״ג' );
		$nutrients['calories']      = __( 'קק״ל' );
		$nutrients['carbohydrates'] = $gram;
		$nutrients['fats']          = $gram;
		$nutrients['protein']       = $gram;
		$nutrients['sodium']        = $m_gram;

		$nutrients['fibers']        = $gram;
		$nutrients['saturated_fat'] = $gram;
		$nutrients['cholesterol']   = $m_gram;
		$nutrients['calcium']       = $m_gram;
		$nutrients['iron']          = $m_gram;
		$nutrients['potassium']     = $m_gram;
		$nutrients['zinc']          = $m_gram;

		$unit = '';

		if ( isset( $nutrients[ $nutrient ] ) ) {
			$unit = $nutrients[ $nutrient ];
		}

		return $unit;
	}

	function __clone() {

	}


	/**
	 * Checks if a commercial brand
	 * should be displayed in this ingredient's html
	 * representation
	 * @return mixed|null|void
	 */
	private function get_sponsor() {
		$sponsor_to_return = null;
		if ( ! empty( $sponsor = get_field( 'sponsor', $this->id ) ) ) {

			$filters = get_field( 'filters', $this->id );
			if ( ! empty( $filters ) ) {

				$post = get_post( $this->recipe_id );

				foreach ( $filters as $filter ) {
					$filter = $filter['filter'];
					$value  = $filter[ 'value_' . $filter['type'] ];

					if ( $value ) {
						switch ( $filter['type'] ) {
							case 'author':
								/** @var WP_User $value */
								if ( $post->post_author == $value->ID ) {
									$sponsor_to_return = $sponsor;
								}
								break;
							case 'category':
								/** @var WP_Term $value */
								if ( in_category( $value->term_id, $post ) ) {
									$sponsor_to_return = $sponsor;
								}
								break;
							case 'tag':
								/** @var WP_Term $value */
								if ( has_tag( $value->term_id, $post ) ) {
									$sponsor_to_return = $sponsor;
								}
								break;
							case 'channel':
								/** @var WP_Post $value */
								$channel_recipes = get_field( 'related_recipes', $value );

								if ( ! empty( $channel_recipes ) && is_array( $channel_recipes ) ) {

									$channel_recipes = array_map( function ( $post ) {
										return $post->ID;
									}, $channel_recipes );

									if ( in_array( $this->recipe_id, $channel_recipes ) ) {
										$sponsor_to_return = $sponsor;
									}
								}
								break;
						}
					}
				}
			} else {
				$sponsor_to_return = $sponsor;
			}

		}

		return $sponsor_to_return;
	}

	/**
	 *
	 * @return mixed|null|void
	 */
	public function the_sponsored_ingredient( $echo = true ) {
		// Fetch rules for recipe
		$rules                = Foody_CommercialRuleMapping::getByIngredientRecipe( $this->recipe_id, $this->id );
		$sponsored_ingredient = '';

		if ( ! empty( $rules ) ) {
			$sponsored_ingredient = foody_print_commercial_rules( $rules );
		}

		if ( $echo ) {
			echo $sponsored_ingredient;
		}

		return $sponsored_ingredient;
	}

}
