<?php
/**
 * @package dbSwitch
 */

/*
Plugin Name: dbSwitch
Plugin URI: https://github.com/aayush518/dbSwitchPlugin
Description: This plugin is used to switch the color of the website from dark to light and vice versa.
Version: 1.0.0
Author: Aayush Adhikari
Author URI: https://github.com/Aayush518
License: GPLv2 or later
Text Domain: dbSwitchPlugin
*/

// Register the settings.
function sdms_register_settings() {
    register_setting('sdms_options_group', 'sdms_default_dark_mode', 'intval');
    register_setting('sdms_options_group', 'sdms_enable_timezone_mode', 'intval');
    register_setting('sdms_options_group', 'sdms_custom_css', 'wp_kses_post');
    register_setting('sdms-settings-group', 'sdms_light_mode_logo');
    register_setting('sdms-settings-group', 'sdms_dark_mode_logo');
    register_setting('sdms-settings-group', 'sdms_logo_width');
    register_setting('sdms-settings-group', 'sdms_logo_height');
}
add_action('admin_init', 'sdms_register_settings');

// Admin options page.
function sdms_create_menu() {
    add_options_page('dbSwitch settings', 'Simple Dark Mode', 'manage_options', 'sdms-settings', 'sdms_options_page');
}
add_action('admin_menu', 'sdms_create_menu');

function sdms_options_page() {
    ?>
    <div class="wrap">
        <h1>Simple Dark Mode Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('sdms_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Default Dark Mode:</th>
                    <td>
                        <input type="checkbox" name="sdms_default_dark_mode" value="1" <?php checked(1, get_option('sdms_default_dark_mode'), true); ?> />
                        <label for="sdms_default_dark_mode">Enable by default</label>
                    </td>
                </tr>
                <!-- Add other form fields here -->
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// AJAX handler to get the default mode and timezone.
function get_sdms_default() {
    $default_dark_mode = get_option('sdms_default_dark_mode', '0');
    $timezone_mode = get_option('sdms_enable_timezone_mode', '0');
    $site_time_offset = get_option('gmt_offset');

    $response = array(
        'default' => $default_dark_mode,
        'timezoneMode' => $timezone_mode,
        'siteTimeOffset' => $site_time_offset
    );

    wp_send_json($response);
}
add_action('wp_ajax_get_sdms_default', 'get_sdms_default');
add_action('wp_ajax_nopriv_get_sdms_default', 'get_sdms_default');

// Enqueue scripts and styles.
function sdms_enqueue_custom_scripts_and_styles() {
    // Check if default dark mode is enabled
    $default_dark_mode = get_option('sdms_default_dark_mode', '0');

    // Check if dark mode should be applied by default
    if ($default_dark_mode == '1') {
        // If dark mode is set to default, apply the dark mode class on the body
        add_filter('body_class', function ($classes) {
            $classes[] = 'dark-mode';
            return $classes;
        });
    }

    // Enqueue styles
    wp_enqueue_style('sdms-dark-mode', plugin_dir_url(__FILE__) . 'assets/styles.css');

    // Enqueue scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('sdms-dark-mode-toggle', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), '1.0', true);

    // Pass ajax_url to script.js and the default dark mode value
    wp_localize_script('sdms-dark-mode-toggle', 'frontendajax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'default_dark_mode' => $default_dark_mode,
    ));
}
add_action('wp_enqueue_scripts', 'sdms_enqueue_custom_scripts_and_styles');

// Shortcode function.
function sdms_dark_mode_toggle_shortcode() {
    return '<button class="dark-mode-toggle" data-enabled-text="Disable Dark Mode" data-disabled-text="Enable Dark Mode">Enable Dark Mode</button>';
}
add_shortcode('dark_mode_toggle', 'sdms_dark_mode_toggle_shortcode');

// Direct call for theme developers.
function sdms_dark_mode_toggle() {
    echo sdms_dark_mode_toggle_shortcode();
}
