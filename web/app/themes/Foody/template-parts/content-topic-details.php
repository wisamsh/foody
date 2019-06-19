<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/1/18
 * Time: 4:25 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var Foody_Topic $topic */
$topic = $template_args['topic'];

?>


<section class="topic-details mx-auto">


    <div class="row">
        <div class="image-container">

            <img src="<?php echo $topic->topic_image() ?>" alt="">

        </div>

        <div class="details col">
			<?php bootstrap_breadcrumb( null, $topic->get_breadcrumbs_path() ); ?>

            <h1 class="title">
				<?php echo $topic->topic_title() ?>
            </h1>
			<?php if ( foody_is_registration_open() ) : ?>
                <span class="followers-count">
                <?php echo $topic->get_followers_count(); ?>
            </span>
			<?php endif; ?>
        </div>
        <div class="follow">
			<?php
			if ( foody_is_registration_open() ) {
				foody_get_template_part(
					get_template_directory() . '/template-parts/content-follow-button.php',
					[ 'topic' => $topic, 'classes' => 'd-none d-lg-inline' ]
				);
			}
			?>
            <div class="social d-none d-sm-block">
				<?php
				foody_get_template_part(
					get_template_directory() . '/template-parts/content-social-actions.php', [ 'hide_rating' => true ]
				)
				?>
            </div>
        </div>

        <div class="social d-block d-lg-none col-12 fish">
			<?php
			if ( foody_is_registration_open() ) {
				foody_get_template_part(
					get_template_directory() . '/template-parts/content-social-actions.php',
					[
						'extra_content' => foody_get_template_part(
							get_template_directory() . '/template-parts/content-follow-button.php',
							[ 'topic' => $topic, 'classes' => '', 'return' => true ]
						)
					]
				);
			} else {
				foody_get_template_part(
					get_template_directory() . '/template-parts/content-social-actions.php',
					[
						'extra_content' => ''
					]
				);

			}
			?>
        </div>

    </div>

    <p class="description">
		<?php echo $topic->get_description(); ?>
    </p>
</section>
