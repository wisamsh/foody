<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

/** @var Foody_Playlist $playlist */
/** @noinspection PhpUndefinedVariableInspection */
$playlist = $template_args['post'];

?>


<div class="playlist-item feed-item">
    <a href="<?php echo $playlist->link ?>">
        <div class="image-container main-image-container">
            <img class="playlist-item-image feed-item-image" src="<?php echo $playlist->getImage() ?>" alt="">
            <div class="playlist-count">
                <i class="icon-timeplay"></i>

                <div class="count">
                    <?php echo sprintf('%s מתכונים', $playlist->num_of_recipes) ?>
                </div>

            </div>
        </div>
    </a>


    <section class="playlist-item-details  d-flex">
        <div class="image-container col-1 nopadding">
            <a href="<?php echo $playlist->get_author_link() ?>">
                <img src="<?php echo $playlist->getAuthorImage() ?>" alt="">
            </a>
        </div>
        <section class="col-11">
            <h3>
                <a href="<?php echo $playlist->link ?>">
                    <?php echo $playlist->getTitle() ?>
                </a>
            </h3>
            <ul>
                <li>
                    <?php echo $playlist->getAuthorName() ?>
                </li>
                <li>
                    <?php echo $playlist->getViewCount() ?>
                </li>
                <li>
                    <?php echo $playlist->getPostedOn() ?>
                </li>
            </ul>
            <div class="description">
                <?php echo $playlist->getDescription() ?>
            </div>
        </section>


    </section>
</div>


