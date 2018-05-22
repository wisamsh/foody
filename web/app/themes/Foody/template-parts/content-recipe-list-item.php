<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 8:21 PM
 */

$recipe = new Recipe();

?>


<div class="recipe-item">

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

    <section class="recipe-item-details  d-flex">
        <div class="image-container col-1 nopadding">
            <img src="<?php echo $recipe->getAuthorImage() ?>" alt="">
        </div>
        <section class="col-10">
            <h3>
                <?php echo $recipe->getTitle() ?>
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
        </section>


        <i class="icon-heart col-1">

        </i>
    </section>
</div>


