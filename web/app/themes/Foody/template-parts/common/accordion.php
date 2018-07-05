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
$title_classes = isset($template_args['title_classes']) ? $template_args['title_classes'] : '';
$title_icon = isset($template_args['title_icon']) ? $template_args['title_icon'] : '';
?>

<div id="accordion-<?php echo $id ?>" role="tablist" class="foody-accordion">
    <div class="foody-accordion-content">
        <div class="" role="tab" id="heading-<?php echo $id ?>">
            <h5 class="mb-0">
                <?php if ($title_icon != ''): ?>
                    <i class="<?php echo $title_icon ?>"></i>
                <?php endif; ?>
                <a class="<?php echo $title_classes ?>" data-toggle="collapse" href="#<?php echo $id ?>"
                   aria-expanded="true"
                   aria-controls="<?php echo $id ?>">
                    <?php echo $title ?>
                </a>

                <i class="icon-side-arrow arrow" data-toggle="collapse" aria-controls="<?php echo $id ?>"></i>

            </h5>
        </div>

        <div id="<?php echo $id ?>" class="collapse show" role="tabpanel"
             aria-labelledby="heading-<?php echo $id ?>" data-parent="#accordion-<?php echo $id ?>">
            <div class="card-body">
                <?php echo $content ?>
            </div>
        </div>
    </div>
</div>