<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/20/18
 * Time: 6:26 PM
 */

$title = $template_args['title'];
$content = $template_args['content'];
$id = $template_args['id'];


?>

<div id="accordion-<?php echo $id?>" role="tablist" class="foody-accordion">
    <div class="foody-accordion-content">
        <div class="" role="tab" id="heading-<?php echo $id?>">
            <h5 class="mb-0">
                <a data-toggle="collapse" href="#<?php echo $id?>" aria-expanded="true"
                   aria-controls="<?php echo $id?>">
                    <?php echo $title ?>
                </a>
            </h5>
        </div>

        <div id="<?php echo $id?>" class="collapse show" role="tabpanel"
             aria-labelledby="heading-<?php echo $id?>" data-parent="#accordion-<?php echo $id?>">
            <div class="card-body">
                <?php echo $content ?>
            </div>
        </div>
    </div>
</div>