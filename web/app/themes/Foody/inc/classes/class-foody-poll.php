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

    private function do_checkboxses($id, $text, $requierd)
    {
        $require = isset($requierd) ? "requierd" : "";
        $thecheckbox = '<div class="col-sm-12 col-md-6 col-lg-6 pol_q">';
       
        $thecheckbox .= '<span class="label_box"><input type="checkbox" id="check_' . $id . '" name="' . $id . '" value="' . $id . '" ' . $require . ' /></span>';
        $thecheckbox .= '<span class="label_box">' . $text . '</span>' ;
        $thecheckbox .= '</div>';
        return $thecheckbox;
    }


    private function do_radioButton($id, $text, $requierd, $name)
    {
        $require = isset($requierd) ? "requierd" : "";
        $theRadio = '<div class="col-sm-12 col-md-6 col-lg-6 pol_q">';
       
        $theRadio .= '<span class="label_box"><input type="radio" id="radio_' . $id . '" name="' . $name . '" value="' . $id . '" ' . $require . ' /></span>';
        $theRadio .= '<span class="label_box">' . $text . '</span>' ;
        $theRadio .= '</div>';
        return $theRadio;
    }



    public function get_poll_questions()
    {
        $poll_q = $this->get_the_poll();
        $poll_second_title = get_field("poll_second_title", $this->pid());
        
        $first_question_check = get_field('first_question_check', $this->pid());
       echo '<form id="poll" method="POST">';
       echo '<div class="poll_second_title">' . $poll_second_title . '</div>';
        foreach ($poll_q as $key => $poll_questions) {
            $text =  $poll_questions["question_added"];
            $poll_question_answer = $poll_questions['poll_question_answer'];
           
            echo '<div class="container pollcont"><div class="row poll_row">';

            if ($poll_questions["poll_question_active"] == 1) {

                echo '<div class="the_question_div">' . $text . '</div>';
                if ($first_question_check == 1 && $key == 0) {

                    foreach ($poll_question_answer as $poll_question_answer) {
                        $id = $poll_question_answer->ID;
                        $text = $poll_question_answer->post_title;

                        echo $this->do_checkboxses($id, $text, '');
                    }
                } else {
                    foreach ($poll_question_answer as $poll_question_answer) {
                       
                        $id = $poll_question_answer->ID;
                        $text = $poll_question_answer->post_title;
                        $name = "nm_" . $key;

                        echo $this->do_radioButton($id, $text, '', $name);
                    }
                }
            }
            echo '</div></div>';
        }
        echo ' <div class="poll_calc_btn"><input type="submit" value="תראו לי תפריטים!"/></div> </form>';
    }


    public function get_poll_text_content()
    {
        $content = get_field("poll_content", $this->pid());
        return $content;
    }



    public function the_sidebar_content($args = array())
    {
        echo '<section class="sidebar-section foody-search-filter">sidebar';

        //$foody_query = SidebarFilter::get_instance();
        //$foody_query->the_filter();

        echo '</section>';
        //dynamic_sidebar('questions');
        //dynamic_sidebar( 'foody-social' );
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