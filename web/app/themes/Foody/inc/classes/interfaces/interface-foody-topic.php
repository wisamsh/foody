<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/1/18
 * Time: 2:01 PM
 */
interface Foody_Topic {

	function topic_image($size = 96);

	function topic_title();

	function get_followers_count();

	function get_description();

	function get_type();

	function get_id();

	function get_breadcrumbs_path();
}