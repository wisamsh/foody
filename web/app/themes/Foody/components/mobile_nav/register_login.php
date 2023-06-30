<?php 
if ($domain_name == "foody.co.il" || $domain_name == "foody-local.co.il" || $domain_name == "staging.foody.co.il"){
    ?>
<div class="regist">
    <?php if (!is_user_logged_in()) {


    ?>

        <a href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/%D7%94%D7%AA%D7%97%D7%91%D7%A8%D7%95%D7%AA/?redirect_to=https://<?php echo $_SERVER['HTTP_HOST']; ?>/%d7%a4%d7%a8%d7%95%d7%a4%d7%99%d7%9c-%d7%90%d7%99%d7%a9%d7%99/">
            הרשמו ל- FOODY
        </a>
    <?php
    } else {

        $current_user = wp_get_current_user();
        echo ('<div class="welcome_div">');
        echo ("שלום " . $current_user->user_nicename);
        echo ('</div>');
    ?>


    <?php
    }
    ?>
</div>
<?php }?>