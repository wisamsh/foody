<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/8/19
 * Time: 10:49 AM
 */
function foody_modify_users_table_columns( $column ) {
	$column['marketing'] = 'אישור דיוור';

	return $column;
}

add_filter( 'manage_users_columns', 'foody_modify_users_table_columns' );

function foody_modify_users_table_columns_values( $val, $column_name, $user_id ) {
	switch ( $column_name ) {
		case 'marketing' :
			return get_user_meta( $user_id, 'marketing', true );
			break;
		default:
	}

	return $val;
}

add_filter( 'manage_users_custom_column', 'foody_modify_users_table_columns_values', 10, 3 );


