<?php
/**
 * Template Name: Profile
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Malam WordPress 1.0
 */

$foody_profile = new Foody_Profile();

get_header(); ?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div id="content" class="site-content" role="main">


                <div class="container-fluid p-0">
                    <div class="row m-0">
                        <aside class="">
                            <?php $foody_profile->sidebar() ?>
                        </aside>

                        <div class="content  pr-0 pl-0">

                            <?php if (function_exists('bootstrap_breadcrumb')): ?>

                                <?php bootstrap_breadcrumb(); ?>

                            <?php endif; ?>
                            <section class="profile-top">
                                <div class="user-details row">
                                    <div class="d-inline-block image-container">
                                        <img src="<?php echo $foody_profile->get_image() ?>" alt="">
                                    </div>
                                    <div class="name-email">
                                        <h1 class="title m-0">
                                            <?php echo $foody_profile->get_name() ?>
                                        </h1>
                                        <span class="email">
                                        <?php echo $foody_profile->get_email() ?>
                                    </span>
                                    </div>

                                    <section class="my-channels col d-sm-none d-block">
                                        <h2 class="title">
                                            הערוצים שלי
                                        </h2>
                                        <section class="channels">
                                            <?php $foody_profile->my_channels() ?>
                                        </section>

                                    </section>
                                </div>

                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" role="tab"
                                           href="#my-recipes" aria-controls="my-recipes" aria-selected="true">המתכונים
                                            שלי (36)</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab" data-toggle="tab"
                                           href="#my-channels-recipes" role="tab" aria-controls="my-channels-recipes"
                                           aria-selected="false">מתכונים מערוצים (13)</a>
                                    </li>
                                </ul>
                            </section>


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
                        </div>
                    </div>

                </div>


            </div><!-- #content -->

        </div><!-- #primary -->
    </div><!-- #main-content -->

<?php
get_footer();
