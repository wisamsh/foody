<?php
$meta_keys = ['first_name', 'last_name', 'phone_number', 'city', 'street', 'street_number', 'zip_code'];

add_action('rest_api_init', 'users_api_register_routs');
function users_api_register_routs()
{

    register_rest_route(users_api_get_base_route(), '/login/user', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'users_api_login_user',
    ));

    register_rest_route(users_api_get_base_route(), '/create/user', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'users_api_create_user'
    ));

    register_rest_route(users_api_get_base_route(), '/update/user', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'users_api_update_user'
    ));

    register_rest_route(users_api_get_base_route(), '/get/user', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'users_api_get_user'
    ));
}

function users_api_login_user(WP_REST_Request $request_data)
{
    $body = $request_data->get_json_params();
    $username = $body['email'];
    $password = $body['password'];

    $user = wp_authenticate($username, $password);

    if (!is_wp_error($user)) {
        $response = users_api_get_user_data($user);

    } else {
        $error = [
            'error_description' => 'username or password incorrect'
        ];
        $response = users_api_generate_error_response($error);
    }

    return $response;
}

function users_api_get_user(WP_REST_Request $request_data){
    $username = $request_data->get_param('email');

    $user = get_user_by( 'email', $username);

    if($user){
        $response = users_api_get_user_data($user);
    }
    else{
        $error = [
            'error_description' => 'email does not exist'
        ];
        $response = users_api_generate_error_response($error);
    }

    return $response;
}

function users_api_create_user(WP_REST_Request $request_data)
{
    $body = $request_data->get_json_params();
    $user = email_exists($body['email']);
    $created_user_fields = [];
    if ($user) {
        $error = [
            'error_description' => "user's email already exists"
        ];
        $response = users_api_generate_error_response($error);
        return $response;
    } else {
        $email_address = $user_name = $body['email'];
        $password = $body['password'];
        $user_id = wp_create_user($user_name, $password, $email_address);
        if ($user = get_user_by('ID', $user_id)) {
            try {
                $created_user_fields = users_api_set_user_metadata($user_id, $body);
            } catch (Exception $e) {
                $response = users_api_generate_error_response('Caught exception: ' . $e->getMessage() . ' the creation of user ' . $user_id . ' stopped');
                return $response;
            }
        }
    }
    return array_merge(['ID' => $user_id], $created_user_fields);
}

function users_api_update_user(WP_REST_Request $request_data)
{
    $body = $request_data->get_json_params();
    $user_id = email_exists($body['email']);
    $user = null;
    $updated_user_fields = [];

    if (!$user_id) {
        $error = [
            'error_description' => "user does not exists"
        ];
        $response = users_api_generate_error_response($error);
        return $response;
    } else {
        $email_address = $body['email'];
        $password = isset($body['pass']) ? $body['pass'] : '';

        /** update password if not empty **/
        if ($password != '') {
            try {
                users_api_update_user_password($user_id, $email_address, $password);
            } catch (Exception $e) {
                $error = [
                    'error_description' => $e->getMessage()
                ];
                $response = users_api_generate_error_response($error);
                return $response;
            }
            $updated_user_fields['pass'] = $password;
        }

        try {
            $updated_user_meta_data = users_api_set_user_metadata($user_id, $body);
        }
        catch (Exception $e) {
            $response = users_api_generate_error_response('Caught exception: ' . $e->getMessage() . ' the update of user ' . $user_id . ' stopped');
            return $response;
        }

        return array_merge($updated_user_fields, $updated_user_meta_data);
    }
}

function users_api_get_user_data($user){
    $data = [
        'ID' => $user->data->ID,
        'user_nicename' => $user->data->user_nicename,
        'user_email' => $user->data->user_email,
        'display_name' => $user->data->display_name,
        'roles' => $user->roles
    ];
    $user_details = users_api_get_user_metadata($user->data->ID);
    $data = array_merge($data, $user_details);
    $response = new WP_REST_Response($data);
    return $response;
}

function users_api_update_user_password($user_id, $email_address, $password)
{
    /** check if password is different **/
    $user = wp_authenticate($email_address, $password);
    if (!is_wp_error($user)) {
        users_api_generate_exception('password is the same as before');
    } else {
        $user = wp_update_user(['ID' => $user_id, 'user_pass' => $password]);
        if (is_wp_error($user) || $user != $user_id) {
            users_api_generate_exception('password update failed');

        }
    }
}

function users_api_get_user_metadata($id)
{
    global $wpdb;
    $associative_arr_results = [];
    $query = "SELECT meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id = {$id}
     and(
        meta_key = 'nickname' or
        meta_key = 'first_name' or
        meta_key = 'last_name' or
        meta_key = 'phone_number' or
        meta_key = 'street' or
        meta_key = 'street_number' or
        meta_key = 'city' or
        meta_key = 'zip_code' or
        meta_key = 'birthday' or
        meta_key = 'gender'
    )";

    $results = $wpdb->get_results($query);
    foreach ($results as $result) {
        $associative_arr_results[$result->meta_key] = $result->meta_value;
    }

    return $associative_arr_results;
}

function users_api_set_user_metadata($id, $body)
{
    global $meta_keys;
    $changes_in_user = [];

    foreach ($meta_keys as $meta_key) {
        $meta_value = isset($body[$meta_key]) ? $body[$meta_key] : '';
        try {
            $updated = users_api_set_meta_key_and_value($id, $meta_key, $meta_value);
            if ($updated) {
                $changes_in_user[$meta_key] = $meta_value;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    return $changes_in_user;
}

function users_api_set_meta_key_and_value($id, $key, $value_to_set)
{
    $updated = false;
    $current_meta_value = get_user_meta($id, $key);
    $current_meta_value = isset($current_meta_value[0]) ? $current_meta_value[0] : '';

    if ($value_to_set != '') {
        if($value_to_set != $current_meta_value) {
            $updated = update_user_meta($id, $key, $value_to_set);
            if ($updated == false) {
                users_api_generate_exception('update of ' . $key . ' key failed.');
            } else {
                $updated = true;
            }
        }
    }
    return $updated ;
}

function users_api_generate_error_response($error)
{
    $response = new WP_REST_Response($error);
    $response->header('Content-Type', 'application/json');

    return $response;
}

function users_api_generate_exception($message)
{
    throw new Exception($message);
}

function users_api_get_base_route(){
    $route_base = 'users-api';
    return $route_base.'/'.users_api_get_version();
}

function users_api_get_version(){
    $version = 'v1';
    return $version;
}