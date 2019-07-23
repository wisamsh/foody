<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Foody
 */

get_header();

$homepage = new Foody_HomePage();
$homepage->init();
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <section class="error-404 not-found">

                <div class="page-content">


                    <div class="image-container not-found-image">
                        <img src="<?php echo $GLOBALS['images_dir'] . '404.svg' ?>" alt="">
                    </div>


                    <h2 class="title not-found-title">
						<?php echo get_option( 'foody_404_text', __( 'אופס… העמוד לא נמצא<br> אבל אולי יעניין אותך גם… ', 'foody' ) ); ?>
                    </h2>


                    <div class="homepage">


                        <div class="content">

							<?php $homepage->promoted_items(); ?>

							<?php
							if ( ! is_multisite() || is_main_site() ) {
								$num = wp_is_mobile() ? 4 : 6;
								echo do_shortcode( '[foody_team max="' . $num . '" show_title="true"]' );
							}
							?>

                            <section class="feed-container row">


                                <section class="sidebar-container d-none d-lg-block">
									<?php
									echo "<aside class=\"sidebar col pl-0\">";

									echo "<div class=\"sidebar-content\">";
									dynamic_sidebar( 'foody-social' );
									echo "</div></aside>";
									?>
                                </section>


                                <section class="content-container col-lg-9 col-12">

									<?php $homepage->feed(); ?>

                                </section>

                            </section>


                        </div>

                    </div>
                </div><!-- .page-content -->
            </section><!-- .error-404 -->

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();
