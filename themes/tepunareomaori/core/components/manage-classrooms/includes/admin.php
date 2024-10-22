<?php 

add_action('bp_groups_admin_meta_boxes', 'TPRM_group_admin_metabox' );
add_action('bp_group_admin_edit_after', 'TPRM_group_save_metabox_fields' );
add_action('bp_after_group_settings_creation_step', 'TPRM_group_render_settings_in_create_form' );
add_action('bp_after_group_settings_admin', 'TPRM_group_render_settings_in_edit_form' );
add_action('groups_group_settings_edited', 'TPRM_group_save_settings_in_edit_form' );
add_action('groups_create_group_step_save_group-settings', 'TPRM_group_save_settings_from_create_form' );

 /**
 *  Change Group Default Tab to resources for curriculum and to subgroups if Ecole
 *
 * @since V2
 */

 function TPRM_group_admin_metabox() {	
	add_meta_box( 
		'bp_school_info', // Meta box ID 
		__('School Infos', 'tprm-theme'), // Meta box title
		'TPRM_group_render_admin_metabox', // Meta box callback function
		get_current_screen()->id, // Screen on which the metabox is displayed. In our case, the value is toplevel_page_bp-groups
		'side', // Where the meta box is displayed
		'core', // Meta box priority
	);
}

/**
 *  TPRM_group Meta box callback function
 *
 * @since V2
 */

 function TPRM_group_render_admin_metabox() {
    $group_id = intval($_GET['gid']);
    //classroom
    $ecole_name = groups_get_groupmeta($group_id, 'ecole_name');
    $ecole_year = groups_get_groupmeta($group_id, 'ecole_year');
    $classroom_code = groups_get_groupmeta($group_id, 'classroom_code');
    $classroom_year = groups_get_groupmeta($group_id, 'classroom_year');
    $classroom_level = groups_get_groupmeta($group_id, 'classroom_level');

    //school
    $ecole_demo = groups_get_groupmeta($group_id, 'ecole_demo');
    $school_trigram = groups_get_groupmeta($group_id, 'school_trigram');
    $school_code = groups_get_groupmeta($group_id, 'school_code');
    $school_seats = groups_get_groupmeta($group_id, 'school_seats');
    $school_creation_year = groups_get_groupmeta($group_id, 'school_creation_year');

    $group_type = bp_groups_get_group_type($group_id);

    ?>
    <div class="bp-groups-settings-section" id="bp-groups-settings-section-content-protection">
        <fieldset>
        <?php if ($group_type !== 'kwf-ecole') { //classroom ?>
            <legend><?php _e('School Name', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="ecole_name" value="<?php echo esc_attr($ecole_name); ?>">
            </label>
            <legend><?php _e('School Year', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="ecole_year" value="<?php echo esc_attr($ecole_year); ?>">
            </label>
            <legend><?php _e('Classroom Level', 'tprm-theme'); ?></legend>
            <label>
                <input type="number" min="1" max="9" name="classroom_level" value="<?php echo esc_attr($classroom_level); ?>">
            </label>
            <legend><?php _e('Classroom Year', 'tprm-theme'); ?></legend>
            <label>
                <input type="number" min="1" max="5" name="classroom_year" value="<?php echo esc_attr($classroom_year); ?>">
            </label>
            <legend><?php _e('Classroom Code', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="classroom_code" value="<?php echo esc_attr($classroom_code); ?>">
            </label>
        <?php } else { //school ?>
            <legend><?php _e('School Code', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="school_code" value="<?php echo esc_attr($school_code); ?>">
            </label>
            <legend><?php _e('School Creation Year', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="school_creation_year" value="<?php echo esc_attr($school_creation_year); ?>">
            </label>
            <legend><?php _e('School Seats', 'tprm-theme'); ?></legend>
            <label>
                <input type="number" min="0" name="school_seats" value="<?php echo esc_attr($school_seats); ?>">
            </label>
            <legend><?php _e('School Trigram', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="school_trigram" value="<?php echo esc_attr($school_trigram); ?>">
            </label>
            <legend><?php _e('Demo ?', 'tprm-theme'); ?></legend>
            <label>
                <input type="checkbox" name="ecole_demo" <?php echo checked($ecole_demo, 'on', false); ?>>
            </label>
        <?php }; ?>
        </fieldset>
    </div>
    <?php
}



/**
 *  TPRM_group save metabox fields
 *
 * @since V2
 * @param int $group_id
 */

function TPRM_group_save_metabox_fields($group_id) {
	//classroom
    $ecole_name = isset($_POST['ecole_name']) ? sanitize_text_field($_POST['ecole_name']) : '';
    $ecole_year = isset($_POST['ecole_year']) ? sanitize_text_field($_POST['ecole_year']) : '';
    $classroom_code = isset($_POST['classroom_code']) ? sanitize_text_field($_POST['classroom_code']) : '';
    $classroom_year = isset($_POST['classroom_year']) ? intval($_POST['classroom_year']) : '';
    $classroom_level = isset($_POST['classroom_level']) ? intval($_POST['classroom_level']) : '';
	//school
    $school_trigram = isset($_POST['school_trigram']) ? sanitize_text_field($_POST['school_trigram']) : '';
    $school_code = isset($_POST['school_code']) ? sanitize_text_field($_POST['school_code']) : '';
    $school_seats = isset($_POST['school_seats']) ? intval($_POST['school_seats']) : '';
    $school_creation_year = isset($_POST['school_creation_year']) ? sanitize_text_field($_POST['school_creation_year']) : '';

    groups_update_groupmeta($group_id, 'ecole_name', $ecole_name);
    groups_update_groupmeta($group_id, 'ecole_year', $ecole_year);
    groups_update_groupmeta($group_id, 'classroom_code', $classroom_code);
    groups_update_groupmeta($group_id, 'classroom_level', $classroom_level);
    groups_update_groupmeta($group_id, 'classroom_year', $classroom_year);
    groups_update_groupmeta($group_id, 'school_trigram', $school_trigram);
    groups_update_groupmeta($group_id, 'school_code', $school_code);
    groups_update_groupmeta($group_id, 'school_seats', $school_seats);
    groups_update_groupmeta($group_id, 'school_creation_year', $school_creation_year);

    
    // Check if classroom_code is empty and set it
    if (empty($classroom_code) && !is_school($group_id)) {
        // Get parent group ID
        $parent_group_id = groups_get_group($group_id)->parent_id;

        // Get school_trigram from parent group
        $school_trigram = groups_get_groupmeta($parent_group_id, 'school_trigram');

        // Generate classroom_code
        $classroom_name = sanitize_title_with_dashes(groups_get_group($group_id)->name);
        $base_code = $school_trigram . $group_id . $classroom_name;
        $classroom_code = substr(md5($base_code), 0, 10);

        // Update group meta with the generated classroom_code
        groups_update_groupmeta($group_id, 'classroom_code', $classroom_code);
    }

    // Check if classroom_code is empty and set it
    if (empty($school_code) && is_school($group_id)) {

        // Get school_trigram from parent group
        $school_trigram = groups_get_groupmeta($group_id, 'school_trigram');

        // Generate classroom_code
        $school_name = sanitize_title_with_dashes(groups_get_group($group_id)->name);
        $base_code = $school_trigram . $group_id . $school_name;
        $school_code = substr(md5($base_code), 0, 10);

        // Update group meta with the generated school_code
        groups_update_groupmeta($group_id, 'school_code', $school_code);
    }

	if (isset($_POST['ecole_demo'])) {
		groups_update_groupmeta($group_id, 'ecole_demo', 'on');
    } else {
		groups_delete_groupmeta($group_id, 'ecole_demo');
    }
}



/**
 *  render settings in create form
 *
 * @since V2
 */

 function TPRM_group_render_settings_in_create_form() {
    ?>
    <div class="bp-groups-settings-section" id="bp-groups-settings-section-content-protection">
        <fieldset>
            <legend><?php _e('School Name', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="ecole_name" value="">
            </label>
            <legend><?php _e('School Year', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="ecole_year" value="">
            </label>
            <legend><?php _e('Classroom Code', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="classroom_code" value="">
            </label>
            <legend><?php _e('Classroom Level', 'tprm-theme'); ?></legend>
            <label>
                <input type="number" min="1" max="9" name="classroom_level" value="">
            </label>
            <legend><?php _e('Classroom Year', 'tprm-theme'); ?></legend>
            <label>
                <input type="number" min="1" max="5" name="classroom_year" value="">
            </label>
            <legend><?php _e('School Code', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="school_code" value="">
            </label>
            <legend><?php _e('School Creation', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="school_creation_year" value="">
            </label>
            <legend><?php _e('School Seats', 'tprm-theme'); ?></legend>
            <label>
                <input type="number" min="0" name="school_seats" value="">
            </label>
            <legend><?php _e('School Trigram', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="school_trigram" value="">
            </label>
            <legend><?php _e('Demo?', 'tprm-theme'); ?></legend>
            <label>
                <input type="checkbox" name="ecole_demo">
            </label>
        </fieldset>
    </div>
    <?php
}


/**
 *  render settings in edit form
 *
 * @since V2
 */

function TPRM_group_render_settings_in_edit_form() {
	$group_id = bp_get_current_group_id();
	//classroom
	$ecole_name = groups_get_groupmeta( $group_id, 'ecole_name' ) ;
	$ecole_year = groups_get_groupmeta( $group_id, 'ecole_year' ) ;
	$classroom_code = groups_get_groupmeta( $group_id, 'classroom_code' ) ;
	$classroom_year = groups_get_groupmeta( $group_id, 'classroom_year' ) ;
	$classroom_level = groups_get_groupmeta( $group_id, 'classroom_level' ) ;
	//school
	$ecole_demo = groups_get_groupmeta( $group_id, 'ecole_demo' ) ;
    $school_trigram = groups_get_groupmeta($group_id, 'school_trigram');
    $school_code = groups_get_groupmeta( $group_id, 'school_code' ) ;
    $school_seats = groups_get_groupmeta($group_id, 'school_seats');
    $school_creation_year = groups_get_groupmeta( $group_id, 'school_creation_year' ) ;
	$group_type = bp_groups_get_group_type($group_id);

	?>

	<div class="bp-groups-settings-section" id="bp-groups-settings-section-content-protection">
		<fieldset>
		<?php if($group_type !== 'kwf-ecole') { //classroom ?>
			<legend><?php _e('School Name', 'tprm-theme'); ?></legend>
			<label>
				<input type="text" name="ecole_name" value="<?php echo esc_attr($ecole_name); ?>">
			</label>
			<legend><?php _e('School Year', 'tprm-theme'); ?></legend>
			<label>
				<input type="text" name="ecole_year" value="<?php echo esc_attr($ecole_year); ?>">
			</label>
			<legend><?php _e('Classroom Code', 'tprm-theme'); ?></legend>
			<label>
				<input type="text" name="classroom_code" value="<?php echo esc_attr($classroom_code); ?>">
			</label>
			<legend><?php _e('Classroom Level', 'tprm-theme'); ?></legend>
			<label>
				<input type="number" min="1" max="9" name="classroom_level" value="<?php echo esc_attr($classroom_level); ?>">
			</label>

			<legend><?php _e('Classroom Year', 'tprm-theme'); ?></legend>
			<label>
				<input type="number" min="1" max="5" name="classroom_year" value="<?php echo esc_attr($classroom_year); ?>">
			</label>
		<?php }else{ //school ?>

            <legend><?php _e('School Seats', 'tprm-theme'); ?></legend>
            <label>
                <input type="number" min="0" name="school_seats" value="<?php echo esc_attr($school_seats); ?>">
            </label>

            <legend><?php _e('School Trigram', 'tprm-theme'); ?></legend>
            <label>
                <input type="text" name="school_trigram" value="<?php echo esc_attr($school_trigram); ?>">
            </label>

            <legend><?php _e('School Code', 'tprm-theme'); ?></legend>
			<label>
				<input type="text" name="school_code" value="<?php echo esc_attr($school_code); ?>">
			</label>

            <legend><?php _e('School Creation Year', 'tprm-theme'); ?></legend>
			<label>
				<input type="text" name="school_creation_year" value="<?php echo esc_attr($school_creation_year); ?>">
			</label>

			<legend><?php _e('Demo ?', 'tprm-theme'); ?></legend>
			<label>
				<input type="checkbox" name="ecole_demo" <?php echo checked($ecole_demo, 'on', false) ?> >
			</label>
			
		<?php }; ?>
		</fieldset>
	</div>

	<?php
}

/**  save settings in create form
*
* @since V2
*/

function TPRM_group_save_settings_in_edit_form($group_id) {
	//classroom
    $ecole_name = isset($_POST['ecole_name']) ? sanitize_text_field($_POST['ecole_name']) : '';
    $ecole_year = isset($_POST['ecole_year']) ? sanitize_text_field($_POST['ecole_year']) : '';
    $classroom_code = isset($_POST['classroom_code']) ? sanitize_text_field($_POST['classroom_code']) : '';
    $classroom_year = isset($_POST['classroom_year']) ? intval($_POST['classroom_year']) : '';
    $classroom_level = isset($_POST['classroom_level']) ? intval($_POST['classroom_level']) : '';
	//school
    $school_trigram = isset($_POST['school_trigram']) ? sanitize_text_field($_POST['school_trigram']) : '';
    $school_code = isset($_POST['school_code']) ? sanitize_text_field($_POST['school_code']) : '';
    $school_seats = isset($_POST['school_seats']) ? intval($_POST['school_seats']) : '';
    $school_creation_year = isset($_POST['school_creation_year']) ? sanitize_text_field($_POST['school_creation_year']) : '';

    groups_update_groupmeta($group_id, 'ecole_name', $ecole_name);
    groups_update_groupmeta($group_id, 'ecole_year', $ecole_year);
    groups_update_groupmeta($group_id, 'classroom_code', $classroom_code);
    groups_update_groupmeta($group_id, 'classroom_level', $classroom_level);
    groups_update_groupmeta($group_id, 'classroom_year', $classroom_year);
    groups_update_groupmeta($group_id, 'school_trigram', $school_trigram);
    groups_update_groupmeta($group_id, 'school_code', $school_code);
    groups_update_groupmeta($group_id, 'school_seats', $school_seats);
    groups_update_groupmeta($group_id, 'school_creation_year', $school_creation_year);

    if (empty($classroom_code) && !is_school($group_id)) {
        // Get parent group ID
        $parent_group_id = groups_get_group($group_id)->parent_id;

        // Get school_trigram from parent group
        $school_trigram = groups_get_groupmeta($parent_group_id, 'school_trigram');

        // Generate classroom_code
        $classroom_name = sanitize_title_with_dashes(groups_get_group($group_id)->name);
        $base_code = $school_trigram . $group_id . $classroom_name;
        $classroom_code = substr(md5($base_code), 0, 10);

        // Update group meta with the generated classroom_code
        groups_update_groupmeta($group_id, 'classroom_code', $classroom_code);
    }

    // Check if classroom_code is empty and set it
    if (empty($school_code) && is_school($group_id)) {

        // Get school_trigram from parent group
        $school_trigram = groups_get_groupmeta($group_id, 'school_trigram');

        // Generate classroom_code
        $school_name = sanitize_title_with_dashes(groups_get_group($group_id)->name);
        $base_code = $school_trigram . $group_id . $school_name;
        $school_code = substr(md5($base_code), 0, 10);

        // Update group meta with the generated school_code
        groups_update_groupmeta($group_id, 'school_code', $school_code);
    }

	if (isset($_POST['ecole_demo'])) {
		groups_update_groupmeta($group_id, 'ecole_demo', 'on');
    } else {
		groups_delete_groupmeta($group_id, 'ecole_demo');
    }
}


/**
 *  save settings in create form
 *
 * @since V2
 */

function TPRM_group_save_settings_from_create_form() {
	$group_id = bp_get_current_group_id();

	// bp_get_current_group_id() may return 0 at the first step
	if ( $group_id === 0 ) {
		$group_id = buddypress()->groups->new_group_id;
	}

	$ecole_demo =  $_POST['ecole_demo'] ;
	//classroom
    $ecole_name = isset($_POST['ecole_name']) ? sanitize_text_field($_POST['ecole_name']) : '';
    $ecole_year = isset($_POST['ecole_year']) ? sanitize_text_field($_POST['ecole_year']) : '';
    $classroom_code = isset($_POST['classroom_code']) ? sanitize_text_field($_POST['classroom_code']) : '';
    $classroom_year = isset($_POST['classroom_year']) ? intval($_POST['classroom_year']) : '';
    $classroom_level = isset($_POST['classroom_level']) ? intval($_POST['classroom_level']) : '';
	//school
    $school_trigram = isset($_POST['school_trigram']) ? sanitize_text_field($_POST['school_trigram']) : '';
    $school_code = isset($_POST['school_code']) ? sanitize_text_field($_POST['school_code']) : '';
    $school_seats = isset($_POST['school_seats']) ? intval($_POST['school_seats']) : '';
    $school_creation_year = isset($_POST['school_creation_year']) ? sanitize_text_field($_POST['school_creation_year']) : '';

	groups_update_groupmeta( $group_id, 'ecole_name', $ecole_name );
	groups_update_groupmeta( $group_id, 'ecole_year', $ecole_year );
	groups_update_groupmeta( $group_id, 'classroom_code', $classroom_code );
	groups_update_groupmeta( $group_id, 'ecole_demo', $ecole_demo );
	groups_update_groupmeta( $group_id, 'classroom_level', $classroom_level);
    groups_update_groupmeta( $group_id, 'classroom_year', $classroom_year);
	groups_update_groupmeta( $group_id, 'school_trigram', $school_trigram );
    groups_update_groupmeta( $group_id, 'school_code', $school_code );
    groups_update_groupmeta( $group_id, 'school_seats', $school_seats);
    groups_update_groupmeta( $group_id, 'school_creation_year', $school_creation_year );

}