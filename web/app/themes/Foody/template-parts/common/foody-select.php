<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/2/18
 * Time: 6:51 PM
 */

?>
<?php if ( isset( $template_args ) ): ?>
	<?php
	$name        = $template_args['name'] ?? '';
	$options     = $template_args['options'];
	$placeholder = $template_args['placeholder'] ?? 'select';
	$id          = $template_args['id'];


	$data       = foody_get_array_default( $template_args, 'data', [] );
	$data_attrs = '';
	foreach ( $data as $key => $value ) {
		$data_attrs .= " data-$key='$value'";
	}


	?>
    <?php
    $count_options = count($options);
    if ('foody_recipe' === get_post_type() && !is_search() && $count_options > 1 ) {?>
        <label class="choose-pan-title" for="number-of-dishes"> בחרו סוג תבנית </label>
        <select <?php echo $data_attrs ?> class="foody-pan-select foody-select foody-sort col-" title="<?php echo $name ?>"
        name="<?php echo $name ?>"
        id="<?php echo $id ?>">
            <option disabled selected>בחר/י תבנית</option>

            <?php if ( ! empty( $placeholder ) ) : ?>
                <option value="">
                    <?php echo $placeholder ?>
                </option>

            <?php endif; ?>

            <?php foreach ( $options as $option ): ?>

                <?php
                $data = '';
                if ( ! empty( $option['data'] ) ) {
                    $data = foody_array_to_data_attr( $option['data'] );
                }
                ?>

                <option <?php echo $data ?>
                        value="<?php echo $option['value'] ?>" >
                    <?php echo $option['label'] ?>
                </option>

            <?php endforeach; ?>

        </select>
   <?php } else { ?>
    <select <?php echo $data_attrs ?> class="foody-select foody-sort col-" title="<?php echo $name ?>"
                                      name="<?php echo $name ?>"
                                      id="<?php echo $id ?>">

        <?php if ( ! empty( $placeholder ) ) : ?>
            <option value="">
                <?php echo $placeholder ?>
            </option>

        <?php endif; ?>
        <?php foreach ( $options as $option ): ?>

            <?php
            $data = '';
            if ( ! empty( $option['data'] ) ) {
                $data = foody_array_to_data_attr( $option['data'] );
            }
            ?>

            <option <?php echo $data ?>
                    value="<?php echo $option['value'] ?>" >
                <?php echo $option['label'] ?>
            </option>

        <?php endforeach; ?>

    </select>
   <?php } ?>



<?php endif; ?>