<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:41 PM
 */
class FoodyGrid {

	const NONE = 1000;

	private $is_in_loop = false;
	private $current_item = 0;

	private $supported_types = [
		'recipe',
		'playlist',
		'article',
		'post',
        'feed_channel'
	];


	/**
	 * RecipesGrid constructor.
	 */
	public function __construct() {

	}


	/**
	 * @param $post Foody_Post
	 * @param $col_num
	 * @param int $col_num_mobile
	 * @param bool $echo
	 * @param string $type
	 * @param null $responsive
	 * @param array $args
	 *
	 * @return string the item html
	 * @throws Exception
	 */
	public function draw( $post, $col_num, $col_num_mobile = 12, $echo = true, $type = 'recipe', $responsive = null, $args = [] ) {
		if ( ! in_array( $type, $this->supported_types ) ) {
			return '';
		}
		if ( $col_num == 0 ) {
			$col_num = self::NONE;
		} elseif ( 12 % $col_num != 0 ) {
			throw new Exception( "RecipesGrid:  invalid col_num $col_num" );
		}

		if ( is_null( $responsive ) ) {
			$responsive = [
				'mobile'   => 'col-12',
				'tablet'   => 'col-sm-6',
				'tablet_l' => 'col-md-6',
			];
		}

		$class = '';
		if ( $col_num != self::NONE ) {
			$grid_col = 12 / $col_num;
			$class    = 'col-xl-' . $grid_col;
			$class    .= ' ' . $responsive['tablet_l'];
			$class    .= ' ' . $responsive['tablet'];
			$class    .= ' col-' . $col_num_mobile;
		}

		$class .= ' grid-item';


		$container_start = '<div data-title="' . $post->getTitle() . '" data-id="' . $post->id . '"  class="' . $class . ' ' . $type . '-item-container" data-sort="' . $post->getTitle() . '" data-order="' . $this->current_item . '">';
		$container_end   = '</div>';

		$item_content = $container_start;

		$item_content .= foody_get_template_part(
			get_template_directory() . '/template-parts/content-' . $type . '-list-item.php',
			[
				'post'   => $post,
				'args'   => $args,
				'lazy'   => true,
				'return' => true
			]
		);

		$item_content .= $container_end;

		if ( $echo ) {
			echo $item_content;
		}

		return $item_content;

	}

	public function grid_debug( $items_count, $col_num ) {

		for ( $i = 0; $i < $items_count; $i ++ ) {
			$this->draw( new Foody_Recipe(), $col_num );
		}
	}

	/**
	 * @param $posts Foody_Post[]
	 * @param $cols
	 * @param bool $echo
	 * @param string $type
	 * @param array $data_attrs
	 * @param null $responsive
	 * @param array $args
	 *
	 * @return string
	 * @throws Exception
	 */
	public function loop( $posts, $cols, $echo = true, $type = null, $data_attrs = [], $responsive = null, $args = [] ) {
		$items            = '';
		$this->is_in_loop = true;
		$reset_type       = false;
		foreach ( $posts as $post ) {

			if ( is_null( $type ) ) {
				$reset_type = true;
				$type       = $post->post->post_type;
				$type       = str_replace( 'foody_', '', $type );

				if ( $type == 'post' ) {
					$type = 'article';
				}
			}

			$items .= $this->draw( $post, $cols, 12, $echo, $type, $responsive, $args );
			$this->current_item ++;
			if ( $reset_type ) {
				$reset_type = false;
				$type       = null;
			}

		}
		$this->current_item = 0;
		$this->is_in_loop   = false;

		return $items;
	}

	/**
	 * @param Foody_Post $post
	 *
	 * @return bool
	 */
	public function is_post_displayable( $post ) {

		$type = $post->post->post_type;
		if ( $type == 'post' ) {
			$type = 'article';
		}

		$type = str_replace( 'foody_', '', $type );

		return in_array( $type, $this->supported_types );
	}


	/**
	 * @param Foody_Post[] $posts
	 *
	 * @return bool
	 */
	public function is_displayable( $posts ) {
		return count( array_filter( $posts, array( $this, 'is_post_displayable' ) ) ) > 0;
	}

}