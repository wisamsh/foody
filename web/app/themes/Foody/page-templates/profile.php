<?php
/**
 * Template Name: Profile
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */

$foody_profile = new Foody_Profile();

get_header(); ?>

    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <div class="site-content" role="main">


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
                                    <div class="d-inline-block image-container col-2">
                                        <?php echo $foody_profile->get_image() ?>
                                    </div>
                                    <div class="name-email col-8">
                                        <h1 class="title m-0">
                                            <?php echo $foody_profile->get_name() ?>
                                        </h1>
                                        <span class="email">
                                            <?php echo $foody_profile->get_email() ?>
                                        </span>

                                        <ul class="nav nav-tabs col-12" id="profile-view-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a role="tab" data-toggle="tab"
                                                   href="#edit-user-details" aria-controls="edit-user-details"
                                                   aria-selected="false">
                                                    <?php echo __('ערוך', 'foody') ?>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a data-toggle="tab" role="tab" data-toggle="tab"
                                                   href="#change-password" aria-controls="change-password"
                                                   aria-selected="false">
                                                    <?php echo __('שנה סיסמא', 'foody') ?>
                                                </a>

                                            </li>
                                            <li class="nav-item">
                                                <a href="<?php echo wp_logout_url(get_home_url()) ?>">
                                                    <?php echo __('יציאה', 'foody') ?>
                                                </a>

                                            </li>
                                        </ul>
                                    </div>

                                    <section class="my-channels col d-xl-none d-block col-12">
                                        <h2 class="title">
                                            <?php echo __('הערוצים שלי', 'foody') ?>
                                        </h2>
                                        <section class="channels">
                                            <?php $foody_profile->my_followed_topics() ?>
                                        </section>

                                    </section>
                                </div>


                            </section>


                            <section class="profile-content tab-content container-fluid m-0">

                                <!-- Content and channels section -->
                                <section class="user-content tab-pane fade show active in row gutter-0" role="tabpanel"
                                         id="user-content">

                                    <!-- Favories and Channels tab links -->
                                    <ul class="nav nav-tabs col-12" id="user-content-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="home-tab" data-toggle="tab" role="tab"
                                               href="#my-recipes" aria-controls="my-recipes" aria-selected="true">
                                                <?php $foody_profile->favorites_tab() ?>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="profile-tab" data-toggle="tab"
                                               href="#my-channels-recipes" role="tab"
                                               aria-controls="my-channels-recipes"
                                               aria-selected="false">
                                                <?php $foody_profile->channels_tab() ?>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content col">
                                        <div class="tab-pane fade show active in row gutter-3" id="my-recipes"
                                             role="tabpanel"
                                             aria-labelledby="my-recipes-tab">
                                            <?php $foody_profile->my_favorites() ?>
                                        </div>
                                        <div class="tab-pane fade row gutter-3" id="my-channels-recipes" role="tabpanel"
                                             aria-labelledby="my-channels-recipes-tab">

                                            <?php $foody_profile->my_topics_content() ?>

                                        </div>
                                    </div>
                                    <!--  Followed authors and channels  -->
                                    <section class="my-channels col d-none d-xl-block pr-0">
                                        <h2 class="title">
                                            <?php echo __('הערוצים שלי', 'foody') ?>
                                        </h2>
                                        <section class="channels">
                                            <?php $foody_profile->my_followed_topics() ?>
                                        </section>

                                    </section>

                                </section>

                                <!-- Edit profile section -->
                                <section class="edit-user-details tab-pane fade" role="tabpanel"
                                         id="edit-user-details">
                                    <?php $foody_profile->the_user_details_form() ?>
                                </section>

                                <!-- Change password section -->
                                <section class="change-password tab-pane fade" role="tabpanel"
                                         id="change-password">
                                    <?php $foody_profile->the_password_change_form(); ?>
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
