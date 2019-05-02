<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/29/19
 * Time: 12:37 PM
 */

add_action('admin_head', 'foody_block_admin_if_need', 1);

function foody_block_admin_if_need()
{
    $duplication_in_progress = get_option('foody_site_duplication_in_progress');

    if ($duplication_in_progress) {
        ?>
        <style>
            .overlay {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100vw;
                height: 100vh;
                background-color: white;
                z-index: 999999999999;
                cursor: progress;
                text-align: center;
                font-size: 28px;
                line-height: 100vh;
            }
        </style>
        <script>
            jQuery(document).ready(function ($) {
                jQuery('body').append('<div id="foody-blocker" onclick="return false;" class="overlay">\n' +
                    '            <section class="message">\n' +
                    '                    מעתיק תוכן, אנא המתן.\n' +
                    '            </section>\n' +
                    '        </div>');

                var maxTries = 300, currentTry = 0;
                var startTime = new Date().getTime();

                function checkProgressStatus() {
                    currentTry++;
                    $.ajax(ajaxurl + '?action=foody_duplication_progress').success(function (data) {

                        // data.data.in_progress = Date.now() > (startTime + (1000 * 5) );
                        if (data.data.in_progress && currentTry < maxTries) {
                            setTimeout(function () {
                                checkProgressStatus();
                            }, 1000);
                        } else {
                            $('#foody-blocker').remove();
                        }
                    });
                }

                checkProgressStatus();
            });


        </script>

        <?php
    }
}

add_action('wp_ajax_foody_duplication_progress', 'foody_duplication_progress');
function foody_duplication_progress()
{
    $duplication_in_progress = get_option('foody_site_duplication_in_progress');

    wp_send_json_success(['in_progress' => $duplication_in_progress]);
}