<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 7:30 PM
 */
$e_book = $template_args['ebook'];

$bg = $GLOBALS['images_dir'] . 'e-book-mobile.png';
if ( ! wp_is_mobile() ) {
	$bg = $GLOBALS['images_dir'] . 'e-book.png';
}

$image                = $e_book->get_hero_image_url();
$mobile_image         = $e_book->get_mobile_hero_image_url();
$promoted_images      = $e_book->get_promoted_images();
$register_link        = $e_book->get_hero_link_url();
$register_lower_link  = $e_book->lower_link;
$content_link         = $e_book->content_link;
$registered_user_link = $e_book->registered_user_link;

?>

<div id="contentt" class="site-content" role="main">
    <div class="hero-container<?php echo $e_book->has_hero_video() ? ' hero-video' : '' ?>">
		<?php if ( $e_book->has_hero_video() ): ?>
			<?php $e_book->the_hero_video() ?>
		<?php else: ?>
			<?php if ( is_user_logged_in() && ! empty( $registered_user_link ) ) {
				echo '<a href="' . $registered_user_link['url'] . '" href="' . $registered_user_link['target'] . '">';
			} else {
				echo '<a href="' . $register_link['url'] . '" href="' . $register_link['target'] . '">';
			} ?>
            <picture>
                <source media="(min-width: 415px)" srcset="<?php echo $image ?>">
                <source media="(max-width: 414px)"
                        srcset="<?php echo $mobile_image ?>">
                <img src="<?php echo $image ?>">
            </picture>
            </a>
		<?php endif; ?>
    </div>
	<?php if ( $e_book->show_social ): ?>
        <div class="text-center share-text">
			<?php echo _( 'שתפו' ) ?>
        </div>

		<?php
		foody_get_template_part( get_template_directory() . '/template-parts/content-social-actions.php', [ 'exclude' => [ 'print' ] ] );
	endif;
	?>

	<?php the_content(); ?>

    <section class="container pl-sm-0 pr-sm-0">
		<?php if ( is_user_logged_in() && ! empty( $registered_user_link ) ) : ?>
            <a class="btn btn-primary cta" href="<?php echo $registered_user_link['url'] ?>"
               target="<?php echo $registered_user_link['target'] ?>">
				<?php echo $content_link['title'] ?>
            </a>
		<?php elseif ( ! empty( $content_link ) ): ?>
            <a class="btn btn-primary cta" href="<?php echo $content_link['url'] ?>"
               target="<?php echo $content_link['target'] ?>">
				<?php echo $content_link['title'] ?>
            </a>
		<?php endif; ?>

        <?php $e_book->the_extra_content(); ?>

        <section class="recipes row">
			<?php if ( ! empty( $promoted_images ) ):
				foreach ( $promoted_images as $promoted_image ):
					?>
                    <figure class="col-12 col-sm-4">
						<?php if ( ! empty( $promoted_image['link'] ) ) {
							echo '<a href="' . $promoted_image['link']['url'] . '" target="' . $promoted_image['link']['target'] . '">';
						} ?>
                        <img src="<?php echo $promoted_image['image']['url'] ?>" alt="">
                        <h3 class="title text-center">
							<?php echo $promoted_image['title'] ?>
                        </h3>
                        <div class="description text-center">
							<?php echo $promoted_image['subtitle'] ?>
                        </div>
						<?php if ( ! empty( $promoted_image['link'] ) ) {
							echo '</a>';
						} ?>
                    </figure>
				<?php
				endforeach;
			endif;
			?>
        </section>

    </section>

	<?php if ( is_user_logged_in() && ! empty( $registered_user_link ) ) : ?>
        <a class="btn btn-primary cta" href="<?php echo $registered_user_link['url'] ?>"
           target="<?php echo $registered_user_link['target'] ?>">
			<?php echo $register_lower_link['title'] ?>
        </a>
	<?php elseif ( ! empty( $register_lower_link ) ): ?>
        <a class="btn btn-primary cta" href="<?php echo $register_lower_link['url'] ?>"
           target="<?php echo $register_lower_link['target'] ?>">
			<?php echo $register_lower_link['title'] ?>
        </a>
	<?php endif; ?>

</div><!-- #content -->