<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/2/19
 * Time: 5:41 PM
 */

$approved_marketing = get_user_meta(get_current_user_id(), 'marketing', true);
$registration_page = get_page_by_title('הרשמה');
$show = get_field('show', $registration_page);

?>
<form id="approvals" method="post">
    <?php if (Foody_User::user_has_meta('marketing')): ?>
        <div class="md-checkbox col-12">
            <input id="check-marketing" type="checkbox" checked name="marketing">
            <label for="check-marketing">
                <?php echo __('הריני לאשר בזה קבלת דואר מאתר Foody הכולל מתכונים ומידע מהאתר, וכן דואר שיווקי גם של מפרסמים הקשורים עם האתר') ?>
            </label>
        </div>
    <?php endif; ?>
    <?php

    if ($show):
        ?>
        <div class="md-checkbox col-12">
            <input id="check-e-book" type="checkbox" checked name="e-book">
            <label for="check-e-book">
                <?php
                $text = get_field('text', $registration_page);
                if (empty($text)) {
                    $text = __('ברצוני לקבל את ספר המתכונים לפסח');
                }
                echo $text;
                ?>
            </label>
        </div>
    <?php endif; ?>

    <input class="btn btn-primary" type="submit" value="<?php echo __('הירשם') ?>">

</form>