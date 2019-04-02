<?php
/**
 * Template Name: Homepage
 *
 * @package WordPress
 * @subpackage Foody_WordPress
 * @since Foody WordPress 1.0
 */


get_header();
Foody_Mailer::send('sub','e-book','zari@moveo.co.il');
$homepage = new Foody_HomePage();
?>

    <div class="homepage">

        <?php $homepage->cover_photo() ?>

        <div class="content">
            <div class="row recipes-grid gutter-10 featured">
                <?php $homepage->featured() ?>
            </div>

            <?php $homepage->categories_listing() ?>


            <?php $homepage->promoted_items(); ?>

            <?php

            $num = wp_is_mobile() ? 4 : 6;
            echo do_shortcode('[foody_team max="' . $num . '" show_title="true"]')

            ?>

            <section class="feed-container row">


                <section class="sidebar-container d-none d-lg-block">
                    <?php
                    $homepage->sidebar();
                    ?>
                </section>


                <section class="content-container col-lg-9 col-12">

                    <?php $homepage->feed(); ?>

                </section>

                <?php Foody_Seo::seo() ?>

            </section>


        </div>

        <?php

        foody_get_template_part(get_template_directory() . '/template-parts/common/mobile-filter.php', [
            'sidebar' => array($homepage, 'sidebar')
        ]);

        ?>

    </div>

<?php
    $is_social = Foody_User::is_current_user_social();
    $seen_approvals = Foody_User::has_user_seen_approvals();

    if($is_social && !$seen_approvals){
        $homepage->the_approvals_popup();
    }
?>
<?php
get_footer();
