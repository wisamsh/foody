<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/8/19
 * Time: 10:13 AM
 */

function foody_recipe_shortcode( $attrs ) {
	if ( empty( $attrs ) ) {
		$attrs = [];
	}
	$attrs['return'] = true;
	$post_id = '';
	if ( isset( get_post()->ID ) ) {
		$post_id = get_post()->ID;
	}

	$display = '';
	if ( $post_id != $attrs['recipe'] ) {
		$post            = get_post( $attrs['recipe'] );
		$recipe          = new Foody_Recipe( $post );
		$attrs['recipe'] = $recipe;
		$display         = foody_get_template_part( get_template_directory() . '/template-parts/content-recipe-shortcode-preview.php', $attrs );
	}

	return $display;
}

add_shortcode( 'foody-recipe', 'foody_recipe_shortcode' );
