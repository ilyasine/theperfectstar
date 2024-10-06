<?php

/**
 * @link              https://wooninjas.com/
 * @since             1.0.0
 * @package           Learndash_Course_Import_Export
 *
 * @wordpress-plugin
 * Plugin Name:       LearnDash Course Import Export
 * Plugin URI:        https://wooninjas.com/downloads/learndash-course-import-export/
 * Description:       Import/Export LearnDash Courses, Lessons and Topics in Excel Spreadsheet
 * Version:           1.4.2
 * Author:            WooNinjas
 * Author URI:        https://wooninjas.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       learndash-course-import-export
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Set plugin FILE to access it globally
 */
define( 'LEARNDASH_COURSE_IMPORT_EXPORT_FILE', __FILE__ );
define( 'LEARNDASH_COURSE_IMPORT_EXPORT_URL', plugin_dir_url( __FILE__ )) ;
define( 'LEARNDASH_COURSE_IMPORT_EXPORT_PLUGIN_NAME', 'LearnDash Course Import Export' );

/**
 * Currently plugin version.
 */
define( 'LEARNDASH_COURSE_IMPORT_EXPORT_VERSION', '1.4.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-learndash-course-import-export-activator.php
 */
function activate_learndash_course_import_export() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-learndash-course-import-export-activator.php';
    Learndash_Course_Import_Export_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-learndash-course-import-export-deactivator.php
 */
function deactivate_learndash_course_import_export() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-learndash-course-import-export-deactivator.php';
    Learndash_Course_Import_Export_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_learndash_course_import_export' );
register_deactivation_hook( __FILE__, 'deactivate_learndash_course_import_export' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-learndash-course-import-export.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_learndash_course_import_export() {
    $plugin = new Learndash_Course_Import_Export();
    $plugin->run();
}

/**
 * Display a notice if LearnDash plugin is missing.
 */
function learndash_course_import_export_missing_plugin_notice() {
    unset( $_GET['activate'] );
    $plugin_data = get_plugin_data( __FILE__ );

    $class   = 'notice notice-error is-dismissible';
    $message = sprintf(
        __( '%s requires <a href="https://www.learndash.com">LearnDash</a> plugin to be activated.', 'learndash-course-import-export' ),
        $plugin_data['Name']
    );

    printf( "<div id='message' class='%s'><p>%s</p></div>", $class, $message );
}

/**
 * Callback function for the 'plugins_loaded' action hook.
 * Checks if LearnDash is active and loads the plugin accordingly.
 */
function learndash_course_import_export_plugins_loaded_cb() {
    if ( current_user_can( 'activate_plugins' ) && ! class_exists( 'SFWD_LMS' ) ) {
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            include_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        deactivate_plugins( plugin_basename( __FILE__ ), true );
        add_action( 'admin_notices', 'learndash_course_import_export_missing_plugin_notice' );
    } else {
        run_learndash_course_import_export();
    }
}
add_action( 'plugins_loaded', 'learndash_course_import_export_plugins_loaded_cb' );
