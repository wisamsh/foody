<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/5/18
 * Time: 4:47 PM
 */
class foody_Search_Filter extends Foody_Widget
{
    public static $foody_widget_id = 'foody_search_filter';

    private $foody_filter;

    public const CSS_CLASSES = 'foody-search-filter';


    /**
     * foody_Search_Filter constructor.
     */
    public function __construct() {
        $widget_options = array(
            'classname'   => 'foody_search_filter',
            'description' => 'Widget listing filtering options',
        );

        $this->foody_filter = new SidebarFilter();

        parent::__construct( self::$foody_widget_id, 'Search Filter', $widget_options );
    }


    protected function display($args, $instance)
    {
        echo $this->foody_filter->get_filter();
    }

    protected function get_css_classes()
    {
        return self::CSS_CLASSES;
    }
}