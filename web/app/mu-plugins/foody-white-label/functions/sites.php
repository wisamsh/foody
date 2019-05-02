<?php
/**
 * Created by PhpStorm.
 * User: omerfishman
 * Date: 2019-04-24
 * Time: 14:14
 */


if (is_admin() && is_main_site()) {
    require_once PLUGIN_DIR . 'Tax-meta-class/Tax-meta-class.php';

    $config = array(
        'id' => 'site_data_meta_box',
        'title' => __('העתקת מידע לאתרים', 'foody'),
        'pages' => array('category', 'post_tag'),
        'context' => 'normal',
        'fields' => array(),
        'local_images' => false,
        'use_with_theme' => false
    );
    $my_meta = new Tax_Meta_Class($config);
    $args = [];//[ 'site__not_in' => [ get_main_site_id() ] ];
    $sites = get_sites($args);

    foreach ($sites as $site) {
        $my_meta->addCheckbox('pass_data_' . $site->id, array('name' => __('העתק ל-' . $site->blogname, 'foody')));
        $my_meta->addCheckbox('index_data_' . $site->id, array('name' => __('index ל-' . $site->blogname, 'foody')));
    }

    $my_meta->Finish();
}

//$saved_data = get_tax_meta( $term->id, 'pass_data_' . $site->id );

// User
add_action('show_user_profile', 'foody_profile_edit_action');
add_action('edit_user_profile', 'foody_profile_edit_action');
function foody_profile_edit_action($user)
{

    if (is_admin() && in_array('author', (array)$user->roles)) {
        $args = ['site__not_in' => [get_main_site_id()]];
        $sites = get_sites($args);

        ?>
        <h3>העתקת מתכונים</h3>
        <?php
        foreach ($sites as $site) {

            $checked = (isset($user->{'pass_data_' . $site->id}) && $user->{'pass_data_' . $site->id}) ? ' checked="checked"' : '';
            $index_checked = (isset($user->{'index_data_' . $site->id}) && $user->{'index_data_' . $site->id}) ? ' checked="checked"' : '';
            ?>
            <label for="pass_data_<?php echo $site->id ?>">
                <input name="pass_data_<?php echo $site->id ?>" type="checkbox" id="pass_data_<?php echo $site->id ?>"
                       value="1"<?php echo $checked; ?>>
                העתק ל-<?php echo $site->blogname ?>
            </label>
            <br/>
            <label for="index_data_<?php echo $site->id ?>">
                <input name="index_data_<?php echo $site->id ?>" type="checkbox" id="index_data_<?php echo $site->id ?>"
                       value="1"<?php echo $index_checked; ?>>
                index ל-<?php echo $site->blogname ?>
            </label>
            <br/>
            <br/>
            <?php

        }
    }
}

add_action('personal_options_update', 'foody_profile_update_action');
add_action('edit_user_profile_update', 'foody_profile_update_action');

function foody_profile_update_action($user_id)
{
    $user = get_user_by('ID', $user_id);
    if (is_admin() && in_array('author', (array)$user->roles)) {

        $args = ['site__not_in' => [get_main_site_id()]];
        $sites = get_sites($args);

        foreach ($sites as $site) {
            update_user_meta($user_id, 'pass_data_' . $site->id, isset($_POST['pass_data_' . $site->id]));
            update_user_meta($user_id, 'index_data_' . $site->id, isset($_POST['index_data_' . $site->id]));
        }
    }
}

add_action('wpmu_new_blog', 'foody_set_permalink_structure', 10);

function foody_set_permalink_structure($blog_id)
{
    switch_to_blog($blog_id);
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    $wp_rewrite->flush_rules();
    restore_current_blog();
}

function foody_is_registration_open() {
	if ( is_multisite() ) {
		return get_option( 'nsur_join_site_enabled' );
	} else {
		return get_option( 'users_can_register' );
	}
}