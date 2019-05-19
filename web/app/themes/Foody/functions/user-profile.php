<?php
/**
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 2019-05-19
 * Time: 12:08
 */


function extra_profile_fields( $user ) { ?>

    <h3>השלמת פרטי קמפיין</h3>
    <table class="form-table">
        <tr>
            <th><label for="street">כתובת</label></th>
            <td>
                <input type="text" name="street" id="street"
                       value="<?php echo esc_attr( get_the_author_meta( 'street', $user->ID ) ); ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="street-number">מספר בית</label></th>
            <td>
                <input type="text" name="street-number" id="street-number"
                       value="<?php echo esc_attr( get_the_author_meta( 'street_number', $user->ID ) ); ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="city">עיר</label></th>
            <td>
                <input type="text" name="city" id="city"
                       value="<?php echo esc_attr( get_the_author_meta( 'city', $user->ID ) ); ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="birthday">תאריך לידה</label></th>
            <td>
                <input type="date" name="birthday" id="birthday"
                       value="<?php echo esc_attr( get_the_author_meta( 'birthday', $user->ID ) ); ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="gender">מין</label></th>
            <td>
                <input <?php echo get_the_author_meta( 'gender', $user->ID ) == 'male' ? 'checked' : ''; ?>
                        type="radio" name="gender" id="gender" value="male" class="regular-text"/>זכר&nbsp;&nbsp;&nbsp;
                <input <?php echo get_the_author_meta( 'gender', $user->ID ) == 'female' ? 'checked' : ''; ?>
                        type="radio" name="gender" id="gender" value="female" class="regular-text"/>נקבה&nbsp;&nbsp;&nbsp;
                <input <?php echo get_the_author_meta( 'gender', $user->ID ) == 'other' ? 'checked' : ''; ?>
                        type="radio" name="gender" id="gender" value="other" class="regular-text"/>אחר
            </td>
        </tr>
    </table>
	<?php

}

// Then we hook the function to "show_user_profile" and "edit_user_profile"
add_action( 'show_user_profile', 'extra_profile_fields', 10 );
add_action( 'edit_user_profile', 'extra_profile_fields', 10 );


function save_extra_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	/* Edit the following lines according to your set fields */
	update_user_meta( $user_id, 'street', $_POST['street'] );
	update_user_meta( $user_id, 'street_number', $_POST['street-number'] );
	update_user_meta( $user_id, 'city', $_POST['city'] );
	update_user_meta( $user_id, 'birthday', $_POST['birthday'] );
	update_user_meta( $user_id, 'gender', $_POST['gender'] );
}

add_action( 'personal_options_update', 'save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_profile_fields' );