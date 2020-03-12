<?php

class Foody_Course_new
{

    private $course_data = [];
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

    public function get_cover_section()
    {
        if (is_array($this->course_data['main_cover_section']) && isset($this->course_data['main_cover_section'])) {
            $cover_section = $this->course_data['main_cover_section'];

            /** cover image */
            $this->get_cover_or_host_images($cover_section['page_cover_desktop'], $cover_section['page_cover_mobile'], 'content-cover-image.php');

            /** host image */
            $this->get_cover_or_host_images($cover_section['host_image'], $cover_section['host_image'], 'content-course-host-image.php');

            /** cover title and texts */
            $this->get_course_cover_information($cover_section);

            /** extra texts, perks and additional data */
            $this->get_additional_data($cover_section);
        }
    }

    public function get_video_section(){
        $video_section ='';
        if (is_array($this->course_data['course_video_group']) && isset($this->course_data['course_video_group'])) {
            $video_group = $this->course_data['course_video_group'];
            $background_images = $this->get_background_images_by_section($video_group);
            $purchase_button_link = $this->get_floating_purchase_button();
            $purchase_button_text = isset($purchase_button_link['title']) ? $purchase_button_link['title'] : '';
            $purchase_button_link = isset($purchase_button_link['url']) ? $purchase_button_link['url'] : '';

            if(isset($video_group['course_video']) && !empty($video_group['course_video'])){
                $video = $video_group['course_video'];

                $video_div = '<div class="main-video">' . $video . '</div>';

                if (isset($background_images['top'])) {
                    $video_section = "<div class=\"course-main-video\"><div class='course-main-video-image-container'><img class='top-image' src=\"" . $background_images['top'] . "\">";
                } else {
                    $video_section = '<div class="course-main-video"><div class="course-main-video-image-container">';
                }

                $video_section .= $video_div;

                if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                    $video_section .= "<a class='video-section-purchase' href='". $purchase_button_link ."' >".$purchase_button_text."</a><img class='bottom-image' src=\"" . $background_images['bottom'] . "\"></div>";
                }
                else{
                    $video_section .= "<a class='video-section-purchase' href='". $purchase_button_link ."' >".$purchase_button_text."</a></div>";
                }
            }
        }
        echo $video_section;
    }

    public function get_gift_and_purchase_buttons_div()
    {
        $purchase_button = $this->get_floating_purchase_button();
        $gift_button = $this->get_floating_gift_button();
        $purchase_button_div = $gift_button_div = '';
        $has_button = false;
        $buttons_div = '';

        if (isset($purchase_button['url']) && !empty($purchase_button['url'])) {
            $has_button = true;
            $purchase_button_text = isset($purchase_button['title']) && !empty($purchase_button['title']) ? $purchase_button['title'] : __('לרכישה');
            $purchase_button_div = '<a class="purchase-button-div" href="' . $purchase_button['url'] . '">' . $purchase_button_text . '</a>';
        }

        if (isset($gift_button['url']) && !empty($gift_button['url'])) {
            $has_button = true;
            $gift_button_text = isset($gift_button['title']) && !empty($gift_button['title']) ? $gift_button['title'] : __('לרכישה');
            $gift_button_div = '<a class="gift-button-div" href="' . $gift_button['url'] . '">' . $gift_button_text . '<img src="' . get_template_directory_uri() . '/resources/images/group-3.svg"/></a>';
        }

        if ($has_button) {
            $buttons_div = '<div class="buttons-container">' . $purchase_button_div . $gift_button_div . '</div>';
        }

        echo $buttons_div;
    }

    public function get_whats_waiting_section()
    {
        $course_what_waiting = '';
        if (is_array($this->course_data['what_waits_section']) && isset($this->course_data['what_waits_section'])) {
            $what_waiting_section = $this->course_data['what_waits_section'];
            $what_waiting_title = isset($what_waiting_section['what_waits_title']) ? $what_waiting_section['what_waits_title'] : '';
            $background_images = $this->get_background_images_by_section($what_waiting_section);
            $course_content_items = isset($what_waiting_section['course_content_items']) ? $what_waiting_section['course_content_items'] : '';

            $what_waiting_title_div = '<div class="what-waiting-title">' . $what_waiting_title . '</div>';
            $course_content_items_div = $this->get_course_content_items_div($course_content_items);

            if (isset($background_images['top'])) {
                $course_what_waiting = "<div class=\"course-what-waiting\"><div class='what-waiting-title-image-container'><img src=\"" . $background_images['top'] . "\">";
            } else {
                $course_what_waiting = '<div class="course-what-waiting"><div class="what-waiting-title-image-container">';
            }

            $course_what_waiting .= $what_waiting_title_div . '</div>' . $course_content_items_div;

            if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                $course_what_waiting .= "<img src=\"" . $background_images['bottom'] . "\"></div>";
            }
            else{
                $course_what_waiting .= '</div>';
            }
        }
        echo $course_what_waiting;
    }

    private function get_course_content_items_div($course_content_items)
    {
        $course_content_items_section = '';
        if (is_array($course_content_items)) {
            $course_content_items_section = '<div class="course-content-items">';

            foreach ($course_content_items as $index => $item) {
                if ($index % 3 == 0) {
                    $course_content_items_section .= '<div class="course-content-items-row">';
                }
                if (isset($course_content_items[$index]) && is_array($course_content_items[$index])) {
                    if (isset($item['course_content_item_image']) && isset($item['course_content_item_image']['url']) && isset($item['course_content_item_title'])) {
                        $course_content_item = '<div class="course-content-item">';
                        $item_div = '<img class="item-image" src="' . $item['course_content_item_image']['url'] . '"/>';
                        $item_title = '<span class="item-title">' . $item['course_content_item_title'] . '</span>';

                        $course_content_item .= $item_div . $item_title . '</div>';
                        $course_content_items_section .= $course_content_item;
                    }
                }
                if($index % 3 == 2){
                    $course_content_items_section .= '</div>';
                }
            }
            $course_content_items_section .= '</div>';
        }
        return $course_content_items_section;
    }

    private function get_additional_data($cover_section)
    {
        $background_images = $this->get_background_images_by_section($cover_section);
        $more_about_course = isset($cover_section['more_about_course']) ? $cover_section['more_about_course'] : '';
        $promotion_title = isset($cover_section['promotion_title']) ? $cover_section['promotion_title'] : '';
        $promotion_text = isset($cover_section['promotion_text']) ? $cover_section['promotion_text'] : '';
        $course_numbers = (isset($cover_section['course_numbers']) && is_array($cover_section['course_numbers'])) ? $cover_section['course_numbers'] : [];

        $additional_data_section = '<div class="additional-data">';
        $more_about_course_div = '<div class="additional-data-text">' . $more_about_course . '</div>';
        $promotion_title_div = '<div class="promotion-title">' . $promotion_title . '</div>';
        $promotion_text_div = '<div class="promotion-text">' . $promotion_text . '</div>';
        $rating = do_shortcode('[ratings]');
        $course_numbers_div = $this->get_course_numbers_div($course_numbers);

        if ($course_numbers_div != '' && isset($background_images['bottom']) && $background_images['bottom'] != '') {
            $course_numbers_div = '<div class="numbers-and-bottom-image">' . $course_numbers_div;
        }

        $additional_data_section .= $more_about_course_div . $promotion_title_div . $promotion_text_div . $rating . $course_numbers_div;

        if (isset($background_images['bottom'])) {
            $additional_data_section .= "<img src=\"" . $background_images['bottom'] . "\"></div></div>";
        } else {
            $additional_data_section .= "</div>";
        }

        echo $additional_data_section;

    }

    private function get_course_numbers_div($course_numbers)
    {
        $texts_for_numbers = ['num_of_videos' => __('סרטונים'), 'time_of_video' => __('ממוצע כל סרטון'), 'course_duration' => __('קורס כולל'), 'course_level' => __('רמה')];
        $course_numbers_div = '';

        if (isset($course_numbers) && is_array($course_numbers)) {
            $course_numbers_div = '<div class="course-numbers-container">';
            foreach ($course_numbers as $key => $number) {
                $course_numbers_div .= '<div class="course-number-item">';
                $course_numbers_div .= '<div class="course-number">' . $number . '</div>';
                $course_numbers_div .= '<div class="course-number-key">' . $texts_for_numbers[$key] . '</div></div>';
            }
            $course_numbers_div .= '</div>';
        }
        return $course_numbers_div;
    }

    private function get_course_cover_information($cover_section)
    {
        $course_title = isset($cover_section['course_title']) ? $cover_section['course_title'] : '';
        $host_name = isset($cover_section['host_name']) ? $cover_section['host_name'] : '';
        $page_cover_text = isset($cover_section['page_cover_text']) ? $cover_section['page_cover_text'] : '';
        $background_images = $this->get_background_images_by_section($cover_section);


        if (isset($background_images['top'])) {
            $course_information = "<div class=\"course-information\"><img src=\"" . $background_images['top'] . "\">";
        } else {
            $course_information = '<div class="course-information">';
        }

        /** title and host */
        $title_div = '<div class="course-title">' . $course_title . '</div>';
        $host_div = '<div class="course-host-name">' . $host_name . '</div>';
        $text_div = '<div class="course-cover-text">' . $page_cover_text . '</div>';

        $course_information .= '<div class="cover-textual-information">' . $title_div . $host_div . $text_div . '</div></div>';

        echo $course_information;
    }

    private function get_background_images_by_section($section)
    {
        $background_images = [];

        if (isset($section['top_background_image']) && isset($section['top_background_image']['url'])) {
            $background_images['top'] = $section['top_background_image']['url'];
        }
        if (isset($section['bottom_background_image']) && isset($section['bottom_background_image']['url'])) {
            $background_images['bottom'] = $section['bottom_background_image']['url'];
        }

        return $background_images;
    }

    private function get_cover_or_host_images($cover_desktop, $cover_mobile, $template_part)
    {
        $desktop_image = '';
        $mobile_image = '';
        if (isset($cover_desktop['url'])) {
            $desktop_image = $cover_desktop;
        }
        if (isset($cover_mobile['url'])) {
            $mobile_image = $cover_mobile;
        }

        foody_get_template_part(get_template_directory() . '/template-parts/' . $template_part, array(
            'image' => $desktop_image,
            'mobile_image' => $mobile_image,
            'link' => ''
        ));
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
        }
    }
}