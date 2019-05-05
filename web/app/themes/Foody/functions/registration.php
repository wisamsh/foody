<?php /** @noinspection PhpComposerExtensionStubsInspection */
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/2/18
 * Time: 11:12 AM
 */


function wsl_change_default_permissons($provider_scope, $provider)
{
    if ('facebook' == strtolower($provider)) {
        $provider_scope = 'email, public_profile';
    }

    return $provider_scope;
}

add_filter('wsl_hook_alter_provider_scope', 'wsl_change_default_permissons', 10, 2);


/**
 * Automatically logs the user in
 * after registration
 * @param int $user_id
 */
function auto_login_new_user($user_id)
{
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    $redirect_url = home_url('הרשמה');
    $redirect_url = add_query_arg('registered', true, $redirect_url);

    Foody_Analytics::get_instance()->user_register();

    wp_redirect($redirect_url);
}

add_action('user_register', 'auto_login_new_user');


function foody_user_login($user_login, $user)
{

    if (Foody_User::is_user_subscriber()) {

        Foody_Analytics::get_instance()->event('login', [
            'email' => $user->user_email
        ]);
    }
}


add_action('wp_login', 'foody_user_login', 10, 2);

function foody_user_logout()
{
    $user = wp_get_current_user();

    if (Foody_User::is_user_subscriber()) {

        Foody_Analytics::get_instance()->event('logout', [
            'email' => $user->user_email
        ]);
    }
}


add_action('clear_auth_cookie', 'foody_user_logout', 10, 2);


//function social_login_redirect($user_id, $provider, $hybridauth_user_profile, $redirect_to)
//{
//    $redirect_to = $redirect_to;
//}
//
//add_action('wsl_hook_process_login_before_wp_safe_redirect', 'social_login_redirect',10,4);

add_action('wp', function () {
    if (isset($_POST['submit_change_pass'])) {

        $required = [
            'current_password',
            'password',
            'password_confirmation'
        ];

        $errors = foody_form_validation($required);

        if (!empty($errors)) {
        } else {
            if (!is_user_logged_in()) {
                $error = ['message' => "unauthorized"];
            } else {
                $user = wp_get_current_user();

                $user_pass = $user->user_pass;

                $current_password = $_POST['current_password'];
                $new_password = $_POST['password'];


                if (wp_check_password($current_password, $user_pass)) {

                    $userID = $user->ID;
                    $user_login = $user->user_login;

                    wp_set_password($new_password, $user->ID);

                    $user = wp_signon(array('user_login' => $user->user_login, 'user_password' => $new_password));

                    wp_set_auth_cookie($user->ID, true, false);

//                if (is_wp_error($user)) {
//                    var_dump($user);
//                } else {
//
////                    wp_set_current_user($userID, $user_login);
//                    wp_set_auth_cookie($user->ID, true, false);
////                    do_action('wp_login', $user_login);
//                }

                } else {
                    $error = [
                        'message' => 'invalid password'
                    ];
                }
            }
        }
    }
});

add_action('wp_login_failed', 'foody_login_fail');  // hook failed login
function foody_login_fail($username)
{
    $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
    // if there's a valid referrer, and it's not the default log-in screen
    if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) {
        wp_redirect(home_url('התחברות') . "/?login=failed&l=$username");  // let's append some information (login=failed) to the URL for the theme to use
        exit;
    }
}

//function wpse125952_redirect_to_request( $redirect_to, $request, $user ){
//    // instead of using $redirect_to we're redirecting back to $request
//    return $request;
//}
//add_filter('login_redirect', 'wpse125952_redirect_to_request', 10, 3);

add_action('wp_login', 'track_user_logins', 10, 2);
function track_user_logins($user_login, $user)
{
    if ($login_amount = get_user_meta($user->ID, 'login_amount', true)) {
        // They've Logged In Before, increment existing total by 1
        update_user_meta($user->ID, 'login_amount', ++$login_amount);
    } else {
        // First Login, set it to 1
        update_user_meta($user->ID, 'login_amount', 1);
    }
}

add_filter('auth_cookie_expiration', 'foody_authentication_expiration');
function foody_authentication_expiration($expire_in)
{
    // 1 year in seconds
    $expire_in_a_year = 31556926;

    if (Foody_User::is_user_subscriber()) {
        $expire_in = $expire_in_a_year;
    }

    return $expire_in;
}

function foody_register_newsletter($email)
{

    $curl = curl_init();

    $base_url = get_viplus_url();

    $query = http_build_query([
        'email' => $email,
        'apikey' => VIPLUS_KEY,
        'viplists' => 489261,
        'exists' => 'merge'
    ]);

    $url = "$base_url?$query";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => ""
    ));

    $response = curl_exec($curl);

    $valid_responses = [
        'OkUpdated',
        'OkInserted'
    ];

    curl_close($curl);

    $user = get_user_by('email', $email);

    $result = in_array($response,$valid_responses);

    update_user_meta($user->ID, 'newsletter', $result);

}

function get_viplus_url()
{
    return VIPLUS_BASE_URL;
}


if ( ! function_exists( 'foody_is_registration_open' ) ) {
	function foody_is_registration_open() {
		return get_option('users_can_register');
	}
}