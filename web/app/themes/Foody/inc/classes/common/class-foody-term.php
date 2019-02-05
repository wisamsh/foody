<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/19/18
 * Time: 11:37 AM
 */
abstract class Foody_Term
{

    /**
     * @var int id
     */
    public $id;

    /**
     * @var string link
     */
    public $link;

    /**
     * @var string title
     */
    public $title;

    /**
     * @var WP_Term term
     */
    public $term;


    /**
     * @var FoodyGrid
     */
    private $grid;

    /**
     * Foody_Term constructor.
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;

        $this->term = get_term($id);

        $this->title = $this->term->name;

        $this->link = get_term_link($id);

        $this->grid = new FoodyGrid();
    }


    public function sidebar(){
        dynamic_sidebar('foody-sidebar');
    }

    /**
     * @see Foody_ContentWithSidebar interface
     */
    function the_sidebar_content()
    {
       $this->sidebar();
        dynamic_sidebar('foody-social');
    }

    /**
     * @see Foody_ContentWithSidebar interface
     */
    function the_content($page)
    {
        ?>

        <div class="container-fluid feed-container term-feed-container">
            <?php $this->feed(); ?>
        </div>

        <?php


        foody_get_template_part(get_template_directory() . '/template-parts/common/mobile-filter.php', [
            'sidebar' => array($this, 'sidebar'),
            'wrap'=>true
        ]);


    }

    /**
     * Not used here
     * @see Foody_ContentWithSidebar interface
     */
    function the_featured_content()
    {

    }

    /**
     * Queries the relevant posts for this term
     * and displays a grid of the results.
     * @throws InvalidArgumentException if the implementation
     * of @see Foody_Term::get_grid_args() lacks the required keys.
     * (@see Foody_Term::get_grid_args() for further explanation).
     */
    function feed()
    {

        $grid_args = $this->get_grid_args();

        if (!isset($grid_args['id']) || !isset($grid_args['header']['title'])) {
            throw new InvalidArgumentException('must provide grid id and grid title');
        }

        $foody_query = Foody_Query::get_instance();

        $args = $foody_query->get_query($this->get_foody_query_handler(), [$this->id], true);

        $query = new WP_Query($args);

        $posts = $query->get_posts();

        $posts = array_map('Foody_Post::create', $posts);

        $grid = [
            'cols' => 2,
            'posts' => $posts,
            'more' => $foody_query->has_more_posts($query),
            'header' => [
                'sort' => true
            ],
//            'title_el'=>'h1'
        ];

        $grid = array_merge_recursive($grid_args, $grid);

        foody_get_template_part(
            get_template_directory() . '/template-parts/common/foody-grid.php',
            $grid
        );


    }

    function getId()
    {
        return $this->id;
    }

    /**
     * Extending classes should implement the
     * relevant query function in @see Foody_Query
     * and return here the name of said function.
     *
     * @return string
     */
    protected abstract function get_foody_query_handler();


    /**
     * Arguments will be merged with the default args (@see Foody_Term::feed()).
     * Arguments will override default args.
     * Note: returned arguments must contain the 'id' and 'header['title']'
     * keys.
     * @return array arguments to use in grid rendering.
     */
    protected abstract function get_grid_args();

}