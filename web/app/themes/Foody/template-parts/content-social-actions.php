<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 1:16 PM
 */

?>

<?php if (!wp_is_mobile()): ?>

    <div class=" social col p-0">
        <?php echo do_shortcode('[easy-social-share buttons="print,mail,pinterest,whatsapp" template="11" counters=0 style="icon" point_type="simple"]'); ?>
        <button class="btn btn-facebook">
            שתף
        </button>
    </div>

<?php else: ?>
    <div class=" social col p-0">
        <?php echo do_shortcode('[easy-social-share buttons="mail,pinterest,whatsapp" template="11" counters=0 style="icon" point_type="simple"]'); ?>
        <button class="btn btn-facebook">
            שתף
        </button>
    </div>


<?php endif; ?>
