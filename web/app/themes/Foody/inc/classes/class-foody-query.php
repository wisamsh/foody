<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 11:02 AM
 *
 *
 * This singleton is responsible for providing
 * handler methods related to querying the database using the @see WP_Query class.
 * Each method adds the relevant query for its context.
 * For example, the method @see Foody_Query::category() ads a 'cat' query to the final
 * query that will finally be executed.
 */
class Foody_Query {

	private static $default_args;

	private static $instance;

	public static $page = 'page';

	public static $query_prefix = 'fd_';

	public static $query_params = [
		'in' => 'ingredients',
		'ca' => 'categories',
		'te' => 'techniques',
		'li' => 'limitations',
		'ac' => 'accessories',
		'ta' => 'tags',
		'au' => 'authors'
	];

	public static $filter_query_arg = 'filter';

	/**
	 * Foody_Query constructor.
	 */
	private function __construct() {
		self::$default_args = [
			'posts_per_page' => get_option( 'posts_per_page' ),
			'post_status'    => 'publish',
			'post_type'      => [ 'foody_recipe', 'foody_playlist', 'post' ]
		];
		if ( is_front_page() ) {
			self::$page = 'page';
		} else {
			self::$page = apply_filters( 'foody_page_query_var', self::$page );
		}
	}

	public static function get_query_params() {
		$ret_val = [];
		foreach ( self::$query_params as $query_param => $type ) {
			$ret_val[ self::$query_prefix . $query_param ] = $type;
		}

		return $ret_val;
	}

	private function parse_query_args( $context, $context_args, $wp_args = [], Foody_Search $search = null ) {
		if ( ! is_null( $search ) ) {
			$foody_search = $search;
		} else {
			$foody_search = new Foody_Search( $context, $context_args );
		}

		$args = [
			'types'             => $this->_parse_query_values(),
			'after_foody_query' => true
		];

		return $foody_search->build_query( $args, $wp_args, '', true );
	}

	private function foody_get_query_arg( $key ) {
		$value = '';

		if ( isset( $_GET[ $key ] ) ) {
			$value = $_GET[ $key ];
		}

		return $value;
	}

	private function _parse_query_values() {
		// return value
		$filter_types = [];

		$filter_value = $this->array_query_string_to_array( self::$filter_query_arg );
		if ( empty( $filter_value ) ) {
			if ( isset( $_POST[ self::$filter_query_arg ] ) && ! isset( $_POST[ self::$filter_query_arg ]['types'] ) && ! isset( $_POST[ self::$filter_query_arg ]['context'] ) ) {
				$filter_value = $_POST[ self::$filter_query_arg ];
				if ( ! empty( $filter_value ) && ! is_array( $filter_value ) ) {
					$filter_value = [ $filter_value ];
				}
			}
		}
		if ( ! empty( $filter_value ) ) {
			// TODO change post id to be relevant to current page
			$filter_options = get_field( 'filters_list', get_filters_id() );
			$all_options    = [];

			// iterate all filter options set in
			// Foody search settings
			// 1st loop is for filter sections
			foreach ( $filter_options as $filter_option ) {
				// safety
				if ( ! empty( $filter_option ) && ! empty( $filter_option['values'] ) ) {
					// 2nd loop - filter items in each section
					foreach ( $filter_option['values'] as $key => $value ) {

						// this is the general list type (the type assigned to the entire list)
						$filter_option['values'][ $key ]['type'] = $filter_option['type'];

						// if 'value_group' is set it means 'switch type'
						// was used in order to override the general list type ($filter_option['type']), so
						// here we assign the proper type to the item
						if ( ! empty( $filter_option['values'][ $key ]['value_group'] ) && ! empty( $filter_option['values'][ $key ]['switch_type'] ) ) {
							$filter_option['values'][ $key ]['type'] = $filter_option['values'][ $key ]['value_group']['type'];
						}
						// if 'value' is not set -> use value from the 'value_group';
						if ( empty( $filter_option['values'][ $key ]['value'] ) ) {
							if ( ! empty( $filter_option['values'][ $key ]['value_group'] ) ) {
								$filter_option['values'][ $key ]['value'] = $filter_option['values'][ $key ]['value_group']['value'];
							}
						}

						// exclude if item is set to exclude or if the entire list
						// is set to exclude (negative search)
						$filter_option['values'][ $key ]['exclude'] = $filter_option['values'][ $key ]['exclude'] || $filter_option['exclude_all'];

						// if no title is set -> use the title from the
						// entity itself (e.g category,limitation,author, etc).
						// See SidebarFilter::get_item_title for more details
						if ( empty( $filter_option['values'][ $key ]['title'] ) ) {
							$id        = $filter_option['values'][ $key ]['value'];
							$item_type = $filter_option['values'][ $key ]['type'];

							$filter_option['values'][ $key ]['title'] = SidebarFilter::get_item_title( $id, $item_type );
						}
					}
					// create a flat array from parsed options
					$all_options = array_merge( $all_options, $filter_option['values'] );
				}
			}

			// iterate filter arguments passed from url ($_GET)
			foreach ( $filter_value as $value ) {

				// match filter item by title
				$filter_option = foody_array_find( $all_options, function ( $filter_item ) use ( $value ) {
					return $filter_item && $filter_item['title'] == urldecode( $value );
				} );

				if ( ! empty( $filter_option ) ) {
					if ( empty( $filter_option['value'] ) ) {
						if ( ! empty( $filter_option['value_group']['value'] ) ) {
							$filter_option['value'] = $filter_option['value_group']['value'];
						}
					}
					$filter_types[] = $filter_option;
				}
			}

			$bad_value = foody_array_find( $filter_types, function ( $item ) {
				return empty( $item ) || empty( $item['value'] ) || empty( $item['type'] ) || empty( $item['title'] );
			} );

			if ( ! empty( $bad_value ) ) {
				throw new Exception( 'bad filter value' . strval( $bad_value ) );
			}

		}

		return $filter_types;
	}

	/**
	 * @param $key string key in $_GET
	 * @param string $delimiter used to separate multiple values, defaults to ','
	 *
	 * @return array
	 */
	private function array_query_string_to_array( $key, $delimiter = ',' ) {
		$arr   = [];
		$value = $this->foody_get_query_arg( $key );
		if ( ! empty( $value ) ) {
			$arr = array_map( function ( $val ) {
				return trim( $val );
			}, explode( $delimiter, $value ) );
			if ( is_array( $arr ) && ! empty( $arr ) ) {
				$arr = array_filter( $arr, function ( $value ) {
					return ! empty( $value );
				} );
			}
		}

		return $arr;
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Foody_Query();
		}

		return self::$instance;
	}

	public function homepage() {
		$args = self::get_args();

		$featured = get_field( 'featured_items', get_option( 'page_on_front' ) );
		$featured = Foody_HomePage::get_relevant_posts($featured);
		if ( ! empty( $featured ) && (! empty( $featured[0] ) || ! empty( $featured[1]))) {
			$args['post__not_in'] = array_map( function ( $row ) {
				return $row['post']->ID;
			}, $featured );
		}

		return $args;
	}

	public function white_label_homepage() {
		$args = self::get_args();

		return $args;
	}

	public function category( $id ) {
		$args = self::get_args( [
			'cat' => $id
		] );

		return $args;
	}

	public function author( $id, $post_type ) {
		$id         = intval( $id );
		$post_types = [
			$post_type,
			'post'
		];
		$args       = self::get_args( [
			'post_type' => $post_types,
			'author'    => $id
		] );

		return $args;
	}

	public function feed_channel() {
		return self::get_args();
	}

	public function foody_filter( $filter_post_id ) {

		$filter = get_field( 'filters_list', $filter_post_id );

		$types = SidebarFilter::parse_search_args_array( $filter );

		$args = [
			'types'             => $types,
			'sort'              => 'popular_desc',
			'after_foody_query' => true,
			'post_type'         => 'foody_recipe'
		];

		$foody_search = new Foody_Search( 'foody_filter' );

		$posts = $foody_search->query( $args, [ 'posts_per_page' => - 1 ] )['posts'];

		if ( is_array( $posts ) ) {
			$posts = array_filter( $posts, function ( $post ) {
				return $post instanceof WP_Post;
			} );

			$posts = array_map( function ( $post ) {
				return $post->ID;
			}, $posts );
		}

		return self::get_args( [
			'post__in' => $posts
		] );
	}

	public function foody_commercial_rule( $filter ) {

		$types = SidebarFilter::parse_search_args_array( $filter );

		$args = [
			'types'             => $types,
			'sort'              => 'popular_desc',
			'after_foody_query' => true
		];

		$foody_search = new Foody_Search( 'foody_commercial_rule' );

		$posts = $foody_search->query( $args, [ 'posts_per_page' => - 1 ] )['posts'];

		if ( is_array( $posts ) ) {
			$posts = array_filter( $posts, function ( $post ) {
				return $post instanceof WP_Post;
			} );

			$posts = array_map( function ( $post ) {
				return $post->ID;
			}, $posts );
		}

		return self::get_args( [
			'post__in' => $posts
		] );
	}

	public function foody_ingredient( $ingredient_post_id ) {


		add_filter( 'posts_where', 'ingredient_posts_where', 10, 2 );

		$args = [
			'has_wildcard_key' => true,
			'post_type'        => 'foody_recipe',
			'meta_query'       => [
				[
					'key'     => 'ingredients_ingredients_groups_$_ingredients_$_ingredient',
					'compare' => '=',
					'value'   => $ingredient_post_id
				]

			],
			'fields'           => 'ids',
			'posts_per_page'   => - 1
		];


		$query = new WP_Query( $args );

		$posts = $query->get_posts();

		remove_filter( 'posts_where', 'ingredient_posts_where' );

		$args = [
			'post__in' => $posts
		];

		if ( count( $posts ) == 0 ) {
			$args = null;
		}

		return self::get_args( $args );
	}

	public function foody_accessory( $accessory_post_id ) {
		$meta_query = [
			[
				'key'     => 'accessories_accessories',
				'compare' => 'LIKE',
				'value'   => '"' . $accessory_post_id . '"'
			]
		];


		$args = [
			'meta_query' => $meta_query,
			'post_type'  => 'foody_recipe'
		];

		return $args;
	}

	public function foody_technique( $technique_post_id ) {
		$meta_query   = [];
		$meta_query[] = [
			'key'     => 'techniques_techniques',
			'compare' => 'LIKE',
			'value'   => '"' . $technique_post_id . '"'
		];

		$args = [
			'meta_query' => $meta_query,
			'post_type'  => 'foody_recipe'
		];

		return $args;
	}

	public function limitations( $limitation_id ) {
		$meta_query = [
			[
				'key'     => 'limitations',
				'compare' => 'LIKE',
				'value'   => '"' . $limitation_id . '"'
			]
		];


		$args = [
			'meta_query' => $meta_query,
			'post_type'  => 'foody_recipe'
		];

		return $args;
	}

	public function search() {
		$search_term = get_search_query();
		$search_term = html_entity_decode( $search_term );
		$search_term = esc_sql( $search_term );
		$search_term = urldecode( $search_term );

		global $wpdb;
		$search_term = $wpdb->esc_like( $search_term );
		if ( empty( $search_term ) ) {
			if ( isset( $_POST['filter']['search'] ) ) {
				$search_term = $_POST['filter']['search'];

			} elseif ( isset( $_POST['data']['search'] ) ) {
				$search_term = $_POST['data']['search'];

			}
		}

		$args = self::get_args( [
			'post_type' => [ 'foody_recipe', 'foody_playlist', 'post' ],
			's'         => $search_term
		] );


		return $args;
	}

	public function tag( $id ) {
		$args = self::get_args( [
			'tag_id' => $id
		] );

		return $args;
	}

	public function purchase_buttons( $post_id ) {
		return self::get_args( [
			'post__in' => [
				$post_id
			]
		] );
	}

	public function profile( $content_type ) {
		$user = new Foody_User();

		if ( ! $user->user->ID ) {
			return [];
		}

		$args = [];

		if ( $content_type == 'favorites' ) {
			$posts = $user->favorites;
			if ( ! empty( $posts ) ) {
				$posts                  = array_map( 'intval', array_values( $posts ) );
				$args['posts_per_page'] = count( $posts );
			}
		} elseif ( $content_type == 'channels' ) {
			$posts = $user->get_followed_content();
			if ( ! empty( $posts ) ) {
				$posts = array_map( function ( $post ) {
					$retval = $post;
					if ( $post instanceof WP_Post || $post instanceof stdClass ) {
						$retval = $post->ID;
					}

					return $retval;
				}, $posts );
			}
		}

		if ( ! isset( $posts ) ) {
			return [];
		}

		$args = array_merge( [
			'post__in' => $posts,
//            'post_type' => ['foody_recipe', 'foody_playlist'],
			'orderby'  => 'date',
			'order'    => 'DESC'
		], $args );

		return self::get_args( $args );
	}

	public function channel( $channel_id ) {
		$posts = get_field( 'related_recipes', $channel_id );

		if ( empty( $posts ) ) {
			$posts = [];
		}

		return $this->post_ids( $posts );
	}

	public function post_ids( $ids ) {
		$ids = array_map( function ( $id ) {
			if ( $id instanceof WP_Post ) {
				$id = $id->ID;
			} elseif ( is_array( $id ) ) {
				if ( isset( $id['ID'] ) ) {
					$id = $id['ID'];
				}
			}

			return $id;
		}, $ids );

		$ids  = array_filter( $ids, 'is_numeric' );
		$args = [
			'post__in' => $ids
		];

		return self::get_args( $args );
	}

	public function has_more_posts( WP_Query $query ) {
		$current_page = intval( $query->get( 'paged', 0 ) );

		$max = $query->max_num_pages;

		if ( $max > $current_page ) {
			if ( $current_page == 0 ) {
				$options_per_page = $this->get_posts_per_page();
				$query_per_page   = intval( $query->get( 'posts_per_page' ) );
				$query->set( 'posts_per_page', $options_per_page );
				$query->get_posts();
				$max          = $query->max_num_pages;
				$current_page = $query_per_page / $options_per_page;
			}
		}

		return $max > $current_page;
	}

	private function get_posts_per_page() {
		return intval( get_option( 'posts_per_page' ) );
	}

	private function get_args( $args = [] ) {
		return array_merge(
			self::$default_args,
			$args
		);
	}

	private function get_param( $variableName, $default = null ) {

		// Was the variable actually part of the request
		if ( array_key_exists( $variableName, $_REQUEST ) ) {
			return $_REQUEST[ $variableName ];
		}

		// Was the variable part of the url
		$urlParts = explode( '/', preg_replace( '/\?.+/', '', $_SERVER['REQUEST_URI'] ) );
		$position = array_search( $variableName, $urlParts );
		if ( $position !== false && array_key_exists( $position + 1, $urlParts ) ) {
			return $urlParts[ $position + 1 ];
		}

		return $default;
	}

	public static function get_search_url( $search_term ) {
		$post_types = [ 'foody_playlist', 'foody_recipe', 'post' ];

		global $wpdb;

		$base = home_url();

		$base = add_query_arg( 's', $wpdb->prepare( $search_term, [] ), $base );

//        foreach ($post_types as $post_type) {
//            $base = add_query_arg('post_type', $post_type, $base);
//        }

		return $base;
	}


	/**
	 * @param $context
	 * @param array $context_args
	 * @param bool $ranged
	 *
	 * @param Foody_Search $search
	 *
	 * @return mixed|WP_Error
	 */
	public function get_query( $context, $context_args = [], $ranged = false, Foody_Search $search = null ) {
		if ( method_exists( $this, $context ) ) {
			$fn = array( $this, $context );

			if ( ! is_array( $context_args ) ) {
				$context_args = array( $context_args );
			}

			$foody_args = call_user_func_array( $fn, $context_args );
			if ( $foody_args != null ) {
				$page = get_query_var( 'paged' );

				if ( ! $page ) {
					if ( isset( $_REQUEST[ self::$page ] ) ) {
						$page = $_REQUEST[ self::$page ];
					} else {
						$page = $this->get_param( self::$page );
						if ( ! $page ) {
							if ( is_single() ) {
								$page = get_query_var( 'page' );
							} else {
								$page = 1;
							}
						}
					}
				}

				$foody_args['paged'] = $page;

				if ( $ranged ) {
					$foody_args['posts_per_page'] = $this->get_posts_per_page() * $page;
					$foody_args['paged']          = 0;
				}
			}


		} else {
			$foody_args = new WP_Error( "invalid context: $context" );
		}

		if ( $foody_args != null ) {
			$filter_args = $this->parse_query_args( $context, $context_args, $foody_args, $search );

			$foody_args = array_merge( $filter_args, $foody_args );
		}

		return $foody_args;
	}

}