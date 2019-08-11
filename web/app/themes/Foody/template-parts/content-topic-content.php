<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/2/18
 * Time: 11:54 AM
 */

// This

?>


<section class="profile-content row m-0">
    <div class="tab-content col">
        <div class="tab-pane fade show active row gutter-3" id="my-recipes" role="tabpanel"
             aria-labelledby="my-recipes-tab">
			<?php $foody_profile->my_favorites() ?>
        </div>
        <div class="tab-pane fade row gutter-3" id="my-channels-recipes" role="tabpanel"
             aria-labelledby="my-channels-recipes-tab">
            <h2 class="title">
				<?php echo __( 'מתכונים מערוצים', 'foody' ) ?>
            </h2>
			<?php $foody_profile->my_topics_content() ?>
        </div>
    </div>
    <section class="my-channels col d-none d-sm-block pr-0">
        <h2 class="title">
			<?php echo __( 'הערוצים שלי', 'foody' ) ?>
        </h2>
        <section class="channels">
			<?php $foody_profile->my_followed_topics() ?>
        </section>

    </section>
</section>
