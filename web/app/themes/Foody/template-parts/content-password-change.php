<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/23/18
 * Time: 4:00 PM
 */

if (isset($_POST['submit_change_pass'])) {

    $required = [
        'current_password',
        'password',
        'password_confirmation'
    ];

    $errors = foody_form_validation($required);

    if (!empty($errors)) {
        var_dump($errors);

    } else {
        if (!is_user_logged_in()) {
            $error = ['message' => "unauthorized"];
            var_dump($error);
        } else {
            $user = wp_get_current_user();

            $user_pass = $user->user_pass;

            $current_password = $_POST['current_password'];
            $new_password = $_POST['password'];

            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $wp_hasher = new PasswordHash(8, true);

            if ($wp_hasher->CheckPassword($current_password, $user_pass)) {

                wp_set_password($new_password, $user->ID);


                $user = wp_signon(array('user_login' => $user->user_login, 'user_password' => $new_password));


                if (is_wp_error($user)) {
                    var_dump($user);
                } else {
                    $userID = $user->ID;
                    $user_login = $user->user_login;
                    wp_set_current_user($userID, $user_login);
                    wp_set_auth_cookie($userID, true, false);
                    do_action('wp_login', $user_login);
                }

            } else {
                $error = [
                    'message' => 'invalid password'
                ];
                var_dump($error);
            }
        }
    }
}
/** @noinspection PhpUndefinedVariableInspection */
$form_classes = foody_get_array_default($template_args, 'form_classes', []);
?>

<h3 class="title">
    <?php echo __('שינוי סיסמא', 'foody'); ?>
</h3>

<form class="<?php foody_el_classes($form_classes) ?>" id="password-reset" novalidate
      action="" method="post">


    <div class="form-group col-12 required-input">
        <label for="current-password">
            <?php echo __('הזן סיסמא נוכחית', 'foody') ?>
        </label>
        <input type="password" id="current-password" name="current_password" required>
    </div>


    <div class="form-group col-12 required-input">
        <label for="password">
            <?php echo __('סיסמא חדשה', 'foody') ?>
        </label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group col-12 required-input">
        <label for="password-confirmation">
            <?php echo __('וידוא סיסמא', 'foody') ?>
        </label>
        <input type="password" id="password-confirmation" aria-describedby="password-help"
               name="password_confirmation"
               required>
    </div>

    <ul id="password-help" class="form-text text-muted">
        <li>
            <?php echo __('לפחות 8 תווים'); ?>
        </li>
        <li>
            <?php echo __('תווים באנגלית בלבד'); ?>
        </li>
        <li>
            <?php echo __('לפחות ספרה אחת'); ?>
        </li>
    </ul>

    <div class="form-group form-submit col-12 row justify-content-between gutter-0">
        <ul class="nolist nav nav-tabs col-lg-4 col-5" id="change-tabs">
            <li>
                <a role="tab" data-toggle="tab"
                   href="#user-content" aria-controls="user-content">
                    <button type="button" class="btn btn-primary btn-cancel">
                        <?php echo __('ביטול') ?>
                    </button>
                </a>
            </li>
        </ul>

        <button type="submit" name="submit_change_pass" class="btn btn-primary col-lg-4 col-5">
            <?php echo __('שיחזור סיסמא') ?>
        </button>
    </div>

</form>

