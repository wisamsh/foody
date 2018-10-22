<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/9/18
 * Time: 5:59 PM
 */

?>

<h2>הגיע הזמן שנכיר</h2>
<p>הירשמו כדי להנות מתוכן מותאם אישית ולהשתתף בדיונים פורים.</p>
<p>משתמש רשום?&nbsp;
    <span style="color: #ed3d48;">
        <a style="color: #ed3d48;" href="http://foody.co.il/%d7%94%d7%a8%d7%a9%d7%9e%d7%94/">התחבר</a>
    </span>
</p>
<p>&nbsp;</p>

<?php
//do_shortcode('[wordpress_social_login]');
//?>

<section class="register">

    <div class="container-fluid">

        <div class="row">

            <div class="row col-12 justify-content-between gutter-0 buttons">
                <a class="col-12 col-sm-5" target="_top"
                   href="
            <?php echo WP_HOME ?>/wp/wp-login.php?action=wordpress_social_authenticate&mode=login&provider=Google&redirect_to=
            <?php echo urlencode(WP_HOME) ?>">
                    <button class="btn btn-google">
                                <span>
                                    <?php echo __('כניסה דרך גוגל', 'foody') ?>
                                </span>
                        <span class="icon-google"></span>
                    </button>
                </a>
                <a class="col-12 col-sm-5" target="_top"
                   href="
            <?php echo WP_HOME ?>/wp/wp-login.php?action=wordpress_social_authenticate&mode=login&provider=Facebook&redirect_to=
            <?php echo urlencode(WP_HOME) ?>">
                    <button class="btn btn-facebook">
                                <span>
                                    <?php echo __('הירשמו דרך פייסבוק', 'foody') ?>
                                </span>
                        <span class="icon-facebook"></span>
                    </button>
                </a>
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

                <div class="md-checkbox col-12">
                    <input id="check-terms" type="checkbox" checked name="terms" required>
                    <label for="check-terms">
                        <?php echo __('בהרשמתי אני מסכים למסירת פרטים , נדרש קופי למדיניות פריטיות ') ?>
                    </label>
                </div>
                <div class="md-checkbox col-12">
                    <input id="check-marketing" type="checkbox" checked name="marketing">
                    <label for="check-marketing">
                        <?php echo __('הריני לאשר בזה קבלת דואר מאתר Foody הכולל מתכונים ומידע מהאתר, וכן דואר שיווקי גם של מפרסמים הקשורים עם האתר') ?>
                    </label>
                </div>
                <!--suppress JSUnusedLocalSymbols -->
                <script>
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
                    <button type="submit" class="btn btn-primary">
                        <?php echo __('הירשם') ?>
                    </button>
                </div>

            </form>


        </div>
    </div>

</section>
