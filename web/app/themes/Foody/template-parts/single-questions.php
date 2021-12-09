<?php
$Foody_Questions = new Foody_Questions();
$Foody_Questions->Mobileattr();
$category = $Foody_Questions->getQuestionMainCategory();

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
        <?php require(get_template_directory() . '/components/question_related_recipy_sidebar.php');?>
        </section>

    </aside>

    <article class="content">
        <section class="details-container">
            <div class="mainImage">
            <?php echo $Foody_Questions->MainQuestionImage(); ?>

            </div>
           
            
            <?php $Foody_Questions->Do_FoodyBeadcrumbs(); ?>
            <h1 class="title question_Title"><?php echo $Foody_Questions->Title(); ?></h1>
        </section>

            <section class="feed-channel-details row">
                <?php echo the_content() ?>

            </section>

            <section class="feed-channel-details row">
            <?php require(get_template_directory() . '/components/qusetion_related_recipy.php');?>

            </section>
            <section class="recipe-categories categories no-print">
                <?php $Foody_Questions->the_categories(); ?>

            </section>

    </article>

</div>