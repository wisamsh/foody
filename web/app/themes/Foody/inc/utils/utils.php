<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 7:43 PM
 */


/**
 * Shortens a number and attaches K, M, B, etc. accordingly
 *
 * @param $number
 * @param int $precision
 * @param null $divisors
 *
 * @return string
 */
function view_count_display( $number, $precision = 0, $divisors = null, $label = '%s צפיות' ) {

	// Setup default $divisors if not provided
	if ( ! isset( $divisors ) ) {
		$divisors = array(
			pow( 1000, 0 ) => '', // 1000^0 == 1
			pow( 1000, 1 ) => 'K', // Thousand
			pow( 1000, 2 ) => 'M', // Million
			pow( 1000, 3 ) => 'B', // Billion
			pow( 1000, 4 ) => 'T', // Trillion
			pow( 1000, 5 ) => 'Qa', // Quadrillion
			pow( 1000, 6 ) => 'Qi', // Quintillion
		);
	}

	// Loop through each $divisor and find the
	// lowest amount that matches
	foreach ( $divisors as $divisor => $shorthand ) {
		if ( abs( $number ) < ( $divisor * 1000 ) ) {
			// We found a match!
			break;
		}
	}

	// We found our match, or there were no matches.
	// Either way, use the last defined value for $divisor.
	return number_format( $number / $divisor, $precision ) . sprintf( '<span class="view-count">' . $label . '</span>', $shorthand );
}

function foody_print_commercial_rules( $rules ) {

	$sponsored_ingredient_container = '';

	if ( ! empty( $rules ) ) {


		// Filter rules - feed channel rules must be shown only under the correct feed channel.
		// Filter rules - date limited rules must be shown only when time is right.
		$rules = array_filter( $rules, function ( $rule ) {
			$rule_id   = $rule['rule_id'];
			$rule_type = get_field( 'type', $rule_id );

			if ( $rule_type == 'area' ) {
				if ( isset( $_GET['referer'] ) ) {

					$rule_areas = get_field( 'comm_rule_area', $rule_id );

					// Make rule areas an array of IDs
					$rule_areas = array_map( function ( $area ) {
						return $area->ID;
					}, $rule_areas );

					$referer_post = $_GET['referer'];

					if ( ! empty( $referer_post ) ) {
						if ( ! in_array( $referer_post, $rule_areas ) ) {
							return false;
						}
					} else {
						return false;
					}
				} else {
					return false;
				}
			}

			$emptyDate = false;

			$from = get_field( 'from', $rule_id );
			$to   = get_field( 'to', $rule_id );
			if ( empty( $from ) && empty( $to ) ) {
				$emptyDate = true;
			} else {
				$from = str_replace( '/', '-', $from );
				$to   = str_replace( '/', '-', $to );
			}

			// Should show according to date
			if ( $emptyDate || ( strtotime( $from ) <= strtotime( 'now' ) && strtotime( $to ) >= strtotime( 'now' ) ) ) {
				return true;
			} else {
				return false;
			}

			return true;
		} );

		// Filter rules by order - greatest order is the one to be shown.
		$orders = [];

		array_map( function ( $rule ) use ( &$orders ) {
			$order                      = get_post_meta( $rule['rule_id'], 'menu_order', true );
			$orders[ $rule['rule_id'] ] = $order;

		}, $rules );

		uasort( $orders, function ( $a, $b ) {
			if ( $a == $b ) {
				return 0;
			}

			return ( $a < $b ) ? - 1 : 1;
		} );

		$rules = array_filter( $rules, function ( $rule ) use ( $orders ) {
			return $rule['rule_id'] == key( $orders );
		} );

		$has_image             = false;
		$sponsored_ingredients = [];

		foreach ( $rules as $rule ) {

			$rule_id = $rule['rule_id'];
			// $rule_post = get_post( $rule_id );

			$sponsor_id = get_field( 'sponsor', $rule_id );

			$chosen_sponsor = get_term( $sponsor_id, 'sponsors' );
			if ( ! empty( $chosen_sponsor->parent ) ) {
				$sponsor_brand = get_term( $chosen_sponsor->parent, 'sponsors' );
				if ( ! empty( $sponsor_brand->parent ) ) {
					$sponsor = get_term( $sponsor_brand->parent, 'sponsors' );
				}
			}

			$show_product            = get_field( 'show_product', $rule_id );
			$show_product_logo       = get_field( 'show_product_logo', $rule_id );
			$show_sponsor_brand      = get_field( 'show_sponsor_brand', $rule_id );
			$show_sponsor_brand_logo = get_field( 'show_sponsor_brand_logo', $rule_id );
			$show_sponsor            = get_field( 'show_sponsor', $rule_id );
			$show_sponsor_logo       = get_field( 'show_sponsor_logo', $rule_id );

			if ( ! $has_image ) {
				$has_image = (
					( $show_product_logo && $show_product ) ||
					( $show_sponsor_brand_logo && $show_sponsor_brand ) ||
					( $show_sponsor_logo && $show_sponsor )
				);
			}

			if ( isset( $chosen_sponsor ) && ! empty( $chosen_sponsor ) && ! is_wp_error( $chosen_sponsor ) ) {

				$has_image = $has_image || ( ! empty( get_field( 'logo', $chosen_sponsor->taxonomy . '_' . $chosen_sponsor->term_id ) ) && $show_product_logo );

				// print $chosen_sponsor;
				if ( isset( $sponsor ) ) {
					// Has grandparent (sponsor - company) -> chosen == product
					$sponsored_ingredients[] = foody_get_commercial_sponsor_data( $chosen_sponsor, $show_product_logo, $show_product, 'product' );
				} else if ( isset( $sponsor_brand ) ) {
					// Has parent only (sponsor - company) -> chosen == brand
					$sponsored_ingredients[] = foody_get_commercial_sponsor_data( $chosen_sponsor, $show_sponsor_brand_logo, $show_sponsor_brand, 'brand' );
				} else {
					// Has no parents -> chosen == company
					$sponsored_ingredients[] = foody_get_commercial_sponsor_data( $chosen_sponsor, $show_sponsor_logo, $show_sponsor, 'company' );

				}
			}
			if ( isset( $sponsor_brand ) && ! empty( $sponsor_brand ) && ! is_wp_error( $chosen_sponsor ) ) {
				// print $sponsor_brand;
				$has_image = $has_image || ( ! empty( get_field( 'logo', $sponsor_brand->taxonomy . '_' . $sponsor_brand->term_id ) ) && $show_sponsor_brand_logo );
				if ( isset( $sponsor ) ) {
					// Has parent (sponsor - company) -> brand == brand
					$sponsored_ingredients[] = foody_get_commercial_sponsor_data( $sponsor_brand, $show_sponsor_brand_logo, $show_sponsor_brand, 'brand' );
				} else {
					// Has no parents -> brand == company
					$sponsored_ingredients[] = foody_get_commercial_sponsor_data( $sponsor_brand, $show_sponsor_logo, $show_sponsor, 'company' );
				}

			}
			if ( isset( $sponsor ) && ! empty( $sponsor ) && ! is_wp_error( $chosen_sponsor ) ) {

				$has_image = $has_image || ( ! empty( get_field( 'logo', $sponsor->taxonomy . '_' . $sponsor->term_id ) ) && $show_sponsor_logo );

				// print $sponsor;
				$sponsored_ingredients[] = foody_get_commercial_sponsor_data( $sponsor, $show_sponsor_logo, $show_sponsor, 'company' );
			}


		}

		$sponsored_ingredient_container_classes = [ 'sponsors-container' ];
		if ( ! $has_image ) {
			$sponsored_ingredient_container_classes[] = 'sponsors-without-image';
		}

		$sponsored_ingredient_container = '<div class="' . implode( ' ', $sponsored_ingredient_container_classes ) . '">';

		$sponsored_ingredients          = array_filter( $sponsored_ingredients );
		$sponsored_ingredients          = implode( '<span class="delimiter">,</span>', $sponsored_ingredients );
		$sponsored_ingredient_container .= $sponsored_ingredients;
		$sponsored_ingredient_container .= '</div>';
	}

	return $sponsored_ingredient_container;
}


function foody_get_commercial_sponsor_data( $sponsor, $show_logo, $show_text, $type ) {
	if ( ! empty( $sponsor ) && ( $show_logo || $show_text ) ) {
		$image = get_field( 'logo', $sponsor->taxonomy . '_' . $sponsor->term_id );
		$link  = get_field( 'link', $sponsor->taxonomy . '_' . $sponsor->term_id );
		$text  = $sponsor->name;

		$content = '';
		$content .= '<span class="sponsored-by ' . $type . '">';
		if ( ! empty( $link ) ) {
			$target = '';
			if ( ! empty( $link['target'] ) ) {
				$target = 'target="' . $link['target'] . '"';
			}
			$content .= '<a href="' . $link['url'] . '" ' . $target . ' >';
		}
		if ( ! empty( $image ) && $show_logo ) {
			$content .= '<img src="' . $image['url'] . '" alt="' . $image['alt'] . '">';
		}
		if ( ! empty( $text ) && $show_text ) {
			$content .= '<div>' . $text . '</div>';
		}
		if ( ! empty( $link ) ) {
			$content .= '</a>';
		}
		$content .= '</span>';

		return $content;
	}
}
