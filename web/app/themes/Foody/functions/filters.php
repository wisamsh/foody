<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/30/18
 * Time: 1:17 PM
 */


/**
 * Wrap content with foody class
 * @param $content
 * @return mixed
 */
function foody_content_filter($content)
{

    $content_class = 'foody-content';

    return '<div class="' . $content_class . '">' . $content . '</div>';

}

add_filter('the_content', 'foody_content_filter');


/**
 * Remove fields from comment form
 * @param $fields
 * @return mixed
 */
function foody_comment_form_fields($fields)
{
    $fields['email'] = '';  //remove default email input
    $fields['url'] = '';  //remove default url input
    $fields['author'] = '';//remove default author input
    return $fields;
}

add_filter('comment_form_default_fields', 'foody_comment_form_fields');