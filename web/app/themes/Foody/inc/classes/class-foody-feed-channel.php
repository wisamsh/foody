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
			'mobile_image' => $mobile_image,
            'type'         => 'foody_feed_channel'
		] );
	}

	public function the_details() {
		bootstrap_breadcrumb();
		the_title( '<h1 class="title">', '</h1>' );
		if (isset($this->id)){
            if( !empty(get_field('blocks', $this->id)[0]['items']) ) {
                $blocks = get_field( 'blocks', $this->id );
                $count_manual=0;
                $count_dynamic=0;
                foreach ($blocks as $block) {
                    if ($block['type'] === 'manual' ) {
                        $count_manual ++;
                    }
                    if ($block['type'] === 'dynamic'){
                        $count_dynamic ++;
                    }
                }
                if ( $count_manual === 1 && get_current_blog_id() === 1 ) {
                    if ( $count_dynamic === 0){
                        // mobile filter
                        foody_get_template_part( get_template_directory() . '/template-parts/common/mobile-feed-filter.php', [
                            'sidebar' => array( $this, 'sidebar' ),
                            'wrap'    => true
                        ] );
                    }

                }
            }
        }

		if ( foody_is_registration_open() && ! empty( get_option( 'foody_show_followers_count_views' ) ) ) {
			echo '<span class="followers-count">' . $this->get_followers_count() . '</span>';
		}
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
					    //if($logo = get_field( 'feed_logo', $this->post->ID )){
					        $block['feed_area_id'] = $this->post->ID;
                        //}
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
	    if( isset($this->id) ){
            if( !empty(get_field('blocks', $this->id)[0]['items']) ) {
                $blocks = get_field( 'blocks', $this->id );
                $count_manual=0;
                $count_dynamic=0;
                foreach ($blocks as $block) {
                    if ($block['type'] === 'manual' ) {
                        $count_manual ++;
                    }
                    if ($block['type'] === 'dynamic'){
                        $count_dynamic ++;
                    }
                }
                if ( $count_manual === 1 ) {
                    if ( $count_dynamic === 0 ){

                    ?>
                    <section class="sidebar-section foody-search-filter">
                        <?php
                        $foody_query = SidebarFilter::get_instance();
                        $foody_query->the_filter();
                        ?> </section>
                <?php }
                }
            }
        }
	}

	function topic_image($size = 96) {

	}

	function topic_title() {

	}

	function get_followers_count() {
		$query = new WP_User_Query( [
			'meta_query'  => [
				[
					[
						'key'     => 'followed_feed_channels',
						'value'   => '"' . $this->getId() . '"',
						'compare' => 'LIKE'
					]
				]
			],
			'meta_key'    => 'followed_feed_channels',
			'count_total' => true
		] );

		$total = $query->get_total();

		return view_count_display( $total, 0, null, '%s עוקבים' );
	}

	function get_description() {

	}

	function get_type() {
		return 'feed_channels';
	}

	function get_breadcrumbs_path() {

	}
}