<?php
/**
 * Template Name: WhiteLabel-Homepage
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */


get_header();

$homepage = new Foody_WhiteLabel_HomePage();
?>

    <div class="homepage">

		<?php $homepage->cover_photo() ?>

		<?php
		$homepage->the_title();
		?>

        <div class="content">
			<?php if ( have_rows( 'brands' ) ): ?>
                <div class="brands-container">
					<?php $homepage->the_brands(); ?>
                </div>
			<?php endif; ?>
            <section class="feed-container row">

                <section class="sidebar-container d-none d-lg-block">
					<?php $homepage->sidebar(); ?>
                </section>

                <section class="content-container col-lg-9 col-12">

                    <!-- Blocks -->
					<?php $homepage->blocks(); ?>

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
		if ( have_rows( 'filters_list', 'foody_search_options' ) ) {
			foody_get_template_part( get_template_directory() . '/template-parts/common/mobile-filter.php', [
				'sidebar' => array( $homepage, 'sidebar' )
			] );
		}
		?>

    </div>
<?php
get_footer();