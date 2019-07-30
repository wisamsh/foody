<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

/** @var Foody_Channel $channel */
/** @noinspection PhpUndefinedVariableInspection */
$channel = $template_args['post'];

?>


<div class="channel-item feed-item">
    <a href="<?php echo $channel->link ?>">
        <div class="image-container main-image-container">
            <img class="channel-item-image feed-item-image" src="<?php echo $channel->getImage() ?>"
                 alt="<?php echo $channel->getTitle() ?>">
        </div>
    </a>


    <section class="channel-item-details  d-flex">
        <div class="image-container col-1 nopadding">
            <img src="<?php echo $channel->getAuthorImage() ?>" alt="<?php echo $channel->getAuthorName() ?>">
        </div>
        <section class="col-11">
            <h3>
                <a href="<?php echo $channel->link ?>">
					<?php echo $channel->getTitle() ?>
                </a>
            </h3>
            <ul>
                <li>
					<?php echo $channel->getAuthorName() ?>
                </li>
				<?php if ( ! empty( get_option( 'foody_show_post_views' ) ) ) : ?>
                    <li>
						<?php echo $channel->getViewCount() ?>
                    </li>
				<?php endif; ?>
                <li>
					<?php echo $channel->getPostedOn() ?>
                </li>
            </ul>
            <div class="description">
				<?php echo $channel->getDescription() ?>
            </div>
        </section>


    </section>
</div>