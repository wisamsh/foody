<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/29/18
 * Time: 2:35 PM
 */

?>

<section class="login">

    <div class="container-fluid">

        <div class="row">

            <div class="row col-12 justify-content-between gutter-0 buttons">
                <a class="col-12 col-sm-5"
                   href="<?php echo WP_HOME ?>/wp/wp-login.php?action=wordpress_social_authenticate&mode=login&provider=Google&redirect_to=<?php echo urlencode(WP_HOME) ?>">
                    <button class="btn btn-google">
                        <span>
                            <?php echo __('כניסה דרך גוגל', 'foody') ?>
                        </span>
                        <i class="google-icon"></i>
                    </button>
                </a>
                <a class="col-12 col-sm-5"
                   href="<?php echo WP_HOME ?>/wp/wp-login.php?action=wordpress_social_authenticate&mode=login&provider=Facebook&redirect_to=<?php echo urlencode(WP_HOME) ?>">
                    <button class="btn btn-facebook">
                    <span>
                        <?php echo __('הירשמו דרך פייסבוק', 'foody') ?>
                    </span>
                        <i class="facebook-icon"></i>
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

            <form id="login-form" action="<?php echo wp_login_url(home_url()); ?>" class="row" method="post">

                <div class="form-group col-12">
                    <label for="email">
                        <?php echo __('כתובת מייל', 'foody') ?>
                    </label>
                    <input type="email" id="email" name="log">
                </div>
                <div class="form-group col-12">
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
                    <a class="forgot-password" href="<?php echo home_url() ?>">
                        <?php echo __('שכחת סיסמא?', 'foody') ?>
                    </a>
                </div>

                <div class="form-group form-submit col-12">
                    <button type="submit" class="btn btn-primary">
                        <?php echo __('המשך') ?>
                    </button>
                </div>

            </form>

        </div>
    </div>

</section>