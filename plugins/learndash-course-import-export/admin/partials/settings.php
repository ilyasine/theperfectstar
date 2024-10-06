<?php

/**
 * Plugin Settings
 */

defined( "ABSPATH" ) || exit;

$ldcie_settings    = get_option( '__ldcie_plugin_global_settings', array() );

// Minimum role required to import courses.
$required_capability = isset( $ldcie_settings['required_capability'] ) ? $ldcie_settings['required_capability'] : 'manage_options';

// Publish courses on import.
$publish_course = isset( $ldcie_settings['publish_course'] ) ? $ldcie_settings['publish_course'] : '';

// Add existing lessons and topics
$update_duplicate = isset( $ldcie_settings['update_duplicate'] ) ? $ldcie_settings['update_duplicate'] : '1';

// Enable logs and systems information
$ldcie_wp_log = isset( $ldcie_settings['ldcie_wp_log'] ) ? $ldcie_settings['ldcie_wp_log'] : '';

?>
<div class="wrap ldcie-setting wn_wrap ldcie-sttg-panel">
    <form method="post">
        <table class="form-table">
        <!-- ldcie nonce field -->
        <?php wp_nonce_field( "learndash_course_import_export_nonce", "learndash_course_import_export_nonce" ); ?>

            <!-- ldcie user roles allowed -->
            <tr>
                <th scope="row" class="cltb-pd qst-mrk-tbl-ldcie">
                    <label for="ldcie_required_capability"><?php echo sprintf(__( 'Minimum role to import %s', 'learndash-course-import-export' ), learndash_get_custom_label_lower('courses')); ?>
                    <i class="fas fa-question"></i>
                    </label>
                    <p class="description">
                        <?php echo sprintf( __('Select the minimum user role which is allowed to import %s.','learndash-course-import-export'), learndash_get_custom_label_lower('courses') ); ?>
                    </p>
                </th>
                <td class="cltb-pd">
                    <select id="ldcie_required_capability" name="required_capability">
                        <option value="manage_options" <?php selected( $required_capability, 'manage_options' ); ?>>
                            <?php esc_html_e( 'Administrator' , 'learndash-course-import-export'); ?>
                        </option>
                        <option value="delete_others_posts" <?php selected( $required_capability, 'delete_others_posts' ); ?>>
                            <?php esc_html_e( 'Editor', 'learndash-course-import-export' ); ?>
                        </option>
                        <option value="publish_posts" <?php selected( $required_capability, 'publish_posts' ); ?>>
                            <?php esc_html_e( 'Author', 'learndash-course-import-export' ); ?>
                        </option>
                    </select>

                    
                </td>
            </tr>

            <!-- ldcie publish course on import -->
            <tr>
                <th scope="row" class="qst-mrk-tbl-ldcie">
                    <label for="ldcie_required_capability"><?php echo sprintf(__( 'Publish %s on import', 'learndash-course-import-export' ), learndash_get_custom_label_lower('courses')); ?>
                   <i class="fas fa-question"></i>
                    </label>
 
                    <p class="description">
                        <?php echo sprintf( __(' %s will be publish on importing otherwise the status will be pending.','learndash-course-import-export'), ucfirst( learndash_get_custom_label_lower('courses') ) ); ?>
                    </p>
                </th>
                <td class="ldscie-flex-row">
                    <label for="ldcie_publish_course">
                        <input name="publish_course" type="checkbox" value="1" <?php checked(true, $publish_course ); ?> >
                    </label>
                    
                </td>
            </tr>

            <!-- ldcie add existing lesson or topic on import -->
            <tr>
                <th scope="row" class="qst-mrk-tbl-ldcie">
                    <label for="ldcie_required_capability">
                        <?php echo __( 'Update duplicate lessons or topics on import', 'learndash-course-import-export' ); ?>
                   <i class="fas fa-question"></i>
                    </label>
 
                    <p class="description">
                        <?php echo __( 'If enabled duplicate lessons or topics will be updated on import.','learndash-course-import-export'); ?>
                    </p>
                </th>
                <td class="ldscie-flex-row">
                    <label for="ldcie_update_duplicate">
                        <input name="update_duplicate" type="checkbox" value="1" <?php checked(true, $update_duplicate ); ?> >
                    </label>
                    
                </td>
            </tr>

            <!-- ldcie enable logs and systems information -->
            <tr>
                <th scope="row" class="qst-mrk-tbl-ldcie">
                    <label for="ldcie_wp_log">
                        <?php echo __( 'Enable Debug Logs', 'learndash-course-import-export' ); ?>
                   <i class="fas fa-question"></i>
                    </label>
 
                    <p class="description">
                        <?php echo __( 'Enable this option to generate import debug logs and send to our support team.','learndash-course-import-export'); ?>
                    </p>
                </th>
                <td class="ldscie-flex-row">
                    <label for="ldcie_wp_log">
                        <input name="ldcie_wp_log" type="checkbox" value="1" <?php checked( true, $ldcie_wp_log ); ?> >
                    </label>
                    

                </td>
            </tr>
        </table>
        <div class="submit">
            <input type="submit" name="ldcie_save_settings_options" class="button-primary" value="<?php esc_attr_e( 'Update Settings', 'learndash-course-import-export' ); ?>">
        </div>
    </form>
</div>
