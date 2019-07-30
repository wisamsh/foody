<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/14/19
 * Time: 5:37 PM
 */

add_filter( 'acfwpcli_fieldgroup_paths', 'foody_add_acf_cli_path' );
function foody_add_acf_cli_path( $paths ) {
	$paths['foody'] = WEB_ROOT . '/acf-json/';

	return $paths;
}


function foody_acf_load_sites( $field ) {
	// reset choices
	$field['choices'] = array();

	$sites = get_sites( [ 'site__not_in' => get_main_site_id() ] );

	/** @var WP_Site $site */
	foreach ( $sites as $site ) {

		// append to choices
		$field['choices'][ $site->blog_id ] = $site->blogname;
	}

	// return the field
	return $field;

}

add_filter( 'acf/load_field/name=foody_sites', 'foody_acf_load_sites' );

/**
 * ACF Rule Type: Is Main Site
 *
 *
 * @param array $choices , all of the available rule types
 *
 * @return array
 */
function foody_acf_rule_type_is_main_site( $choices ) {
	$choices['MultiSite']                 = [];
	$choices['MultiSite']['is_main_site'] = 'Is Main Site';

	return $choices;
}

add_filter( 'acf/location/rule_types', 'foody_acf_rule_type_is_main_site' );

/**
 * ACF Rule Values: Is Main Site
 *
 *
 * @param array $choices , available rule values for this type
 *
 * @return array
 */
function foody_acf_rule_values_is_main_site( $choices ) {

	$choices['irrelevant'] = 'Irrelevant';

	return $choices;
}

add_filter( 'acf/location/rule_values/is_main_site', 'foody_acf_rule_values_is_main_site' );

/**
 * ACF Rule Match: Is Main Site
 *
 * @param boolean $match , whether the rule matches (true/false)
 * @param array $rule , the current rule you're matching. Includes 'param', 'operator' and 'value' parameters
 * @param array $options , data about the current edit screen (post_id, page_template...)
 *
 * @return boolean $match
 */
function foody_acf_rule_match_is_main_site( $match, $rule, $options ) {

	$match = get_current_blog_id() === get_main_site_id();

	return $match;
}

add_filter( 'acf/location/rule_match/is_main_site', 'foody_acf_rule_match_is_main_site', 10, 3 );