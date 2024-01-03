<?php

error_reporting(0);
class ManualScriptAdd {
   
    public $print_the_script = array();

    protected function ManualScript() {
        $manualScript = get_field("manual_site_script", "option");
        foreach ($manualScript as $key=>$script) {
            $area_zone = $script["script_area_type"];
            $script_content = $script['script_content'];
            $this->print_the_script[$key][$area_zone] = $script_content ;
        }
        return $this->print_the_script;
    }

public function AddScriptToSite(){
    $ScriptArray = $this->ManualScript();
    
    if(!empty( $ScriptArray )){
        return $ScriptArray;
    }
   else{
    return "0"; 
   }
}

}

?>
