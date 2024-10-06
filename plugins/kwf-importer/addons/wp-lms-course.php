<?php

/* this addon was originally developed by @egraznov https://wordpress.org/support/topic/lifterlms-addon/ */

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'lifterlms/lifterlms.php' ) ){
	return;
}

add_filter( 'TPRM_importer_restricted_fields', 'TPRM_importer_wlms_restricted_fields', 10, 1 );
add_action( 'TPRM_importer_documentation_after_plugins_activated', 'TPRM_importer_wlms_documentation_after_plugins_activated' );
add_action( 'post_TPRM_importer_import_single_user', 'TPRM_importer_wlms_post_import_single_user', 10, 3 );

function TPRM_importer_wlms_restricted_fields( $TPRM_importer_restricted_fields ){
	return array_merge( $TPRM_importer_restricted_fields, array( 'lms_courses' ) );
}

function TPRM_importer_wlms_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "LifterLMS is activated", 'kwf-importer' ); ?></th>
		<td>
			<?php _e( "You can import users and assign them to LMS Course using next format", 'kwf-importer' ); ?>.
			<ul style="list-style:disc outside none; margin-left:2em;">
				<li><?php _e( "lms_courses as the column title", 'kwf-importer' ); ?></li>
				<li><?php _e( "The value of each cell will be the NUMBER of the course to enroll (do not use slugs)", 'kwf-importer' ); ?></li>
				<li><?php _e( "If you want to import multiple values, you can use a list using / to separate items", 'kwf-importer' ); ?></li>
			</ul>
		</td>
	</tr>
	<?php
}

function TPRM_importer_wlms_post_import_single_user( $headers, $row, $user_id ){
	$pos = array_search( 'lms_courses', $headers );

	if( $pos === FALSE )
		return;

	$lms_courses = explode( '/', $row[ $pos ] );
	$lms_courses = array_filter( $lms_courses, function( $value ){ return $value !== ''; } );

	foreach ($lms_courses as $course) {
		if ( is_int( (int)$course ) ) {
			$trigger = 'admin_import_' . $user_id;
			$enrolled = llms_enroll_student( $user_id, (int)$course, $trigger );
		}    
	}
}