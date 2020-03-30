<?php

class Foody_Course_register
{

    private $course_data;
    private $page_data;
    private $course_id;

    /**
     * register page constructor.
     */
    public function __construct($course_id)
    {
        $this->course_data = get_field('course_register_data', $course_id);
        $this->page_data = get_field('register_page_data');
        $this->course_id = $course_id;
    }

    public function get_cover_section()
    {
        $course_data = $this->course_data = $this->get_course_data();

        /** host image */
        $this->get_host_images($course_data['host_image'], $course_data['host_image_mobile'], 'content-course-host-image.php');

        /** cover title and texts */
        $register_subtext = isset($course_data['register_subtext']) ? $course_data['register_subtext'] : '';
        $this->get_course_cover_information($course_data['course_name'], $register_subtext);
    }

    public function get_form_section()
    {
        $course_price = isset($this->course_data['final_price']) ? $this->course_data['final_price'] : '';
        $coupon_text = isset($this->course_data['coupon_group']) ? $this->get_coupon_text($this->course_data['coupon_group']) : false;

        $title_div = '<h5 class="form-title">' . __('הרשמה:') . '</h5>';
        $form_div = $this->get_form($course_price, $coupon_text);

        $form_section = '<div class="form-container">' . $title_div . $form_div ;
        echo $form_section;
    }

    public function get_bottom_image(){
        $background_images = $this->get_background_images_by_section($this->page_data);
        if (isset($background_images['bottom']) && $background_images['bottom'] != '') {
            echo "<img class='bottom-image' src=\"" . $background_images['bottom'] . "\">";
        }
    }

    private function get_course_data()
    {
        return array_merge([
            'course_name' => get_the_title($this->course_id),
            'host_name' => get_field('course_page_main_cover_section_host_name', $this->course_id),
            'host_image' => get_field('course_page_main_cover_section_host_image', $this->course_id),
            'host_image_mobile' => get_field('course_page_main_cover_section_host_image_mobile', $this->course_id),
        ], $this->course_data);
    }

    private function get_course_cover_information($course_name, $register_subtext)
    {
        $course_information = '<div class="course-information">';
        $host_name = get_field('course_page_main_cover_section_host_name', $this->course_id);
        /** title and host */
        $title_div = '<div class="course-title" data-host="'. $host_name .'">' . $course_name . '</div>';
        $text_div = '<div class="course-cover-text">' . $register_subtext . '</div>';


        $course_information .= $title_div . $text_div . '</div>';

        echo $course_information;
    }

    private function get_host_images($cover_desktop, $cover_mobile, $template_part)
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

    private function get_background_images_by_section($page_data)
    {
        $background_images = [];

        $top_image_key = 'top_background_image';
        $bottom_image_key = 'bottom_background_image';

        if (wp_is_mobile()) {
            $top_image_key = "{$top_image_key}_mobile";
            $bottom_image_key = "{$bottom_image_key}_mobile";
        }

        if (isset($page_data[$top_image_key]) && isset($page_data[$top_image_key]['url'])) {
            $background_images['top'] = $page_data[$top_image_key]['url'];
        }
        if (isset($page_data[$bottom_image_key]) && isset($page_data[$bottom_image_key]['url'])) {
            $background_images['bottom'] = $page_data[$bottom_image_key]['url'];
        }

        return $background_images;
    }

    private function get_coupon_text($group)
    {
        if (isset($group['enable_coupon_text']) && $group['enable_coupon_text']) {
            return isset($group['coupon_text']) ? $group['coupon_text'] : false;
        }

        return false;
    }

    private function get_newsletter_terms_checkboxes()
    {
        $newsletter_terms_checkboxes = '';
        $newsletter_div = '';
        $has_newsletter = false;
        if (isset($this->page_data['newsletter_group'])) {
            $newsletter_group = $this->page_data['newsletter_group'];
            if (isset($newsletter_group['enable_newsletter']) && $newsletter_group['enable_newsletter'] !== false) {
                $newsletter_text = isset($newsletter_group['newsletter_text']) ? $newsletter_group['newsletter_text'] : '';
                $newsletter_div = $newsletter_text != '' ? '<div class="checkbox-label"><input class="form-checkbox" type="checkbox" value="checked" id="newsletter" /><label>' . $newsletter_text . '</label></div>' : '';
                $has_newsletter = true;
            }

        }
        $terms_text = __('הנני מאשר את ') . '<a class="terms-link" href="' . get_permalink(get_page_by_path('תנאי-שימוש')) . '">' . __('תנאי השימוש') . '</a>' . __(' באתר');
        $terms_div = '<div class="checkbox-label"><input class="form-checkbox" type="checkbox" value="checked" name="terms" id="terms" required/><label for="terms" class="terms-label">' . $terms_text . '</label></div>';

        $newsletter_terms_checkboxes .= '<div class="newsletter-and-terms">';
        if ($has_newsletter) {
            $newsletter_terms_checkboxes .= $newsletter_div;
        }

        $newsletter_terms_checkboxes .= $terms_div . '</div>';
        return $newsletter_terms_checkboxes;
    }

    private function get_buttons_section()
    {
        $enable_credit = isset($this->page_data['enable_credit_button']) && $this->page_data['enable_credit_button'];
        $enable_bit = isset($this->page_data['enable_bit_button']) && $this->page_data['enable_bit_button'];
        $buttons_div = '';

        $course_payment_link = isset($this->course_data['register_link_text']) ? $this->course_data['register_link_text'] : __('לרכישה') ;

        if($enable_credit || $enable_bit){
            $buttons_div = '<div class="button-container">';
        }
        if($enable_credit){
            $link_to_purchase = isset($this->course_data['link_for_purchase']) && isset($this->course_data['link_for_purchase']['url']) ? $this->course_data['link_for_purchase']['url'] : '' ;
            $link_thank_you = isset($this->course_data['link_thank_you']) ? $this->course_data['link_thank_you'] : get_home_url();
            $invoice_mail = isset($this->page_data['mail_invoice']) ? $this->page_data['mail_invoice'] : '';
            $credit_button = '<div class="credit-card-pay" data-invoice-mail="'. $invoice_mail .'" data-thank-you="'. $link_thank_you . '?course_id='.  $this->course_id .'" data-link="' . $link_to_purchase . '">' . $course_payment_link . '<img src="' . get_template_directory_uri() . '/resources/images/course-register-button.svg"/></div>';
            $buttons_div .= $credit_button ;

        }

        if($enable_credit){
        }

        if($enable_credit || $enable_bit){
            $buttons_div .= '</div>';
        }

        return $buttons_div;
    }

    private function get_form($course_price, $coupon_text)
    {
        $form_container = '<div class="container-fluid" <div class="row"><form id="course-register-form" action="" class="row">';

        $form_fields = [
            'email' => ['type' => 'email', 'name' => 'email', 'label' => 'כתובת מייל:'],
            'first-name' => ['type' => 'text', 'name' => 'first_name', 'label' => 'שם פרטי:'],
            'last-name' => ['type' => 'text', 'name' => 'last_name', 'label' => 'שם משפחה:'],
            'phone-number' => ['type' => 'tel', 'name' => 'phone_number', 'label' => 'מספר טלפון:']
        ];

        foreach ($form_fields as $key => $field_data){
            $form_group = '<div class="form-group col-12 required-input">';
            $label = '<label for="'. $key .'">' . __( $field_data['label'], 'foody') . '</label>';
            $input = '<input type="'. $field_data['type'] .'" id="'. $key .'" name="'. $field_data['name'] .'" required>';

            $form_group .= $label . $input . '</div>';
            $form_container .= $form_group;
        }

        $price_div = '<span class="price-line">' . __('מחיר הקורס ') . __('₪') . $course_price . '</span>';
        $coupon_div = $coupon_text !== false ? '<span class="coupon-line">' . $coupon_text . '</span>' : '';
        $coupon_and_price_div = '<div class="coupon-and-price-container">' . $price_div . $coupon_div . '</div>';
        $newsletter_terms_checkboxes = $this->get_newsletter_terms_checkboxes();
        $buttons = $this->get_buttons_section();

        $form_container .= $coupon_and_price_div. $newsletter_terms_checkboxes . $buttons. '</form></div></div>';

        return $form_container;
    }
}

function foody_sign_to_newsletter_by_email() {

    $response ='';
    $marketing = isset( $_POST['marketing'] ) ? $_POST['marketing'] : false;


    $email = isset( $_POST['email'] ) ? $_POST['email'] : false;

    if ( $marketing ) {
        if ( ! empty( $email ) ) {
            $response = foody_register_newsletter( $email );
        }
    }


    $user = get_user_by( 'email', $email );

    if($user !== false) {
        update_user_meta( $user->ID, 'seen_approvals', true );
    }
}

add_action( 'wp_ajax_foody_sign_to_newsletter_by_email', 'foody_sign_to_newsletter_by_email' );
