
<?php
/**
 * Implement the Custom Header feature.
 */

function getfaq_header_script(){
    //wp_register_script('HeaderQuestionsScript', get_template_directory_uri(). '/resources/js/layout/header.js');
//wp_enqueue_script('HeaderQuestionsScript');

echo '<script id="faqscript" src="'. get_template_directory_uri() .'/resources/js/layout/header.js"></script>';

}
add_action('wp_head', 'getfaq_header_script');
function FAQ_Scripts(){
    $VersionHashCss = date('Y.m.d h.m');
    wp_register_style('QuestionsCSS', get_template_directory_uri().'/components/css/css_questions.css', array(), $VersionHashCss);
    wp_enqueue_style('QuestionsCSS');
//wp_register_script('HeaderQuestionsScript', get_template_directory_uri(). '/resources/js/layout/header.js');
//wp_enqueue_script('HeaderQuestionsScript');
}
add_action('get_footer', 'FAQ_Scripts');

$Foody_Questions = new Foody_Questions();

//$Foody_Questions->the_details();
?>
<style>
.quadmenu-container{display:none;}

</style>
<div class="container-fluid demicont">
    <div class="row">
        <div class="col"><?php echo $Foody_Questions->Docommorcial();?></div>
    </div>
</div>
<div class="container-fluid demicont"> 
    <div class="row m-0">
        <div class="col-lg-3 d-xs-none d-print-none aside"><?php $Foody_Questions->the_sidebar_content(); ?></div>
        <div class="col-lg-9 col-xs-12"><?php $Foody_Questions->feed(); ?></div>
    </div>
</div>