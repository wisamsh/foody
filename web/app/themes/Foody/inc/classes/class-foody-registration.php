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
        $user_data = array(
            'user_login' => $email,
            'user_email' => $email,
            'user_pass' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'nickname' => $first_name,
        );

        $user_id = wp_insert_user($user_data);
        if (!is_wp_error($user_id)) {
            /** @noinspection PhpUndefinedVariableInspection */
            update_user_meta($user_id, 'phone_number', $phone_number);
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

            if (!get_option('users_can_register')) {
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
                    }


                    $vars = ['email', 'first_name', 'last_name', 'password', 'phone_number', 'terms', 'marketing'];

                    $user_data = compact('user_data', $vars);

                    $result = $this->register_user($user_data);

                    if (is_wp_error($result)) {
                        // Parse errors into a string and append as parameter to redirect
                        $errors = join(',', $result->get_error_codes());
                        $redirect_url = add_query_arg('register-errors', $errors, $redirect_url);
                    } else {
                        // Success, redirect to home page.
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

            // The rest are redirected to the login page
            $login_url = home_url('התחברות');
            if (!empty($redirect_to)) {
                $login_url = add_query_arg('redirect_to', $redirect_to, $login_url);
            }

            wp_redirect($login_url);
            exit;
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
        $user = wp_get_current_user();
        if (user_can($user, 'manage_options')) {
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

        global $user;
        if (isset($user->roles) && is_array($user->roles)) {
            if (!Foody_User::is_user_subscriber()) {
                return admin_url();
            } else {
                return $redirect_to;
            }
        } else {
            return $redirect_to;
        }
    }

}

new Foody_Registration();