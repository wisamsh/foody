<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 2:34 PM
 */

global $post;
function foody_ajax_load_more() {

	$foody_query = Foody_Query::get_instance();
	$required    = [
		'context',
		'page',
		'filter'
	];

	$valid = foody_validate_post_required( $required );

	if ( $valid ) {


		$filter = $_POST['filter'];
		if ( ! is_array( $filter ) ) {
			$error = "invalid filter: " . strval( $filter );
		} else {

			$context_args = [];
			if ( isset( $_POST['context_args'] ) ) {
				$context_args = $_POST['context_args'];
				if ( ! is_array( $context_args ) ) {
					$context_args = [ $context_args ];
				}
			}
			$context = $_POST['context'];

			$page = $_POST['page'];

			$ranged = isset( $_POST['ranged'] ) && $_POST['ranged'];

			$page_args = $foody_query->get_query( $context, $context_args, $ranged );

			if ( is_wp_error( $page_args ) ) {
				$error = $page_args->get_error_message();
			} else {

                if(is_array($_POST) && isset($_POST['context'])  && $_POST['context'] === 'category' && isset($_POST['context_args'])   &&  is_array($_POST['context_args']) && $_POST['context_args'][0] &&  empty($_POST['sort'] )){
                    $pinned_posts = get_field('pinned_recipes',  get_term_by( 'term_taxonomy_id', $_POST['context_args'][0]));
                    if($pinned_posts){
                        //'post__not_in' => $recipes_ids,
                        $recipes_ids = array_map(function ($item) {
//                    /** @var Foody_Recipe $recipe */
                            return $item['recipe']->ID;
                        }, $pinned_posts);

                        $page_args['post__not_in'] = $recipes_ids;
                    }
                }

				unset( $filter['context'] );

				if ( empty( $filter['types'] ) || ! is_array( $filter['types'] ) ) {
					$filter['types'] = [];
				}

				$foody_search = new Foody_Search( $context, $context_args );

				$sort = '';
				if ( ! empty( $_POST['sort'] ) ) {
					$sort = $_POST['sort'];
					if ( get_query_var( 'paged', null ) ) {
						unset( $page_args['paged'] );
					}
				}
//
//				if(isset($filter['search']) && !empty($filter['search'])){
//                    $author_id = foody_search_user_by_name( $filter['search'] );
//                    if(!empty($author_id)){
//                        $page_args['author__in'] = [$author_id];
//                    }
//                }

				$query = $foody_search->build_query( $filter, $page_args, $sort );


				$next = $query->max_num_pages > $page;

				$foody_search->before_query();

				$posts = $query->get_posts();

				$foody_search->after_query();

				$grid = new FoodyGrid();

				$foody_posts = array_map( 'Foody_Post::create', $posts );

				$cols  = foody_get_array_default( $_POST, 'cols', 2 );
				$cols  = intval( $cols );

				if(($context == 'category' || $context == 'tag') && isset($_POST['referer']) && $_POST['referer']){
                    $args['feed_area_id'] = $_POST['referer'];
                    $items = $grid->loop( $foody_posts, $cols, false , null, [], null, $args);

				}
				else {
                    $items = $grid->loop($foody_posts, $cols, false);
                }

				$response = [
					'next'  => $next && strlen( $items ) > 0,
					'items' => $items,
					'found' => $query->found_posts
				];

			}
		}

	} else {
		$error = 'bad request';
	}

	if ( ! empty( $error ) ) {
		wp_send_json_error( [ 'message' => $error ], 400 );
	} elseif ( ! isset( $response ) ) {
		wp_send_json_error( [ 'message' => 'unknown error' ] );
	} else {
		wp_send_json_success( $response );
	}


}

add_action( 'wp_ajax_load_more', 'foody_ajax_load_more' );
add_action( 'wp_ajax_nopriv_load_more', 'foody_ajax_load_more' );