<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/21/18
 * Time: 10:07 AM
 */

/** @noinspection PhpUndefinedVariableInspection */
/** @var WP_Term $pan */
$pan = $template_args['pan'];
$conversions = $template_args['conversions'];


$options = array_map(function ($conversion) {

    // TODO remove after checking with the Krut
    if(empty($conversion['pan'])){
        $conversion['pan'] = $conversion['תבנית'];
    }

    return [
        'value' => $conversion['conversion_rate'],
        'label' => get_term($conversion['pan'])->name
    ];

}, $conversions);

array_unshift($options, [
    'value' => 1,
    'label' => $pan->name,
    'selected' => true,
    'data' => [
        'original' => true
    ]
]);

$select_args = [
    'id' => 'pan-conversions',
    'placeholder' => '',
    'options' => $options,
    'data' => [
        'original' => true
    ]
];

foody_get_template_part(get_template_directory() . '/template-parts/common/foody-select.php', $select_args)


?>

