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
        <div class="faq-breadcrumbs"> <?php $faqClass->Do_FoodyBeadcrumbs(); ?></div>

        <div id="primary" class="content-area">

            <div class="container fluid">
                <h1 class="title">שאלות ותשובות</h1>
                <?php if (wp_is_mobile()) { ?>
                    <div class="search_container">
                        <p class="sq">חפש שאלה</p>
                        <input type="text" id="search_Q" name="search_Q" class="search_question" />
                    </div>

                <?php } ?>


                <?php
                $allQuestions = $faqClass->get_all_Questions();

                foreach ($allQuestions as $allQuestions) {
                ?>

                    <h1 class="title_snich" data-name="<?php echo $allQuestions['post_title']; ?>">
                        <a class="all_q" href="/questions/<?php echo trim($allQuestions['post_name']); ?>" target="_blank">
                            <?php echo $allQuestions['post_title']; ?></a>
                        <i class="fas fa-question" style="font-size:21px;"></i>
                    </h1>



                <?php }

                ?>
            </div>

        </div><!-- #CONTAINER -->
</div><!-- primary -->
</article>
<script>
jQuery(document).ready(function() {
jQuery('#search_Q').on('input', jQuery.debounce(250, function() {
let str = jQuery(this).val();
//title_snich
jQuery(".title_snich").each(function(){
jQuery(this).attr("style", "display:none");
jQuery(".title_snich:contains("+ str +")" ).attr( "style", "block" );

});


}));

    });
</script>


<?php
get_footer();
?>

<?php if (wp_is_mobile()) {
    require(get_template_directory() . '/components/mobile_bottom_menu.php');
}
?>