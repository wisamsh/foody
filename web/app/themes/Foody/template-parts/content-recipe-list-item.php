<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

/** @var Foody_Recipe $recipe */
$recipe = $template_args['post'];

?>


<div class="recipe-item">
    <a href="<?php echo $recipe->link ?>">
        <div class="image-container main-image-container">
            <img class="recipe-item-image" src="<?php echo $recipe->getImage() ?>" alt="">
            <div class="duration">


                <i class="icon icon-timeplay">

                </i>
                <span>
                   <?php echo $recipe->getDuration() ?>
            </span>
            </div>
        </div>
    </a>
    <section class="recipe-item-details  d-flex">
        <div class="image-container col-1 nopadding">
            <img src="<?php echo $recipe->getAuthorImage() ?>" alt="">
        </div>
        <section class="col-11">
            <h3>
                <a href="<?php echo $recipe->link ?>">
                    <?php echo $recipe->getTitle() ?>
                </a>
            </h3>
            <ul>
                <li>
                    <?php echo $recipe->getAuthorName() ?>
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
                    'id' => $recipe->id
                )
            );
            ?>
        </section>


    </section>
</div>


