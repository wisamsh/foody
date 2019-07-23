<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/29/18
 * Time: 2:56 PM
 */

add_filter( 'query_vars', 'foody_add_vars' );
function foody_add_vars( $public_query_vars ) {
	$public_query_vars[] = 'recipe';
	$public_query_vars[] = 'registered';
	$public_query_vars[] = 'hid_page';
	$public_query_vars[] = Foody_Query::$page;

	return $public_query_vars;
}

function foody_filter_query_args( $js_vars ) {
	$args = Foody_Query::get_query_params();

	$js_vars['queryArgs'] = $args;

	return $js_vars;
}

add_filter( 'foody_js_globals', 'foody_filter_query_args' );