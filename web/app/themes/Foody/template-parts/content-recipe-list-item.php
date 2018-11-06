<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

/** @var Foody_Recipe $recipe */
/** @noinspection PhpUndefinedVariableInspection */
$recipe = $template_args['post'];

?>


<div class="recipe-item feed-item">
    <a href="<?php echo $recipe->link ?>">
        <div class="image-container main-image-container">
            <img class="recipe-item-image feed-item-image" src="<?php echo $recipe->getImage('list-item') ?>" alt="">

            <?php if (!empty($label = $recipe->get_label())): ?>

                <div class="recipe-label">
                    <!--                    <img src="-->
                    <?php //echo $GLOBALS['images_dir']. 'label.svg' ?><!--" alt="">-->
                    <span>

                    <?php echo $label ?>
                    </span>
                </div>

            <?php endif; ?>
            <?php if ($recipe->video != null): ?>
                <div class="duration">
                    <i class="icon icon-timeplay">

                    </i>
                    <span>
                        <?php echo $recipe->getDuration() ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </a>
    <section class="recipe-item-details  d-flex">
        <div class="image-container col-1 nopadding">
            <a href="<?php echo $recipe->get_author_link() ?>">
                <img src="<?php echo $recipe->getAuthorImage() ?>" alt="">
            </a>
        </div>

        <section class="col-11">
            <h3>
                <a href="<?php echo $recipe->link ?>">
                    <?php echo $recipe->getTitle() ?>
                </a>
            </h3>
            <ul>
                <li>
                    <a href="<?php echo $recipe->get_author_link() ?>">
                        <?php echo $recipe->getAuthorName() ?>
                    </a>
                </li>
                <li>
                    <?php echo $recipe->getViewCount() ?>
                </li>
                <li>
                    <?php echo $recipe->getPostedOn() ?>
                </li>
            </ul>
            <div class="description">
                <?php echo $recipe->getDescription() ?>
            </div>


            <?php
            foody_get_template_part(
                get_template_directory() . '/template-parts/common/favorite.php',
                array(
                    'id' => $recipe->id,
                    'post' => $recipe
                )
            );
            ?>
        </section>


    </section>
</div>


