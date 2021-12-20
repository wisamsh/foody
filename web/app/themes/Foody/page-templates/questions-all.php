<?php

/**
 * Template Name: FAQ All Questions
 * Created by Wisam Shomar.
 * Date: 10/12/2021
 * Time: 13:00PM
 */
get_header();
$faqClass = new Foody_Questions();
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
    <?php $faqClass->Do_FoodyBeadcrumbs(); ?>

    <div id="primary" class="content-area">
        <div class="container fluid">
            
                <?php
                $allQuestions = $faqClass->get_all_Questions();

                foreach ($allQuestions as $allQuestions) {
                ?>
                    <div class="accordion" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h1>
                        <a href="/questions/<?php echo $allQuestions['post_name']; ?>" target="_blank">
                        <?php echo $allQuestions['post_title']; ?></a> 
                        <i class="fas fa-question fa-xs"></i> 
                    </h1>
                    </div>
                    <div class="panel" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">

                        <div itemprop="text">
                           <?php echo $allQuestions['answer'];?>
                        </div>
                   
                    </div>
                     <?php }
                    ?>
           
            <!--CONTAINER-->
        </div><!-- #primary -->
    </div><!-- #main-content -->
    
    <script>
        var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
acc[i].addEventListener("click", function() {
    this.classList.toggle("activeacc");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
panel.style.maxHeight = panel.scrollHeight + "px";
    }
});
}
    </script>
    <?php

    get_footer();
?>