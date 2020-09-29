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

    <h1 class="col p-0">
		<?php echo $foody_page->getTitle() ?>
    </h1>

    <!-- Description -->
    <div class="description <?php if(wp_is_mobile()){ echo 'show-read-more';}?>">
		<?php echo $foody_page->getDescription() ?>
    </div>


    <section class="recipe-details  d-flex">

        <!-- Author image -->
        <div class="image-container col-lg-1 col-2 nopadding">
            <a href="<?php echo $foody_page->get_author_link() ?>">
                <img src="<?php echo $foody_page->getAuthorImage() ?>" alt="">
            </a>
        </div>
        <!-- Bullets desktop -->
        <section class="col-lg-11 col-10  content-details-bullets-container">

            <section class="wrapper">
				<?php
				$args = array(
					'foody_page'    => $foody_page,
					'show_favorite' => $show_favorite,
					'hide'          => [
						'views' => true,
                        'date' => true// wp_is_mobile()
					]
				);

				foody_get_template_part( get_template_directory() . '/template-parts/content-post-bullets.php', $args );

				?>
				<?php if ( ! wp_is_mobile() ): ?>
                    <section class="rating-container d-lg-block">
						<?php $foody_page instanceof Foody_Recipe ? $foody_page->ratings_new() : Foody_Recipe::ratings() ?>
                    </section>
				<?php endif; ?>
            </section>

			<?php if ( $show_favorite ): ?>
                <section class="favorite-container">
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

        </section>

    </section>

    <?php if ( wp_is_mobile() ): ?>


        <section class="rating-container d-lg-block">
<!--            --><?php //$foody_page->ratings_new() ?>
            <?php $foody_page instanceof Foody_Recipe ? $foody_page->ratings_new() : Foody_Recipe::ratings() ?>
        </section>
    <?php endif; ?>

    <!-- Social buttons -->
<!--    <section class="">-->
<!--		--><?php //foody_get_template_part(
//			get_template_directory() . '/template-parts/content-social-actions.php',
//			[
//				'extra_content' => $foody_page->the_purchase_buttons( 'd-none d-lg-flex', false )
//			]
//		);
//		?>
<!--    </section>-->


</div>
