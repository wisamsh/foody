<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 15/9/19
 * Time: 10:13 AM
 */

function foody_article_shortcode( $attrs ) {
	if ( empty( $attrs ) ) {
		$attrs = [];
	}
	$attrs['return'] = true;
	$post_id = '';
	if ( isset( get_post()->ID ) ) {
		$post_id = get_post()->ID;
	}

	$display = '';
	if ( $post_id != $attrs['article'] ) {
		$post            = get_post( $attrs['article'] );
        $article          = new Foody_Article( $post, false );
		$attrs['article'] = $article;
		$display         = foody_get_template_part( get_template_directory() . '/template-parts/content-article-shortcode-preview.php', $attrs );
	}

	return $display;
}

add_shortcode( 'foody-article', 'foody_article_shortcode' );
