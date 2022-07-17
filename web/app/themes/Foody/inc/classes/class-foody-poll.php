<?php

class Foody_poll
{


    private function pid($request_pid = null)
    {
        return $pid = isset($request_pid) ?  $request_pid : get_the_ID();
    }

    public function Title()
    {
        return $Title = get_the_title();
    }

    private function ImageVideoBanner($type, $src, $link = null, $src_mob = null)
    {
        switch ($type) {
            case 'image':

                $rtn = isset($link) && $link != null ? '<a href=' . $link . ' target="_blank">' : '';
                $rtn .= '<picture>';
                $rtn .= '<source media="(min-width: 800px)" srcset="' . $src . '"> ';
                $rtn .= '<source media=" (max-width: 799px)" srcset="' . $src_mob . '"> ';
                $rtn .= '<img src="' . $src . '"/>';
                $rtn .= '</picture>';
                $rtn .= isset($link) && $link != null ? '</a>' : '';

                break;




            case 'video':
                $src = 'https://www.youtube.com/embed/' . get_field('fq_type_video_embed', $this->pid());
                $VideoHeight = wp_is_mobile() ? '200' : '380';
                $rtn = '<iframe id="questionvideo" width="100%" height="' . $VideoHeight . '" src="' . $src . '"controls=0">
			</iframe>';
                break;
        }
        return $rtn;
    }



    public function Do_FoodyBeadcrumbs()
    {
        echo '<section class="accessory-details-container">';
        bootstrap_breadcrumb();
        echo '</section>';
    }

    public function doCommercialBanner($PostId = null)
    {
        $imagelink = '';
        $src = get_field('poll_banner', $this->pid());
        $src_mobile = get_field('poll_banner_mobile', $this->pid());

        $rtn = $this->ImageVideoBanner('image', $src, $imagelink, $src_mobile);

        return $rtn;
    }

    private function get_the_poll()
    {
        $poll_questions = get_field("poll_questions", $this->pid());
        return $poll_questions;
    }

    private function do_checkboxses($id, $text, $requierd, $name)
    {
       // $require = isset($requierd) ? "requierd" : "";
        $thecheckbox = '<div class="col-sm-12 col-md-6 col-lg-6 pol_q">';
$for = "check_" . $id ;
        $thecheckbox .= '<span class="label_box"><input type="checkbox" id="check_' . $id . '" name="' . $name . '" value="' . $id . '"  /></span>';
        $thecheckbox .= '<label  for="'.$for.'"><span class="label_box">' . $text . '</span></lable>';
        $thecheckbox .= '</div>';
        return $thecheckbox;
    }


    private function do_radioButton($id, $text, $requierd, $name)
    {
        //$require = isset($requierd) ? "requierd" : "";
        $theRadio = '<div class="col-sm-12 col-md-6 col-lg-6 pol_q">';
$for = 'radio_' . $id  ;
        $theRadio .= '<span class="label_box"><input type="radio" id="radio_' . $id . '" name="' . $name . '" value="' . $id . '"  /></span>';
        $theRadio .= '<label  for="'.$for.'"><span class="label_box">' . $text . '</span></lable>';
        $theRadio .= '</div>';
        return $theRadio;
    }



    public function get_poll_questions()
    {
        $poll_q = $this->get_the_poll();
        $poll_second_title = get_field("poll_second_title", $this->pid());

        $first_question_check = get_field('first_question_check', $this->pid());
		echo '<div class="form_wrapper">';       
		echo '<form id="poll" method="POST">';
        echo '<div class="poll_second_title">' . $poll_second_title . '</div>';
        foreach ($poll_q as $key => $poll_questions) {
            $text =  $poll_questions["question_added"];
            $poll_question_answer = $poll_questions['poll_question_answer'];
			$requierd =  $poll_questions["required_field"] == true ? "required" : ""  ;

            if ($poll_questions["poll_question_active"] == 1) {
                echo '<div class="container pollcont"><div class="row poll_row">';

                echo '<div class="the_question_div">' . $text . '</div>';
                if ($first_question_check == 1 && $key == 0) {
					
                    foreach ($poll_question_answer as $poll_question_answer) {
                        $id = $poll_question_answer->ID;
                        $text = get_field("poll_client_answer", $id);//$poll_question_answer->post_title;
                        $name = "nm_" . $key;
						
                        echo $this->do_checkboxses($id, $text, $requierd ,$name);
                    }
                } else {
                    foreach ($poll_question_answer as $poll_question_answer) {

                        $id = $poll_question_answer->ID;
                        $text =  $text = get_field("poll_client_answer", $id);//$poll_question_answer->post_title;
                        $name = "nm_" . $key;

                        echo $this->do_radioButton($id, $text, $requierd, $name);
                    }
                }
                echo '</div></div>';
            }
        }
        echo ' <div class="poll_calc_btn" id="poll_calc_btn">'; 
		echo ' <input type="hidden" id="cluster" name="cluster" value="'.$this->pid().'"> ';
		echo '<input type="submit" id="submit_btn" value="תראו לי תפריטים!"/></div> </form></div>'; //closer for form_wrapper
    }

    public function get_poll_text_content()
    {
        $content = get_field("poll_content", $this->pid());
        return $content;
    }

public function DoBackgroundImage(){
    $bk_image =  get_field("background_poll_image", $this->pid());
   echo '<style>
   body{
       background : url("'.$bk_image.'");
       background-attachment: fixed; 
       background-repeat: no-repeat;
       background-size: cover;
   }
   .site-footer{
       background : #fafafaf0 !important;
       z-index:9999;
       
   }
   </style>' ;
}
    public function Get_Poll_Posts_IntrestYou()
    {
        $content = get_field("menus_can_interes_you", $this->pid());
        $Title = get_field("menu_title", $this->pid());
        $rtn = '';
        $img = '';
        if (!empty($content)) {
            $rtn .= '<div class="the_Special_div" id="interesting_menus">' . $Title . '</div>';
            $rtn .= '<div class="container">';
            $rtn .= '<div class="row text-center">';
            foreach ($content as $link) {
                $rtn .= '<div class="col-6 col-md-3 col-lg-3 col-xl-3">';



                $img = !wp_is_mobile() ? $link["mciy_image_desktop"] : $link["mciy_image_mobile"];
                $rtn .= '<a href="/?p=' . $link["mciy_post_url"] . '"><img src="' . $img . '" class="int_imge"/>';
                //if(wp_is_mobile()){
                // $rtn .= '<span class="fr">'.$link["mciy_link_text"].'</span>';
                //  $rtn .='<span class="fl">»»</span>';
                // $rtn .='<div class="cb"></div>';
                //}
                //else{
                $rtn .= '<div class="titles_div">' . $link["mciy_link_text"] . '</div>';

                //}

                $rtn .= '</a></div>'; //col
            }
            $rtn .= '</div>'; //row
            $rtn .= '</div>'; //container
        }
        return $rtn;
    }




    public function pol_side_the_recipe()
    {
        $pol_side_the_recipe = get_field("pol_side_the_recipe", $this->pid());
        $poll_right_side_title = get_field("poll_right_side_title", $this->pid());
        $rtn = '';
        if (!empty($pol_side_the_recipe)) {
            $rtn .= '<div class="title related-content-title">' . $poll_right_side_title . '</div><br>';
            $rtn .= '<ul class="related-content nolist related-recipes">';

            foreach ($pol_side_the_recipe as $p) {

                $title = get_the_title($p);
                $thumb = get_the_post_thumbnail_url($p);
                $author_id = get_post_field('post_author', $p);
                $recent_author = get_user_by('ID', $author_id);
                $author_display_name = $recent_author->display_name;

                $rtn .= '<li class="related-item playlist">
                <div class="image-container">
                <a href="/?p=' . $p . '">
                <img class="recipe-item-image" src="' . $thumb . '" alt="' . $title . '">
                </div>
                </a>
                <div class="details">
                <div class="post-title">
                <a href="/?p=' . $p . '">' . $title . ' </a>
                </div>
                <a class="author-name" href="/?p=' . $p . '">' . $author_display_name . '</a>
                </div>
                <div class="excerpt">              
                </li>';
                            }
            $rtn .= '</ul>';
        }
        return $rtn;
    }



    public function Mobile_Recepies()
    {
        $rtn = "";
        $poll_mobile_recipe_title = get_field("poll_mobile_recipe_title", $this->pid());
        $poll_mobile_recipe_list = get_field("poll_mobile_recipe_list", $this->pid());
        if (!empty($poll_mobile_recipe_list)) {
            
            $rtn .= '<h2 class="title">' . $poll_mobile_recipe_title . '</h2>';
            $rtn .= '<div class="container fluid"><div class="row">';
            //$rtn .= '<div class="related_recepies_conduct">';
                 
            foreach ($poll_mobile_recipe_list as $p) {

                $title = get_the_title($p);
                $thumb = get_the_post_thumbnail_url($p);
                
                $rtn .= '
                    <div class="related_recepies_conduct" data-title="'.$title.'">
                    <a href="/?p='.$p.'" >
                    <img src="'.$thumb.'">
                    <p>'.$title.'</p></a>
                    </div>';

               
            }
            $rtn .= '</div></div>';

            return $rtn;
        }
    }



    public function Mobileattr()
    {
        if (wp_is_mobile()) {
            echo '<style> 
				#masthead{display:none;}
				#content {
				padding-top: 0px; 
				}
				</style>';
        }
    }




    public function the_details()
    {
        echo '<section class="technique-details-container">';
        bootstrap_breadcrumb();
        echo '</section>';
    }


    public function get_answers($pid)
    {
        $answers_array = get_field("answers", $pid);
        return $answers_array;
    }

    private function poll_answers()
    {
        $defaults = array(
            'numberposts'      => -1,
            'category'         => 0,
            'orderby'          => 'date',
            'order'            => 'DESC',
            'include'          => array(),
            'exclude'          => array(),
            'meta_key'         => '',
            'meta_value'       => '',
            'post_type'        => 'poll_answers',
            'post_status'      => array('publish'),
            'suppress_filters' => true,

        );

        return $defaults;
    }
}//class ends here