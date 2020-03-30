<?php

class Foody_Courses_Homepage
{

    private $homepage_data = [];
    private $cover_video = '';
    private $bottom_banner = '';


    /**
     * Homepage constructor.
     */
    public function __construct()
    {
        $this->homepage_data = get_field('courses_homepage');
    }

    /** return true if has relevant banner and store it on $bottom_banner  */
    public function has_banner_image()
    {
        $has_mobile = isset($this->homepage_data['image_mobile']) && !empty($this->homepage_data['image_mobile']);
        $has_desktop = isset($this->homepage_data['image_desktop']) && !empty($this->homepage_data['image_desktop']);

        if (wp_is_mobile()) {
            if ($has_mobile) {
                $result = true;
                $this->bottom_banner = $this->homepage_data['image_mobile'];
            } else {
                $result = false;
            }
        } elseif ($has_desktop) {
            $result = true;
            $this->bottom_banner = $this->homepage_data['image_desktop'];
        } else {
            $result = false;
        }

        return $result;
    }

    /** return true if has cover video and store it on $cover_video  */
    public function has_cover_video()
    {
        $has_video = isset($this->homepage_data['cover_section']['cover_video']) && !empty($this->homepage_data['cover_section']['cover_video']);
        if ($has_video) {
            $this->cover_video = $this->homepage_data['cover_section']['cover_video'];
        }

        return $has_video;
    }

    public function get_cover_video()
    {
        echo '<div class="cover-video-container"><div class="cover-video">' . $this->cover_video . '</div></div>';
    }

    public function get_banner_image()
    {
        echo '<div class="bottom-banner-container"><img class="bottom-banner" src="' . $this->bottom_banner['url'] . '">';
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

    public function get_advantages_section()
    {
        $advantages_section = $this->homepage_data['advantages_section'];
        $background_images = $this->get_background_images_by_section($advantages_section);
        $has_cover_video = isset($advantages_section['video']) && !empty($advantages_section['video']);
        $main_text = isset($advantages_section['main_text']) ? $advantages_section['main_text'] : '';
        $cover_image_desktop = isset($advantages_section['image_desktop']) ? $advantages_section['image_desktop'] : '';
        $cover_image_mobile = isset($advantages_section['image_mobile']) ? $advantages_section['image_mobile'] : '';

        /** building the html */
        $cover = $has_cover_video ? '<div class="cover-video">' . $advantages_section['video'] . '</div>' : '<img class="cover-image" src="' . $this->get_relevant_image($cover_image_desktop, $cover_image_mobile) . '">';
        $cover_div = $has_cover_video ? $cover : '<div class="advantages-cover-container">' . $cover . '</div>';
        $top_background_image = isset($background_images['top']) ? '<img class="top-image" src="' . $background_images['top'] . '">' : '';
        $bottom_background_image = isset($background_images['bottom']) ? '<img class="bottom-image" src="' . $background_images['bottom'] . '">' : '';
        $main_text_paragraph = '<p class="advantages-main-text">' . $main_text . '</p>';
        $advantages_list_div = isset($advantages_section['advantages_list']) ? $this->get_advantages_list_div($advantages_section['advantages_list']) : '';
        $advantages_container = '<div class="advantages-container">' . $top_background_image . $cover_div . $main_text_paragraph . $advantages_list_div . $bottom_background_image . '</div>';

        echo $advantages_container;
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
        $courses_list_div = isset($courses_section['courses_list']) ? $this->get_courses_list_div($courses_section['courses_list']) : '';

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

    public function get_team_section()
    {
        $team_section = $this->homepage_data['team_section'];
        $background_images = $this->get_background_images_by_section($team_section);
        $title = isset($team_section['title']) ? $team_section['title'] : '';


        /** building the html */
        $title_div = '<h2 class="title">' . $title . '</h2>';
        $top_background_image = isset($background_images['top']) ? '<img class="top-image" src="' . $background_images['top'] . '">' : '';
        $bottom_background_image = isset($background_images['bottom']) ? '<img class="bottom-image" src="' . $background_images['bottom'] . '">' : '';
        $team_list_div = isset($team_section['hosts_list']) ? $this->get_team_list_div($team_section['hosts_list']) : '';

        $team_container = '<div class="team-container">' . $top_background_image . $title_div . $team_list_div . $bottom_background_image . '</div>';
        echo $team_container;
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

    private function get_team_list_div($team_list)
    {
        $team_list_section = '';
        if (is_array($team_list)) {
            $team_list_section = '<div class="team-list-container">';

            foreach ($team_list as $index => $item) {
                if (isset($team_list[$index]) && is_array($team_list[$index])) {
                    if (isset($item['image']) && isset($item['image']['url'])) {
                        $image_div = '<img class="item-image" src="' . $item['image']['url'] . '"/>';
                        $host_name = isset($item['host_name']) ? $item['host_name'] : '';
                        $text = isset($item['summary']) ? $item['summary'] : '';
                        $link = isset($item['link']) && !empty($item['link']) && isset($item['link']['url']) ? $item['link']['url'] : '';
                        $course_details = $this->get_course_details_and_pricing($item);

                        $title_div = '<h5 class="host-name">' . $host_name . '</h5>';
                        $text_div = '<p class="item-text">' . $text . '</p>';
                        if($link != '') {
                            $course_button_div = '<a  href="' . $link . '" target="_blank" class="course-item-button">' . $course_details . '</a>';
                        }
                        else{
                            $course_button_div = '<div class="course-item-button">' . $course_details . '</div>';
                        }

                        if (wp_is_mobile()) {
                            $team_content_item = '<div class="team-item">' . $image_div . $title_div . $text_div . $course_button_div . '</div>';
                        } else {
                            $team_content_item = '<div class="team-item">' . $image_div . '<div class="team-item-info">' . $title_div . $text_div . $course_button_div . '</div></div>';
                        }

                        $team_list_section .= $team_content_item;
                    }
                }
            }
            $team_list_section .= '</div>';
        }

        return $team_list_section;
    }

    private function get_advantages_list_div($advantages_list)
    {
        $advantages_list_section = '';
        if (is_array($advantages_list)) {
            $advantages_list_section = '<div class="advantages-list-container">';

            foreach ($advantages_list as $index => $item) {
                if ($index % 2 == 0) {
                    $advantages_list_section .= '<div class="advantages-list-row">';
                }
                if (isset($advantages_list[$index]) && is_array($advantages_list[$index])) {
                    if (isset($item['icon']) && isset($item['icon']['url'])) {
                        $icon_div = '<img class="item-icon" src="' . $item['icon']['url'] . '"/>';
                        $text = isset($item['text']) ? $item['text'] : '';
                        $text_div = '<p class="item-text">' . $text . '</p>';
                        $advantages_content_item = '<div class="advantages-item">' . $icon_div . $text_div . '</div>';

                        $advantages_list_section .= $advantages_content_item;
                    }
                }
                if ($index % 2 == 1) {
                    $advantages_list_section .= '</div>';
                }
            }
            if (count($advantages_list) % 2 == 0) {
                $advantages_list_section .= '</div>';
            } else {
                $advantages_list_section .= '</div></div>';
            }
        }

        return $advantages_list_section;
    }

    private function get_courses_list_div($courses_list)
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
                        $link = isset($item['link']) && isset($item['link']['url']) && !empty($item['link']['url']) ? '<a class="course-link" target="_blank" href="' . $this->get_course_item_link($item, 'link') . '">' : '';
                        $course_content_item = '<div class="course-item">' . $link . '<div class="course-item-top">';
                        $item_div = '<img class="item-image" src="' . $item['image']['url'] . '"/>';
                        $host_name = isset($item['host_name']) ? '<span class="host-name">' . $item['host_name'] . '</span>' : '';
                        $course_name = isset($item['course_name']) ? '<span class="course-name">' . $item['course_name'] . '</span>' : '';

                        $use_host_course_names = isset($item['enable_dynamic_details']) ? $item['enable_dynamic_details'] : false;

                        if($use_host_course_names) {
                            $course_content_item .= $item_div . '<div class="course-name-container" data-host="'. $item['host_name'] . '"  data-course="'. $item['course_name'] . '">' . $host_name . $course_name . '</div></div>';
                        }
                        else{
                            $course_content_item .= $item_div . '<div class="course-name-container" data-host="'. $item['host_name'] . '"  data-course="'. $item['course_name'] . '"></div></div>';
                        }

                        /** bottom part of list item */
                        $course_summary = $this->get_course_details_and_pricing($item);
                        $course_content_item .= '<div class="course-item-bottom">' . $course_summary . '</div>';
                        if (isset($item['link'])) {
                            $course_content_item .= '</a></div>';
                        } else {
                            $course_content_item .= '</div>';
                        }

                        $courses_list_section .= $course_content_item;
                    }
                }
                if ($index % 3 == 2) {
                    $courses_list_section .= '</div>';
                }
            }
            if (count($courses_list) % 3 == 0) {
                $courses_list_section .= '</div>';
            } else {
                $courses_list_section .= '</div></div>';
            }
        }

        return $courses_list_section;
    }

    private function get_course_details_and_pricing($course_item)
    {
        $courses_details_div = '';
        $money_char = '₪';
        if (isset($course_item['old_price'])&& isset($course_item['new_price'])) {
            /** add line through */
            $old_price = $this->add_line_on_old_price($course_item['old_price'],$course_item['old_price']);
            $action_text = isset($course_item['action_item_text']) ? $course_item['action_item_text'] : '';

            if(!empty($course_item['old_price'])  && !empty($course_item['new_price'])) {
                /** pricing row */
                $courses_details_div = '<span class="pricing-row"><span class="sale-text">' . __('מחיר מבצע! ') . '</span><span class="new-price">' . $money_char . $course_item['new_price'] . ' ' . '</span>';
                $courses_details_div .= __('במקום ') . $money_char . $old_price . ' ' . '</span>';
            }
            if(!empty($course_item['action_item_text']) )
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

    private function get_relevant_image($image_desktop, $image__mobile)
    {
        $image = wp_is_mobile() ? $image__mobile : $image_desktop;

        if (!empty($image) && isset($image['url'])) {
            $image = $image['url'];
        }

        return $image;
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

    private function add_line_on_old_price($old_price, $text){
        $crossed_old_price = '<span class="crossed-price" style="text-decoration: line-through;">'.$old_price . '</span>';

        if(!empty($old_price) && strpos($text, $old_price) !== false){
            return str_replace($old_price, $crossed_old_price , $text);
        }
        return $text;
    }
}