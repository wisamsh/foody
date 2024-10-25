<?php 
class MainPageContent{
public $pageID;
public function __construct()
{
    $this->pageID = get_the_ID(); 
}


public function get_MainBanner(){
 if(!wp_is_mobile()){
    return get_field("ns_hp_fetured_image_desktop");
 }
 else{
    return get_field("ns_hp_fetured_image_mobile");
 }
}


}//END CLASS
?>