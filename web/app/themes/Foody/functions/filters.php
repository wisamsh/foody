<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/30/18
 * Time: 1:17 PM
 */

// TODO move hooks to the relevant files under Foody/functions/

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


/**
 * Hooks into the page template and
 * overrides the default template.
 * Used to make sure the relevant
 * post types are rendered with Foody's
 * custom templates.
 * @param $template
 * @return string
 */
function default_page_template($template)
{
    if (is_singular(array('post', 'foody_recipe', 'foody_article'))) {
        $default_template = locate_template(array('page-templates/content-with-sidebar.php'));
        if ('' != $default_template) {
            return $default_template;
        }
    }

    return $template;
}

add_filter('template_include', 'default_page_template', 10);

add_filter('show_admin_bar', '__return_false');

