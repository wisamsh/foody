<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/2/18
 * Time: 3:34 PM
 */
class Foody_Category implements Foody_ContentWithSidebar
{
    public $id;

    public $link;

    public $title;

    public $category;


    /**
     * @var RecipesGrid
     */
    private $grid;

    /**
     * Foody_Category constructor.
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;

        $this->category = get_term($id);

        $this->title = $this->category->name;

        $this->link = get_term_link($id);

        $this->grid = new RecipesGrid();
    }


    /**
     * Get the category image (ACF Field)
     * @return mixed|null|string
     */
    public function get_image()
    {
        $image = '';
        if ($this->category != null) {
            $image = get_field('image', $this->category->taxonomy . '_' . $this->category->term_id);
        }

        return $image;
    }


    /**
     * Get array of sub categories
     * as Foody_Category objects
     * @return Foody_Category[]
     */
    public function get_sub_categories()
    {
        $parent_id = get_queried_object_id();
        $wp_categories = get_categories(['parent' => $parent_id, 'hide_empty' => false]);
        $sub_categories = [];
        if (is_array($wp_categories)) {
            $sub_categories = array_map(function ($category) {
                return new Foody_Category($category->term_id);
            }, $wp_categories);
        }

        return $sub_categories;
    }


    /**
     * Get the category tree.
     * Returns parents categories in
     * desc order.
     * @return array
     */
    function get_tree()
    {

        $current = get_term($this->id, 'category');

        $tree = [];

        while ($current->parent) {
            $tree[] = $current->term_id;
            $current = get_term($current->parent, 'category');
        }
        $tree[] = $current->term_id;
        $tree = array_reverse($tree);

        return $tree;
    }

    function feed()
    {

        $args = [
            'post_type' => ['foody_recipe', 'foody_playlist', 'post'],
            'post_status' => 'publish',
            'category' => $this->id
        ];

        $query = new WP_Query($args);

        $posts = $query->get_posts();

        $posts = array_map('post_to_foody_post', $posts);

        $this->grid->loop($posts, 3);


    }


    // === Foody_ContentWithSidebar === //

    function the_featured_content()
    {

    }

    function the_sidebar_content()
    {
        // TODO: Implement the_sidebar_content() method.
    }

    function the_details()
    {
        bootstrap_breadcrumb();
        if (!empty($this->get_sub_categories())) {
            foody_get_template_part(get_template_directory() . '/template-parts/content-categories-slider.php', [
                'category' => $this
            ]);
        }
    }

    function the_content($page)
    {
        $select_args = array(
            'id' => 'sort-categories-posts',
            'placeholder' => 'סדר על פי',
            'options' => array(
                array(
                    'value' => 1,
                    'label' => 'א-ת'
                ),
                array(
                    'value' => -1,
                    'label' => 'ת-א'
                )
            )
        );

        $gutter = wp_is_mobile() ? ' ' : ' gutter-30 '

        ?>
        <div class="container">
            <div class="feed-header row<?php echo $gutter ?>justify-content-space-between">

                <h2 class="title col-sm-6 col-8">
                    <?php echo sprintf('מתכוני %s', $this->title) ?>
                </h2>
                <div class="sort col-sm-6 col-4">
                    <?php
                    foody_get_template_part(
                        get_template_directory() . '/template-parts/common/foody-select.php',
                        $select_args
                    )
                    ?>
                </div>
            </div>
        </div>


        <div class="container-fluid feed-container">
            <div class="row gutter-3">
                <?php $this->feed(); ?>
            </div>
        </div>
        <?php

    }

    function getId()
    {
        return $this->id;
    }
}