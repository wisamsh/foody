<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/24/19
 * Time: 4:45 PM
 */

class Foody_Feed_Channel extends Foody_Post implements Foody_Topic {

	private $foody_search;
	private $foody_query;
	private $blocks_drawer;

	/**
	 * Foody_Feed_Channel constructor.
	 */
	public function __construct( $post ) {
		parent::__construct( $post );
		$this->foody_search  = new Foody_Search( 'feed_channel' );
		$this->foody_query   = Foody_Query::get_instance();
		$this->blocks_drawer = new Foody_Blocks( $this->foody_search );
		add_action( 'foody_after_body', [ $this, 'the_background_image' ] );
	}

	public function before_content() {
		$cover_image  = get_field( 'cover_image' );
		$mobile_image = get_field( 'mobile_cover_image' );

		foody_get_template_part( get_template_directory() . '/template-parts/content-cover-image.php', [
			'image'        => $cover_image,
			'mobile_image' => $mobile_image
		] );
	}

	public function the_details() {
		bootstrap_breadcrumb();
		the_title( '<h1 class="title">', '</h1>' );
		foody_get_template_part( get_template_directory() . '/template-parts/content-feed-channel-details.php', [
			'feed_channel' => $this
		] );
	}

	/**
	 * Custom css to control channel related design
	 */
	public function the_css() {
		$titles_color = get_field( 'titles_color', $this->id );
		if ( ! empty( $titles_color ) ) {

			if ( preg_match( '/^#/', $titles_color ) === false ) {
				$titles_color = "#$titles_color";
			}

			?>
            <style id="feed-style">
                .title {
                    color: <?php echo $titles_color?>;
                }

                a:hover {
                    color: <?php echo $titles_color ?>;
                }

                .block-see-more a {
                    color: <?php echo $titles_color ?>;
                }
            </style>
			<?php
		}
	}

	public function the_background_image() {
		$background_image = get_field( 'background_image', $this->id );

		if ( ! empty( $background_image ) ) {
			?>
            <img src="<?php echo $background_image['url'] ?>" alt=""
                 style=" position: fixed; top: 0; left: 0; min-width: 100%; min-height: 100%;">
			<?php
		}

	}

	public function blocks() {
		$blocks = get_field( 'blocks', $this->id );

		if ( ! empty( $blocks ) ) {

			foreach ( $blocks as $block ) {
				$type = $block['type'];

				if ( ! empty( $type ) ) {
					$this->blocks_drawer->validate_block( $block );

					$block_fn = "draw_{$type}_block";
					if ( method_exists( $this->blocks_drawer, $block_fn ) ) {
						$block_options = call_user_func( [ $this->blocks_drawer, $block_fn ], $block );
						if ( ! empty( $block_options ) && ! empty( $block_options['content'] ) ) {
							$this->blocks_drawer->wrap_block( $block_options );
						}
					}
				}
			}
		}
	}


	public function the_sidebar_content( $args = array() ) {
		if ( get_field( 'hide_widgets', $this->id ) === false ) {
			parent::the_sidebar_content( $args );
		}
	}

	function topic_image() {

	}

	function topic_title() {

	}

	function get_followers_count() {

	}

	function get_description() {

	}

	function get_type() {
		return 'feed_channels';
	}

	function get_breadcrumbs_path() {

	}
}