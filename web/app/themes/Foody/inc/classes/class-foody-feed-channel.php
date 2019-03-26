<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/24/19
 * Time: 4:45 PM
 */

class Foody_Feed_Channel extends Foody_Post implements Foody_Topic
{

    private $foody_search;
    private $foody_query;


    /**
     * Foody_Feed_Channel constructor.
     */
    public function __construct($post)
    {
        parent::__construct($post);
        $this->foody_search = new Foody_Search('feed_channel');
        $this->foody_query = Foody_Query::get_instance();
        add_action('foody_after_body', [$this, 'the_background_image']);
    }

    public function before_content()
    {
        $cover_image = get_field('cover_image');

        foody_get_template_part(get_template_directory() . '/template-parts/content-cover-image.php', $cover_image);
    }

    public function the_details()
    {
        bootstrap_breadcrumb();
        the_title('<h1 class="title">', '</h1>');
        foody_get_template_part(get_template_directory() . '/template-parts/content-feed-channel-details.php', [
            'feed_channel' => $this
        ]);
    }

    /**
     * Custom css to control channel related design
     */
    public function the_css()
    {
        $titles_color = get_field('titles_color', $this->id);
        if (!empty($titles_color)) {

            if (preg_match('/^#/', $titles_color) === false) {
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

    public function the_background_image()
    {
        $background_image = get_field('background_image', $this->id);

        if (!empty($background_image)) {
            ?>
            <img src="<?php echo $background_image['url'] ?>" alt=""
                 style=" position: fixed; top: 0; left: 0; min-width: 100%; min-height: 100%;">
            <?php
        }

    }

    public function blocks()
    {
        $blocks = get_field('blocks', $this->id);

        if (!empty($blocks)) {

            foreach ($blocks as $block) {
                $type = $block['type'];

                if (!empty($type)) {
                    $this->validate_block($block);

                    $block_fn = "draw_{$type}_block";
                    if (method_exists($this, $block_fn)) {
                        $block_options = call_user_func([$this, $block_fn], $block);
                        if (!empty($block_options) && !empty($block_options['content'])) {
                            $this->wrap_block($block_options);
                        }
                    }
                }
            }
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
     * @uses Foody_Feed_Channel::draw_dynamic_block()
     * @uses Foody_Feed_Channel::draw_manual_block()
     * @uses Foody_Feed_Channel::draw_categories_block()
     * @uses Foody_Feed_Channel::draw_banner_block()
     *
     * @param $block
     * @return array the block options
     * @throws Exception if query filter is wrong
     */
    private function draw_dynamic_block($block)
    {

        $block_options = [];

        /** @var WP_Post $filter_post */
        $filter_post = isset($block['filter']) ? $block['filter'] : null;

        if (!empty($filter_post) && $filter_post) {

            $filter = get_field('filters_list', $filter_post->ID);

            $title = $block['title'];

            if (empty($title)) {
                $title = $filter_post->post_title;
            }

            $types = SidebarFilter::parse_search_args_array($filter);

            $args = [
                'types' => $types,
                'sort' => 'popular_desc'
            ];

            $posts = $this->foody_search->query($args)['posts'];

            if (is_array($posts)) {
                $posts = array_map('Foody_Post::create', $posts);
                if (count($posts) > 4) {
                    $posts = array_slice($posts, 0, 4);
                }
                $block_content = foody_get_template_part(get_template_directory() . '/template-parts/common/foody-grid.php', [
                    'id' => uniqid(),
                    'posts' => $posts,
                    'cols' => 2,
                    'more' => false,
                    'header' => [
                        'title' => ''
                    ],
                    'return' => true
                ]);

                $see_more_link = $block['see_more_link'];
                if (empty($see_more_link)) {
                    $see_more_link = get_permalink($filter_post->ID);
                }

                $see_more_text = $block['see_more_text'];

                $block_options = [
                    'title' => $title,
                    'see_more_link' => $see_more_link,
                    'see_more_text' => $see_more_text,
                    'content' => $block_content
                ];
            }
        }


        return $block_options;
    }

    private function draw_categories_block($block)
    {

        $items = $block['items'];

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

            $items_content = "<section class='categories-block-content categories-listing row'>$items_content</section>";

            $title = $block['title'];

            $see_more_text = $block['see_more_text'];
            $see_more_link = $block['see_more_link'];

            if (empty($see_more_link)) {
                $see_more_link = ['url' => ''];
            }

            $block_options['title'] = $title;
            $block_options['see_more_text'] = $see_more_text;
            $block_options['see_more_link'] = $see_more_link['url'];
            $block_options['content'] = $items_content;
        }

        return $block_options;
    }

    private function draw_banner_block($block)
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

    private function draw_manual_block($block)
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
            $block_options['see_more_text'] = $see_more_text;
            $block_options['see_more_link'] = $see_more_link['url'];
            $block_options['content'] = $items_content;
        }

        return $block_options;
    }

    /**
     * Outputs a generic block html structure.
     * @param $block_options array
     */
    private function wrap_block($block_options)
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
                    <?php if (!empty($see_more_link) || !empty($see_more_text)): ?>
                        <h3 class="block-see-more title col">
                            <a href=" <?php echo $see_more_link ?>">
                                <?php echo $see_more_text ?>
                            </a>
                            <i class="icon-arrowleft"></i>
                        </h3>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <section class="block-content">
                <?php echo $content; ?>
            </section>
        </div>
        <?php
    }

    private function validate_block($block)
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

    public function the_sidebar_content($args = array())
    {
        if (get_field('hide_widgets', $this->id) === false) {
            parent::the_sidebar_content($args);
        }
    }

    function topic_image()
    {

    }

    function topic_title()
    {

    }

    function get_followers_count()
    {

    }

    function get_description()
    {

    }

    function get_type()
    {
        return 'feed_channels';
    }

    function get_breadcrumbs_path()
    {

    }
}