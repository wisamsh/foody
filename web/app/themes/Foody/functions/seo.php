<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 1/17/19
 * Time: 3:17 PM
 */

add_filter( 'wpseo_robots', 'foody_handle_no_index' );
function foody_handle_no_index( $robots_str ) {
	$no_index = 'noindex,nofollow';

	if ( is_single() ) {
		$comments_regex = '/comment-page-[0-9]+(\/?)$/';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$uri = $_SERVER['REQUEST_URI'];

			if ( preg_match( $comments_regex, $uri ) != false ) {
				$robots_str = $no_index;
			}
		}
	} elseif ( isset( $_GET['redirect_to'] ) && ! empty( $_GET['redirect_to'] ) ) {
		$robots_str = $no_index;
	}

	return $robots_str;
}


add_filter( 'wpseo_metadesc', 'foody_set_meta_desc' );
function foody_set_meta_desc( $desc ) {
	if ( is_tag() ) {
		$name = get_term_field( 'name', get_queried_object_id() );
		$desc = sprintf( "%s - כל המתכונים והכתבות הכי מעניינות וטעימות. פודי, הצטרפו לפודי - קהילת האוכל הגדולה של ישראל.", $name );
	}

	return $desc;
}

add_action( 'wpseo_frontend_presenters', 'change_yoast_seo_og_meta' );
function change_yoast_seo_og_meta() {
	if ( is_single() && ! in_array( get_post_type(), [ 'foody_feed_channel' ] ) ) {
		add_filter( 'wpseo_opengraph_image', 'foody_change_image' );

		global $post;
		if ( has_post_thumbnail( $post->ID ) ) {
			$img_src = get_the_post_thumbnail_url( ( $post->ID ), 'medium' );
		} else {
			$img_src = "";
		}
		?>

        <meta property="og:image" content="<?php echo $img_src; ?>"/>
        <meta property="og:image:width" content="1024">
        <meta property="og:image:height" content="683">

		<?php
	} else {
		return;
	}
}


function foody_change_image( $image ) {

	$image = get_the_post_thumbnail_url();

	return $image;
}

//add_filter('wpseo_sitemap_exclude_author','foody_xml_sitemap_authors');
//
//function foody_xml_sitemap_authors($users){
//
//
//    return $users;
//}