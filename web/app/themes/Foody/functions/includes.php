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

/*
 * Classes
 * */

require_once get_template_directory() . "/inc/classes/class-homepage.php";
require_once get_template_directory() . "/inc/classes/class-foody-team.php";
require_once get_template_directory() . "/inc/classes/common/class-foody-post.php";
require_once get_template_directory() . "/inc/classes/class-recipe.php";
require_once get_template_directory() . "/inc/classes/class-recipes-grid.php";
require_once get_template_directory() . "/inc/classes/common/class-sidebar-filter.php";


/*
 * Functions
 * */

require_once get_template_directory() . "/functions/post-types.php";
require_once get_template_directory() . "/functions/images.php";
