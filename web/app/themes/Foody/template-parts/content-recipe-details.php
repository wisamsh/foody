<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/21/18
 * Time: 2:48 PM
 */


/** @var Foody_Recipe $foody_page */
/** @noinspection PhpUndefinedVariableInspection */
$foody_page    = $template_args['page'];
$show_favorite = foody_get_array_default( $template_args, 'show_favorite', true );
if ( ! foody_is_registration_open() ) {
	$show_favorite = false;
}
?>
<div class="details container">
	<?php bootstrap_breadcrumb() ?>
    <section class="recipe-details  d-flex">

        <!-- Author image -->
        <div class="image-container col-sm-1 col-2 nopadding">
            <a href="<?php echo $foody_page->get_author_link() ?>">
                <img src="<?php echo $foody_page->getAuthorImage() ?>" alt="<?php echo $foody_page->getAuthorName() ?>">
            </a>
        </div>

        <!-- Title -->
        <section class="col-sm-11 col-10">
            <div class="row justify-content-between m-0">
                <h1 class="col p-0">
					<?php echo $foody_page->getTitle() ?>
                </h1>
            </div>
        </section>

    </section>

    <div class="description">

        <!-- Bullets mobile-->
        <section class="post-bullets-container d-block d-lg-none">
			<?php

			$args = array(
				'foody_page' => $foody_page
			);

			foody_get_template_part( get_template_directory() . '/template-parts/content-post-bullets.php', $args );

			?>
        </section>
		<?php if ( wp_is_mobile() ): ?>
            <section class="d-block d-lg-none">
				<?php Foody_Recipe::ratings() ?>
            </section>
		<?php endif; ?>

        <!-- Description -->
		<?php echo $foody_page->getDescription() ?>

    </div>

    <!-- Bullets desktop -->
    <section class="d-none d-lg-block content-details-bullets-container">
		<?php
		$args = array(
			'foody_page'    => $foody_page,
			'show_favorite' => $show_favorite
		);

		foody_get_template_part( get_template_directory() . '/template-parts/content-post-bullets.php', $args );

		?>

    </section>

    <!-- Favorite mobile -->
	<?php if ( $show_favorite ): ?>
        <section class="d-block d-xl-none favorite-container" style="visibility: <?php echo get_option( 'foody_show_favorite', true ) ? 'visible' : 'hidden' ?>">
			<?php

			foody_get_template_part(
				get_template_directory() . '/template-parts/common/favorite.php',
				array(
					'post'      => $foody_page,
					'show_text' => true,
				)
			);
			?>
        </section>
	<?php endif; ?>

    <!-- Social buttons -->
    <section class="">
		<?php foody_get_template_part(
			get_template_directory() . '/template-parts/content-social-actions.php',
			[
				'extra_content' => $foody_page->the_purchase_buttons( 'd-none d-lg-flex', false )
			]
		);
		?>
    </section>


</div>
