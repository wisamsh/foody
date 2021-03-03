<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/29/18
 * Time: 2:35 PM
 */
$login_status = '';
$text         = isset( $template_args ) && isset( $template_args['text'] ) ? $template_args['text'] : '';

$email = isset( $_GET['l'] ) ? $_GET['l'] : '';
if ( isset( $_GET['login'] ) ) {
	$login_status = $_GET['login'];
}

$foody_lost_password = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm' && false;
?>

<div class="login-title">התחברות</div>
<p>
	<?php echo $text ?>
</p>
<p>
    <span>
    משתמשים חדשים?
</span>
    <?php if ( isset($_GET['campaign']) ) { ?>
        <a class="go-to-register" href="<?php echo get_permalink( get_page_by_path( 'הרשמה' ) ) ?>?rishom=true">הירשמו</a>
    <?php } else { ?>
        <a class="go-to-register" href="<?php echo get_permalink( get_page_by_path( 'הרשמה' ) ) ?>">הירשמו</a>
    <?php } ?>


</p>
<?php
echo do_shortcode( '[wordpress_social_login]' );
?>
<section class="login <?php echo $login_status ?>">

    <div class="container-fluid">

        <div class="row">

            <div class="row col-12 justify-content-center gutter-0 buttons">
                <?php if (get_option('foody_show_google_login') != "") { ?>
                    <button class="btn btn-google col-12 col-sm-5" aria-label="google">
                    <span>
                        <?php echo __('להמשיך עם Google', 'foody') ?>
                    </span>
                        <i class="google-icon"></i>
                    </button>
                <?php } ?>
                <button class="btn btn-facebook col-12 col-sm-5" aria-label="facebook">
                    <span>
                        <?php echo __( 'התחברו דרך פייסבוק', 'foody' ) ?>
                    </span>
                    <i class="icon-Facebook"></i>
                </button>

            </div>

            <div class="row col-12 justify-content-between gutter-0 dividers">

                <div class="divider col-5"></div>
                <div class="col-2 or">
                                <span>
                                    <?php echo __( 'או', 'foody' ) ?>
                                </span>
                </div>
                <div class="divider col-5"></div>

            </div>
            <?php $x=1; ?>
            <form id="login-form" action="<?php echo wp_login_url( home_url() ); ?>" class="row" method="post">

                <div role="alert" class="alert foody-alert alert-dismissible alert-danger login-failed-alert">
                    <span><?php echo __( 'התחברות נכשלה. אנא ודא/י את כתובת המייל והסיסמא', 'foody' ); ?></span>
                    <a class="close" data-dismiss="alert">
                        ×
                    </a>
                </div>

				<?php if ( $foody_lost_password ): ?>
                    <div role="alert" class="alert foody-alert alert-dismissible alert-success login-change-passsword">
                        <span><?php echo __( 'לינק איפוס סיסמא נשלח לכתובת שהוזנה', 'foody' ); ?></span>
                        <a class="close" data-dismiss="alert">
                            ×
                        </a>
                    </div>
				<?php endif; ?>

                <div class="form-group col-12 required-input">
                    <label for="email">
						<?php echo __( 'כתובת מייל', 'foody' ) ?>
                    </label>
                    <input type="text" id="email" name="log" value="<?php echo $email ?>">
                </div>
                <div class="form-group col-12 required-input">
                    <label for="password">
						<?php echo __( 'סיסמא', 'foody' ) ?>
                    </label>
                    <input type="password" id="password" aria-describedby="password-help" name="pwd">
                </div>


                <div class="md-checkbox col-6">
                    <input id="stay-connected" type="checkbox" checked name="forever">
                    <label for="stay-connected">
						<?php echo __( 'הישאר מחובר' ) ?>
                    </label>
                </div>

                <div class="form-group col-6">
                    <a class="forgot-password" href="<?php echo wp_lostpassword_url() ?>">
						<?php echo __( 'שכחת סיסמא?', 'foody' ) ?>
                    </a>
                </div>

                <div class="form-group form-submit col-12">
                    <button type="submit" class="btn btn-primary" aria-label="המשך">
						<?php echo __( 'המשך' ) ?>
                    </button>
                </div>

                <?php

                $queried_object = get_queried_object();
                // redirect to category/tag page
                if(is_category() || is_tag()){
                    $redirect_to =  get_term_link($queried_object);
                }
                // redirect to author page
                elseif (is_author()){
                    if(isset($queried_object->ID)) {
                        $redirect_to = get_author_posts_url($queried_object->ID);
                    } else{
                        $redirect_to = home_url();
                    }
                }
                // redirect to post page
                else {
                    $redirect_to = get_permalink();
                }
                // redirect to home page
                if ( strpos( $redirect_to, 'התחברות' ) !== false ) {
                    $redirect_to = home_url();
                }

                if($redirect_to === home_url() || $redirect_to === home_url().'/' || $redirect_to === rtrim(home_url(), '/')){
                    if(strpos($redirect_to, '?') === false){
                        $redirect_to = $redirect_to . '?logister_popup=1';
                    } else {
                        $redirect_to = $redirect_to . '&logister_popup=1';
                    }
                }
                ?>
                <input type="hidden" name="redirect_to" value="<?php echo $redirect_to; ?>">

            </form>

        </div>
    </div>

</section>