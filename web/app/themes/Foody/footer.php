<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Foody
 */
$footer = new Foody_Footer();

?>

</div><!-- #content -->

<?php if ( ! empty( $edit_link = get_edit_post_link() ) ): ?>
    <div dir="rtl" style="text-align: right; max-width: 960px;margin: 0 auto;">
        <a href="<?php echo $edit_link ?>">
			<?php echo __( 'ערוך' ) ?>
        </a>
    </div>
<?php endif; ?>
<footer id="colophon" class="site-footer no-print">

    <section class="footer-social-container d-block d-lg-none">
		<?php dynamic_sidebar( 'foody-social' ); ?>
    </section>

    <section class="newsletter d-block d-lg-none">
		<?php
		$footer->newsletter();
		?>

    </section>

    <section class="social d-block d-lg-none">
		<?php
		$show_instagram = get_theme_mod( 'foody_show_social_instagram' );
		$instagram_link = get_theme_mod( 'foody_social_instagram' );
		$show_facebook  = get_theme_mod( 'foody_show_social_facebook' );
		$facebook_link  = get_theme_mod( 'foody_social_facebook' );
		$show_youtube   = get_theme_mod( 'foody_show_social_youtube' );
		$youtube_link   = get_theme_mod( 'foody_social_youtube' );
		foody_get_template_part( get_template_directory() . '/template-parts/footer-social-bar.php',
			[
				'show_instagram' => $show_instagram,
				'show_facebook'  => $show_facebook,
				'show_youtube'   => $show_youtube,
				'instagram_link' => $instagram_link,
				'facebook_link'  => $facebook_link,
				'youtube_link'   => $youtube_link
			]
		);
		?>
    </section>

	<?php

	$footer->menu();

	?>

    <section class="footer-pages footer-pages-mobile d-block d-lg-none">
        <ul>
			<?php $footer->display_menu_items( $footer->footer_pages ) ?>
        </ul>
        <section class="foody-israel-footer">
			<?php $footer->the_foody_israel( true ); ?>
        </section>
		<?php if ( get_theme_mod( 'foody_show_moveo_logo', true ) ) : ?>
            <section class="powered-by">
				<?php $footer->the_moveo() ?>
            </section>
		<?php endif; ?>


    </section>

</footer><!-- #colophon -->
</div><!-- #page -->

<?php
if ( ! is_user_logged_in() && ( ! function_exists( 'foody_is_registration_open' ) || foody_is_registration_open() ) ) {
	$login_popup_args = [
		'id'                  => 'login-modal',
		'body'                => do_shortcode( '[foody-login]' ),
		'btn_approve_classes' => 'hide',
		'btn_cancel_classes'  => 'hide',
		'title'               => '',
		'hide_buttons'        => true
	];

	foody_get_template_part( get_template_directory() . '/template-parts/common/modal.php', $login_popup_args );
}
?>

<?php wp_footer(); ?>


<?php

$footer->add_nagish_li_script();

?>

</body>
</html>
