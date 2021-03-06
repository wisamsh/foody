<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/8/19
 * Time: 10:13 AM
 */

function foody_approvals_shortcode( $atts ) {
	if ( empty( $atts ) ) {
		$atts = [];
	}
	$atts['return'] = true;
	if ( isset( $atts['extended-campaign'] ) && ! empty ( $atts['extended-campaign'] ) ) {
		$display = foody_get_template_part( get_template_directory() . '/template-parts/content-campaign-approvals.php', $atts );
	} else {
		$display = foody_get_template_part( get_template_directory() . '/template-parts/content-approvals.php', $atts );
	}

	return $display;
}


add_shortcode( 'foody-approvals', 'foody_approvals_shortcode' );
