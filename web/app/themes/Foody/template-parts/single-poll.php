<?php

$Foody_Poll = new Foody_poll();
$Foody_Poll->Mobileattr();
function FAQ_Scripts()
{
    $VersionHashCss = date('Y.m.d h.m');
    wp_register_style('QuestionsCSS', get_template_directory_uri() . '/components/css/css_questions.css', array(), $VersionHashCss);
    wp_enqueue_style('QuestionsCSS');
    wp_register_style('poll_css', get_template_directory_uri() . '/components/css/poll.css', array(), $VersionHashCss);
    wp_enqueue_style('poll_css');

    wp_register_script('poll', get_template_directory_uri() . '/components/js/poll.js', array(), $VersionHashCss);
    wp_enqueue_script('poll');
}
add_action('get_footer', 'FAQ_Scripts');


wp_enqueue_script('GlobalsW', get_template_directory_uri() . '/components/js/questions_events.js', array('wp-api'));

?>
<?php 

echo $Foody_Poll->DoBackgroundImage();

?>
<div class="row m-0">


    <section class="cover-image no-print">
        <div class="cover-image">
            <?php echo $Foody_Poll->doCommercialBanner(); ?>
        </div>
    </section>
    <aside class="col d-none d-lg-block no-print" style="padding-top:0px;">

        <section class="sidebar-section foody-search-filter">
            <?php echo $Foody_Poll->pol_side_the_recipe(); ?>
        </section>

    </aside>

    <article class="content">
        <section class="details-container">

            <?php $Foody_Poll->Do_FoodyBeadcrumbs(); ?>

            <div class="mainentity" itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">

                <h1 itemprop="name" class="title question_Title">
                    <?php echo $Foody_Poll->Title(); ?>
                </h1>

                <div class="poll_content"> <?php echo $Foody_Poll->get_poll_text_content(); ?></p>
                </div>


                <?php $Foody_Poll->get_poll_questions(); ?>



        </section>

        
        <section class="recipe-categories categories no-print">
            תפריטים עבורך--TODO
        </section>
        <section class="recipe-categories categories no-print">

            <?php echo $Foody_Poll->Get_Poll_Posts_IntrestYou(); ?>
        </section>


        <section class="feed-channel-details row no-print dn">
            <?php if (wp_is_mobile()) {
                echo $Foody_Poll->Mobile_Recepies();
            } ?>


        </section>


        <section class="newsletter no-print">

            <?php if (!wp_is_mobile()) { ?>
                <section class="newsletter no-print">
                    <div class="newsletter-title">
                        אל תפספסו את המתכונים החמים!</div>

                    <section class="newsletter">
                        <?php echo do_shortcode('[contact-form-7 id="10340" title="ניוזלטר"]'); ?>

                    </section>

                </section>
            <?php } ?>
    </article>
    <input style="display:none;" type="text" id="user_holdon" class="user_holdon" value="0" />
</div>
<?php

if (wp_is_mobile()) {
    require(get_template_directory() . '/components/mobile_bottom_menu.php');
}
?>