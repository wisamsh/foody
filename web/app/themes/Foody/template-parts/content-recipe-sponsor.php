<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/2/18
 * Time: 11:53 AM
 */

$sponsor = $template_args['sponsor'];
$sponsor_text = $template_args['sponsor_text'];

?>


<div class="recipe-sponsors">

        <span class="sponsor">

<!--            <a href="--><?php //echo get_term_link($sponsor->term_id) ?><!--">-->
                <?php echo $sponsor_text ?>
                <?php echo $sponsor ?>
<!--            </a>-->
        </span>

</div>
