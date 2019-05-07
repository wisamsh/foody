<?php
/**
 * Template Name: Foody Campaign
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$e_book = new Foody_Campaign();

$cta_text = get_field( 'cta_text' );
$cta_link = get_field( 'cta_link' );

$bg = $GLOBALS['images_dir'] . 'e-book-mobile.png';
if ( ! wp_is_mobile() ) {
	$bg = $GLOBALS['images_dir'] . 'e-book.png';
}

$image               = $e_book->get_hero_image_url();
$mobile_image        = $e_book->get_mobile_hero_image_url();
$promoted_images     = $e_book->get_promoted_images();
$register_link       = $e_book->get_hero_link_url();
$register_lower_link = $e_book->lower_link;

?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="contentt" class="site-content" role="main">
                <a href="<?php echo $register_link['url'] ?>" href="<?php echo $register_link['target'] ?>">
                    <picture>
                        <source media="(min-width: 415px)" srcset="<?php echo $image ?>">
                        <source media="(max-width: 414px)"
                                srcset="<?php echo $mobile_image ?>">
                        <img src="<?php echo $image ?>">
                    </picture>
                </a>

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

                <a class="btn btn-primary cta" href="<?php echo $register_lower_link['url'] ?>"
                   target="<?php echo $register_lower_link['target'] ?>">
					<?php echo $register_lower_link['title'] ?>
                </a>


            </div><!-- #content -->

        </div><!-- #primary -->
    </div><!-- #main-content -->

<?php

get_footer();
