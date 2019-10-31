<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 16/9/19
 * Time: 2:41 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$modal = $template_args;

$id = $modal['page_id'];
$desktop_image = $modal['desktop_img'];
$mobile_image = $modal['mobile_img'];
$banner_text = isset($modal['banner_text']) ? $modal['banner_text'] : null;
$banner_link = $modal['banner_link'];
$button_text = $modal['button_text'];


$modal_body_class = isset($modal['banner_text']) ? "with-text" : "without-text";
?>


<div class="modal " tabindex="-1" role="dialog" id="<?php echo $id ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body <?php echo $modal_body_class; ?>">
                <?php if ($banner_text != null) { ?>
                <div class="modal-body-container">
                    <div class="banner-text-box">
                        <p class="banner-text"> <?php echo $banner_text; ?> </p>
                        <a class="banner-button"
                           href=" <?php echo $banner_link; ?>" target="_blank"><?php echo $button_text; ?></a>
                    </div>
                    <?php if (wp_is_mobile()) { ?>
                    <div class="banner-image-container"
                         style="background-image: url(<?php echo $mobile_image; ?>)">
                        <div type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                                <path fill="#FFF" fill-rule="nonzero"
                                      d="M0 1.124L1.124 0l4.869 4.869L10.876 0 12 1.124 7.116 5.993 12 10.876 10.876 12 5.993 7.116 1.123 12 0 10.876l4.869-4.883z"/>
                            </svg>

                        </div>
                    </div>
                </div>
                <?php } else {
                ?>
                <div class="banner-image-container"
                     style="background-image: url(<?php echo $desktop_image; ?>)">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                            <path fill="#FFF" fill-rule="nonzero"
                                  d="M0 1.124L1.124 0l4.869 4.869L10.876 0 12 1.124 7.116 5.993 12 10.876 10.876 12 5.993 7.116 1.123 12 0 10.876l4.869-4.883z"/>
                        </svg>

                    </button>
                </div>
            </div>
            <?php } ?>
            <?php } else {
                ?>
                <?php if (wp_is_mobile()) { ?>
                    <div class="banner-image-container"
                         style="background-image: url(<?php echo $mobile_image; ?>)">
                        <a class="banner-button"
                           href=" <?php echo $banner_link; ?>" target="_blank"><?php echo $button_text; ?></a>
                        <div type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                                <path fill="#FFF" fill-rule="nonzero"
                                      d="M0 1.124L1.124 0l4.869 4.869L10.876 0 12 1.124 7.116 5.993 12 10.876 10.876 12 5.993 7.116 1.123 12 0 10.876l4.869-4.883z"/>
                            </svg>

                        </div>
                    </div>
                <?php } else {
                    ?>
                    <div class="banner-image-container"
                         style="background-image: url(<?php echo $desktop_image; ?>)">
                        <a class="banner-button"
                           href=" <?php echo $banner_link; ?>" target="_blank"><?php echo $button_text; ?></a>
                        <div type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                                <path fill="#FFF" fill-rule="nonzero"
                                      d="M0 1.124L1.124 0l4.869 4.869L10.876 0 12 1.124 7.116 5.993 12 10.876 10.876 12 5.993 7.116 1.123 12 0 10.876l4.869-4.883z"/>
                            </svg>

                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
</div>