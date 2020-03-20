<?php

class Foody_Courses_Homepage
{

    private $homepage_data = [];

    /**
     * Homepage constructor.
     */
    public function __construct()
    {
        $this->homepage_data = get_field('courses_homepage');
    }


    public function should_show_section($section_name)
    {
        return !empty($this->homepage_data[$section_name]);
    }

    public function get_cover_section($section_name)
    {

        $cover_section = $this->homepage_data[$section_name];
        $this->get_cover($cover_section['image_desktop'], $cover_section['image_mobile'], 'content-cover-image.php');
    }

    public function get_courses_section()
    {
        $courses_section = $this->homepage_data['courses_section'];
        $background_images = $this->get_background_images_by_section($courses_section);
        $main_text = isset($courses_section['main_text']) ? $courses_section['main_text'] : '';
        $title = isset($courses_section['title']) ? $courses_section['title'] : '';

        /** building the html */
        $main_text_div = '<p class="main-text">' . $main_text . '</p>';
        $title_div = '<h2 class="title">' . $title . '</h2>';
        $courses_list_div = isset($courses_section['courses_list']) ? $this->get_courses_list_div($courses_section['courses_list'], 'courses_section') : '';

        if (isset($background_images['top'])) {
            $courses_section_div = "<div class=\"courses-list\"><div class='title-image-container'>" . $main_text_div . "<img class='top-image' src=\"" . $background_images['top'] . "\"></div>";
        } else {
            $courses_section_div = "<div class=\"courses-list\"><div class='title-image-container'>" . $main_text_div . "</div>";
        }

        $courses_section_div .= $title_div . $courses_list_div;

        if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
            $courses_section_div .= "<img class='bottom-image' src=\"" . $background_images['bottom'] . "\"></div>";
        } else {
            $courses_section_div .= '</div>';
        }

        echo $courses_section_div;
    }

    public function get_course_item_link($item, $key)
    {
        $link_url = '';
        if (isset($item[$key]) && !empty($item[$key])) {
            $link_url = isset($item[$key]['url']) && !empty($item[$key]['url']) ? $item[$key]['url'] : '';
        }

        return $link_url;
    }

    public function get_link($section_name, $key)
    {
        $section = $this->homepage_data[$section_name];
        $link_url = '';
        if (isset($section[$key]) && !empty($section[$key])) {
            $link_url = isset($section[$key]['url']) && !empty($section[$key]['url']) ? $section[$key]['url'] : '';
        }

        return $link_url;
    }

    private function get_courses_list_div($courses_list, $section_name)
    {
        $courses_list_section = '';
        if (is_array($courses_list)) {
            $courses_list_section = '<div class="courses-list-container">';

            foreach ($courses_list as $index => $item) {
                if ($index % 3 == 0) {
                    $courses_list_section .= '<div class="courses-list-row">';
                }
                if (isset($courses_list[$index]) && is_array($courses_list[$index])) {
                    if (isset($item['image']) && isset($item['image']['url'])) {

                        /** top part of list item */
                        $link = isset($item['link']) ? '<a class="course-link" href="' . $this->get_course_item_link($item, 'link') . '">' : '';
                        $course_content_item = '<div class="course-item">' . $link . '<div class="course-item-top">';
                        $item_div = '<img class="item-image" src="' . $item['image']['url'] . '"/>';
                        $host_name = isset($item['host_name']) ? '<span class="host-name">' . $item['host_name'] . '</span>' : '';
                        $course_name = isset($item['course_name']) ? '<span class="host-name">' . $item['course_name'] . '</span>' : '';
                        $course_content_item .= $item_div . '<div class="course-name-container">' .$host_name . $course_name . '</div></div>';

                        /** bottom part of list item */
                        $course_summary = $this->get_course_details_and_pricing($item);
                        $course_content_item .= '<div class="course-item-bottom">' . $course_summary . '</div>';
                        if (isset($item['link'])) {
                            $course_content_item .= '</a></div>';
                        }
                        else{
                            $course_content_item .= '</div>';
                        }

                        $courses_list_section .= $course_content_item;
                    }
                }
                if ($index % 3 == 2) {
                    $courses_list_section .= '</div>';
                }
            }
            $courses_list_section .= '</div>';
        }

        return $courses_list_section;
    }

    private function get_course_details_and_pricing($course_item)
    {
        $courses_details_div = '';
        $money_char = '₪';
        if (isset($course_item['old_price']) && !empty($course_item['old_price']) && isset($course_item['new_price']) && !empty($course_item['new_price'])) {
            $action_text = isset($course_item['action_item_text']) ? $course_item['action_item_text'] : '';

            /** pricing row */
            $courses_details_div = '<span class="pricing-row"><span class="sale-text">' . __('מחיר מבצע! ') . '</span><span class="new-price">' . $money_char . $course_item['new_price'] . ' ' .'</span>';
            $courses_details_div .= __('במקום ') . $money_char . $course_item['old_price'] . ' ' . '</span>';

            /** action item row */
            $courses_details_div .= '<span class="action-item-text">' . $action_text . '</span>';
        }
        return $courses_details_div;
    }

    private function get_background_images_by_section($section)
    {
        $background_images = [];

        $top_image_key = 'top_background_image';
        $bottom_image_key = 'bottom_background_image';

        if (wp_is_mobile()) {
            $top_image_key = "{$top_image_key}_mobile";
            $bottom_image_key = "{$bottom_image_key}_mobile";
        }

        if (isset($section[$top_image_key]) && isset($section[$top_image_key]['url'])) {
            $background_images['top'] = $section[$top_image_key]['url'];
        }
        if (isset($section[$bottom_image_key]) && isset($section[$bottom_image_key]['url'])) {
            $background_images['bottom'] = $section[$bottom_image_key]['url'];
        }

        return $background_images;
    }


    private function get_cover($cover_desktop, $cover_mobile, $template_part)
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


}