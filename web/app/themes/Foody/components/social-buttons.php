<?php $Pagetitle = wp_title( '|', false, 'right' );
 $Pagetitle ;?>
<div class="social-btn-container">
    <i class="icon-share"></i>
    <div class="social-buttons-container hidden">
<?php
foody_get_template_part(
    get_template_directory() . '/template-parts/content-social-actions-mobile-menu.php'
);

?>
<!--
        <div class=" social col">
            <div class="essb_links essb_counter_modern_right essb_displayed_shortcode essb_share essb_template_round-retina essb_1616723342 print-no" id="essb_displayed_shortcode_1616723342" data-essb-postid="85235" data-essb-position="shortcode" data-essb-button-style="icon" data-essb-template="round-retina" data-essb-counter-pos="right" data-essb-url="https://foody.co.il/foody_recipe/%d7%91%d7%9e%d7%a7%d7%95%d7%9d-%d7%a1%d7%a0%d7%93%d7%95%d7%95%d7%99%d7%a5-%d7%9e%d7%90%d7%a4%d7%99%d7%a0%d7%a1-%d7%91%d7%98%d7%98%d7%94-%d7%95%d7%92%d7%91%d7%99%d7%a0%d7%94-%d7%a9%d7%99%d7%9c%d7%93/" data-essb-twitter-url="https://foody.co.il/foody_recipe/%d7%91%d7%9e%d7%a7%d7%95%d7%9d-%d7%a1%d7%a0%d7%93%d7%95%d7%95%d7%99%d7%a5-%d7%9e%d7%90%d7%a4%d7%99%d7%a0%d7%a1-%d7%91%d7%98%d7%98%d7%94-%d7%95%d7%92%d7%91%d7%99%d7%a0%d7%94-%d7%a9%d7%99%d7%9c%d7%93/" data-essb-instance="1616723342">
                <ul class="essb_links_list essb_force_hide_name essb_force_hide">
                    <li class="essb_item essb_link_gmail nolightbox">
                        <a href="https://mail.google.com/mail/u/0/?view=cm&fs=1&su=במקום סנדוויץ: מאפינס בטטה וגבינה שילדים אוהבים - פודי - Foody&body= מתכון מומלץ מפודי: https://foody.co.il/foody_recipe/%d7%91%d7%9e%d7%a7%d7%95%d7%9d-%d7%a1%d7%a0%d7%93%d7%95%d7%95%d7%99%d7%a5-%d7%9e%d7%90%d7%a4%d7%99%d7%a0%d7%a1-%d7%91%d7%98%d7%98%d7%94-%d7%95%d7%92%d7%91%d7%99%d7%a0%d7%94-%d7%a9%d7%99%d7%9c%d7%93/&ui=2&tf=1" title="" 
                        onclick="essb.window('https://mail.google.com/mail/u/0/?view=cm&fs=1&su=במקום סנדוויץ: מאפינס בטטה וגבינה שילדים אוהבים - פודי - Foody&body= מתכון מומלץ מפודי: https://foody.co.il/foody_recipe/%d7%91%d7%9e%d7%a7%d7%95%d7%9d-%d7%a1%d7%a0%d7%93%d7%95%d7%95%d7%99%d7%a5-%d7%9e%d7%90%d7%a4%d7%99%d7%a0%d7%a1-%d7%91%d7%98%d7%98%d7%94-%d7%95%d7%92%d7%91%d7%99%d7%a0%d7%94-%d7%a9%d7%99%d7%9c%d7%93/&ui=2&tf=1','gmail','1616723342'); return false;" 
                        target="_blank" rel="nofollow">
                        <span class="essb_icon essb_icon_gmail"></span>
                        <span class="essb_network_name essb_noname"></span></a>
                    </li>
                    <li class="essb_item essb_link_pinterest nolightbox">
                        <a href="#" title="" onclick="essb.pinterest_picker('1616723342'); return false;" target="_blank" rel="nofollow">
                            <span class="essb_icon essb_icon_pinterest"></span><span class="essb_network_name essb_noname"></span></a>
                    </li>
                    <li class="essb_item essb_link_whatsapp nolightbox">
                        <a href="whatsapp://send?text=במקום%20סנדוויץ%3A%20מאפינס%20בטטה%20וגבינה%20שילדים%20אוהבים%20-%20פודי%20-%20Foody%20https%3A%2F%2Ffoody.co.il%2F%3Fp%3D85235" title="" onclick="essb.tracking_only('', 'whatsapp', '1616723342', true);" target="_self" rel="nofollow">
                            <span class="essb_icon essb_icon_whatsapp"></span>
                            <span class="essb_network_name essb_noname"></span></a>
                    </li>
                    <li class="essb_item essb_link_facebook nolightbox">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=https://foody.co.il/foody_recipe/%d7%91%d7%9e%d7%a7%d7%95%d7%9d-%d7%a1%d7%a0%d7%93%d7%95%d7%95%d7%99%d7%a5-%d7%9e%d7%90%d7%a4%d7%99%d7%a0%d7%a1-%d7%91%d7%98%d7%98%d7%94-%d7%95%d7%92%d7%91%d7%99%d7%a0%d7%94-%d7%a9%d7%99%d7%9c%d7%93/&t=במקום סנדוויץ: מאפינס בטטה וגבינה שילדים אוהבים - פודי - Foody" title="" onclick="essb.window('https://www.facebook.com/sharer/sharer.php?u=https://foody.co.il/foody_recipe/%d7%91%d7%9e%d7%a7%d7%95%d7%9d-%d7%a1%d7%a0%d7%93%d7%95%d7%95%d7%99%d7%a5-%d7%9e%d7%90%d7%a4%d7%99%d7%a0%d7%a1-%d7%91%d7%98%d7%98%d7%94-%d7%95%d7%92%d7%91%d7%99%d7%a0%d7%94-%d7%a9%d7%99%d7%9c%d7%93/&t=במקום סנדוויץ: מאפינס בטטה וגבינה שילדים אוהבים - פודי - Foody','facebook','1616723342'); return false;" target="_blank" rel="nofollow"><span class="essb_icon essb_icon_facebook"></span><span class="essb_network_name essb_noname"></span></a>
                    </li>
                    <li class="essb_item essb_link_print nolightbox">
                        <a href="#" title="" onclick="essb.print('1616723342'); return false;" target="_blank" rel="nofollow">
                            <span class="essb_icon essb_icon_print"></span>
                            <span class="essb_network_name essb_noname"></span></a>
                    </li>
                </ul>
            </div>

        </div>
-->
    </div>
</div>