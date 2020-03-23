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
require_once get_template_directory() . "/inc/utils/foody-ingredients-export.php";


/*
 * Interfaces
 * */

require_once get_template_directory() . "/inc/classes/interfaces/interface-content-with-sidebar.php";
require_once get_template_directory() . "/inc/classes/interfaces/interface-foody-topic.php";

/*
 * Foody Sitemap
 * */

require_once get_template_directory() . "/inc/classes/sitemap/foody-sitemap-group-shortcode.php";
require_once get_template_directory() . "/inc/classes/sitemap/foody-sitemap-shortcode.php";
require_once get_template_directory() . "/inc/classes/sitemap/foody-sitemap-tax-shortcode.php";
require_once get_template_directory() . "/inc/classes/sitemap/foody_shortcodes_utility.php";
require_once get_template_directory() . "/inc/classes/sitemap/wpgo-foody-sitemap-pro.php";


/*
 * Classes
 * */
require_once get_template_directory() . "/inc/classes/common/class-foody-term.php";
require_once get_template_directory() . "/inc/classes/common/class-foody-user.php";
require_once get_template_directory() . "/inc/classes/class-header.php";
require_once get_template_directory() . "/inc/classes/class-foody-footer.php";
require_once get_template_directory() . "/inc/classes/class-foody-blocks.php";
require_once get_template_directory() . "/inc/classes/class-foody-comment-walker.php";
require_once get_template_directory() . "/inc/classes/class-foody-how-i-did-walker.php";
require_once get_template_directory() . "/inc/classes/class-foody-homepage.php";
require_once get_template_directory() . "/inc/classes/class-foody-courses-homepage.php";
require_once get_template_directory() . "/inc/classes/class-foody-course-register.php";
require_once get_template_directory() . "/inc/classes/class-foody-white-label-homepage.php";
require_once get_template_directory() . "/inc/classes/class-foody-campaign.php";
require_once get_template_directory() . "/inc/classes/class-foody-campaign-extended.php";
require_once get_template_directory() . "/inc/classes/class-foody-category.php";
require_once get_template_directory() . "/inc/classes/class-foody-categories.php";
require_once get_template_directory() . "/inc/classes/class-foody-team.php";
require_once get_template_directory() . "/inc/classes/common/class-foody-post.php";
require_once get_template_directory() . "/inc/classes/class-foody-accessory.php";
require_once get_template_directory() . "/inc/classes/class-foody-technique.php";
require_once get_template_directory() . "/inc/classes/class-foody-ingredient.php";
require_once get_template_directory() . "/inc/classes/class-foody-recipe.php";
require_once get_template_directory() . "/inc/classes/class-foody-course.php";
require_once get_template_directory() . "/inc/classes/class-foody-course-new.php";
require_once get_template_directory() . "/inc/classes/class-foody-courses-homepage.php";
require_once get_template_directory() . "/inc/classes/class-foody-playlist.php";
require_once get_template_directory() . "/inc/classes/class-foody-channel.php";
require_once get_template_directory() . "/inc/classes/class-foody-grid.php";
require_once get_template_directory() . "/inc/classes/common/class-sidebar-filter.php";
require_once get_template_directory() . "/inc/classes/class-foody-profile.php";
require_once get_template_directory() . "/inc/classes/class-foody-comments.php";
require_once get_template_directory() . "/inc/classes/class-foody-how-i-did.php";
require_once get_template_directory() . "/inc/classes/class-foody-article.php";
require_once get_template_directory() . "/inc/classes/class-foody-page-content-factory.php";
require_once get_template_directory() . "/inc/classes/class-bootstrap-collapse-nav-walker.php";
require_once get_template_directory() . "/inc/classes/class-foody-channels-menu.php";
require_once get_template_directory() . "/inc/classes/class-foody-author.php";
require_once get_template_directory() . "/inc/classes/class-foody-search.php";
require_once get_template_directory() . "/inc/classes/class-foody-search.php";
require_once get_template_directory() . "/inc/classes/class-foody-search-page.php";
require_once get_template_directory() . "/inc/classes/class-foody-registration.php";
require_once get_template_directory() . "/inc/classes/class-foody-categories-accordion-walker.php";
require_once get_template_directory() . "/inc/classes/class-foody-query.php";
require_once get_template_directory() . "/inc/classes/common/class-foody-analytics.php";
require_once get_template_directory() . "/inc/classes/class-foody-sidebar.php";
require_once get_template_directory() . "/inc/classes/class-foody-tag.php";
require_once get_template_directory() . "/inc/classes/class-foody-categories-accordion.php";
require_once get_template_directory() . '/inc/wp-bootstrap-navwalker.php';
require_once get_template_directory() . '/inc/class-foody-collapse-navwalker.php';
require_once get_template_directory() . '/inc/class-foody-bootstrap-accordion-navwalker.php';
require_once get_template_directory() . '/inc/classes/class-foody-items-page.php';
require_once get_template_directory() . '/inc/classes/class-foody-purchase-buttons.php';
require_once get_template_directory() . '/inc/classes/class-foody-seo.php';
require_once get_template_directory() . '/inc/classes/class-foody-feed-channel.php';
require_once get_template_directory() . '/inc/classes/class-foody-feed-filter.php';
require_once get_template_directory() . '/inc/classes/common/class-foody-mailer.php';
require_once get_template_directory() . '/inc/classes/class-foody-commercial-rule-mapping.php';
require_once get_template_directory() . '/inc/foody-ingredients-wp-list-table.php';

/*
 * Widgets
 * */

require_once get_template_directory() . "/inc/widgets/class-foody-widget.php";
require_once get_template_directory() . "/inc/widgets/widget-categories-list.php";
require_once get_template_directory() . "/inc/widgets/widget-search-filter.php";
require_once get_template_directory() . "/inc/widgets/widget-categories-accordion.php";
require_once get_template_directory() . "/inc/widgets/widget-product.php";


/*
 * Functions
 * */

require_once get_template_directory() . "/functions/foody-session.php";
require_once get_template_directory() . "/functions/core.php";
require_once get_template_directory() . "/functions/authors.php";
require_once get_template_directory() . "/functions/admin-comments.php";
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
require_once get_template_directory() . "/functions/acf-utils.php";
require_once get_template_directory() . "/functions/editor.php";
require_once get_template_directory() . "/functions/http.php";
require_once get_template_directory() . "/functions/redirects.php";
require_once get_template_directory() . "/functions/menus.php";
require_once get_template_directory() . "/functions/registration.php";
require_once get_template_directory() . "/functions/foody-head.php";
require_once get_template_directory() . "/functions/search.php";
require_once get_template_directory() . "/functions/foody-fut.php";
require_once get_template_directory() . "/functions/seo.php";
require_once get_template_directory() . "/functions/user-profile.php";
require_once get_template_directory() . "/functions/theme.php";
require_once get_template_directory() . "/functions/client-messages.php";
require_once get_template_directory() . "/functions/cookies.php";
require_once get_template_directory() . "/functions/pages.php";
require_once get_template_directory() . "/functions/users.php";
if ( defined( 'WP_ENV' ) && in_array( WP_ENV, [ 'local', 'development-mu' ] ) ) {
	require_once get_template_directory() . "/functions/debug.php";
}


/*
 * Shortcodes
 * */
require_once get_template_directory() . "/inc/shortcodes/shortcode-team.php";
require_once get_template_directory() . "/inc/shortcodes/shortcode-register.php";
require_once get_template_directory() . "/inc/shortcodes/shortcode-login.php";
require_once get_template_directory() . "/inc/shortcodes/shortcode-foody-sitemap.php";
require_once get_template_directory() . "/inc/shortcodes/shortcode-approvals.php";
require_once get_template_directory() . "/inc/shortcodes/shortcode-recipe.php";
require_once get_template_directory() . "/inc/shortcodes/shortcode-article.php";
require_once get_template_directory() . "/inc/shortcodes/shortcode-newsletter.php";


/*
 * Plugins
 * */

// Foody API

require_once get_template_directory() . "/foody-api/foody-api.php";
require_once get_template_directory() . "/foody-users-api/users-API.php";
