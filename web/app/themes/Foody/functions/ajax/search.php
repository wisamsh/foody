<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/25/18
 * Time: 4:52 PM
 */

/**
 * Live autocomplete search.
 *
 * @since 1.0.0
 */
function foody_ajax_autocomplete() {
	$search = stripslashes( $_POST['search'] );

	$results = new WP_Query( array(
		'post_type'      => array( 'foody_recipe', 'post' ),
		'post_status'    => 'publish',
		'posts_per_page' => 10,
		'orderby'        => 'title',
		's'              => $search,
	) );


	$items = [];
	if ( ! empty( $results->posts ) ) {
		foreach ( $results->posts as $result ) {
			$items[] = [
				'name' => $result->post_title,
				'link' => Foody_Query::get_search_url( $result->post_title )
			];
		}
	}

	$authors = foody_search_user_by_name( $search, false );

	if ( is_array( $authors ) && count( $authors ) > 0 ) {
		$authors = array_unique( $authors );
		$authors = array_map( function ( $author ) {
			$user = get_user_by( 'ID', $author );

			return [
				'name' => $user->display_name,
				'link' => Foody_Query::get_search_url( $user->display_name )
			];
		}, $authors );

		$items = array_merge( $authors, $items );

	}

	wp_send_json_success( $items );
}

add_action( 'wp_ajax_search_site', 'foody_ajax_autocomplete' );
add_action( 'wp_ajax_nopriv_search_site', 'foody_ajax_autocomplete' );


/**
 * Sidebar filter
 *
 * @since 1.0.0
 */
function foody_ajax_filter() {
	if ( ! isset( $_POST['data'] ) ) {
		wp_die( foody_ajax_error( 'no data provided for filter' ) );
	}

	$filter  = $_POST['data'];
	$options = $_POST['options'];

	$context_args = [];
	if ( isset( $_POST['data']['context_args'] ) ) {
		$context_args = $_POST['data']['context_args'];
		if ( ! is_array( $context_args ) ) {
			$context_args = [ $context_args ];
		}
	}

	$context = $_POST['data']['context'];

	$foody_search = new Foody_Search( $context, $context_args );

	$query = $foody_search->query( $filter );

	$posts = $query['posts'];

	$posts = array_map( 'Foody_Post::create', $posts );

	$grid = new FoodyGrid();

    $res = [
        'next' => count($posts) < $query['found'] ,
        'count' => count($posts),
        'found' => $query['found']
    ];


	if ( ! empty( $posts ) && ! empty( array_filter( $posts, array( $grid, 'is_post_displayable' ) ) ) ) {
		$res['items'] = $grid->loop( $posts, $options['cols'], false );
	} else {
		$res['items'] = foody_get_template_part( get_template_directory() . '/template-parts/no-results.php', [ 'return' => true ] );
		$res['next']  = false;
	}


	wp_send_json_success( $res );

}

add_action( 'wp_ajax_foody_filter', 'foody_ajax_filter' );
add_action( 'wp_ajax_nopriv_foody_filter', 'foody_ajax_filter' );


/**
 * TODO move this to relevant functions script
 *
 * @param string $search the search query
 * @param WP_Query $wp_query
 *
 * @return string sql query
 */
function __search_by_title_only( $search, $wp_query ) {
	global $wpdb;

	if ( empty( $search ) ) {
		return $search;
	} // skip processing - no search term in query

	$q = $wp_query->query_vars;
	$n = ! empty( $q['exact'] ) ? '' : '%';

	$search =
	$searchand = '';

	foreach ( (array) $q['search_terms'] as $term ) {
		$term      = esc_sql( $wpdb->esc_like( $term ) );
		$search    .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
		$searchand = ' AND ';
	}

	if ( ! empty( $search ) ) {
		$search = " AND ({$search}) ";
		if ( ! is_user_logged_in() ) {
			$search .= " AND ($wpdb->posts.post_password = '') ";
		}
	}

	return $search;
}

add_filter( 'posts_search', '__search_by_title_only', 500, 2 );
