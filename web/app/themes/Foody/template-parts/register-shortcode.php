<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/27/18
 * Time: 3:36 PM
 */

?>

<section class="register">

    <div class="container-fluid">

        <div class="row">

            <div class="row col-12 justify-content-between gutter-0 buttons">
                <button class="btn btn-google col-12 col-sm-5">
                    <span>
                        <?php echo __('כניסה דרך גוגל', 'foody') ?>
                    </span>
                    <span class="icon-google"></span>
                </button>

                <button class="btn btn-facebook col-12 col-sm-5">
                    <span>
                        <?php echo __('הירשמו דרך פייסבוק', 'foody') ?>
                    </span>
                    <span class="icon-facebook"></span>
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

                <div class="form-group col-12">
                    <label for="email">
                        <?php echo __('כתובת מייל', 'foody') ?>
                    </label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-group col-12">
                    <label for="first-name">
                        <?php echo __('שם פרטי', 'foody') ?>
                    </label>
                    <input type="text" id="first-name" name="first_name">
                </div>
                <div class="form-group col-12">
                    <label for="last-name">
                        <?php echo __('שם משפחה', 'foody') ?>
                    </label>
                    <input type="text" id="last-name" name="last_name">
                </div>

                <div class="form-group col-12">
                    <label for="password">
                        <?php echo __('סיסמא', 'foody') ?>
                    </label>
                    <input type="password" id="password" aria-describedby="password-help" name="password">
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


                <div class="form-group col-12">
                    <label for="password-confirmation">
                        <?php echo __('וידוא סיסמא', 'foody') ?>
                    </label>
                    <input type="password" id="password-confirmation" name="password-confirmation">
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
                    <input id="check-terms" type="checkbox" checked name="terms">
                    <label for="check-terms">
                        <?php echo __('בהרשמתי אני מסכים למסירת פרטים , נדרש קופי למדיניות פריטיות ') ?>
                    </label>
                </div>
                <div class="md-checkbox col-12">
                    <input id="check-marketing" type="checkbox" checked name="marketing">
                    <label for="check-marketing">
                        <?php echo __('הנני מעוניין בקבלת תוכן מדהים למייל.') ?>
                    </label>
                </div>
                <!--suppress JSUnusedLocalSymbols -->
                <script>
                    function captchaCallback(token) {
                        document.getElementById('register-form').submit();
                    }
                </script>
                <div class="form-group form-submit col-12">
                    <button type="submit" class="btn btn-primary g-recaptcha"
                            data-sitekey="6Lc7eXIUAAAAAEURxX4tNyGuL4y4UJD-pGM2jzlh" disabled
                            data-callback="captchaCallback">
                        <?php echo __('הירשם') ?>
                    </button>
                </div>

            </form>


        </div>
    </div>

</section>