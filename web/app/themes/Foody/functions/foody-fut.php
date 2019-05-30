<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 11/29/18
 * Time: 3:54 PM
 */

$subscriber = get_role('subscriber');

add_role('foody_fut_user', "משתמש פיילוט", $subscriber->capabilities);

function foody_customize_colors_css() {
	$titles_color_css      = get_theme_mod( 'foody_title_color' );
	$subtitles_color_css   = get_theme_mod( 'foody_subtitle_color' );
	$text_color_css        = get_theme_mod( 'foody_text_color' );
	$links_color_css       = get_theme_mod( 'foody_links_color' );
	$links_hover_color_css = get_theme_mod( 'foody_links_hover_color' );
	$show_titles_underline = get_theme_mod( 'foody_show_titles_underline' );
	$foody_underline_color = get_theme_mod( 'foody_underline_color' );

	if ( ! empty( $show_titles_underline ) && ! empty( $foody_underline_color ) ) {
		?>
		<style>
			.title {
				text-decoration: underline;
				text-underline-position: under;
				text-decoration-color: <?php echo $foody_underline_color ?> !important;
			}

			.title > a {
				text-decoration: underline;
				text-underline-position: under;
				text-decoration-color: <?php echo $foody_underline_color ?> !important;
			}
		</style>
		<?php

	}
	if ( ! empty( $titles_color_css ) ) {
		?>
		<style>
			:root {
				--color__text-title: <?php echo $titles_color_css; ?>;
				--color__primary: <?php echo $titles_color_css; ?>;
			}
		</style>
		<?php
	}

	if ( ! empty( $links_color_css ) ) {
		?>
		<style>
			:root {
				--color__link: <?php echo $links_color_css; ?>;
			}
		</style>
		<?php
	}

	if ( ! empty( $text_color_css ) ) {
		?>
		<style>
			:root {
				--color__text-main: <?php echo $text_color_css; ?>;
			}
		</style>
		<?php
	}

	if ( ! empty( $links_hover_color_css ) ) {
		?>
		<style>
			:root {
				--color__link-hover: <?php echo $links_hover_color_css; ?>;
			}
		</style>
		<?php
	}
}

add_action( 'wp_footer', 'foody_customize_colors_css' );