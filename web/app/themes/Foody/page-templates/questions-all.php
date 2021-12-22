<?php

/**
 * Template Name: FAQ All Questions
 * Created by Wisam Shomar.
 * Date: 10/12/2021
 * Time: 13:00PM
 */
get_header();
$faqClass = new Foody_Questions();
$faqClass->Mobileattr();
function FAQ_Scripts()
{
    $VersionHashCss = date('Y.m.d h.m');
    wp_register_style('QuestionsCSS', get_template_directory_uri() . '/components/css/css_questions.css', array(), $VersionHashCss);
    wp_enqueue_style('QuestionsCSS');
}
add_action('get_footer', 'FAQ_Scripts');
?>
<link href="https://pro.fontawesome.com/releases/v6.0.0-beta3/css/all.css" rel="stylesheet">


<div id="main-content" class="main-content">

    <article class="content">
        <div class="faq-breadcrumbs"> <?php $faqClass->Do_FoodyBeadcrumbs(); ?>
            <h1 class="title">שאלות ותשובות</h1>
        </div>

        <div id="primary" class="content-area">
            <div class="container fluid">

                <?php
                $allQuestions = $faqClass->get_all_Questions();

                foreach ($allQuestions as $allQuestions) {
                ?>
                    
                        <h1 >
                            <a class="all_q" href="/questions/<?php echo trim($allQuestions['post_name']); ?>" target="_blank">
                                <?php echo $allQuestions['post_title']; ?></a>
                            <i class="fas fa-question fa-xs"></i>
                        </h1>
                    
                    
                    
                <?php }
                
                ?>
</div>
              
            </div><!-- #CONTAINER -->
        </div><!-- primary -->
    </article>
    
    <?php

    get_footer();
    ?>

    <?php if (wp_is_mobile()) {
        require(get_template_directory() . '/components/mobile_bottom_menu.php');
    }
    ?>