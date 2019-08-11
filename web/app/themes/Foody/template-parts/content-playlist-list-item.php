<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

/** @var Foody_Playlist $playlist */
/** @noinspection PhpUndefinedVariableInspection */
$playlist   = $template_args['post'];
$args       = foody_get_array_default( $template_args, 'args', [] );
$title_el   = foody_get_array_default( $args, 'title_el', 'h2' );
$image_size = isset( $args['image_size'] ) ? $args['image_size'] : 'list-item';

$target = '';
if ( ! empty( $playlist->link_attrs['target'] ) ) {
	$target = "target='{$playlist->link_attrs['target']}'";
}

?>


<div class="playlist-item feed-item">
    <a href="<?php echo $playlist->link ?>" <?php echo $target ?>>
        <div class="image-container main-image-container">
            <img class="playlist-item-image feed-item-image" src="<?php echo $playlist->getImage() ?>" alt="">
            <div class="playlist-count">
                <i class="icon-timeplay"></i>

                <div class="count">
					<?php echo sprintf( '%s מתכונים', $playlist->num_of_recipes ) ?>
                </div>

            </div>
        </div>
    </a>

    <section class="feed-item-details-container">
        <section class="title-container">
            <<?php echo $title_el ?> class="grid-item-title">
            <a href="<?php echo $playlist->link ?>" <?php echo $target ?>>
				<?php echo $playlist->getTitle() ?>
            </a>
        </<?php echo $title_el ?>>

        <div class="description">
			<?php echo $playlist->getDescription() ?>
        </div>
    </section>
    <section class="playlist-item-details  d-flex">
        <div class="image-container col-12 nopadding">
            <a href="<?php echo $playlist->get_author_link() ?>">
                <img src="<?php echo $playlist->getAuthorImage() ?>" alt="">
            </a>
            <ul>
                <li>
                    <a href="<?php echo $playlist->get_author_link() ?>">
						<?php echo $playlist->getAuthorName() ?>
                    </a>
                </li>
				<?php if ( ! empty( get_option( 'foody_show_post_views' ) ) ) : ?>
                    <li>
						<?php echo $playlist->getViewCount() ?>
                    </li>
				<?php endif; ?>
                <li>
					<?php echo $playlist->getPostedOn() ?>
                </li>
            </ul>
        </div>

    </section>
    </section>
</div>


