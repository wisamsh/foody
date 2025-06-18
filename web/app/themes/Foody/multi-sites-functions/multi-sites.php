<?php 
// get_site_url();

if(get_site_url() == "https://foody.moveodevelop.com/"){

    function Do_disabilityCode(){

//echo '<script type="text/javascript" src="//acc.magixite.com/license/la?litk=guwoig46a1m"></script>';


wp_enqueue_script('dis-mehadrin', '//acc.magixite.com/license/la?litk=guwoig46a1');
    }

   // add_action('wp_head', 'Do_disabilityCode');
   add_action ('wp_enqueue_scripts', 'Do_disabilityCode');

}


?>