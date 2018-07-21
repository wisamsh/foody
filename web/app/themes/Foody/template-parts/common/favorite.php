<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/21/18
 * Time: 3:36 PM
 */

/** @noinspection PhpUndefinedVariableInspection */
$post_id = foody_get_array_default($template_args, 'id', 0);

?>

<div class="favorite" data-id="<?php echo $post_id ?>">
    <i class="icon-heart">

    </i>
    <span>
                    הוספה למועדפים
                </span>
</div>
