<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/26/19
 * Time: 4:21 PM
 */

function foody_api_user_role()
{
    $role_name = 'foody_api_user';
    add_role(
        $role_name,
        'Foody API User',
        ['read']
    );

    $role = get_role($role_name);
    $role->add_cap('access_foody_api', true);
}

add_action('init', 'foody_api_user_role');

function foody_custom_user_profile_fields($user)
{
    ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="type_of_writer">סוג כותב</label>
            </th>
            <td>
                <select name="type_of_writer" id="type_of_writer">
                    <option value="team">נבחרת</option>
                    <option value="israel-cooks">בשלני ישראל</option>
                </select>
            </td>
        </tr>
    </table>
    <!--    <table class="form-table">-->
    <!--        <tr>-->
    <!--            <th><label for="company">Company Name</label></th>-->
    <!--            <td>-->
    <!--                <input type="text" class="regular-text" name="company" value="--><?php //echo esc_attr( get_the_author_meta( 'company', $user->ID ) );
    ?><!--" id="company" /><br />-->
    <!--                <span class="description">Where are you?</span>-->
    <!--            </td>-->
    <!--        </tr>-->
    <!--    </table>-->
    <?php
}

add_action('user_new_form', 'foody_custom_user_profile_fields');

function foody_save_custom_user_profile_fields($user_id)
{
    # again do this only if you can
    if (!current_user_can('manage_options'))
        return false;

    # save my custom field
    update_user_meta($user_id, 'type_of_writer', $_POST['type_of_writer']);
}

add_action('user_register', 'foody_save_custom_user_profile_fields');
add_action('profile_update', 'foody_save_custom_user_profile_fields');