<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/24/19
 * Time: 4:45 PM
 */

class Foody_Blocks
{
    /**
     * @var $foody_search Foody_Search
    */
    private $foody_search;

    /**
     * Foody_Feed_Channel constructor.
     */
    public function __construct($foody_search)
    {
        if (isset($foody_search)) {
            $this->foody_search = $foody_search;
        }
    }

    /*
     *  All draw_{type}_block methods below must return
     *  and the block options including the following:
     *  - title
     *  - see more text
     *  - see more link
     *  - block html content
     *  These methods are meant to be used in conjunction with wrap_block()
     *  method in order to create a mutual block html structure
     * */

	/**
	 *
	 * @uses Foody_Feed_Channel::get_dynamic_block_posts()
	 * @uses Foody_Feed_Channel::get_manual_block_posts()
	 *
	 * @param $block
	 *
	 * @return array the block options
	 * @throws Exception if query filter is wrong
	 */
	public function get_dynamic_block_posts( $block ) {
		$posts = [];

		/** @var WP_Post $filter_post */
		$filter_post = isset( $block['filter'] ) ? $block['filter'] : null;


		if ( ! empty( $filter_post ) && $filter_post ) {

			$filter = get_field( 'filters_list', $filter_post->ID );

			$types = SidebarFilter::parse_search_args_array( $filter );

			$args = [
				'types' => $types,
				'sort'  => 'popular_desc'
			];

			$posts = $this->foody_search->query( $args )['posts'];
		}

		return $posts;
	}

	/**
     *
     * @uses Foody_Feed_Channel::draw_dynamic_block()
     * @uses Foody_Feed_Channel::draw_manual_block()
     * @uses Foody_Feed_Channel::draw_categories_block()
     * @uses Foody_Feed_Channel::draw_banner_block()
     * @uses Foody_Feed_Channel::draw_product_block()
     *
     * @param $block
     *
     * @return array the block options
     * @throws Exception if query filter is wrong
     */
	public function draw_dynamic_block( $block ) {

		$block_options = [];
		$filter_post   = isset( $block['filter'] ) ? $block['filter'] : null;

		if ( ! empty( $filter_post ) && $filter_post ) {

			$title = $block['title'];

			if ( empty( $title ) ) {
				$title = $filter_post->post_title;
			}

			$posts = $this->get_dynamic_block_posts( $block );

			if ( ! empty( $posts ) && is_array( $posts ) ) {
				$posts = array_map( 'Foody_Post::create', $posts );
				if ( count( $posts ) > 4 ) {
					$posts = array_slice( $posts, 0, 4 );
				}
				$block_content = foody_get_template_part( get_template_directory() . '/template-parts/common/foody-grid.php', [
					'id'     => uniqid(),
					'posts'  => $posts,
					'cols'   => 2,
					'more'   => false,
					'header' => [
						'title' => ''
					],
					'return' => true
				] );

				$see_more_link = $block['see_more_link'];
				if ( empty( $see_more_link ) ) {
					$see_more_link = [ 'url' => get_permalink( $filter_post->ID ) ];
				}

				$see_more_text = $block['see_more_text'];

				$block_options = [
					'title'         => $title,
					'see_more_target' => isset( $see_more_link['target'] ) && ! empty( $see_more_link['target'] ) ? $see_more_link['target'] : '',
				    'see_more_link' => $see_more_link['url'],
					'see_more_text' => $see_more_text,
					'content'       => $block_content
				];
			}
		}


		return $block_options;
	}

    public function draw_categories_block($block, $block_id = '')
    {
        $items = $block['items'];

	    $block_id = ! empty( $block_id ) ? ' id="' . $block_id . '"' : '';

        $block_options = [];

        if (!empty($items) && is_array($items)) {

            $items = array_filter($items, function ($item) {
                return $item['category'] instanceof WP_Term;
            });

            $items = array_map(function ($item) {

                /**
                 * @var $title
                 * @var $image
                 * @var $mobile_image
                 * @var $link
                 * @var $title
                 * @var $category WP_Term
                 */
                extract($item);


                if (empty($title)) {
                    $title = $category->name;
                }

                if (empty($image)) {
                    $image = get_field('image', $category->taxonomy . '_' . $category->term_id);
                    if (empty($image)) {
                        $image = ['url' => ''];
                    }
                }

                $image = $image['url'];

                if (empty($mobile_image)) {
                    $mobile_image = $image;
                } else {
                    $mobile_image = $mobile_image['url'];
                }


                if (empty($link)) {
                    $link = ['url' => get_term_link($category->term_id)];
                }

                $link = $link['url'];
                $return = true;
                $item_args = compact('title', 'image', 'link', 'mobile_image', 'return');

                return $item_args;

            }, $items);

            $items_content = implode('', array_map(function ($item) {
                return foody_get_template_part(get_template_directory() . '/template-parts/content-category-listing.php', $item);
            }, $items));

            if (isset($block['show_more_flag']) && $block['show_more_flag']) {

                $show_more_link = !empty($block['see_more_link']['url']) ? $block['see_more_link']['url'] : '';
                $items_content = "<section class='categories-block-content categories-listing show-more row' data-more-link='".esc_attr($show_more_link)."' data-count=''" . $block_id . ">$items_content</section>";
            } else {
                $items_content = "<section class='categories-block-content categories-listing block-more row' " . $block_id . ">$items_content</section>";
            }

            $title = $block['title'];

            $see_more_text = $block['see_more_text'];
            $see_more_link = $block['see_more_link'];

            if (empty($see_more_link)) {
                $see_more_link = ['url' => ''];
            }

            if (empty($see_more_text)) {
                $see_more_text = $see_more_link['title'];
            }

            $block_options['title'] = $title;
	        $block_options['see_more_target'] = isset( $see_more_link['target'] ) && ! empty( $see_more_link['target'] ) ? $see_more_link['target'] : '';
            $block_options['see_more_text'] = $see_more_text;
            $block_options['see_more_link'] = $see_more_link['url'];
            $block_options['content'] = $items_content;
        }

        return $block_options;
    }

    public function draw_banner_block($block)
    {
        /**
         * @var $image
         * @var $link
         */
        extract($block['banner']);

        $block_options = [
            'hide_header' => true
        ];
        if (!empty($image)) {
            $block_options['content'] = foody_get_template_part(
                get_template_directory() . '/template-parts/content-banner.php',
                [
                    'image' => $image,
                    'link' => $link,
                    'return' => true
                ]
            );
        }

        return $block_options;
    }

    public function draw_manual_block($block)
    {

        $items = $block['items'];

        $block_options = [];

        if (!empty($items) && is_array($items)) {

            $items = array_filter($items, function ($item) {
                return $item['post'] instanceof WP_Post;
            });

            $items = array_map(function ($item) {

                /**
                 * @var $title
                 * @var $secondary_text
                 * @var $image
                 * @var $mobile_image
                 * @var $link
                 * @var $title
                 * @var $post WP_Post
                 */
                extract($item);


                $foody_post = Foody_Post::create($post);

                if (!empty($title)) {
                    $foody_post->setTitle($title);
                }

                if (!empty($image)) {

                    $foody_post->setImage($image['url']);
                }


                if (!empty($link)) {
                    $foody_post->link = $link['url'];
                    $foody_post->link_attrs = $link;
                }

                if (!empty($secondary_text)) {
                    $foody_post->setDescription($secondary_text);
                }


                return $foody_post;

            }, $items);

            $grid_args = [
                'id' => uniqid(),
                'more' => false,
                'cols' => 2,
                'posts' => $items,
                'return' => true
            ];

            $items_content = foody_get_template_part(get_template_directory() . '/template-parts/common/foody-grid.php', $grid_args);

            $items_content = "<section class='categories-block-content categories-listing row'>$items_content</section>";

            $title = $block['title'];

            $see_more_text = $block['see_more_text'];
            $see_more_link = $block['see_more_link'];

            if (empty($see_more_link)) {
                $see_more_link = ['url' => ''];
            }

            $block_options['title'] = $title;
	        $block_options['see_more_target'] = isset( $see_more_link['target'] ) && ! empty( $see_more_link['target'] ) ? $see_more_link['target'] : '';
	        $block_options['see_more_text'] = $see_more_text;
            $block_options['see_more_link'] = $see_more_link['url'];
            $block_options['content'] = $items_content;
        }

        return $block_options;
    }

    public function draw_product_block($block)
    {

        $product = $block['product'];

        $block_options = [];

        if (!empty($product) && is_array($product)) {

            $items_content = foody_get_template_part(get_template_directory() . '/template-parts/white-label/content-foody-product.php', ['product' => $product]);

            $block_options['content'] = $items_content;
        }

        return $block_options;
    }

    /**
     * Outputs a generic block html structure.
     *
     * @param $block_options array
     */
    public function wrap_block($block_options)
    {

        /**
         * @var string $title
         * @var string $see_more_link
         * @var string $see_more_text
         * @var string $content
         */
        extract($block_options);

        ?>
        <div class="container block-container">
            <?php if (!isset($hide_header) || $hide_header == false): ?>
                <section class="block-header row">
                    <h2 class="block-title title col">
                        <?php echo $title ?>
                    </h2>
                    <?php if (!empty($see_more_link) && !empty($see_more_text)): ?>
                        <h2 class="block-see-more title col">
                            <a href="<?php echo $see_more_link ?>" <?php echo isset( $see_more_target ) && ! empty ( $see_more_target ) ? "target='" . $see_more_target . "'" : ''; ?>">
                                <?php echo $see_more_text ?>
                            </a>
                            <i class="icon-arrowleft"></i>
                        </h2>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <section class="block-content">
                <?php echo $content; ?>
            </section>
        </div>
        <?php
    }

    public function validate_block($block)
    {
        $type = $block['type'];

        if (!empty($type)) {


            switch ($type) {
                case 'dynamic':

                    break;

                case 'manual':

                    break;
                case 'categories':

                    break;
            }
        }

    }
}