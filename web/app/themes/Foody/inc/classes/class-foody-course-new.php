<?php

class Foody_Course_new
{

    private $course_data = [];
    private $floating_buttons = [];
    private $links_target = 'blank';
    private $old_price = '';

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

    public function get_always_wanted_section()
    {
        $alwayes_wanted_div = '';
        if (is_array($this->course_data['alwayes_wanted_section']) && isset($this->course_data['alwayes_wanted_section'])) {
            $alwayes_wanted_section = $this->course_data['alwayes_wanted_section'];
            $background_images = $this->get_background_images_by_section($alwayes_wanted_section);

            $alwayes_wanted_title = isset($alwayes_wanted_section['alwayes_wanted_title']) ? $alwayes_wanted_section['alwayes_wanted_title'] : '';
            $alwayes_wanted_subtitle = isset($alwayes_wanted_section['alwayes_wanted_subtitle']) ? $alwayes_wanted_section['alwayes_wanted_subtitle'] : '';
            $alwayes_wanted_text = isset($alwayes_wanted_section['alwayes_wanted_text']) ? $alwayes_wanted_section['alwayes_wanted_text'] : '';
            $alwayes_wanted_image = isset($alwayes_wanted_section['alwayes_wanted_image']) ? $alwayes_wanted_section['alwayes_wanted_image'] : '';
            $alwayes_wanted_image = is_array($alwayes_wanted_image) && isset($alwayes_wanted_image['url']) ? $alwayes_wanted_image['url'] : '';

            $purchase_button_link = $this->get_floating_purchase_button();
            $purchase_button_text = isset($purchase_button_link['title']) ? $purchase_button_link['title'] : '';
            $purchase_button_link = isset($purchase_button_link['url']) ? $purchase_button_link['url'] : '';


            /** creating html for each element */
            $alwayes_wanted_title_div = '<h2 class="always-wanted-title">' . $alwayes_wanted_title . '</h2>';
            $alwayes_wanted_image_div = '<img class="always-wanted-main-image" src="' . $alwayes_wanted_image . '">';
            $alwayes_wanted_subtitle_div = '<h5 class="always-wanted-subtitle">' . $alwayes_wanted_subtitle . '</h5>';
            $alwayes_wanted_subtitle_paragraph = '<p class="always-wanted-text">' . $alwayes_wanted_text . '</p>';
            $alwayes_wanted_purchase = "<a class='always-wanted-section-purchase' href='" . $purchase_button_link . "' target=\"'.$this->links_target.'\">" . $purchase_button_text . "</a>";

            $alwayes_wanted_div = '<div class="always-wanted-container">' . $alwayes_wanted_title_div . $alwayes_wanted_image_div . $alwayes_wanted_subtitle_div . $alwayes_wanted_subtitle_paragraph . $alwayes_wanted_purchase;

            if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                $alwayes_wanted_div .= "<img class='bottom-image' src=\"" . $background_images['bottom'] . "\"></div>";
            } else {
                $alwayes_wanted_div .= "</div>";
            }
        }
        echo $alwayes_wanted_div;
    }

    public function get_buy_kit_section()
    {
        $buy_kit_div = '';
        if (is_array($this->course_data['buy_kit_section']) && isset($this->course_data['buy_kit_section'])) {
            $buy_kit_section = $this->course_data['buy_kit_section'];
            $background_images = $this->get_background_images_by_section($buy_kit_section);

            $buy_kit_title = isset($buy_kit_section['buy_kit_title']) ? $buy_kit_section['buy_kit_title'] : '';
            $buy_kit_subtitle = isset($buy_kit_section['buy_kit_subtitle']) ? $buy_kit_section['buy_kit_subtitle'] : '';
            $buy_kit_details = isset($buy_kit_section['kit_details']) ? $buy_kit_section['kit_details'] : [];
            $buy_kit_price = isset($buy_kit_section['kit_price']) ? $buy_kit_section['kit_price'] : '';
            $buy_kit_image = isset($buy_kit_section['buy_kit_image']) ? $buy_kit_section['buy_kit_image'] : '';
            $buy_kit_image = is_array($buy_kit_image) && isset($buy_kit_image['url']) ? $buy_kit_image['url'] : '';

            $purchase_button_link = isset($buy_kit_section['kit_buy_link']) ? $buy_kit_section['kit_buy_link'] : [];
            $purchase_button_text = isset($purchase_button_link['title']) ? $purchase_button_link['title'] : '';
            $purchase_button_link = isset($purchase_button_link['url']) ? $purchase_button_link['url'] : '';

            /** creating html for each element */
            $buy_kit_title_div = '<h2 class="buy-kit-title">' . $buy_kit_title . '</h2>';
            $buy_kit_image_div = '<img class="buy-kit-main-image" src="' . $buy_kit_image . '">';
            $buy_kit_details = $this->get_kit_details_html($buy_kit_subtitle, $buy_kit_details, $buy_kit_price);
            $kit_purchase = "<a class='course-v2-purchase-button' href='" . $purchase_button_link . "' target=\"'.$this->links_target.'\">" . $purchase_button_text . "</a>";

            if (isset($background_images['top'])) {
                $buy_kit_div = "<div class=\"buy-kit-container\"><div class='buy-kit-title-image-container'><img class='top-image' src=\"" . $background_images['top'] . "\">";
            } else {
                $buy_kit_div = '<div class="buy-kit-container"><div class="buy-kit-title-image-container">';
            }

            $buy_kit_div .= $buy_kit_title_div . '</div>' . $buy_kit_image_div . $buy_kit_details;

            if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                $buy_kit_div .= "<div class=\"buy-kit-purchase-image-container\"><img class='bottom-image' src=\"" . $background_images['bottom'] . "\">" . $kit_purchase . "</div></div>";
            } else {
                $buy_kit_div .= $kit_purchase . "</div>";
            }
        }
        echo $buy_kit_div;
    }

    public function get_syllabus_section()
    {
        $syllabus_div = '';
        if (is_array($this->course_data['syllabus_section']) && isset($this->course_data['syllabus_section'])) {
            $syllabus_data = $this->course_data['syllabus_section'];
            $background_images = $this->get_background_images_by_section($syllabus_data);
            $chapters_section = $this->get_chapters_section($syllabus_data['chapters']);
            $syllabus_title_div = isset($syllabus_data['syllabus_title']) && $syllabus_data['syllabus_title'] != '' ? '<h2 class="syllabus-title">' . $syllabus_data['syllabus_title'] . '</h2>' : '';

            $purchase_button_link = $this->get_floating_purchase_button();
            $purchase_button_text = isset($purchase_button_link['title']) ? $purchase_button_link['title'] : '';
            $purchase_button_link = isset($purchase_button_link['url']) ? $purchase_button_link['url'] : '';
            $purchase_button_link = "<a class='course-v2-purchase-button' href='" . $purchase_button_link . "' target=\"'.$this->links_target.'\" >" . $purchase_button_text . "</a>";


            if (isset($background_images['top'])) {
                $syllabus_div = "<div class=\"syllabus-container\"><div class='syllabus-title-image-container'><img class='top-image' src=\"" . $background_images['top'] . "\">";
            } else {
                $syllabus_div = '<div class="syllabus-container"><div class="syllabus-title-image-container">';
            }

            $syllabus_div .= $syllabus_title_div . '</div>' . $chapters_section;

            if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                $syllabus_div .= "<div class=\"syllabus-purchase-image-container\"><img class='bottom-image' src=\"" . $background_images['bottom'] . "\">" . $purchase_button_link . "</div></div>";
            } else {
                $syllabus_div .= $purchase_button_link . "</div>";
            }
        }
        echo $syllabus_div;
    }

    public function get_video_section()
    {
        $video_section = '';
        if (is_array($this->course_data['course_video_group']) && isset($this->course_data['course_video_group'])) {
            $video_group = $this->course_data['course_video_group'];
            $background_images = $this->get_background_images_by_section($video_group);
            $purchase_button_link = $this->get_floating_purchase_button();
            $purchase_button_text = isset($purchase_button_link['title']) ? $purchase_button_link['title'] : '';
            $purchase_button_link = isset($purchase_button_link['url']) ? $purchase_button_link['url'] : '';

            if (isset($video_group['course_video']) && !empty($video_group['course_video'])) {
                $video = $video_group['course_video'];

                $video_div = '<div class="main-video">' . $video . '</div>';

                if (isset($background_images['top'])) {
                    $video_section = "<div class=\"course-main-video\"><div class='course-main-video-image-container'><img class='top-image' src=\"" . $background_images['top'] . "\">";
                } else {
                    $video_section = '<div class="course-main-video"><div class="course-main-video-image-container">';
                }

                $video_section .= $video_div;

                if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                    $video_section .= "<a class='video-section-purchase' href='" . $purchase_button_link . "' target=\"'.$this->links_target.'\">" . $purchase_button_text . "</a><img class='bottom-image' src=\"" . $background_images['bottom'] . "\"></div>";
                } else {
                    $video_section .= "<a class='video-section-purchase' href='" . $purchase_button_link . "' target=\"'.$this->links_target.'\">" . $purchase_button_text . "</a></div>";
                }
            }
        }
        echo $video_section;
    }

    public function get_banner_image()
    {
        if (!empty($banner = $this->course_data['banner_image_group'])) {
            $image_key = 'banner_image';
            if (wp_is_mobile()) {
                $image_key = "{$image_key}_mobile";
            }

            $image = $banner[$image_key];

            if (!empty($image)) {
                echo "<img class='banner-image' src='{$image['url']}' alt='{$image['alt']}' >";
            }
        }
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
            $purchase_button_div = '<a class="purchase-button-div" href="' . $purchase_button['url'] . '" target="' . $this->links_target . '">' . $purchase_button_text . '</a>';
        }

        if (isset($gift_button['url']) && !empty($gift_button['url'])) {
            $has_button = true;
            $gift_button_text = isset($gift_button['title']) && !empty($gift_button['title']) ? $gift_button['title'] : __('לרכישה');
            $gift_button_div = '<a class="gift-button-div" href="' . $gift_button['url'] . '" target="' . $this->links_target . '">' . $gift_button_text . '<img src="' . get_template_directory_uri() . '/resources/images/group-3.svg"/></a>';
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

            $what_waiting_title_div = '<h2 class="what-waiting-title">' . $what_waiting_title . '</h2>';
            $course_content_items_div = $this->get_course_content_items_div($course_content_items);

            if (isset($background_images['top'])) {
                $course_what_waiting = "<div class=\"course-what-waiting\"><div class='what-waiting-title-image-container'><img src=\"" . $background_images['top'] . "\">";
            } else {
                $course_what_waiting = '<div class="course-what-waiting"><div class="what-waiting-title-image-container">';
            }

            $course_what_waiting .= $what_waiting_title_div . '</div>' . $course_content_items_div;

            if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                $course_what_waiting .= "<img src=\"" . $background_images['bottom'] . "\"></div>";
            } else {
                $course_what_waiting .= '</div>';
            }
        }
        echo $course_what_waiting;
    }

    private function get_chapters_section($chapters)
    {
        $chapters_container = '';
        if (!empty($chapters)) {
            $chapters_container = '<div class="chapters-container">';
            $chapter_number = 1;
            foreach ($chapters as $chapter) {
                $chapter_title_div = isset($chapter['chapter_name']) ? '<h5 class="chapter-title">' . $chapter['chapter_name'] . '</h5>' : '';
                $chapter_summery_div = isset($chapter['chapter_summery']) ? '<p class="chapter-summery">' . $chapter['chapter_summery'] . '</p>' : '';
                $chapter_bullets_div = isset($chapter['chapter_bullets']) ? '<div class="chapter-bullets ">' . $this->get_ul_list($chapter['chapter_bullets'], 'bullet_row') . '</div>' : '';
                $chapter_icon = '<i class="collapse-icon" src="' . get_template_directory_uri() . '/resources/images/stroke-379.svg"></i>';
                $chapter_div = '<div class="chapter-container"><div class="chapter-details">' . $chapter_title_div . $chapter_icon . $chapter_summery_div . '</div>';

                /** add bullets collapse */
                $chapter_div .= '<div class="" id="chapter' . $chapter_number . '"><div class="chapter-bullets">' . $chapter_bullets_div . '</div></div></div>';
                $chapters_container .= $chapter_div;
                $chapter_number++;
            }
            $chapters_container .= '</div>';
        }

        return $chapters_container;
    }

    private function get_kit_details_html($kit_title, $kit_details, $kit_price)
    {
        $kit_details_div = '<div class="kit-details-container">';

        $kit_details_title_div = '<h5 class="buy-kit-subtitle">' . $kit_title . '</h5>';
        $kit_details = $this->get_ul_list($kit_details, 'kit_detail_row');
        $kit_price_div = '<div class="buy-kit-price">' . $kit_price . '</div>';

        return $kit_details_div . $kit_details_title_div . $kit_details . $kit_price_div . '</div>';

    }

    public function get_q_and_a_section()
    {
        $faq_div = '';
        if (!empty($this->course_data['faq_section'])) {
            $faq_data = $this->course_data['faq_section'];
            $background_images = $this->get_background_images_by_section($faq_data);
            $chapters_section = $this->get_faq_list($faq_data['faq_list']);
            $faq_title_div = isset($faq_data['faq_title']) && $faq_data['faq_title'] != '' ? '<h2 class="faq-title">' . $faq_data['faq_title'] . '</h2>' : '';

            $purchase_button_link = $this->get_floating_purchase_button();
            $purchase_button_text = isset($purchase_button_link['title']) ? $purchase_button_link['title'] : '';
            $purchase_button_link = isset($purchase_button_link['url']) ? $purchase_button_link['url'] : '';
            $purchase_button_link = "<a class='course-v2-purchase-button' href='" . $purchase_button_link . "' target=\"'.$this->links_target.'\">" . $purchase_button_text . "</a>";


            if (isset($background_images['top'])) {
                $faq_div = "<div class=\"faq-container\"><div class='faq-title-image-container'><img class='top-image' src=\"" . $background_images['top'] . "\">";
            } else {
                $faq_div = '<div class="faq-container"><div class="faq-title-image-container">';
            }

            $faq_div .= $faq_title_div . '</div>' . $chapters_section;

            if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
                $faq_div .= "<div class=\"faq-purchase-image-container\"><img class='bottom-image' src=\"" . $background_images['bottom'] . "\">" . $purchase_button_link . "</div></div>";
            } else {
                $faq_div .= $purchase_button_link . "</div>";
            }
        }
        echo $faq_div;
    }

    public function get_images_section()
    {
        $section = $this->course_data['images_section'];
        $desktop_image = isset($section['desktop_image']) ? $section['desktop_image'] : '';
        $mobile_image = isset($section['mobile_image']) ? $section['mobile_image'] : '';

        if (!empty($desktop_image) || !empty($mobile_image)) {
            $this->get_cover_or_host_images($desktop_image, $mobile_image, 'content-cover-image.php');
        }
    }

    private function get_faq_list($list)
    {
        $content = '<div class="faq-list">';

        foreach ($list as $item) {

            $item_content = "<div class=\"faq-item\"> <div class=\"faq-item-q\"><span>{$item['question']}</span></div> <div class=\"faq-item-a\">{$item['answer']}</div> </div>";

            $content .= $item_content;
        }

        $content .= '</div>';

        return $content;

    }

    public function get_recommending_students()
    {

        $testimonials_content = '';

        if (!empty($testimonials = $this->course_data['testimonials_section']) && !empty($images = $testimonials['testimonials_image_list'])) {
            $testimonials_content = '<div class="testimonials-container">';
            $testimonials_title_div = isset($testimonials['testimonials_title']) && $testimonials['testimonials_title'] != '' ? '<h2 class="testimonials-title">' . $testimonials['testimonials_title'] . '</h2>' : '';

            $slider = '<div class="d-flex testimonials-slider justify-content-between">';

            $images = array_column($images, 'testimonial_image');

            foreach ($images as $image) {

                $item = '<div class="testimonial-item">';

                $image_content = "<img src='{$image['url']}' alt='{$image['alt']}' />";

                $item .= $image_content;

                $item .= '</div>';

                $slider .= $item;
            }

            $slider .= '</div>';

            $testimonials_content = "$testimonials_content $testimonials_title_div $slider";

            if (!empty($testimonials['testimonials_video'])) {
                $testimonials_content .= '<div class="video-container">';
                $testimonials_content .= $testimonials['testimonials_video'];
                $testimonials_content .= '</div>';
            }

            $purchase_button_link = $this->get_floating_purchase_button();
            $purchase_button_text = isset($purchase_button_link['title']) ? $purchase_button_link['title'] : '';
            $purchase_button_link = isset($purchase_button_link['url']) ? $purchase_button_link['url'] : '';
            $purchase_button_link = "<a class='course-v2-purchase-button' href='" . $purchase_button_link . "' target=\"'.$this->links_target.'\">" . $purchase_button_text . "</a>";

            $testimonials_content .= "$purchase_button_link</div>";
        }


        echo $testimonials_content;

    }

    public function get_recommendation_list()
    {
        if (!empty($recommended_section = $this->course_data['recommended_section']) && !empty($items = $recommended_section['recommendation_list'])) {
            $content = '<div>';

            if (!empty($title = $recommended_section['recommended_title'])) {
                $content .= "<h2> {$title} </h2>";
            }

            $recommendations_container = "<div class='d-flex justify-content-between flex-column flex-lg-row recommendations-items-wrapper'>";

            foreach ($items as $item) {

                if (!empty($link = $item['recommendation_link']) && is_array($link)) {

                    $title = $item['recommendation_title'];

                    $target = '';
                    if (!empty($link['target'])) {
                        $target = "target={$link['target']}";
                    }
                    if (empty($link['title'])) {
                        $link['title'] = $title;
                    }

                    $item_content = "<div class=\"recommendation-item\"> <a class='d-flex flex-column' href='{$link['url']}' $target>";

                    $image = $item['recommendation_image'];

                    if (empty($image['alt'])) {
                        $image['alt'] = $title;
                    }

                    $item_content .= "<img src='{$image['url']}' alt='{$image['alt']}' >";

                    $item_content .= "<div class=\"item-title text-center\">$title</div>";

                    $item_content .= '</a></div>';

                    $recommendations_container .= $item_content;
                }


            }

            $recommendations_container .= '</div>';
            $content .= $recommendations_container;

            $content .= '</div>';

            echo $content;
        }
    }

    private function get_ul_list($list, $key)
    {
        $ul_li_list = '';
        if (!empty($list)) {
            $ul_li_list = '<ul class="details-list">';
            foreach ($list as $item) {
                if (isset($item[$key]) && $item[$key] != '') {
                    $ul_li_list .= '<li class="details-item">' . $item[$key] . '</li>';
                }
            }
            $ul_li_list .= '</ul>';
        }

        return $ul_li_list;
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
                if ($index % 3 == 2) {
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
        $promotion_text = isset($cover_section['promotion_text']) ? $this->add_line_on_old_price($this->old_price, $cover_section['promotion_text']) : '';
        $course_numbers = (isset($cover_section['course_numbers']) && is_array($cover_section['course_numbers'])) ? $cover_section['course_numbers'] : [];

        $additional_data_section = '<div class="additional-data">';
        $more_about_course_div = '<div class="additional-data-text">' . $more_about_course . '</div>';
        $promotion_title_div = '<div class="promotion-title">' . $promotion_title . '</div>';
        $promotion_text_div = '<div class="promotion-text">' . $promotion_text . '</div>';

        $rating_image_key = "rating_image";
        if (wp_is_mobile()) {
            $rating_image_key = "{$rating_image_key}_mobile";
        }

        $rating = '';

        if (!empty($rating_image = $cover_section[$rating_image_key])) {
            $rating = "<img class='rating' src='{$rating_image['url']}' alt='{$rating_image['alt']}'>";
        }

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
        $is_mobile = wp_is_mobile();
        $texts_for_numbers = [
            'num_of_videos' => __('סרטונים'),
            'time_of_video' => __('ממוצע כל סרטון'),
            'course_duration' => __('קורס כולל'),
            'course_level' => __('רמה')
        ];
        $course_numbers_div = '';

        if (isset($course_numbers) && is_array($course_numbers)) {
            $course_numbers_div = '<div class="course-numbers-container">';
            $counter = 0;

            foreach ($course_numbers as $key => $number) {
                if ($is_mobile && $counter % 2 == 0) {
                    $course_numbers_div .= '<div class="numbers-row">';
                }

                $course_numbers_div .= '<div class="course-number-item">';
                $course_numbers_div .= '<div class="course-number">' . $number . '</div>';
                $course_numbers_div .= '<div class="course-number-key">' . $texts_for_numbers[$key] . '</div></div>';
                $counter++;

                if ($is_mobile && $counter % 2 == 0) {
                    $course_numbers_div .= '</div>';
                }
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

        $has_top_image = !empty($background_images['top']);
        if ($has_top_image) {
            $course_information = "<div class=\"course-information\"><img src=\"" . $background_images['top'] . "\">";
        } else {
            $course_information = '<div class="course-information">';
        }

        /** title and host */
        $title_div = '<div class="course-title">' . $course_title . '</div>';
        $host_div = '<h2 class="course-host-name">' . $host_name . '</h2>';
        $text_div = '<div class="course-cover-text">' . $page_cover_text . '</div>';

        $information_classes = "cover-textual-information";
        if (!$has_top_image) {
            $information_classes = "$information_classes no-top-image";
        }
        $course_information .= '<div class="' . $information_classes . '">' . $title_div . $host_div . $text_div . '</div></div>';

        echo $course_information;
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

            if(isset($course_data['old_price'])){
                $this->old_price = $course_data['old_price'];
                unset($this->course_data['old_price']);
            }
        }
    }

    public function should_show_section($section_name)
    {
        $not_empty_section = !empty($this->course_data[$section_name]);

        if ($not_empty_section) {
            foreach ($this->course_data[$section_name] as $item) {
                if(!empty($item) && $item !== false){
                    return true;
                }
                else {
                    $not_empty_section = false;
                }
            }
        }

        return $not_empty_section;
    }

    private function add_line_on_old_price($old_price, $text){
        $crossed_old_price = '<span class="crossed-price" style="text-decoration: line-through;">'.$old_price . '</span>';

        if(!empty($old_price) && strpos($text, $old_price) != false){
            return str_replace($old_price, $crossed_old_price , $text);
        }

        return $text;
    }
}