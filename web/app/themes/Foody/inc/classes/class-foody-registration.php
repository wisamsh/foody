<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/27/18
 * Time: 6:46 PM
 */
class Foody_Registration
{

    private $fields = [
        'email' => 'אימייל',
        'first_name' => 'אימייל',
        'last_name' => 'אימייל',
        'password' => 'אימייל'
    ];

    /**
     * Foody_Registration constructor.
     */
    public function __construct()
    {
        add_action('login_form_register', array($this, 'do_register_user'));
        add_action('login_form_login', array($this, 'redirect_to_custom_login'));
        add_filter("login_redirect", array($this, 'redirect_admin'), 10, 3);
        add_filter('wp_mail_content_type', array($this, 'foody_wp_email_content_type'));
        $this->register_custom_password_reset();
//        add_filter('login_url', function (/** @noinspection PhpUnusedParameterInspection */
//            $url, $redirect, $force_reauth) {
//            return home_url('התחברות');
//        }, 10, 3);
    }

    private function register_user($user_data)
    {

        extract($user_data);

        $errors = new WP_Error();

        // Email address is used as both username and email. It is also the only
        // parameter we need to validate
        /** @noinspection PhpUndefinedVariableInspection */
        if (!is_email($email)) {
            $errors->add('email', $this->get_error_message('email'));
            return $errors;
        }

        if (username_exists($email) || email_exists($email)) {
            $errors->add('email_exists', $this->get_error_message('email_exists'));
            return $errors;
        }

        /** @noinspection PhpUndefinedVariableInspection */
        if (!$this->validate_password($password)) {
            $errors->add('password', $this->get_error_message('password'));
            return $errors;
        }

        /** @noinspection PhpUndefinedVariableInspection */
        $user_data_db = array(
            'user_login' => $email,
            'user_email' => $email,
            'user_pass' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'nickname' => $first_name,
        );

        $user_id = wp_insert_user($user_data_db);
        if (!is_wp_error($user_id)) {

            if (!empty($user_data['marketing'])) {
                update_user_meta($user_id, 'marketing', true);
                foody_register_newsletter($email);
            }

            /** @noinspection PhpUndefinedVariableInspection */
            update_user_meta($user_id, 'phone_number', $phone_number);
            update_user_meta($user_id, 'seen_approvals', true);
            if (!empty($user_data['e_book']) && !empty($user_data['marketing'])) {
                Foody_Mailer::send(__('איזה כיף לך! קיבלת את ספר מתכוני הפסח של FOODY'), 'e-book', $email);
                update_user_meta($user_id, 'e_book', true);
            }
        }
//        wp_new_user_notification($user_id, $password);

        return $user_id;
    }

    /**
     * Handles the registration of a new user.
     *
     * Used through the action hook "login_form_register" activated on wp-login.php
     * when accessed through the registration action.
     */
    public function do_register_user()
    {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $redirect_url = home_url('הרשמה');

            if (!foody_is_registration_open()) {
                // Registration closed, display error
                $redirect_url = add_query_arg('register-errors', 'closed', $redirect_url);
            } elseif (!$this->verify_recaptcha()) {
                // Recaptcha check failed, display error
                $redirect_url = add_query_arg('register-errors', 'captcha', $redirect_url);
            } else {
                $errors = [];

                // validate required fields
                foreach ($this->fields as $name => $var) {
                    if (empty($_POST[$name])) {
                        $errors[] = $this->get_error_message('required', $var);
                    }
                }

                if (!empty($errors)) {
                    $redirect_url = add_query_arg('register-errors', 'required', $redirect_url);
                    $redirect_url = add_query_arg('required', implode(',', $errors), $redirect_url);
                } else {

                    /** @noinspection PhpUnusedLocalVariableInspection */
                    {
                        $email = $_POST['email'];
                        $first_name = sanitize_text_field($_POST['first_name']);
                        $last_name = sanitize_text_field($_POST['last_name']);
                        $password = sanitize_text_field($_POST['password']);
                        $phone_number = sanitize_text_field($_POST['phone_number']);
                        $terms = sanitize_text_field($_POST['terms']);
                        $marketing = sanitize_text_field($_POST['marketing']);
                        $e_book = sanitize_text_field($_POST['e-book']);
                    }


                    $vars = ['email', 'first_name', 'last_name', 'password', 'phone_number', 'terms', 'marketing', 'e_book'];

                    $user_data = compact('user_data', $vars);

                    $result = $this->register_user($user_data);

                    if (is_wp_error($result)) {
                        // Parse errors into a string and append as parameter to redirect
                        $errors = join(',', $result->get_error_codes());
                        $redirect_url = add_query_arg('register-errors', $errors, $redirect_url);
                    } else {
                        // Success, redirect to home page.
                        $redirect_url = home_url('הרשמה');
                        $redirect_url = add_query_arg('registered', true, $redirect_url);
                    }
                }
            }

            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Checks that the reCAPTCHA parameter sent with the registration
     * request is valid.
     *
     * @return bool True if the CAPTCHA is OK, otherwise false.
     */
    private function verify_recaptcha()
    {
        // This field is set by the recaptcha widget if check is successful
        if (isset ($_POST['g-recaptcha-response'])) {
            $captcha_response = $_POST['g-recaptcha-response'];
        } else {
            return false;
        }

        // Verify the captcha response from Google
        $response = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            array(
                'body' => array(
                    'secret' => GOOGLE_CAPTCHA_SECRET,
                    'response' => $captcha_response
                )
            )
        );

        $success = false;
        if ($response && is_array($response)) {
            $decoded_response = json_decode($response['body']);
            $success = $decoded_response->success;
        }

        return $success;
    }

    private function get_error_message($type, $field = '')
    {

        switch ($type) {
            case 'email':
                $message = __('כתובת המייל שהוזנה אינה תקינה', 'foody');
                break;
            case
            'email_exists':
                $message = __('כתובת המייל קיימת במערכת', 'foody');
                break;
            case 'closed':
                $message = __('הרשמה סגורה', 'foody');
                break;
            case 'password':
                $message = __('על הסיסמא להכיל לפחות 8 תווים, ספרה אחת, תווים באנגלית בלבד', 'foody');
                break;
            case 'required':
                $message = __(sprintf('שדה %s הינו שדה חובה', $field), 'foody');
                break;
            default:
                $message = __('שגיאה לא ידועה', 'foody');
                break;
        }

        return $message;
    }

    private function validate_password($pass)
    {
        $valid = false;
        if (!empty($pass)) {
            $reg = '/[^a-z0-9]/i';
            $valid = preg_match($reg, $pass) == false;
        }

        return $valid;
    }

    /**
     * Redirect the user to the custom login page instead of wp-login.php.
     */
    function redirect_to_custom_login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : null;

            if (is_user_logged_in()) {
                $this->redirect_logged_in_user($redirect_to);
                exit;
            }

            $login_page = get_page_by_title('התחברות', OBJECT, 'page');
            switch_to_blog(get_main_site_id());

            $exists = ($login_page instanceof WP_Post);

            if ($exists){
                // The rest are redirected to the login page
                $login_url = home_url('התחברות');
                if (!empty($redirect_to)) {
                    $login_url = add_query_arg('redirect_to', $redirect_to, $login_url);
                }

                wp_redirect($login_url);
                exit;
            }
        }
    }

    /**
     * Redirects the user to the correct page depending on whether he / she
     * is an admin or not.
     *
     * @param string $redirect_to An optional redirect_to URL for admin users
     */
    private function redirect_logged_in_user($redirect_to = null)
    {
        if (!Foody_User::is_user_subscriber()) {
            if ($redirect_to) {
                wp_safe_redirect($redirect_to);
            } else {
                wp_redirect(admin_url());
            }
        } else {
            wp_redirect(home_url());
        }
    }


    public function redirect_admin($redirect_to, $request, $user)
    {
        if (!Foody_User::is_user_subscriber()) {
            $redirect_to = admin_url();
        }

        return $redirect_to;
    }


    public function register_custom_password_reset()
    {
        // Information needed for creating the plugin's pages
        $page_definitions = array(
            'שכחתי-סיסמא' => array(
                'title' => __('שכחת סיסמא?', 'personalize-login'),
                'content' => '[custom-password-lost-form]'
            ),
            'שינוי-סיסמא' => array(
                'title' => __('בחירת סיסמא חדשה', 'personalize-login'),
                'content' => '[custom-password-reset-form]'
            )
        );

        foreach ($page_definitions as $slug => $page) {
            // Check that the page doesn't exist already
            $query = new WP_Query('pagename=' . $slug);
            if (!$query->have_posts()) {
                // Add the page using the data from the array above
                $id = wp_insert_post(
                    array(
                        'post_content' => $page['content'],
                        'post_name' => $slug,
                        'post_title' => $page['title'],
                        'post_status' => 'publish',
                        'post_type' => 'page',
                        'ping_status' => 'closed',
                        'comment_status' => 'closed',
                    )
                );

                if (!is_wp_error($id)) {
                    update_post_meta($id, '_wp_page_template', 'page-templates/centered-content.php');
                }
            }
        }

        add_action('login_form_lostpassword', array($this, 'redirect_to_custom_lost_password'));

        add_shortcode('custom-password-lost-form', array($this, 'render_password_lost_form'));

        add_action('login_form_lostpassword', array($this, 'do_password_lost'));

        add_filter('retrieve_password_message', array($this, 'foody_replace_retrieve_password_message'), 10, 4);

        add_action('login_form_rp', array($this, 'redirect_to_custom_password_reset'));
        add_action('login_form_resetpass', array($this, 'redirect_to_custom_password_reset'));
        add_shortcode('custom-password-reset-form', array($this, 'render_password_reset_form'));

        add_action('login_form_rp', array($this, 'do_password_reset'));
        add_action('login_form_resetpass', array($this, 'do_password_reset'));
    }


    public function foody_replace_retrieve_password_message($message, $key, $user_login, $user_data)
    {
        $GLOBALS["use_html_content_type"] = TRUE;
        // Create new message
        $msg = '<div dir="rtl" style="text-align: right">';
        $msg .= __('שלום!', 'personalize-login') . "<br>";
        $msg .= sprintf(__('ביקשת לאפס סיסמא עבור כתובת המייל %s.', 'foody'), $user_login) . "<br>";
        $msg .= __("אם חלה טעות, או שלא ביקשת לאפס סיסמא - התעלמ/י ממייל זה.", 'foody') . "<br>";
        $msg .= __('על מנת לאפס את הסיסמא, היכנס/י לכתובת הבאה:', 'foody') . "<br>";
        $msg .= site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "<br>";
        $msg .= __('תודה!', 'foody') . "\r\n";
        $msg .= '</div>';
        return $msg;
    }


    /**
     * Initiates password reset.
     */
    public function do_password_lost()
    {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            retrieve_password();

            $redirect_url = home_url('שכחתי-סיסמא');
            $redirect_url = add_query_arg('checkemail', 'confirm', $redirect_url);

            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * A shortcode for rendering the form used to initiate the password reset.
     *
     * @param  array $attributes Shortcode attributes.
     * @param  string $content The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function render_password_lost_form($attributes, $content = null)
    {
        // Parse shortcode attributes
        $default_attributes = array('show_title' => false);
        $attributes = shortcode_atts($default_attributes, $attributes);

        if (is_user_logged_in()) {
            return __('You are already signed in.', 'personalize-login');
        } else {
            return $this->get_template_html('password_lost_form', $attributes);
        }
    }

    public function render_password_reset_form($attributes, $content = null)
    {
        // Parse shortcode attributes
        $default_attributes = array('show_title' => false);
        $attributes = shortcode_atts($default_attributes, $attributes);

        if (is_user_logged_in()) {
            return __('You are already signed in.', 'personalize-login');
        } else {
            if (isset($_REQUEST['login']) && isset($_REQUEST['key'])) {
                $attributes['login'] = $_REQUEST['login'];
                $attributes['key'] = $_REQUEST['key'];

                // Error messages
                $errors = array();
                if (isset($_REQUEST['error'])) {
                    $error_codes = explode(',', $_REQUEST['error']);

                    foreach ($error_codes as $code) {
                        $errors [] = $this->get_error_message($code);
                    }
                }
                $attributes['errors'] = $errors;

                return $this->get_template_html('password_reset_form', $attributes);
            } else {
                return __('לינק אינו תקין', 'foody');
            }
        }
    }

    public function redirect_to_custom_lost_password()
    {
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            if (is_user_logged_in()) {
                $this->redirect_logged_in_user();
                exit;
            }

            wp_redirect(home_url('שכחתי-סיסמא'));
            exit;
        }
    }

    public function redirect_to_custom_password_reset()
    {
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            // Verify key / login combo
            $user = check_password_reset_key($_REQUEST['key'], $_REQUEST['login']);
            if (!$user || is_wp_error($user)) {
                if ($user && $user->get_error_code() === 'expired_key') {
                    wp_redirect(home_url('התחברות?login=expiredkey'));
                } else {
                    wp_redirect(home_url('התחברות?login=invalidkey'));
                }
                exit;
            }

            $redirect_url = home_url('שינוי-סיסמא');
            $redirect_url = add_query_arg('login', esc_attr($_REQUEST['login']), $redirect_url);
            $redirect_url = add_query_arg('key', esc_attr($_REQUEST['key']), $redirect_url);

            wp_redirect($redirect_url);
            exit;
        }
    }

    /**
     * Renders the contents of the given template to a string and returns it.
     *
     * @param string $template_name The name of the template to render (without .php)
     * @param array $attributes The PHP variables for the template
     *
     * @return string               The contents of the template.
     */
    private function get_template_html($template_name, $attributes = null)
    {
        if (!$attributes) {
            $attributes = array();
        }

        ob_start();

        require(get_template_directory() . '/template-parts/' . $template_name . '.php');

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * Resets the user's password if the password reset form was submitted.
     */
    public function do_password_reset()
    {
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $rp_key = $_REQUEST['rp_key'];
            $rp_login = $_REQUEST['rp_login'];

            $user = check_password_reset_key($rp_key, $rp_login);

            if (!$user || is_wp_error($user)) {
                if ($user && $user->get_error_code() === 'expired_key') {
                    wp_redirect(home_url('התחברות?login=expiredkey'));
                } else {
                    wp_redirect(home_url('התחברות?login=invalidkey'));
                }
                exit;
            }

            if (isset($_POST['pass1'])) {
                if ($_POST['pass1'] != $_POST['pass2']) {
                    // Passwords don't match
                    $redirect_url = home_url('שינוי-סיסמא');

                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('error', 'password_reset_mismatch', $redirect_url);

                    wp_redirect($redirect_url);
                    exit;
                }

                if (empty($_POST['pass1'])) {
                    // Password is empty
                    $redirect_url = home_url('שינוי-סיסמא');

                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('error', 'password_reset_empty', $redirect_url);

                    wp_redirect($redirect_url);
                    exit;
                }

                // Parameter checks OK, reset password
                reset_password($user, $_POST['pass1']);
                wp_redirect(home_url('התחברות?password=changed'));
            } else {
                echo "אירעה שגיאה";
            }

            exit;
        }
    }

    function foody_wp_email_content_type()
    {
        if (isset($GLOBALS["use_html_content_type"]) && $GLOBALS["use_html_content_type"]) {
            return 'text/html';
        } else {
            return 'text/plain';
        }
    }

}

new Foody_Registration();