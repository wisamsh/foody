<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/2/18
 * Time: 11:53 AM
 */

$sponsor_name = $template_args['sponsor_name'];
$sponsor_text = $template_args['sponsor_text'];
$sponsor_link = $template_args['sponsor_link'];

?>


<div class="recipe-sponsor">

        <span class="sponsor">

            <?php if ( ! empty( $sponsor_link ) ): ?>
                <a href="<?php echo $sponsor_link['url'] ?>" target="<?php echo $sponsor_link['target'] ?>"
                   title="<?php echo $sponsor_link['title'] ?>">
                    <?php echo $sponsor_text ?>
                    <?php echo $sponsor_name ?>
               </a>
            <?php else: ?>
                <?php echo $sponsor_text ?>
                <?php echo $sponsor_name ?>
            <?php endif; ?>
        </span>

</div>
