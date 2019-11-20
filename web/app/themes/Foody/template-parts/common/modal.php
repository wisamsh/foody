<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/7/18
 * Time: 2:41 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$modal = $template_args;

$btn_approve = foody_get_array_default($modal, 'btn_approve', __('אישור', 'foody'));
$btn_cancel = foody_get_array_default($modal, 'btn_cancel', __('ביטול', 'foody'));

$dialog_classes = foody_get_array_default($modal, 'dialog_classes', '');
$btn_approve_classes = foody_get_array_default($modal, 'btn_approve_classes', 'btn-approve');
$btn_cancel_classes = foody_get_array_default($modal, 'btn_cancel_classes', 'btn-cancel');


$id = $modal['id'];

$hide_buttons = isset($modal['hide_buttons']) && $modal['hide_buttons'];
$close_id = isset($modal['close_id']) ? $modal['close_id'] : "";

?>


<div class="modal fade" tabindex="-1" role="dialog" id="<?php echo $id ?>">
    <div class="modal-dialog <?php if (!empty($dialog_classes)) {
        echo $dialog_classes;
    } ?>" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php echo $modal['title'] ?>
                </h5>
                <button <?php if($close_id != ""){ ?> id="<?php echo $close_id;?>" <?php } ?> type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <?php if ($id == 'newsletter-modal'){ ?>
                <div class="popup-text-container">
                    <div class="popup-text">
                        <span class="first-line">
                        <?php echo __('רוצים לקבל מתכונים קלים ומהירים לארוחת הצהריים או הערב? האורחים בדלת ואין לכם רעיון לעוגה? בשביל זה אנחנו כאן!'); ?>
                            </span>
                        <br/>
                        <span class="second-line">
                        <?php echo __('הירשמו לקבלת כל המתכונים שיקצרו מלא מחמאות'); ?>
                        </span>
                    </div>
                    <div class="popup-image">
                        <img src="<?php echo $GLOBALS['images_dir'] . 'shnitzel.svg' ?>" alt="">
                    </div>
                </div>
                <?php } ?>
                <?php echo $modal['body']; ?>
                </p>
            </div>
            <div class="modal-footer">
                <?php if (!$hide_buttons): ?>
                    <button type="button" class="btn btn-secondary <?php echo $btn_cancel_classes ?>"
                            data-dismiss="modal" aria-label="<?php echo $btn_cancel ?>">
                        <?php echo $btn_cancel ?>
                    </button>
                    <button type="button" class="btn btn-primary <?php echo $btn_approve_classes ?>"
                            aria-label="<?php echo $btn_approve ?>">
                        <?php echo $btn_approve ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
