<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/1/18
 * Time: 5:21 PM
 */

function foody_team_shortcode( $atts ) {

	$team = new FoodyTeam();

	$team_display = $team->team(shortcode_atts( array(
		'show_count' => false,
		'sort'       => 'ASC',
		'display' => 'row',
		'max' =>1000,
		'grid_col_span' => 0,
		'show_title' => false,
		'allow_sort' => false
	), $atts ));


	return $team_display;
}


add_shortcode( 'foody_team', 'foody_team_shortcode' );
