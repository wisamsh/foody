<?php

/****** option page ******/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define the path to the options page.
 */
define('FOOTABC_OPTIONS_PAGE_PATH', '/wp-admin/options-general.php?page=footab_client_foody');

// ---------------------------------------------------------------------------
// Define the plugin options.
// ---------------------------------------------------------------------------

/**
 * The approach we are taking here is to have all of the options for the plugin
 * contained in one array. We'll store that array as a single WordPress
 * option. This simplifies a lot of things, and cuts down on the boilerplate.
 */

/**
 * We will need to define the default options array, since it won't be set the
 * first time it is needed.
 *
 * Here we'll add a couple of simple fields - an integer, and a list of strings
 * that will be stored as an indexed array.
 */
function footabc_default_options()
{
    return array(
        'footabc_url' => '',
    );
}

/**
 * Since we have a default options array, it is best to provide a helper
 * function to obtain the options for the plugin, in order to wrap the necessary
 * get_option() call.
 */
function footabc_get_options()
{
    return get_option('footabc_options', footabc_default_options());
}

/**
 * All options set in the WordPress administrative interface must be on the
 * whitelist. So we'll register our compound options array as
 * 'footabc_options'.
 *
 * The important thing here is to provide the sanitize_callback function, as
 * that function is where all of the error check and error message setting has
 * to happen.
 *
 * If you have unusual requirements, such as storing options in custom database
 * tables, then the sanitize_callback function is where that will have to happen
 * as well.
 */
function footabc_admin_init()
{
    register_setting(
        'footabc_options_group',
        'footabc_options',
        array(
            'type' => 'array',
            'default' => footabc_default_options(),
            'sanitize_callback' => 'footabc_sanitize_options'
        )
    );
}

add_action('admin_init', 'footabc_admin_init');

/**
 * The function for sanitizing options entered by the user.
 *
 * This actually has to do more than just that. It also must set error messages
 * and juggle whether or not to reject and replace specific values.
 *
 * It isn't an ideal situation, as some approaches to form UI are not possible
 * to implement via this interface. Whatever is returned from this method will
 * be set as the options value regardless of everything else, so about the best
 * that can be done is to adjust values or reject all changes. You can't, for
 * example, choose not to save the results, provide errors, and still show the
 * user-entered invalid values in the form when the page reloads.
 *
 * In the example here, we take the approach of discarding all entered data if
 * any of it is bad, and issuing appropriate error messages. When the page
 * reloads, the user will see the prior values.
 */
function footabc_sanitize_options($input)
{
    $output = footabc_get_options();
    $error = false;

    if (isset($input['footabc_reset']) && $input['footabc_reset']) {
        update_option('footabc_data', '');
    }

    // Only update the existing data in the absence of errors.
    if (!count(get_settings_errors('footabc_options'))) {
        $output['footabc_url'] = $input['footabc_url'];
        $output['footabc_reset'] = '';
    }

    return $output;
}

// ---------------------------------------------------------------------------
// Define the options page.
// ---------------------------------------------------------------------------

/**
 * This function emits the HTML for the options page.
 */
function footabc_options_page_html()
{
    // Don't proceed if the user lacks permissions.
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = footabc_get_options();

    // First show the error or update messages at the head of the page.
    settings_errors('footabc_messages');

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <?php
        /*var_dump($options);*/
        $footabc_url = isset($options['footabc_url']) ? $options['footabc_url'] : '';
        ?>
        <form action="options.php" method="post">
            <?php settings_fields('footabc_options_group'); ?>

            <table class="form-table">

                <tr>
                    <th scope="row">
                        <label for="footabc_options[footabc_url]">
                            <?php esc_html_e('Foody Taboola Url', 'footab_client_foody'); ?>:
                        </label>
                    </th>
                    <td>
                        <input
                                type="text"
                                id="footabc_options[footabc_url]"
                                name="footabc_options[footabc_url]"
                                value="<?php echo esc_attr($footabc_url); ?>"
                        />
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="footabc_options[footabc_reset]">
                            <?php esc_html_e('Re-fetch Option', 'footab_client_foody'); ?>:
                        </label>
                    </th>
                    <td>
                        <input
                                type="checkbox"
                                id="footabc_options[footabc_reset]"
                                name="footabc_options[footabc_reset]"
                        />
                    </td>
                </tr>

            </table>

            <?php submit_button(__('Save Settings', 'footab_client_foody')); ?>

        </form>
    </div>
    <?php
}

// ---------------------------------------------------------------------------
// Add the options page link to the admin menu.
// ---------------------------------------------------------------------------

/**
 * Add a link to the settings page into the settings submenu.
 */
function footabc_options_page()
{
    add_options_page(
        __('Foody Taboola Options', 'footab_client_foody'),
        __('Foody Taboola', 'footab_client_foody'),
        'manage_options',
        'footab_client_foody',
        'footabc_options_page_html'
    );
}

add_action('admin_menu', 'footabc_options_page');

// ---------------------------------------------------------------------------
// Add options page links to the plugin entry in the plugins list.
// ---------------------------------------------------------------------------

/**
 * Add a link to the options page to the plugin name block.
 */
function footabc_plugin_action_links($links, $file)
{
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . FOOTABC_OPTIONS_PAGE_PATH . '">' . __('Settings', 'footab_client_foody') . '</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}

add_filter('plugin_action_links', 'footabc_plugin_action_links', 10, 2);


/**
 * Add a link to the options page to the plugin description block.
 */
function footabc_register_plugin_links($links, $file)
{
    if ($file == plugin_basename(__FILE__)) {
        $links[] = '<a href="' . FOOTABC_OPTIONS_PAGE_PATH . '">' . __('Settings', 'footab_client_foody') . '</a>';
    }

    return $links;
}

add_filter('plugin_row_meta', 'footabc_register_plugin_links', 10, 2);
