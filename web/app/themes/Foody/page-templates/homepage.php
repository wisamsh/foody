<?php
/**
 * Template Name: Homepage
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */


get_header();
$homepage = new Foody_HomePage();
?>

    <div class="homepage">

        <?php $homepage->cover_photo() ?>

        <div class="content">
            <div class="row recipes-grid gutter-10 featured">
                <?php $homepage->featured() ?>
            </div>

            <?php $homepage->categories_listing() ?>


            <?php $homepage->promoted_items(); ?>

            <?php

            $num = wp_is_mobile() ? 4 : 6;
            echo do_shortcode('[foody_team max="' . $num . '" show_title="true"]')

            ?>

            <section class="feed-container row">


                <section class="sidebar-container d-none d-lg-block">
                    <?php

                    $homepage->sidebar('aside.sidebar-desktop  .sidebar-content');
                    ?>
                </section>


                <section class="content-container col-lg-9 col-12">

                    <?php $homepage->feed(); ?>

                </section>

                <?php Foody_Seo::seo() ?>

            </section>

	        <?php if ( $homepage->show_google_adx() ): ?>
                <section class="google-adx-container col-lg-9 col-12">
			        <?php $homepage->the_google_adx(); ?>
                </section>
	        <?php endif; ?>
        </div>

        <?php

        foody_get_template_part(get_template_directory() . '/template-parts/common/mobile-filter.php', [
            'sidebar' => array($homepage, 'sidebar')
        ]);

        ?>

    </div>
<?php
get_footer();