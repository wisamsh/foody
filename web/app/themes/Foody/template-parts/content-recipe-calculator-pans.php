<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/21/18
 * Time: 10:07 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var WP_Term $pan */
$pan         = $template_args['pan'];
$conversions = $template_args['conversions'];
$slices      = $template_args['slices'];

$options = array_map( function ( $conversion ) {
	if ( ! empty( $conversion ) ) {
		if ( empty( $conversion['pan'] ) ) {
			return null;
		}

		$pan = get_term( $conversion['pan'], 'pans' );

		if ( ! empty( $pan ) && ! is_wp_error( $pan ) ) {
			return [
				'value' => $conversion['conversion_rate'],
				'label' => $pan->name,
				'data'  => [
					'slices' => get_field( 'slices', $conversion['pan'] )
				]
			];
		}

	}

}, $conversions );

$options = array_filter( $options, function ( $conv ) {
	return ! empty( $conv );
} );

array_unshift( $options, [
	'value'    => 1,
	'label'    => $pan->name,
	'selected' => true,
	'data'     => [
		'original' => true,
		'slices'   => $slices
	]
] );

$select_args = [
	'id'          => 'pan-conversions',
	'placeholder' => '',
	'options'     => $options,
	'data'        => [
		'original' => true
	]
];
if ( empty( $conversions)){
	?>
	<div class="filter-option" > <?php echo $pan->name; ?> </div>
<?php
}
else {
	foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $select_args);
}

?>

