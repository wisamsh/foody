<?php

/**
 * Template Name: OG tester
 * Created by Wisam Shomar.
 * Date: 10/12/2021
 * Time: 13:00PM
 */
$actual_link = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
function encodeURIComponent($str)
{
    $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
    return strtr(rawurlencode($str), $revert);
}



if ($_REQUEST['u'] == trim('')) {
    die("חסרים פרמטרים!");
}

?>

<script src="https://code.jquery.com/jquery-1.11.3.js"></script>
<meta property="og:url" content="<?php echo $actual_link; ?>" />
<meta property="og:type" content="article" />
<meta property="og:title" content="פודי" />
<meta property="og:description" content="שולחם לך תמונה באהבה" />

<meta property="og:image" itemdrop="image" content="<?php echo $_REQUEST['u']; ?>" />

<body>

    <?php
    $classPhoto = wp_is_mobile() ? 'style="width:100%;"' : '';
    $buttonClass = wp_is_mobile() ? 'style="margin:0 auto;width:90%;height:200px;font-size:80px;"' : '';
    ?>

    <div style="margin: 0 auto ;margin-top:40px;width: 100%;text-align: center;">
        <img src="<?php echo $_REQUEST['u']; ?>" <?php echo $classPhoto; ?> />
        <p>
            <input type="button" value="שתף בוואטסאפ" id="whatsapp_share" <?php echo $buttonClass; ?> />
        </p>
    </div>


    <script>
        $(document).ready(function() {

            //https://api.whatsapp.com/send?text=
            let url = "<?php echo $actual_link; ?>";
            $("#whatsapp_share").click(function() {
                location.href = "https://api.whatsapp.com/send/?text=" + url;

            });

            //$( "#whatsapp_share" ).trigger( "click" );


        });
    </script>
</body>