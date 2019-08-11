<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/25/19
 * Time: 11:29 AM
 */
class Foody_WhiteLabelTermDuplicatorProcess extends WP_Background_Process {
	/**
	 * @var string
	 */
	protected $action = 'foody_wl_term_duplicator';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed
	 * @throws Exception
	 */
	protected function task( $item ) {
		try {
			$taxonomy = $item['taxonomy'];
			$term_id  = $item['term_id'];

			Foody_WhiteLabelLogger::info( "starting task {$this->action}", $item );

			$duplicatedTerms = [
				'category',
				'post_tag'
			];

			// if this taxonomy can be duplicated
			if ( in_array( $taxonomy, $duplicatedTerms ) ) {

				$sites_to_copy_to = get_field( 'sites', "{$taxonomy}_$term_id" );

				if ( ! empty( $sites_to_copy_to ) ) {

					foreach ( $sites_to_copy_to as $site_to_copy_to ) {
						$blog_id       = $site_to_copy_to['foody_sites'];
						$copied_to_key = "copied_to_$blog_id";
						$copied        = get_term_meta( $term_id, $copied_to_key, true );
						if ( empty( $copied ) ) {
							if ( $taxonomy == 'post_tag' ) {
								$result = Foody_WhiteLabelDuplicator::duplicateTag( $term_id, $blog_id, $site_to_copy_to );
							} elseif ( $taxonomy == 'category' ) {
								$result = Foody_WhiteLabelDuplicator::duplicateCategory( $term_id, $blog_id, $site_to_copy_to );
							}

							if ( ! empty( $result['success'] ) ) {
								update_term_meta( $term_id, $copied_to_key, true );
							}
						}
					}
				}
			}
		} catch ( Exception $e ) {
			Foody_WhiteLabelLogger::exception( $e );
		}

		return false;
	}
}