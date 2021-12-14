<?php
$Foody_Questions = new Foody_Questions();
$Foody_Questions->Mobileattr();
$category = $Foody_Questions->getQuestionMainCategory();
$QuestionLink = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
/**
 * Implement the Custom Header feature.
 */

function FAQ_Scripts()
{
    $VersionHashCss = date('Y.m.d h.m');
    wp_register_style('QuestionsCSS', get_template_directory_uri() . '/components/css/css_questions.css', array(), $VersionHashCss);
    wp_enqueue_style('QuestionsCSS');
}
add_action('get_footer', 'FAQ_Scripts');
?>



<div class="row m-0">
    <div class="progress-wrapper no-print">
        <progress dir="ltr"></progress>
    </div>

    <section class="cover-image">
        <div class="cover-image">
            <?php echo $Foody_Questions->doCommercialBanner(); ?>
        </div>
    </section>
    <aside class="col d-none d-lg-block no-print" style="padding-top:0px;">

        <section class="sidebar-section foody-search-filter">
            <?php require(get_template_directory() . '/components/question_related_recipy_sidebar.php'); ?>
        </section>

    </aside>

    <article class="content">
        <section class="details-container">
            <div class="mainImage">
                <?php echo $Foody_Questions->MainQuestionImage(); ?>
            </div>
            <?php $Foody_Questions->Do_FoodyBeadcrumbs(); ?>
            <div class="mainentity" itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
                <div style="display:none;" itemprop="upvoteCount">1</div>
                <h2 itemprop="name" class="title question_Title">
                    <?php echo $Foody_Questions->Title(); ?>
                </h2>
                <div style="display:none;" itemprop="text">FOODY עונים על שאלות</div>
                <div style="display:none;"><span itemprop="answerCount">1</span> תשובות</div>
                <div style="display:none;"><span itemprop="upvoteCount">0</span> הצבעות</div>

                <div itemprop="acceptedAnswer" itemscope itemtype="https://schema.org/Answer">
                    <div style="display:none;" itemprop="upvoteCount">1</div>
                    <div itemprop="text" class="answer">
                        <?php
                        $answersArr = $Foody_Questions->get_answers(get_the_ID());
                        echo $answersArr[0]['answer_ind'];
                        ?>
                    </div>
                    <a itemprop="url" href="<?php echo $QuestionLink; ?>#acceptedAnswer" style="display:none;">לינק לתשובה</a>
                </div>

            </div>


        </section>

        <section class="feed-channel-details row">
            <?php require(get_template_directory() . '/components/qusetion_related_recipy.php'); ?>

        </section>
        <section class="recipe-categories categories no-print">
            <?php require(get_template_directory() . '/components/questions_related_questions.php'); ?>

        </section>
        <section class="recipe-categories categories no-print">
            <?php $Foody_Questions->the_categories(); ?>

        </section>

        <section class="recipe-categories categories accessories no-print">
            <?php echo $Foody_Questions->the_accessories(); ?>

        </section>

        <section class="recipe-categories categories technics no-print">
            <?php echo $Foody_Questions->the_techniques(); ?>

        </section>
        <section class="recipe-categories categories technics no-print">
            <?php echo $Foody_Questions->the_tags(); ?>

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

</div>
<?php //require(get_template_directory() . '/components/mobile_bottom_menu.php');
?>