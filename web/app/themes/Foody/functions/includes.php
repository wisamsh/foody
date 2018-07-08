<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/14/18
 * Time: 6:23 PM
 *
 * This file contains all required classes
 * and scripts.
 *
 * add in functions.php
 */



/*
 * Globals
 * */


require_once get_template_directory() . "/inc/globals.php";
require_once get_template_directory() . "/inc/utils/utils.php";
require_once get_template_directory() . "/functions/js-globals.php";



/*
 * Utils
 * */
require_once get_template_directory() . "/inc/classes/common/class-foody-social.php";


/*
 * Classes
 * */

require_once get_template_directory() . "/inc/classes/common/class-foody-user.php";
require_once get_template_directory() . "/inc/classes/class-header.php";
require_once get_template_directory() . "/inc/classes/class-foody-footer.php";
require_once get_template_directory() . "/inc/classes/class-foody-comment-walker.php";
require_once get_template_directory() . "/inc/classes/class-homepage.php";
require_once get_template_directory() . "/inc/classes/class-foody-category.php";
require_once get_template_directory() . "/inc/classes/class-foody-categories.php";
require_once get_template_directory() . "/inc/classes/class-foody-team.php";
require_once get_template_directory() . "/inc/classes/common/class-foody-post.php";
require_once get_template_directory() . "/inc/classes/class-foody-ingredient.php";
require_once get_template_directory() . "/inc/classes/class-foody-recipe.php";
require_once get_template_directory() . "/inc/classes/class-recipes-grid.php";
require_once get_template_directory() . "/inc/classes/common/class-sidebar-filter.php";
require_once get_template_directory() . "/inc/classes/class-foody-profile.php";
require_once get_template_directory() . "/inc/classes/class-foody-comments.php";
require_once get_template_directory() . "/inc/classes/class-foody-article.php";
require_once get_template_directory() . "/inc/classes/class-foody-page-content-factory.php";

/*
 * Widgets
 * */

require_once get_template_directory() . "/inc/widgets/class-foody-widget.php";
require_once get_template_directory() . "/inc/widgets/widget-categories-list.php";
require_once get_template_directory() . "/inc/widgets/widget-search-filter.php";


/*
 * Functions
 * */

require_once get_template_directory() . "/functions/core.php";
require_once get_template_directory() . "/functions/post-types.php";
require_once get_template_directory() . "/functions/sidebars.php";
require_once get_template_directory() . "/functions/widgets.php";
require_once get_template_directory() . "/functions/images.php";
require_once get_template_directory() . "/functions/custom-plugins.php";
require_once get_template_directory() . "/functions/bootstrap-breadcrumbs.php";
require_once get_template_directory() . "/functions/custom-options.php";
require_once get_template_directory() . "/functions/filters.php";
require_once get_template_directory() . "/functions/actions.php";
require_once get_template_directory() . "/functions/foody-ajax.php";


/*
 * Shortcodes
 * */
require_once get_template_directory() . "/inc/shortcodes/shortcode-team.php";
