<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/29/18
 * Time: 2:35 PM
 */
$login_status = '';
$email = isset($_GET['l']) ? $_GET['l'] : '';
if (isset($_GET['login'])) {
    $login_status = $_GET['login'];
}

$foody_lost_password = isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm' && false;

?>

<h2>התחברות</h2>
<p>
    <?php echo __('התחברו ותתחילו להנות ממגוון עצום של תכנים קולינריים ומתכונים עם אלפי שעות וידאו, להרכיב לעצמכם ספר מתכונים אישי עם המתכונים שהכי אהבתם, לשתף בתמונות, להגיב ולשאול שאלות.') ?>
</p>
<p>
    <span>
    משתמש חדש?
</span>

    <a class="go-to-register" href="<?php echo get_permalink(get_page_by_path('הרשמה')) ?>">הירשם</a>

</p>
<?php
echo do_shortcode('[wordpress_social_login]');
?>
<section class="login <?php echo $login_status ?>">

    <div class="container-fluid">

        <div class="row">

            <div class="row col-12 justify-content-between gutter-0 buttons">

                <button class="btn btn-google col-12 col-sm-5" aria-label="google">
                    <span>
                        <?php echo __('התחברו דרך גוגל', 'foody') ?>
                    </span>
                    <i class="icon-Shape1"></i>
                </button>
                <button class="btn btn-facebook col-12 col-sm-5" aria-label="facebook">
                    <span>
                        <?php echo __('התחברו דרך פייסבוק', 'foody') ?>
                    </span>
                    <i class="icon-Facebook"></i>
                </button>

            </div>

            <div class="row col-12 justify-content-between gutter-0 dividers">

                <div class="divider col-5"></div>
                <div class="col-2 or">
                                <span>
                                    <?php echo __('או', 'foody') ?>
                                </span>
                </div>
                <div class="divider col-5"></div>

            </div>

            <form id="login-form" action="<?php echo wp_login_url(home_url()); ?>" class="row" method="post">

                <div role="alert" class="alert foody-alert alert-dismissible alert-danger login-failed-alert">
                    <span><?php echo __('התחברות נכשלה. אנא ודא/י את כתובת המייל והסיסמא', 'foody'); ?></span>
                    <a class="close" data-dismiss="alert">
                        ×
                    </a>
                </div>

                <?php if ($foody_lost_password): ?>
                    <div role="alert" class="alert foody-alert alert-dismissible alert-success login-change-passsword">
                        <span><?php echo __('לינק איפוס סיסמא נשלח לכתובת שהוזנה', 'foody'); ?></span>
                        <a class="close" data-dismiss="alert">
                            ×
                        </a>
                    </div>
                <?php endif; ?>

                <div class="form-group col-12 required-input">
                    <label for="email">
                        <?php echo __('כתובת מייל', 'foody') ?>
                    </label>
                    <input type="text" id="email" name="log" value="<?php echo $email ?>">
                </div>
                <div class="form-group col-12 required-input">
                    <label for="password">
                        <?php echo __('סיסמא', 'foody') ?>
                    </label>
                    <input type="password" id="password" aria-describedby="password-help" name="pwd">
                </div>


                <div class="md-checkbox col-6">
                    <input id="stay-connected" type="checkbox" checked name="forever">
                    <label for="stay-connected">
                        <?php echo __('הישאר מחובר') ?>
                    </label>
                </div>

                <div class="form-group col-6">
                    <a class="forgot-password" href="<?php echo wp_lostpassword_url() ?>">
                        <?php echo __('שכחת סיסמא?', 'foody') ?>
                    </a>
                </div>

                <div class="form-group form-submit col-12">
                    <button type="submit" class="btn btn-primary" aria-label="המשך">
                        <?php echo __('המשך') ?>
                    </button>
                </div>

                <?php
                $redirect_to = get_permalink();
                if (strpos($redirect_to, 'התחברות') !== false) {
                    $redirect_to = home_url();
                }
                ?>

                <input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>">

            </form>

        </div>
    </div>

</section>