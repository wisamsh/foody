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
        <button class="btn btn-facebook btn-facebook-share">
            <a target="popup"
               onclick="window.open(this.href,'popup','width=600,height=600'); return false;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()) ?>">
                <?php echo __('שתף', 'foody') ?>
            </a>

        </button>
    </div>

<?php else: ?>
    <div class="social col">
        <?php echo do_shortcode('[easy-social-share buttons="mail,pinterest,whatsapp" template="11" counters=0 style="icon" point_type="simple"]'); ?>
        <button class="btn btn-facebook  btn-facebook-share">
            <a target="popup"
                 onclick="window.open(this.href,'popup','width=600,height=600'); return false;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()) ?>">
                <?php echo __('שתף', 'foody') ?>
            </a>

        </button>
    </div>


<?php endif; ?>
