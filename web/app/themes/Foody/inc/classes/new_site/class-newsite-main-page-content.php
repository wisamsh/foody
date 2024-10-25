<?php 
class MainPageContent{
public $pageID;
public function __construct()
{
    $this->pageID = get_the_ID(); 
}


public function get_MainBanner(){
return get_field("ns_hp_fetured_image_desktop", $this->pageID);
}


}//END CLASS
?>