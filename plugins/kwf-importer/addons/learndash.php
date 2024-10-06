<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !class_exists( 'SFWD_LMS' ) ){
	return;
}

class TPRM_importer_LearnDash{
	function __construct(){
		add_filter( 'TPRM_importer_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'TPRM_importer_documentation_after_plugins_activated', array( $this, 'documentation' ) );
	}

	function restricted_fields( $TPRM_importer_restricted_fields ){
		return array_merge( $TPRM_importer_restricted_fields, array( 'lms_courses' ) );
	}

	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "LearnDash is activated", 'kwf-importer' ); ?></th>
			<td>
				<?php _e( "This plugin lets you bulk import users into Learndash courses:", 'kwf-importer' ); ?>.
				<ul style="list-style:disc outside none; margin-left:2em;">
					<li><?php _e( "Column ", 'kwf-importer' ); ?> lms_courses: <?php _e( "You have to specify the course IDs you want to enrol a specific user into", 'kwf-importer' ); ?></li>
                    <li><?php _e( "You also need to create, for each course, the respective ", 'kwf-importer' ); ?> course_courseID_access_from <?php _e( "where courseID needs to be replaced (as column header) with the same id you used in lms_courses, and the column cells populated with a string such as the one you will find in the metakey section of the plugin looking for that particular metakey (course_courseID_access_from). 
This is because Learndash needs to know when a user was enrolled to which course.", 'kwf-importer' ); ?></li>
                </ul>
                <span class="description"><?php _e( "Thanks to @prangesco who explain us how to do it", 'kwf-importer' ); ?></span>
			</td>
		</tr>
		<?php
	}
}

new TPRM_importer_LearnDash();