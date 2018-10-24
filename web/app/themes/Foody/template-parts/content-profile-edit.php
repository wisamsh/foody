<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/23/18
 * Time: 2:39 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$form_classes = foody_get_array_default($template_args,'form_classes',[]);

?>



<h3 class="title">
    <?php echo __('עריכת פרופיל','foody');?>
</h3>


<form class="<?php foody_el_classes($form_classes) ?>" id="edit-user-details" action="">
    <h4>
        <?php echo __('עריכת פרטים אישיים','foody');?>
    </h4>

    <div class="form-group col-12 required-input">
        <label for="first-name">
            <?php echo __('שם פרטי', 'foody') ?>
        </label>
        <input type="text" id="first-name" name="first_name" required>
    </div>
    <div class="form-group col-12 required-input">
        <label for="last-name">
            <?php echo __('שם משפחה', 'foody') ?>
        </label>
        <input type="text" id="last-name" name="last_name" required>
    </div>

    <div class="form-group col-12">
        <label for="phone-number">
            <?php echo __('מספר טלפון', 'foody') ?>
            <span>
                             <?php echo __('לשליחת אסמסים מותאמים אישית', 'foody') ?>
                        </span>
        </label>
        <input type="tel" id="phone-number" name="phone_number">
    </div>

    <div class="form-group form-submit col-12 row justify-content-between gutter-0">

        <ul class="nolist nav nav-tabs col-lg-4 col-5" id="edit-tabs">
            <li>
                <a role="tab" data-toggle="tab"
                   href="#user-content" aria-controls="user-content">
                    <button type="button" class="btn btn-primary btn-cancel">
                        <?php echo __('ביטול') ?>
                    </button>
                </a>
            </li>
        </ul>


        <button type="submit" class="btn btn-primary col-lg-4 col-5">
            <?php echo __('שמור שינויים') ?>
        </button>
    </div>

</form>

