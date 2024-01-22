<?php
$ManualScriptAdd = new ManualScriptAdd();
$Site_ManualScripts = $ManualScriptAdd->AddScriptToSite();

if (is_array($Site_ManualScripts)) {
    foreach ($Site_ManualScripts as $key => $value) {

        foreach ($value as $page_key => $page_script) {
            

            switch ($page_key) {
                case (trim('home')):
                    if (is_front_page() || is_home()) {

                       if(str_ends_with($page_script, '.js')){
                        echo '<script src = "'. $page_script .'"></script>';
                       }
                        else{ echo '<script>' . $page_script . '</script>';}
                  
                    }

                    break;
                case (trim('recipe')):

                    if ('foody_recipe' === get_post_type()) {
                        if(str_ends_with($page_script, '.js')){
                            echo '<script src = "'. $page_script .'"></script>';
                           }
                            else{ echo '<script>' . $page_script . '</script>';}
                   
                    }

                    break;

                case (trim('post')):
                    if ('post' === get_post_type()) {
                        if(str_ends_with($page_script, '.js')){
                            echo '<script src = "'. $page_script .'"></script>';
                           }
                            else{ echo '<script>' . $page_script . '</script>';}
                    }

                    break;
            }
        }
    }
}
