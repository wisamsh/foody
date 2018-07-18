<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/10/18
 * Time: 8:03 PM
 */
$modal = $template_args;

$btn_approve = foody_get_array_default($modal, 'btn_approve', __('שלח', 'foody'));
$btn_cancel = foody_get_array_default($modal, 'btn_cancel', __('ביטול', 'foody'));

$btn_approve_classes = foody_get_array_default($modal, 'btn_approve_classes', 'btn-approve');
$btn_cancel_classes = foody_get_array_default($modal, 'btn_cancel_classes', 'btn-cancel');

?>


<div class="modal fade" tabindex="-1" role="dialog" id="upload-image-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
                <form id="image-upload-form">
                    <div class="image-container">
                        <img id="image-preview" alt="">
                    </div>
                    <div class="comment-input-container">
                        <label for="comment">
                            <?php echo wp_get_current_user()->display_name ?>
                        </label>
                        <input max="80" class="comment" type="text" name="comment" id="comment"
                               placeholder="הקלד תיאור…">
                    </div>
                    <input type="file" id="file" name="attachment">
                    <button type="button" class="btn btn-secondary <?php echo $btn_cancel_classes ?>"
                            data-dismiss="modal">
                        <?php echo $btn_cancel ?>
                    </button>
                    <button type="submit" class="btn btn-primary <?php echo $btn_approve_classes ?>">
                        <?php echo $btn_approve ?>
                    </button>
                </form>
            </div>



            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
