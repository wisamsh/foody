<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/7/18
 * Time: 2:41 PM
 */

$modal = $template_args;

$btn_approve = foody_get_array_default($modal, 'btn_approve', __('אישור', 'foody'));
$btn_cancel = foody_get_array_default($modal, 'btn_cancel', __('ביטול', 'foody'));

$btn_approve_classes = foody_get_array_default($modal, 'btn_approve_classes', 'btn-approve');
$btn_cancel_classes = foody_get_array_default($modal, 'btn_cancel_classes', 'btn-cancel');

?>


<div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
    <div class="modal-dialog" role="document">
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
                    <?php echo $modal['body'] ?>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary <?php echo $btn_cancel_classes ?>" data-dismiss="modal">
                    <?php echo $btn_cancel ?>
                </button>
                <button type="button" class="btn btn-primary <?php echo $btn_approve_classes ?>">
                    <?php echo $btn_approve ?>
                </button>
            </div>
        </div>
    </div>
</div>
