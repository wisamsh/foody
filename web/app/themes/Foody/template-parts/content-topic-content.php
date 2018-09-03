<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/2/18
 * Time: 11:54 AM
 */

?>



<section class="profile-content row m-0">
    <div class="tab-content col">
        <div class="tab-pane fade show active row gutter-3" id="my-recipes" role="tabpanel"
             aria-labelledby="my-recipes-tab">
            <?php $foody_profile->my_recipes() ?>
        </div>
        <div class="tab-pane fade row gutter-3" id="my-channels-recipes" role="tabpanel"
             aria-labelledby="my-channels-recipes-tab">
            <h2 class="title">
                מתכונים מערוצים
            </h2>
            <?php $foody_profile->my_channels_recipes() ?>
        </div>
    </div>
    <section class="my-channels col d-none d-sm-block pr-0">
        <h2 class="title">
            הערוצים שלי
        </h2>
        <section class="channels">
            <?php $foody_profile->my_channels() ?>
        </section>

    </section>
</section>
