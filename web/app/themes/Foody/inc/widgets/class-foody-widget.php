<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/7/18
 * Time: 2:43 PM
 */
abstract class Foody_Widget extends WP_Widget
{

    protected abstract function display($args, $instance);

    protected abstract function get_css_classes();

    public function widget($args, $instance)
    {
        $element = $this->get_wrapping_element();
        $before = '<%s class="%s">';
        $before = sprintf($before, $element, $this->get_css_classes());

        echo $before;

        $this->display($args, $instance);

        echo sprintf('</%s>', $element);
    }


    protected function get_wrapping_element()
    {
        return 'section';
    }

}