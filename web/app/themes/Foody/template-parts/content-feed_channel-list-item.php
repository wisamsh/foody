<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 12/27/19
 */
/** @var Foody_Feed_Channel $feed_area */
/** @noinspection PhpUndefinedVariableInspection */
$feed_area    = $template_args['post'];
$args       = foody_get_array_default( $template_args, 'args', [] );
$title_el   = foody_get_array_default( $args, 'title_el', 'h2' );
$image_size = isset( $args['image_size'] ) ? $args['image_size'] : 'list-item';

$target = '';
if ( ! empty( $feed_area->link_attrs['target'] ) ) {
	$target = "target='{$feed_area->link_attrs['target']}'";
}

$lazy = ! empty( $template_args['lazy'] );

?>

<div class="article-item feed-item">
    <a href="<?php echo $feed_area->link ?>" <?php echo $target ?>>
        <div class="image-container main-image-container">
			<?php if ( $lazy ): ?>
                <img src="<?php echo $GLOBALS['images_dir'] . 'recipe-placeholder.svg' ?>"
                     class="article-item-image feed-item-image lazyload"
                     data-foody-src="<?php echo $feed_area->getImage() ?>"
                     alt="<?php echo foody_get_featured_image_alt( $feed_area ) ?>">
			<?php else: ?>
                <img class="article-item-image feed-item-image" src="<?php echo $feed_area->getImage() ?>"
                     alt="<?php echo foody_get_featured_image_alt( $feed_area ) ?>">

			<?php endif; ?>
			<?php if ( ! empty( $label = $feed_area->get_label() ) ): ?>

                <div class="recipe-label">
                    <span>

                    <?php echo $label ?>
                    </span>
                </div>

			<?php endif; ?>
			<?php if ( $feed_area->video != null ): ?>
                <div class="duration">
                    <i class="icon icon-timeplay">

                    </i>
                    <span>
                        <?php echo $feed_area->getDuration() ?>
                    </span>


                </div>
			<?php endif; ?>
        </div>
    </a>

    <section class="feed-item-details-container">

        <section class="title-container">
            <<?php echo $title_el ?> class="grid-item-title">
            <a href="<?php echo $feed_area->link ?>" <?php echo $target ?>>
				<?php echo $feed_area->getTitle() ?>
            </a>
        </<?php echo $title_el ?>>

        <div class="description">
			<?php echo $feed_area->getDescription() ?>
        </div>
    </section>
    <section class="article-item-details  d-flex">
        <div class="image-container col-12 nopadding">
            <a href="<?php echo $feed_area->get_author_link() ?>">
                <img class="lazyload" src="<?php echo $GLOBALS['images_dir'] . 'author-placeholder.svg' ?>"
                     data-foody-src="<?php echo $feed_area->getAuthorImage() ?>"
                     alt="<?php echo $feed_area->getAuthorName() ?>">
            </a>
            <ul>
                <li>
                    <a href="<?php echo $feed_area->get_author_link() ?>">
						<?php echo $feed_area->getAuthorName() ?>
                    </a>
                </li>
				<?php if ( ! empty( get_option( 'foody_show_post_views' ) ) ) : ?>
                    <li>
						<?php echo $feed_area->getViewCount() ?>
                    </li>
				<?php endif; ?>
                <!--                    <li>-->
                <!--                        --><?php //echo $feed_area->getPostedOn() ?>
                <!--                    </li>-->
            </ul>
        </div>

    </section>
    </section>
</div>
