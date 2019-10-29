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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <?php if ($id == 'newsletter-modal'){ ?>
                <div class="popup-text-container">
                    <div class="popup-text">
                        <?php echo __('נרדמתם?');?>
                        <br/>
                        <?php echo __('יש לנו משהו שיעיר אתכם! הירשמו לקבלת המתכונים החמים'); ?>
                    </div>
                    <div class="popup-image">
                        <img src="<?php echo $GLOBALS['images_dir'] . '_MG_1957-2.png' ?>" alt="">
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
