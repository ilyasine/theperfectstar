<?php 

add_action('bp_actions', 'change_school_tabs_name' );
add_filter('bp_nouveau_get_nav_count', 'classrooms_count_int', 99, 3);
//add_filter('bp_nouveau_get_loop_classes','enable_grid_view_for_subgroups');

/*
* ************** School  **************
 */


/**
 * @since V2
 *
 * @global function get school details for a registred user

 * @param int|Null $user_id User ID if provided or use the current user's ID otherwise
 * @return array of school details for the given user
 */

 function get_school_details($user_id = null) {

	global $wpdb;
	$bp = buddypress();
	$limit = false;
	$page = false;
	$pag_sql = '';
	$school_details = array();

    // If $user_id is not provided, use the current user's ID.
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }
	 
	if ( !empty( $limit ) && !empty( $page ) )
		$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
	
	// show hidden and private groups if the user is not logged.
	if ( $user_id != bp_loggedin_user_id() ) {
		$group_sql = $wpdb->prepare( "SELECT DISTINCT m.group_id FROM {$bp->groups->table_name_members} m, {$bp->groups->table_name} g WHERE m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0{$pag_sql}", $user_id );
		$total_groups = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.group_id) FROM {$bp->groups->table_name_members} m, {$bp->groups->table_name} g WHERE g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id ) );
	} 
	/* 
	* If the user is logged in and viewing their own groups, we can show hidden and private groups.
	* this will handle the case where no user_id is provided (  current user instead )
	*/
	else {
		$group_sql = $wpdb->prepare( "SELECT DISTINCT group_id FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0{$pag_sql}", $user_id );
		$total_groups = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT group_id) FROM {$bp->groups->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0", $user_id ) );
	}
	
	$groups = $wpdb->get_col( $group_sql );

    foreach ($groups as $group) {
        // Get class details (child group)
        $classe = groups_get_group($group);
        $classe_name = $classe->name;

        // Get school details (parent group)
        $school_id = $classe->parent_id;
        $school = groups_get_group($school_id);
        $school_name = $school->name;
        $school_year = groups_get_groupmeta($school_id, 'ecole_year');

        // Add school details to the array
        $school_details[] = array(
            'school_name' => $school_name,
            'school_year' => $school_year,
            'classe_name' => $classe_name,
        );
    }

    return $school_details;

}

 /**
 * Deprecated
 * //Enable grid view for classes inside school group
 *
 * @since V2
 */

function enable_grid_view_for_subgroups($classes){

	if( function_exists('bp_is_group_subgroups') && bp_is_group_subgroups()){
		$classes[] = 'grid';
	}

	return $classes;
}


function getCurriculum() {
    return [
        ['',  '',  '',  '',  '',  ''],
        ['', 100, 100, 100, 100, 100],
        ['', 100, 200, 200, 200, 200],
        ['', 100, 320, 300, 300, 300],
        ['', 410, 320, 430, 400, 400],
        ['', 410, 520, 430, 500, 500],
        ['', 410, 520, 630, 640, 600],
        ['', 710, 720, 730, 700, 700],
        ['', 710, 820, 830, 840, 800],
        ['', 710, 820, 930, 940, 900]
    ];
}


function get_school_creation_year( $group_id ) {

    $group = groups_get_group($group_id);

    if( $group->parent_id != 0 ) return;

    $timestamp = strtotime( $group->date_created );

    return date('Y', $timestamp);
}


function school_implementation_year($school_id) {
    $implementation_year = 0;

    // Get the current school year from the global option
    $this_year_global = get_option('school_year'); // Assuming this returns the current school year in "YYYY-YYYY" format
    $this_year_start = (int)substr($this_year_global, 0, 4); // Start year of the current school year

    // Get the school creation year from BuddyPress group meta
    $school_creation_year_global = groups_get_groupmeta($school_id, 'school_creation_year'); // Assuming this returns the school creation year in "YYYY-YYYY" format
    $school_creation_year_start = (int)substr($school_creation_year_global, 0, 4); // Start year of the school creation year

    // Calculate the school implementation year
    if ($this_year_start >= $school_creation_year_start && ($this_year_start - $school_creation_year_start < 6)) {
        $implementation_year = $this_year_start - $school_creation_year_start + 1;
    }

    // Ensure the implementation year does not exceed 5
    if ($implementation_year > 5) {
        $implementation_year = 5;
    }

    return $implementation_year;
}


function get_user_school(){
    $user_groups = array();
    $user_school = array();
    if ( is_user_logged_in() ) {
        $groups = groups_get_user_groups( bp_loggedin_user_id() );
        if ( ! empty( $groups['groups'] ) ) {
            $user_groups = $groups['groups'];
        } else {
            $user_groups = array();
        }
    }
    foreach ($user_groups as $user_group) {

        $group = groups_get_group($user_group);

        if( $group->parent_id == 0 ){
            $user_school[] = $user_group;
        } 
    }

    return $user_school;
}


function get_last_user_school() {
    $user_school = array();
    if ( is_user_logged_in() ) {
        $groups = groups_get_user_groups( bp_loggedin_user_id() );
        if ( ! empty( $groups['groups'] ) ) {
            $user_groups = $groups['groups'];
        } else {
            $user_groups = array();
        }
    
        foreach ($user_groups as $user_group) {
            $group = groups_get_group($user_group);
            if( $group->parent_id == 0 ){
                $group_type = bp_groups_get_group_type($user_group);
                if ($group_type === 'tprm-school') {
                    $user_school[] = $user_group;
                }
            } 
        }

        if (!empty($user_school)) {
            usort($user_school, function($a, $b) {
                $group_a = groups_get_group($a);
                $group_b = groups_get_group($b);
                return strtotime($group_b->date_created) - strtotime($group_a->date_created);
            });

            return $user_school[0];
        }
    }

    return null;
}


function get_user_school_name($user_school){
    
    $user_school = bp_get_group_name(groups_get_group( $user_school ));

    return $user_school;
}

add_filter('bp_get_search_default_text', 'change_search_placeholder');

function change_search_placeholder(){
    $default_text = __( 'Search &hellip;', 'tprm-theme' );
    if( ( bp_current_component() == 'groups' && (function_exists('bp_is_group_subgroups') && bp_is_group_subgroups()) || (function_exists('bp_is_groups_directory') && bp_is_groups_directory()) ) ){
        $default_text = __( 'Search Classroom&hellip;', 'tprm-theme');      
    }   

    return $default_text;
}

function get_previous_year() {

    $current_year = get_option( 'school_year' );
    // Split the input string by the hyphen
    list($start_year, $end_year) = explode('-', $current_year);

    // Convert the start and end years to integers
    $start_year = (int)$start_year;
    $end_year = (int)$end_year;

    // Subtract one from each year
    $previous_start_year = $start_year - 1;
    $previous_end_year = $end_year - 1;

    // Construct the previous school year string
    $previous_year = $previous_start_year . '-' . $previous_end_year;

    // Return the previous school year string
    return $previous_year;
}

function get_previous_year_from_date($year_string) {
    // Define the expected format using a regular expression
    $pattern = '/^\d{4}-\d{4}$/';

    // Check if the input matches the expected format
    if (!preg_match($pattern, $year_string)) {
        return new WP_Error('invalid_year', __('Error: Not a valid year format. Expected YYYY-YYYY.', 'tprm-theme'));
    }
    

    // Split the input string by the hyphen
    list($start_year, $end_year) = explode('-', $year_string);

    // Convert the start and end years to integers
    $start_year = (int)$start_year;
    $end_year = (int)$end_year;

    // Subtract one from each year
    $previous_start_year = $start_year - 1;
    $previous_end_year = $end_year - 1;

    // Construct the previous school year string
    $previous_year = $previous_start_year . '-' . $previous_end_year;

    // Return the previous school year string
    return $previous_year;
}



function get_school_teachers_from_classrooms_and_move_them_to_school($school_id) {
    $teachers = array();
    $classrooms = bp_get_descendent_groups($school_id, bp_loggedin_user_id());
    $teacher_ids = array(); // To track unique teacher IDs

    foreach ($classrooms as $classroom) {
        $classroom_id = $classroom->id;
        $admins = groups_get_group_admins($classroom_id);

        foreach ($admins as $admin) {
            $teacher_id = $admin->user_id;

            // Check if the user has the 'school_staff' role
            if (user_has_role($teacher_id, 'school_staff') && !in_array($teacher_id, $teacher_ids)) {

                 // Enroll the user in the school group as moderator
                $enroll_school = groups_join_group($school_id, $teacher_id);
                if ($enroll_school) {
                    groups_promote_member($teacher_id, $school_id, 'mod');
                }

            }
        }
    }

    //return $teachers;
}

function get_school_students_from_classrooms_and_move_them_to_school($school_id) {
    $students = array();
    $classrooms = bp_get_descendent_groups($school_id, bp_loggedin_user_id());
    $student_ids = array(); // To track unique student IDs

    foreach ($classrooms as $classroom) {
        $classroom_id = $classroom->id;

        // Fetch group members using the 'group_id' parameter
        $members = groups_get_group_members(array(
            'group_id' => $classroom_id,
            'exclude_admins_mods' => false, // Include admins and mods if needed
            'exclude_banned' => true, // Exclude banned members
            'exclude' => false, // Include all members
        ));

        if (!empty($members['members'])) {
            foreach ($members['members'] as $member) {
                $user_id = $member->ID;

                // Check if the user has the 'school_student' role
                if ((user_has_role($user_id, 'school_student') || user_has_role($user_id, 'tprm-student')) && !in_array($user_id, $student_ids)) {
                    $student_ids[] = $user_id; // Track the student ID to avoid duplicates

                    // Enroll the student in the parent group
                    groups_join_group($school_id, $user_id);
                }
            }
        }
    }
}


function get_school_teachers($school_id){

    if (!is_school($school_id)) {
        return new WP_Error('invalid_school', __('Invalid School', 'tprm-theme'));
    }
    // Get all members of the group
    $args = array(
        'group_id' => $school_id,
        'exclude_admins_mods' => false,
        'role' => 'mod', // moderators of school are teachers
    );

    $group_members = groups_get_group_members($args);

    if (empty($group_members['members'])) {
        return array();
    }

    $teachers = array();

    // Loop through members and check if they have the 'school_staff' role
    foreach ($group_members['members'] as $member) {
        $user = new WP_User($member->ID);

        if (in_array('school_staff', (array) $user->roles)) {
            $teachers[] = $member->ID;
        }
    }

    return $teachers;
}


function get_school_students($school_id){

    if (!is_school($school_id)) {
        return new WP_Error('invalid_school', __('Invalid School', 'tprm-theme'));
    }
    // Get all members of the group
    $args = array(
        'group_id' => $school_id,
        'exclude_admins_mods' => true,
        'role' => 'member',
    );

    $group_members = groups_get_group_members($args);

    if (empty($group_members['members'])) {
        return array();
    }

    $students = array();

    // Loop through members and check if they have the 'school_student' role
    foreach ($group_members['members'] as $member) {
        $user = new WP_User($member->ID);

        if (in_array('school_student', (array) $user->roles) || in_array('tprm-student', (array) $user->roles)) {
            $students[] = $member->ID;
        }
    }

    return $students;
}

function get_students_without_classroom_for_year($school_id, $year) {
    // Step 1: Get all students for the school and year
    $all_students = get_school_students($school_id, $year);

    // Step 2: Retrieve all classrooms for the school and year
    $classrooms = get_school_classrooms_for_year($school_id, $year);
    
    // Initialize an array to hold the IDs of students who are assigned to classrooms
    $students_with_classroom = array();

    // Step 3: Get the list of students for each classroom
    foreach ($classrooms as $classroom_id) {
        $classroom_students = get_classroom_students($classroom_id);
        // Merge the list of classroom students into the array
        $students_with_classroom = array_merge($students_with_classroom, $classroom_students);
    }

    // Step 4: Remove duplicates from the list of students with classrooms
    $students_with_classroom = array_unique($students_with_classroom);

    // Step 5: Filter out students who are assigned to any classroom
    $students_without_classroom = array_diff($all_students, $students_with_classroom);

    // Return the list of students who are not assigned to any classroom
    return $students_without_classroom;
}


function get_students_classroom_for_year($school_id, $year) {
    // Step 1: Retrieve all classrooms for the school and year
    $classrooms = get_school_classrooms_for_year($school_id, $year);
    
    // Initialize an array to hold the IDs of students assigned to classrooms
    $students_in_classrooms = array();

    // Step 2: Get the list of students for each classroom
    foreach ($classrooms as $classroom_id) {
        $classroom_students = get_classroom_students($classroom_id);
        // Merge the list of classroom students into the array
        $students_in_classrooms = array_merge($students_in_classrooms, $classroom_students);
    }

    // Step 3: Remove duplicates from the list of students
    $students_in_classrooms = array_unique($students_in_classrooms);

    // Return the list of students assigned to any classroom
    return $students_in_classrooms;
}


function is_school($school_id){
    $group_type = bp_groups_get_group_type($school_id);
    $group = groups_get_group($school_id);
    if ($group_type == 'tprm-school' && $group->parent_id == 0) {
        return true;
    }

    return false;
}


function get_classroom_teachers($classroom_id) {
    $teachers = array();
    $admins = groups_get_group_admins($classroom_id);

    foreach ($admins as $admin) {
        $user_id = $admin->user_id;
        // Check if the user has the 'school_staff' role
        if (user_has_role($user_id, 'school_staff') && !in_array($user_id, $teachers)) {
            $teachers[] = $user_id; // Track the teacher ID to avoid duplicates
        }
    }

    return $teachers;
}


function get_school_directors($school_id) {
    $directors = array();   
    $admins = groups_get_group_admins($school_id);

    foreach ($admins as $admin) {
        $user_id = $admin->user_id;
        // Check if the user has the 'school_principal' role
        if (user_has_role($user_id, 'school_principal') && !in_array($user_id, $directors)) {
            $directors[] = $user_id; // Track the director ID to avoid duplicates
        }
    }  

    return $directors;
}


function get_school_leaders($school_id) {
    $school_leaders = array();   
    $admins = groups_get_group_admins($school_id);

    foreach ($admins as $admin) {
        $user_id = $admin->user_id;
        // Check if the user has the 'school_leader' role
        if (user_has_role($user_id, 'school_leader') && !in_array($user_id, $school_leaders)) {
            $school_leaders[] = $user_id; // Track the school_leader ID to avoid duplicates
        }
    }  

    return $school_leaders;
}


// Helper function to check user role
function user_has_role($user_id, $role) {
    $user = get_userdata($user_id);
    if (!empty($user) && in_array($role, $user->roles)) {
        return true;
    }
    return false;
}


function get_school_trigram($school_name) {
    $school_trigrams = get_option('school_trigrams', []);
    
    // Check if the school name exists in the monograms array
    if (isset($school_trigrams[$school_name])) {
        return $school_trigrams[$school_name];
    } else {
        return false; // Return false if the school name doesn't have a monogram
    }
}

// Remove grid from classes
function remove_grid_from_classes_classes( $classes, $component ) {
    // Remove 'grid' class if it exists in the classes array
    if ( ( $key = array_search( 'grid', $classes ) ) !== false ) {
        unset( $classes[$key] );
    }
    return $classes;
}
add_filter( 'bp_nouveau_get_loop_classes', 'remove_grid_from_classes_classes', 10, 2 );

/**
 * Get the Classrooms of a given school for a given year as a numeric array of IDs
 *
 * @since V3
 * @param int $parent_id
 * @param int|null $the_year
 * @return array
 */
function get_school_classrooms_for_year($parent_id, $the_year = null) {
    global $wpdb;
    
    // If the year is not provided, get the current school year from the options
    if (is_null($the_year)) {
        $the_year = get_option('school_year');
    }

    // Prepare the query to fetch group IDs
    $group_ids_query = $wpdb->prepare("
        SELECT g.id 
        FROM {$wpdb->prefix}bp_groups g
        JOIN {$wpdb->prefix}bp_groups_groupmeta gm1 ON g.id = gm1.group_id
        WHERE gm1.meta_key = 'ecole_year' 
        AND gm1.meta_value = %s
        AND g.parent_id = %d
    ", $the_year, $parent_id);
    
    // Get the group IDs
    $group_ids = $wpdb->get_col($group_ids_query);
    
    return $group_ids;
}


function school_header() {
    if ( !is_user_logged_in() ) {
        return;
    }

    // Define the current user ID and roles
    $user_id = get_current_user_id();
    $is_student = is_student(); // Function to check if the user is a student
    $is_library = is_library(); // Function to check if the user is in the library role
    $is_libraries_manager = is_libraries_manager(); // Function to check if the user is a libraries manager
    $update_last_user_school_nonce = wp_create_nonce("update_last_user_school_nonce");

    $user = wp_get_current_user();
    $user_first_name = $user->user_firstname;
    $user_last_name = $user->user_lastname;
    $user_id = $user->ID;

    $groups = groups_get_user_groups($user_id);
    if (empty($groups['groups']) && (!$is_library && !$is_libraries_manager)) {
        return;
    }

    $TPRM_ecole_groups = [];

    foreach ($groups['groups'] as $group_id) {
        $group_types = bp_groups_get_group_type($group_id, false); // Fetch all types as an array
        if (is_array($group_types) && in_array('tprm-school', $group_types)) {
            $group = groups_get_group(array('group_id' => $group_id));
            if (!is_wp_error($group)) {
                $TPRM_ecole_groups[] = $group;
            }
        }
    }

    if (empty($TPRM_ecole_groups) && (!$is_library && !$is_libraries_manager)) {
        return;
    }

    if ($is_library || $is_libraries_manager) {
        $first_name = get_user_meta($user_id, 'first_name', true);
        $display_name = function_exists('bp_core_get_user_displayname') ? bp_core_get_user_displayname($user_id) : $user->display_name;
        // Display different headers for library and libraries manager
        ?>
        <div class="tprm-header">
            <div class="welcome-message">
                <p>
                    <?php _e('Tēnā koe', 'tprm-theme'); ?>, 
                    <div>
                        <span class="user-name"><?php echo esc_html($first_name); ?></span>
                        <?php if (function_exists('bp_is_active') && function_exists('bp_activity_get_user_mentionname')) : ?>
                            <span class="user-mention"><?php echo '@' . esc_html(bp_activity_get_user_mentionname($user_id)); ?></span>
                        <?php else : ?>
                            <span class="user-mention"><?php echo '@' . esc_html($user->user_login); ?></span>
                        <?php endif; ?>
                    </div>
                </p>
            </div>
            <?php if ($is_library) : ?>
                <div class="library-header school-info">
                    <!-- <a href="<?php echo esc_url(library_main_page); ?>">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/images/library-avatar.svg'); ?>" alt="Library Page" />
                    </a> -->
                    <a href="<?php echo esc_url(library_main_page); ?>">Library Dashboard</a>
                </div>
            <?php elseif ($is_libraries_manager) : ?>
                <div class="libraries-manager-header school-info">
                   <!--  <a href="<?php echo esc_url(libraries_manager_main_page); ?>">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/images/libraries-manager-avatar.svg'); ?>" alt="Libraries Manager Page" />
                    </a> -->
                    <a href="<?php echo esc_url(libraries_manager_main_page); ?>">Libraries Manager Dashboard</a>
                </div>
            <?php endif; ?>

            <div class="user-info">
                <!-- Add any additional elements here -->
                <?php get_template_part('template-parts/user-info', 'none'); ?>
                <a href="<?php echo esc_attr(wp_logout_url()) ?>"
                   data-bp-tooltip-pos="down"
                   data-bp-tooltip="<?php esc_attr_e('Log Out', 'tprm-theme') ?>">
                    <i class="_mi _before bb-icon-l buddyboss bb-icon-power-on" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <?php
    } else {
        // Default content for other roles
        $display_group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : $TPRM_ecole_groups[0]->id;
        $display_group = null;
        foreach ($TPRM_ecole_groups as $group) {
            if ($group->id == $display_group_id) {
                $display_group = $group;
                break;
            }
        }

        if (!$display_group) {
            $display_group = $TPRM_ecole_groups[0];
        }

        $first_name = get_user_meta($user_id, 'first_name', true);
        $display_name = function_exists('bp_core_get_user_displayname') ? bp_core_get_user_displayname($user_id) : $user->display_name;
        $display_group_name = $display_group->name;
        $display_group_url = bp_get_group_permalink($display_group);
        $display_group_image = bp_core_fetch_avatar(array('item_id' => $display_group->id, 'object' => 'group', 'type' => 'full', 'html' => false));
        ?>
        <div class="tprm-header">
            <div class="welcome-message">
                <p>
                    <?php _e('Tēnā koe', 'tprm-theme'); ?> 
                    <div>
                        <span class="user-name"><?php echo esc_html($first_name); ?></span>
                    </div>
                </p>
            </div>

            <div class="school-info">
                <?php if ($is_student) : ?>
                    <div class="school-display">
                        <a href="<?php echo esc_url(student_main_page); ?>">
                            <img src="<?php echo esc_url($display_group_image); ?>" alt="<?php echo esc_attr($display_group_name); ?>" />
                        </a>
                        <a href="<?php echo esc_url(student_main_page); ?>"><?php echo esc_html($display_group_name); ?></a>
                    </div>
                <?php else : ?>
                    <?php if (count($TPRM_ecole_groups) == 1) : ?>
                        <div class="school-display">
                            <a href="<?php echo esc_url($display_group_url); ?>">
                                <img src="<?php echo esc_url($display_group_image); ?>" alt="<?php echo esc_attr($display_group_name); ?>" />
                            </a>
                            <a href="<?php echo esc_url($display_group_url); ?>"><?php echo esc_html($display_group_name); ?></a>
                        </div>
                    <?php elseif (count($TPRM_ecole_groups) > 1) : ?>
                        <?php
                        $selected_school_label = get_user_meta($user_id, 'selected_school_label', true);
                        $selected_school_avatar = get_user_meta($user_id, 'selected_school_avatar', true);
                        $selected_school_url = get_user_meta($user_id, 'selected_school_url', true);
                        $has_selected_school = !empty($selected_school_label) && !empty($selected_school_avatar) && !empty($selected_school_url);

                        if (!$has_selected_school) {
                            $selected_school_label = $display_group_name;
                            $selected_school_avatar = $display_group_image;
                            $selected_school_url = $display_group_url;
                        }
                        ?>
                        <div id="selected-school-info">
                            <a href="<?php echo esc_url($selected_school_url); ?>">
                                <img id="selected-school-avatar" src="<?php echo esc_url($selected_school_avatar); ?>" alt="<?php echo esc_attr($selected_school_label); ?>" class="selected_school_avatar" />
                            </a>
                        </div>
                        <div class="school-dropdown">
                            <select id="school-select" data-security="<?php echo esc_attr($update_last_user_school_nonce); ?>">
                                <?php foreach ($TPRM_ecole_groups as $group) :
                                    $group_url = bp_get_group_permalink($group);
                                    $group_avatar = bp_core_fetch_avatar(array('item_id' => $group->id, 'object' => 'group', 'type' => 'thumb', 'html' => false));
                                    $selected = ($group_url === $selected_school_url) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo esc_url($group_url); ?>" data-avatar="<?php echo esc_url($group_avatar); ?>" <?php echo $selected; ?>>
                                        <?php echo esc_html($group->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="user-info">
                <!-- Add any additional elements here -->
                <?php get_template_part('template-parts/user-info', 'none'); ?>
                <a href="<?php echo esc_attr(wp_logout_url()); ?>"
                   data-bp-tooltip-pos="down"
                   data-bp-tooltip="<?php esc_attr_e('Log Out', 'tprm-theme'); ?>">
                    <i class="_mi _before bb-icon-l buddyboss bb-icon-power-on" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <?php
    }
}

add_action(THEME_HOOK_PREFIX . 'header', 'school_header');


add_filter('bp_groups_default_extension', 'TPRM_set_group_default_tab');
 /**
 *  Change Group Default Tab to courses for classroom and to subgroups if Ecole
 *
 * @since 3.0.0
 */

function TPRM_set_group_default_tab(){

	$group = groups_get_current_group();
	$group_type = bp_groups_get_group_type( $group->id );
	$user_id = bp_loggedin_user_id();
		if( is_tprm_leader() ){
			if ( $group_type !== 'tprm-school') {
				return 'content';
			}elseif( $group_type == 'tprm-school' && bp_get_descendent_groups( $group->id, $user_id )) {
				return 'subgroups';
			}
		}
	return 'students';
  
}

 /**
 * Change Subgroups and members name for school group
 *
 * @since V2
 */

 function change_school_tabs_name() {
 
    if ( ! bp_is_group() ) {
        return;
    }

	if(is_school(bp_get_current_group_id())){
		buddypress()->groups->nav->edit_nav( 
            array( 
                'name' => __('Groups', 'tprm-theme'),
                'position' => 0, 
            ),
                'subgroups', bp_current_item()
                 
        );
	}
    
}

function classrooms_count_int( $count, $nav_item, $displayed_nav ) {
    // Check if the nav item is 'subgroups'
    if ( $nav_item->slug == 'subgroups' ) {

        if(is_school(bp_get_current_group_id())){
            $current_year_classrooms = get_school_classrooms_for_year(bp_get_current_group_id());
            $total_classrooms = count($current_year_classrooms);

            // Set the count to the number of classrooms
            $count = $total_classrooms;
        }
    }

    return $count;
}

function get_school_seats($school_id){
    $school_seats = groups_get_groupmeta($school_id, 'school_seats');
    return $school_seats;
}

