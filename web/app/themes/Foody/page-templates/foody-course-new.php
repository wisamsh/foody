<?php
/**
 * Template Name: Foody Course new
 * Template Post Type: foody_course
 *
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 3/31/19
 * Time: 7:30 PM
 */
get_header();
$course = new Foody_Course_new();

$purchase_link = $course->get_floating_purchase_button();
$gift_link = $course->get_floating_gift_button();
if ((!empty($purchase_link) && isset($purchase_link['url']) && !empty($purchase_link['url'])) || (!empty($gift_link) && isset($gift_link['url']) && !empty($gift_link['url']))) {
    echo '<div class="sticky-registration">';
    if (isset($purchase_link['url'])) {
        foody_get_template_part(get_template_directory() . '/template-parts/common/link.php', array('link' => $purchase_link));
    }
    if (isset($gift_link['url']))
        foody_get_template_part(get_template_directory() . '/template-parts/common/link.php', array('link' => $gift_link));
    echo '</div>';
}

?>
    <div id="main-content" class="main-content">

        <div id="primary" class="content-area">
            <section class="course-cover">
                    <?php
                    $course->get_cover_section();
                    ?>
            </section>
            <section class="gift-and-purchase-buttons">
                <?php
                $course->get_gift_and_purchase_buttons();
                ?>
            </section>
        </div>
    </div>
    </div>

<?php
get_footer();


