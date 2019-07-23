<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/10/19
 * Time: 4:52 PM
 */

// async blog creation hook
add_action( 'wp_async_wpmu_new_blog', 'foody_do_duplicate_site', 10, 1 );
/**
 * Copy all relevant content to a newly created blog
 *
 * @param $blog_id
 */
function foody_do_duplicate_site( $blog_id ) {
	update_option( 'foody_site_duplication_in_progress', true );
	$max_execution_time = ini_get( 'max_execution_time' );
	ini_set( 'max_execution_time', 300 );
	Foody_WhiteLabelDuplicator::whiteLabelCreate( $blog_id );
	ini_set( 'max_execution_time', $max_execution_time );
}

if ( is_main_site() ) {

	// load async tasks
	add_action( 'init', 'foody_init_async_tasks' );
	/**
	 * Initializes async operations that take place
	 * on blog creation and content edit.
	 * Hooked into 'plugins_loaded'
	 * @see WP_Async_Task
	 */
	function foody_init_async_tasks() {
		try {
			new Foody_WhiteLabelDuplicatorTask();
			global $term_duplicator_process;
			$term_duplicator_process = new Foody_WhiteLabelTermDuplicatorProcess();
			global $author_duplicator_process;
			$author_duplicator_process = new Foody_WhiteLabelAuthorDuplicatorProcess();
		} catch ( Exception $e ) {
			Foody_WhiteLabelLogger::exception( $e );
		}
	}

	// auto sync core Foody post types
	add_action( 'wp_insert_post', 'foody_auto_sync_post', 10, 2 );
	/**
	 * Automatically create/update a post in all blogs
	 * when created/updated in main site.
	 *
	 * @param $post_id
	 * @param $post_object
	 *
	 * @throws Exception
	 * @see $foody_auto_synced_post_types
	 */
	function foody_auto_sync_post( $post_id, $post_object ) {
		global $foody_auto_synced_post_types;

		if ( ! in_array( $post_object->post_type, $foody_auto_synced_post_types ) ) {
			return;
		}

		// Check to see if we are autosaving
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( in_array( $post_object->post_status, array( 'auto-draft', 'inherit' ) ) ) {
			return;
		}

		// auto sync on main site only
		if ( get_current_blog_id() != get_main_site_id() ) {
			return;
		}

		$sites = get_sites( [ 'site__not_in' => get_main_site_id() ] );

		/** @var WP_Site $site */
		foreach ( $sites as $site ) {
			Foody_WhiteLabelDuplicator::duplicate( $post_object, $site->blog_id );
		}
	}


//    add_action('save_post', 'foody_sync_post_by_query', 10, 3);
//
//    function foody_sync_post_by_query($post_id, $post, $update)
//    {
//
//        if (!in_array($post->post_type, ['foody_recipe', 'post', 'foody_playlist'])) {
//            return;
//        }
//
//        $sites = get_sites(['site__not_in' => get_main_site_id()]);
//
//        /** @var WP_Site $site */
//        foreach ($sites as $site) {
//
//            $should_sync = false;
//
//            $post_categories = wp_get_post_categories($post_id);
//
//            $copied_to_site_key = "copied_to_{$site->blog_id}";
//
//            foreach ($post_categories as $post_category) {
//
//                if (get_term_meta($post_category, $copied_to_site_key, true)) {
//                    $should_sync = true;
//                    break;
//                }
//            }
//
//            if (!$should_sync){
//
//            }
//
//            Foody_WhiteLabelDuplicator::duplicate($post, $site->blog_id);
//        }
//    }


	// auto sync core Foody taxonomies
	add_action( 'edit_term', 'foody_auto_sync_term', 10, 3 );
	/**
	 * Automatically create/update a custom taxonomy in all blogs
	 * when created/updated in main site.
	 *
	 * @param $term_id
	 * @param $tt_id
	 * @param $taxonomy
	 *
	 * @see $foody_auto_synced_taxonomies
	 */
	function foody_auto_sync_term( $term_id, $tt_id, $taxonomy ) {
		global $foody_auto_synced_taxonomies;

		if ( ! in_array( $taxonomy, $foody_auto_synced_taxonomies ) ) {
			return;
		}

		// auto sync on main site only
		if ( get_current_blog_id() != get_main_site_id() ) {
			return;
		}

		$term  = get_term( $term_id, $taxonomy );
		$sites = get_sites( [ 'site__not_in' => get_main_site_id() ] );

		/** @var WP_Site $site */
		foreach ( $sites as $site ) {
			Foody_WhiteLabelDuplicator::duplicateTerm( $term, $site->blog_id );
		}
	}

	// copy term posts
	add_action( 'edit_term', 'foody_copy_posts_by_term', 10, 3 );
	/**
	 * Copy posts by term to a specific blog
	 *
	 * @param $term_id
	 * @param $taxonomy
	 *
	 * @throws Exception only locally
	 */
	function foody_copy_posts_by_term( $term_id, $tt_id, $taxonomy ) {
		global $term_duplicator_process;
		try {
			$term_duplicator_process
				->push_to_queue( [ 'taxonomy' => $taxonomy, 'term_id' => $term_id ] )
				->save()
				->dispatch();
		} catch ( Exception $e ) {
			Foody_WhiteLabelLogger::exception( $e );
		}

	}

	// Copy author posts
	add_action( 'edit_user_profile_update', 'foody_copy_posts_by_author' );
	/**
	 * Copy posts by authors to a specific blog
	 *
	 * @param $user_id
	 *
	 * @throws Exception only locally
	 */
	function foody_copy_posts_by_author( $user_id ) {
		global $author_duplicator_process;
		try {
			$author_duplicator_process
				->push_to_queue( [ 'user_id' => $user_id ] )
				->save()
				->dispatch();
		} catch ( Exception $e ) {
			Foody_WhiteLabelLogger::exception( $e );
		}

	}


//    add_action('wp_insert_post', 'foody_duplicate_post', 10, 2);

	function foody_duplicate_post( $post_id, $post_object ) {

		if ( ! in_array( $post_object->post_type, [ 'foody_recipe', 'foody_playlist', 'post' ] ) ) {
			return;
		}

		// Check to see if we are autosaving
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( in_array( $post_object->post_status, array( 'auto-draft', 'inherit' ) ) ) {
			return;
		}

		// auto sync on main site only
		if ( get_current_blog_id() != get_main_site_id() ) {
			return;
		}

		$sites_for_post = Foody_WhiteLabelPostMapping::getByPost( $post_id );

		if ( ! empty( $sites_for_post ) ) {
			$sites_for_post = array_map( function ( $site ) {
				return isset( $site['blog_id'] ) ? $site['blog_id'] : null;
			}, $sites_for_post );
		} else {
			$sites_for_post = [];
		}

		$excluded_sites = array_merge( $sites_for_post, [ get_main_site_id() ] );
		$sites          = get_sites( [ 'site__not_in' => $excluded_sites ] );

		if ( ! empty( $sites ) ) {
			/** @var WP_Site $site */
			foreach ( $sites as $site ) {

				$copy_to_site = get_post_meta( $post_id, "copy_to_{$site->blog_id}", true );

				if ( $copy_to_site ) {
					Foody_WhiteLabelDuplicator::duplicate( $post_object, $site->blog_id );
				}
			}
		}
	}
}



