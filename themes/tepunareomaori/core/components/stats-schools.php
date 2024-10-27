<?php     

$schools = get_tprm_schools();
$schools_count = count($schools);

$global_active_count = 0;
$global_inactive_count = 0;
$global_total_count = 0;
$global_activation_rate = '';
$group_members = array();


add_filter('bp_nouveau_feedback_messages', 'schools_feedback_message');
add_action('wp_ajax_schools_tab_content', 'load_schools_tab_content');
add_action('wp_ajax_stats_tab_content', 'load_stats_tab_content');

function schools_feedback_message($feedback_messages){
	$schools_feedback_message = array(
		'schools_feedback_message' => array(
			'type'    => 'loading',
			'message' => __( 'Loading schools. Please wait.', 'tprm-theme' ),
		),
		'stats_feedback_message' => array(
			'type'    => 'loading',
			'message' => __( 'Loading stats. Please wait.', 'tprm-theme' ),
		)
	);
	
	$feedback_messages = array_merge($feedback_messages, $schools_feedback_message);

	return $feedback_messages;
}

function compareActivationRates($a, $b) {
    return $a['activation_rate'] <=> $b['activation_rate'];
}

// States
function TPRM_states(){

	global $schools, $schools_count, $group_members;
	
	if ($schools) { ?>

		<div class="tprm-school-stats">

			<table class="schools-accounts schools_stats" cellspacing="0">
				<tbody>
					<tr>
						<td class="accounts-label"><?php _e('School', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Accounts Activated', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Accounts inactivated', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Total Accounts', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Activation rate', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Language', 'tprm-theme') ?></td>
					</tr>
				<?php

                // Initialize an array to store schools with activation rates
                $schoolsWithRates = array();

				foreach ($schools as $school) :

                    $active_count = 0;
                    $inactive_count = 0;
                    $total_count = 0;
                    $activation_rate = '';

                    $school_id = $school['id'];
                    $school_name = $school['name'];
                    $school_lang = groups_get_groupmeta( $school_id, 'ecole_lang' ) ;

                    // loop for schools
                    $pg_subgroups = bp_get_descendent_groups($school_id);

                    foreach ($pg_subgroups as $pg_subgroup) {
                        $pg_subgroup_id = $pg_subgroup->id;
                        $subgroup_members = groups_get_group_members(array(
                            'group_id' => $pg_subgroup_id,
                            'exclude_admins_mods' => true,
                            'exclude_banned' => true,
                            'exclude' => false,
                            'group_role' => array('member'),
                        ));

                        if (isset($subgroup_members['members'])) {
                            // Merge subgroup members into the main array
                            $group_members = array_merge($group_members, $subgroup_members['members']);
                        }
                    }

                    // stats by school
                    $global_students_credentials = array_reverse($group_members);

                    if ($global_students_credentials) {
                        foreach ($global_students_credentials as $student) {
                            $id = $student->ID;
                            if (is_active_student($id)) {
                                $active_count++;
                                $global_active_count++;
                            } else {
                                $inactive_count++;
                                $global_inactive_count++;
                            }
                        }
                        $total_count = $active_count + $inactive_count;
                        $global_total_count = $global_active_count + $global_inactive_count;

                        $activation_rate = number_format(($active_count / $total_count) * 100, 2) ;
                        $global_activation_rate = number_format(($global_active_count / $global_total_count) * 100, 2);                   

                        // Store school details along with activation rate
                        $schoolsWithRates[] = array(
                            'name' => $school_name,
                            'active_count' => $active_count,
                            'inactive_count' => $inactive_count,
                            'total_count' => $total_count,
                            'lang' => $school_lang,
                            'activation_rate' => $activation_rate,
                        );
                    
                    }

                    // Reset counts for the next school
                    $active_count = 0;
                    $inactive_count = 0;
                    $total_count = 0;
                    $activation_rate = '';
                    $group_members = array();
                    
                endforeach;

                usort($schoolsWithRates, 'compareActivationRates');

                foreach( $schoolsWithRates as $school ): 

                    $school_name = $school['name'];
                    $active_count = $school['active_count'];
                    $inactive_count = $school['inactive_count'];
                    $total_count = $school['total_count'];
                    $school_lang = $school['lang'];
                    $activation_rate = $school['activation_rate'];

                    // Display stats for each school in an HTML table

                ?>

                <tr>
                    <td class="accounts-count accounts-school"><?php echo esc_html($school_name) ?></td>
                    <td class="accounts-count accounts-activated"><?php echo esc_html($active_count) ?></td>
                    <td class="accounts-count accounts-inactivated"><?php echo esc_html($inactive_count) ?></td>
                    <td class="accounts-count accounts-total"><?php echo esc_html($total_count) ?></td>
                    <td class="accounts-count accounts-activation-rate"><?php echo esc_html($activation_rate) ?> %</td>
                    <td class="accounts-count accounts-lang"><?php echo esc_html($school_lang) ?></td>
                            
                </tr>

                <?php endforeach;
				?>
				</tbody>
			</table>
		</div>
			<!-- Display total stats for all schools in an HTML table -->
		<div class="tprm-global-schools-stats">

			<table class="schools-accounts schools_stats" cellspacing="0">
				<tbody>
					<tr>
						<td class="accounts-label"><?php _e('Total Schools', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Total Activated Accounts', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Total Inactivated Accounts', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Total Accounts', 'tprm-theme') ?></td>
						<td class="accounts-label"><?php _e('Global activation rate', 'tprm-theme') ?></td>
					</tr>
					<tr>
						<td class="accounts-count accounts-schools"><?php echo esc_html($schools_count) ?></td>
						<td class="accounts-count accounts-activated"><?php echo esc_html($global_active_count) ?></td>
						<td class="accounts-count accounts-inactivated"><?php echo esc_html($global_inactive_count) ?></td>
						<td class="accounts-count accounts-total"><?php echo esc_html($global_total_count) ?></td>
						<td class="accounts-count accounts-activation-rate"><?php echo esc_html($global_activation_rate) ?> %</td>
					</tr>
				</tbody>
			</table>

		</div>
		<?php
	} else {
		echo 'No groups found.';
	}
}
// schools loop
function schools_loop(){

	global $schools, $schools_count, $group_members;

	if ($schools) { ?>

		<ul id="groups-list" class="<?php bp_nouveau_loop_classes(); echo esc_attr( ' ' . $cover_class . ' ' . $group_alignment );?> grid schools groups-dir-list">
		<?php			

			foreach ($schools as $school) :

                // get all details about groups we want to display            
                //$school = groups_get_group($school);
                $school_name = $school['name'];
                $school_permalink = home_url('/groupes/' . $school['slug']);
                $school_avatar_url = bp_core_fetch_avatar(
                    array(
                        'object'  => 'group',
                        'item_id' => $school['id'],
                        'html'    => false,
                    )
                );
                $school_cover_url = bp_attachments_get_attachment(
                    'url',
                    array(
                        'object_dir' => 'groups',
                        'item_id'    => $school['id'],
                    )
                );
                $descendant_groups = bp_get_descendent_groups($school['id']);

                $nbr_classes = count($descendant_groups);

                $students_count = 0;

                foreach ($descendant_groups as $pg_subgroup) {
                    $pg_subgroup_id = $pg_subgroup->id;					
                    $group_members = groups_get_group_members(array(
                        'group_id'            => $pg_subgroup_id	
                    ));
                    $count = $group_members['count'];
                    $students_count += $count;
                }

                ?>
                <li class="item-entry odd hidden is-admin is-member group-has-avatar" school_id="<?php echo esc_attr($school['id']) ?>">
                
                    <div class="ecole-group-container bb-dash-grid__block list-wrap">
                        <!-- ecole-group-cover -->
                        <div class="ecole-group-cover">
                            <a href="<?php echo esc_url($school_permalink); ?>" rel="noopener noreferrer">
                                <?php if (!empty($school_cover_url)) { ?>
                                    <img src="<?php echo esc_url($school_cover_url); ?>">
                                <?php } ?>
                            </a>
                        </div>
                        <!-- ecole-group-avatar -->
                        <div class="ecole-group-avatar">
                            <div class="ecole-group-avatar-wrap">
                                <a href="<?php echo esc_url($school_permalink); ?>" rel="noopener noreferrer">
                                    <?php if (!empty($school_avatar_url)) { ?>
                                        <img src="<?php echo esc_url($school_avatar_url); ?>">
                                    <?php } ?>
                                </a>
                            </div>
                        </div>
                        <!-- ecole-group-main -->
                        <div class="ecole-group-main">
                            <div class="ecole-group-desc">
                                <span class="bb-current-group-tprm-school"><?php _e('School group', 'tprm-theme') ?></span>
                            </div>
                            <div class="ecole-group-name">
                                <a href="<?php echo esc_url($school_permalink); ?>" rel="noopener noreferrer">
                                    <span class="name"><?php echo esc_html($school_name); ?></span>
                                </a>
                            </div>
                            <div class="ecole-group-classes-count">
                                <span class="classes-count"><?php _e('Number of classes', 'tprm-theme'); ?></span>
                                <span class="count-nbr"><?php echo esc_html($nbr_classes); ?></span>
                            </div>
                            <div class="ecole-group-students-count">
                                <span class="students-count"><?php _e('Number of students', 'tprm-theme'); ?></span>
                                <span class="count-nbr"><?php echo esc_html($students_count); ?></span>
                            </div>

                        </div>
                    </div>
                </li>
			<?php
				
            endforeach; ?>

			<ul>

			<?php 
	}


}

// AJAX handler for schools tab content
function load_schools_tab_content() {
    ob_start();
    schools_loop();
    $content = ob_get_clean();
    echo $content;

    wp_die();
}

// AJAX handler for stats tab content
function load_stats_tab_content() {
    ob_start();
    TPRM_states();
    $content = ob_get_clean();
    echo $content;

    wp_die();
}