<?php

class Foody_Course_new
{

    private $course_data = [];
    private $course_sections = [];
    private $course_video;
    private $floating_buttons = [];

    /**
     * Course constructor.
     */
    public function __construct()
    {
        $this->course_data = get_field('course_page');

        $this->populate_course_properties();
    }

    public function get_floating_purchase_button()
    {
        $floating_purchase_button = '';
        if (isset($this->floating_buttons['floating_purchase_button'])) {
            $floating_purchase_button = $this->floating_buttons['floating_purchase_button'];
        }

        return $floating_purchase_button;
    }

    public function get_floating_gift_button()
    {
        $floating_gift_button = '';
        if (isset($this->floating_buttons['floating_gift_button'])) {
            $floating_gift_button = $this->floating_buttons['floating_gift_button'];
        }

        return $floating_gift_button;
    }

    public function get_cover_section(){
        if(is_array($this->course_data['main_cover_section']) && isset($this->course_data['main_cover_section'])){
            $cover_section = $this->course_data['main_cover_section'];

            /** cover image */
            $this->get_cover_or_host_images($cover_section['page_cover_desktop'],$cover_section['page_cover_mobile'], 'content-cover-image.php');

            /** host image */
            $this->get_cover_or_host_images($cover_section['host_image'], $cover_section['host_image'], 'content-course-host-image.php');

            /** cover title and texts */
            $this->get_course_cover_information($cover_section);

            /** extra texts, perks and additional data */
            $this->get_additional_data($cover_section);
        }
    }

    public function get_gift_and_purchase_buttons(){

    }

    private function get_additional_data($cover_section){
        $background_images = $this->get_background_images_by_section($cover_section);
        $more_about_course = isset($cover_section['more_about_course']) ? $cover_section['more_about_course'] : '';
        $promotion_title = isset($cover_section['promotion_title']) ? $cover_section['promotion_title'] : '';
        $promotion_text = isset($cover_section['promotion_text']) ? $cover_section['promotion_text'] : '';
        $course_numbers = (isset($cover_section['course_numbers']) && is_array($cover_section['course_numbers'])) ? $cover_section['course_numbers'] : [];

        $additional_data_section = '<div class="additional-data">';
        $more_about_course_div = '<div class="additional-data-text">' . $more_about_course . '</div>';
        $promotion_title_div = '<div class="promotion-title">' . $promotion_title . '</div>';
        $promotion_text_div = '<div class="promotion-text">' . $promotion_text . '</div>';
        $rating =  do_shortcode('[ratings]');
        $course_numbers_div = $this->get_course_numbers_div($course_numbers);

        if($course_numbers_div != '' && isset($background_images['bottom']) && $background_images['bottom'] != ''){
            $course_numbers_div = '<div class="numbers-and-bottom-image">' . $course_numbers_div;
        }

        $additional_data_section .= $more_about_course_div .  $promotion_title_div . $promotion_text_div . $rating . $course_numbers_div;

        if(isset($background_images['bottom'])) {
            $additional_data_section .= "<img src=\"". $background_images['bottom']."\"></div></div>";
        }
        else{
            $additional_data_section .= "</div>";
        }

        echo $additional_data_section;

    }

    private function get_course_numbers_div($course_numbers){
        $texts_for_numbers = ['num_of_videos' => __('סרטונים'), 'time_of_video' => __('ממוצע כל סרטון'), 'course_duration' => __('קורס כולל'), 'course_level' => __('רמה')];
        $course_numbers_div = '';

        if(isset($course_numbers) && is_array($course_numbers)){
            $course_numbers_div =  '<div class="course-numbers-container">';
            foreach ($course_numbers as $key => $number){
                $course_numbers_div .= '<div class="course-number-item">';
                $course_numbers_div .= '<div class="course-number">'. $number . '</div>';
                $course_numbers_div .= '<div class="course-number-key">'. $texts_for_numbers[$key] . '</div></div>';
            }
            $course_numbers_div .= '</div>';
        }
        return $course_numbers_div;
    }

    private function get_course_cover_information($cover_section){
        $course_title = isset($cover_section['course_title']) ? $cover_section['course_title'] : '';
        $host_name = isset($cover_section['host_name']) ? $cover_section['host_name'] : '';
        $page_cover_text = isset($cover_section['page_cover_text']) ? $cover_section['page_cover_text'] : '';
        $background_images = $this->get_background_images_by_section($cover_section);


        if(isset($background_images['top'])) {
//            $course_information = "<div class=\"course-information\" style=\"background-image: url( " . $background_images['top'] . " )\">";
            $course_information = "<div class=\"course-information\"><img src=\"". $background_images['top']."\">";
        }
        else{
            $course_information = '<div class="course-information">';
        }

        /** title and host */
        $title_div = '<div class="course-title">' . $course_title . '</div>';
        $host_div = '<div class="course-host-name">' . $host_name . '</div>';
        $text_div = '<div class="course-cover-text">' . $page_cover_text . '</div>';

        $course_information .= '<div class="cover-textual-information">'.$title_div . $host_div . $text_div . '</div></div>';

        echo $course_information;
    }

    private function get_background_images_by_section($section){
        $background_images = [];

        if(isset($section['top_background_image']) && isset($section['top_background_image']['url'])){
            $background_images['top'] = $section['top_background_image']['url'];
        }
        if(isset($section['bottom_background_image']) && isset($section['bottom_background_image']['url'])){
            $background_images['bottom'] = $section['bottom_background_image']['url'];
        }

        return $background_images;
    }

    private function get_cover_or_host_images($cover_desktop, $cover_mobile, $template_part){
        $desktop_image = '';
        $mobile_image = '';
        if(isset($cover_desktop['url'])){
            $desktop_image = $cover_desktop;
        }
        if(isset($cover_mobile['url'])){
            $mobile_image = $cover_mobile;
        }

        foody_get_template_part( get_template_directory() . '/template-parts/'. $template_part , array(
            'image'        => $desktop_image,
            'mobile_image' => $mobile_image,
            'link'         => ''
        ) );
    }

    /*
     * populate floating buttons and course video
     * section will remain in $course_data property
     */
    private function populate_course_properties()
    {
        $course_data = $this->course_data;
        if (is_array($course_data)) {
            /** floating buttons */
            if (isset($course_data['floating_buttons'])) {
                foreach ($course_data['floating_buttons'] as $key => $button) {
                    $this->floating_buttons[$key] = $button;
                }

                // remove from $course_data property
                unset($this->course_data['floating_buttons']);
            }

            /** video **/
            if (isset($course_data['course_video'])) {
                $this->course_video = $course_data['course_video'];

                // remove from $course_data property
                unset($this->course_data['course_video']);
            }
        }
    }
}