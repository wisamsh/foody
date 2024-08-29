<?php

/**
 * Template Name: Unsubscribe
 * Created by Wisam Shomar.
 * Date: 10/12/2021
 * Time: 13:00PM
 */
get_header();
$Foody_Verfication = new Foody_Verfication;


?>
<center>
    <?php if (wp_is_mobile()) {
        echo "<a href='/'>{$Foody_Verfication->BindLogo()}</a>";
    } ?>
</center>
<div class="terminator_Wrap">

    <?php
    if (isset($_GET['cat']) && $_GET['cat'] != '') {
        $cat_arr = explode("-", $_GET['cat']);
        $cat_id = $cat_arr[0];
        $author_id = $cat_arr[1];
        print_r($cat_arr);
        $author = get_user_by('ID', $author_id);

        if ($author) {
            // Access the author's details
            $author_name = $author->display_name;
            $author_email = $author->user_email;
        }

        $email_encripted = $Foody_Verfication->getEncryptEmail;
        $email = $Foody_Verfication->decrypt_string($email_encripted, 'bar');
        $emailExist = $Foody_Verfication->CheckingEmailifExist($email);
    }
    if ($emailExist) {
        $category = get_category($cat_id);

    ?>
        <h1>להסרה מרשימת התפוצה לקבלת התראות</h1>
        <h4><?php echo $email . $author_name; ?></h4>
        <input type="button" id="terminate_all" value="הסירו אותי" class="terminate" />
        <input type="button" id="category_btn" value="להסרה מקטגוריה <?php echo $category->name; ?>" class="terminate" />
    <?php } else {
    ?>
        <h1>האימייל אינו קיים במערכת יתכן ונמחק או שלא היה קיים!</h1>
    <?php } ?>
    <div id="response"></div>
</div>
<style>
    .terminator_Wrap {
        width: 100%;
        text-align: center;
        padding: 10px;
        min-height: 200px;

        align-items: center;
        border: solid 1px #56a0bb;
        border-radius: 5px;
        margin-top: 40px;
        margin-bottom: 40px;
    }

    .terminator_Wrap h1,
    h4 {
        width: 100%;
        text-align: center;
    }

    .terminate {


        padding: 10px;
        margin: 10px;
        border: solid 1px #f5f5f5;
        font-size: 20px !important;
        transform: scale();
        cursor: pointer;

    }

    h4 {
        color: #56a0bb !important;
    }

    .terminate:hover {
        background-color: #56a0bb !important;
    }

    @media (min-width: 900px) {
        .terminate {
            display: inline-block !important;
            width: 42% !important;
        }
    }

    @media (max-width: 900px) {
        .terminate {
            display: block !important;
            width: 100%;
        }
    }
</style>

<?php get_footer(); ?>