<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/9/18
 * Time: 5:59 PM
 */

$failed = !empty(isset($_GET['register-errors']));

$classes = 'register';
if ($failed) {
    $classes = "$classes failed";
}
?>

<h2>הגיע הזמן שנכיר</h2>
<p>
    <?php echo $text ?>
</p>
<p>משתמש רשום?&nbsp;
    <span style="color: #ed3d48;">
        <a style="color: #ed3d48;" href="<?php echo get_permalink(get_page_by_path('התחברות')) ?>">התחבר</a>
    </span>
</p>
<p>&nbsp;</p>

<?php
echo do_shortcode('[wordpress_social_login]');
?>

<section class="<?php echo $classes ?>">

    <div class="container-fluid">

        <div class="row">

            <div class="row col-12 justify-content-between gutter-0 buttons">
                <button class="btn btn-google col-12 col-sm-5" aria-label="google">
                                <span>
                                    <?php echo __('הירשמו דרך גוגל', 'foody') ?>
                                </span>
                    <i class="icon-Shape1"></i>
                </button>
                <button class="btn btn-facebook col-12 col-sm-5" aria-label="facebook">
                                <span>
                                    <?php echo __('הירשמו דרך פייסבוק', 'foody') ?>
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

            <form id="register-form" action="<?php echo wp_registration_url(); ?>" class="row" method="post">
                <?php if (isset($_GET['register-errors']) && $_GET['register-errors'] == 'email_exists') : ?>
                    <div role="alert" class="alert foody-alert alert-dismissible alert-danger login-failed-alert">
                        <span><?php echo __('כתובת המייל כבר קיימת במערכת.', 'foody'); ?></span>
                        <a href="<?php echo wp_login_url() ?>">
                            <?php echo __('התחבר') ?>
                        </a>
                        <a class="close" data-dismiss="alert">
                            ×
                        </a>
                    </div>
                <?php endif; ?>
                <div class="form-group col-12 required-input">
                    <label for="email">
                        <?php echo __('כתובת מייל', 'foody') ?>
                    </label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group col-12 required-input">
                    <label for="first-name">
                        <?php echo __('שם פרטי', 'foody') ?>
                    </label>
                    <input type="text" id="first-name" name="first_name" required>
                </div>
                <div class="form-group col-12 required-input">
                    <label for="last-name">
                        <?php echo __('שם משפחה', 'foody') ?>
                    </label>
                    <input type="text" id="last-name" name="last_name" required>
                </div>

                <div class="form-group col-12 required-input">
                    <label for="password">
                        <?php echo __('סיסמא', 'foody') ?>
                    </label>
                    <input type="password" id="password" aria-describedby="password-help" name="password" required>
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


                <div class="form-group col-12 required-input">
                    <label for="password-confirmation">
                        <?php echo __('וידוא סיסמא', 'foody') ?>
                    </label>
                    <input type="password" id="password-confirmation" name="password-confirmation" required>
                </div>

                <div class="form-group col-12">
                    <label for="phone-number">
                        <?php echo __('מספר טלפון', 'foody') ?>
                        <span>
                             <?php echo __('לשליחת אסמסים מותאמים אישית', 'foody') ?>
                        </span>
                    </label>
                    <input type="tel" id="phone-number" name="phone_number">
                </div>

                <!--                <div class="md-checkbox col-12">-->
                <!--                    <input id="check-terms" type="checkbox" checked name="terms" required>-->
                <!--                    <label for="check-terms">-->
                <!--                    </label>-->
                <!--                </div>-->
                <div class="md-checkbox col-12">
                    <input id="check-marketing" type="checkbox" checked name="marketing">
                    <label for="check-marketing">
                        <?php echo __('הריני לאשר בזה קבלת דואר מאתר Foody הכולל מתכונים ומידע מהאתר, וכן דואר שיווקי גם של מפרסמים הקשורים עם האתר') ?>
                    </label>
                </div>
                <?php if (get_field('show')): ?>
                    <div class="md-checkbox col-12">
                        <input id="check-e-book" type="checkbox" checked name="e-book">
                        <label for="check-e-book">
                            <?php
                            $text  = get_field('text');
                            if(empty($text)){
                                $text = __('ברצוני לקבל את ספר המתכונים לפסח');
                            }
                            echo $text;
                            ?>
                        </label>
                    </div>
                <?php endif; ?>
                <!--suppress JSUnusedLocalSymbols -->
                <script async defer>
                    function captchaCallback(token) {
                        document.getElementById('register-form').submit();
                    }
                </script>
                <div class="form-group form-submit col-12">
                    <div class="g-recaptcha"
                         data-sitekey="6Lc7eXIUAAAAAEURxX4tNyGuL4y4UJD-pGM2jzlh"
                         data-size="invisible"
                         data-callback="captchaCallback"
                    >
                    </div>
                    <button type="submit" class="btn btn-primary" aria-label="הירשם">
                        <?php echo __('הירשם') ?>
                    </button>
                </div>

            </form>


        </div>
    </div>

</section>
