<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/2/18
 * Time: 6:51 PM
 */

?>
<?php if (isset($template_args)): ?>
    <?php
    $name = $template_args['name'] ?? '';
    $options = $template_args['options'];
    $placeholder = $template_args['placeholder'] ?? 'select';
    $id = $template_args['id'];
    ?>
    <select class="foody-select" title="<?php echo $name ?>" name="<?php echo $name ?>" id="<?php echo $id ?>">
        <option value="">
            <?php echo $placeholder ?>
        </option>
        <?php foreach ($options as $option): ?>

            <option value="<?php echo $option['value'] ?>">
                <?php echo $option['label'] ?>
            </option>

        <?php endforeach; ?>

    </select>


<?php endif; ?>