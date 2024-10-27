<?php
/**
 * The template for tepunareomaori Component's groups filters template
 *
 * This template can be overridden by copying it to yourtheme/buddypress/common/filters/group-filters.php.
 *
 * @since   Kofingschools 2.0.0
 * @version 2.0.0
 */

// Check group type enable?
if ( false === bp_disable_group_type_creation() ) {
	return '';
}

// No need to show the group type select dropdown.
$group_type = bp_get_current_group_directory_type();
if ( ! empty( $group_type ) ) {
	return '';
}


$args = array(
	'orderby'    => 'menu_order',
	'order'      => 'ASC',
	'meta_query' => array(
		array(
			'key'   => '_bp_group_type_enable_filter',
			'value' => 1,
		),
	),
);

// Get active group types.
if ( bp_is_groups_directory() ) {
	$args['meta_query'][] = array(
		'key'   => '_bp_group_type_enable_remove',
		'value' => 0,
	);
	//search
	?>
	<div class="subnav-search groups-search">
		<?php bp_nouveau_search_form(); ?>
	</div>
	<?php
}



// Curriculums
/* if(function_exists('get_groups_type')) :
	$group_types = get_groups_type();
	//$group_types = bp_get_active_group_types( $args );


	if ( ! empty( $group_types ) ) { */
		?>
		<!-- <div id="group-type-filters" class="component-filters clearfix">
			<div id="group-type-select" class="last filter">
				<label class="bp-screen-reader-text" for="group-type-order-by">
					<span><?php bp_nouveau_filter_label(); ?></span>
				</label>
				<div class="select-wrap">
					<select id="group-type-order-by" style="width: 210px;"
							data-bp-group-type-filter="<?php bp_nouveau_search_object_data_attr() ?>">
						<option value=""><?php _e( 'All Curriculums', 'tprm-theme' ); ?></option><?php
						foreach ( $group_types as $group_type_id ) {
							$group_type_key   = bp_group_get_group_type_key( $group_type_id );
							if(!empty( $group_type_key)) :
							$group_type_label = bp_groups_get_group_type_object( $group_type_key )->labels['name'];
							?>
							<option
							value="<?php echo esc_attr( $group_type_key ); ?>"><?php echo esc_attr( $group_type_label ); ?></option><?php
							endif;
						}
						?>
					</select>
					<span class="select-arrow" aria-hidden="true"></span>
				</div>
			</div>
		</div> -->
		<?php
/* 	}

endif; */


// Schools
if(function_exists('get_schools')) :
	$schools_id = get_schools();

	if ( count($schools_id) > 1 && function_exists('bp_is_groups_directory') && bp_is_groups_directory()) {
		global $TPRM_ajax_nonce ;

		$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );
		?>
		<div id="school-filters" class="component-filters clearfix">
			<div id="school-select" class="last filter">
				<label class="bp-screen-reader-text" for="school-order-by">
					<span><?php bp_nouveau_filter_label(); ?></span>
				</label>
				<div class="select-wrap">
					<select id="school-order-by"
							data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>"
							data-bp-school-filter="<?php bp_nouveau_search_object_data_attr() ?>">
						<option value=""><?php _e( 'All Schools', 'tprm-theme' ); ?></option><?php
						foreach ( $schools_id as $school_id ) {
							$school_key   = $school_id;
							$school_label = groups_get_group($school_id)->name;
							?>
							<option
							value="<?php echo esc_attr( $school_key ); ?>"><?php echo esc_attr( $school_label ); ?></option><?php
						}
						?>
					</select>
					<span class="select-arrow" aria-hidden="true"></span>
				</div>
			</div>
		</div>
		<?php
	}
endif;


// years
if(function_exists('get_groups_year')) :
	$classes_year = get_groups_year();
	$this_year = get_option('school_year');

	if ( ! empty( $classes_year ) ) {

		global $TPRM_ajax_nonce ;

		$TPRM_ajax_nonce = wp_create_nonce( "TPRM_nonce" );
		?>
		<div id="year-filters" class="component-filters clearfix">
			<div id="year-select" class="last filter">
				<label class="bp-screen-reader-text" for="year-order-by">
					<span><?php bp_nouveau_filter_label(); ?></span>
				</label>
				<div class="select-wrap" style="min-width: 100px;">
					<select id="year-order-by"
							data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>"
							<?php if( function_exists('bp_is_group_subgroups') && bp_is_group_subgroups() ) : ?>
								data-this_school ="<?php echo esc_attr(bp_get_current_group_id())?>"
							<?php endif ?>
							data-bp-year-filter="<?php bp_nouveau_search_object_data_attr() ?>">
						<option value="<?php echo esc_attr( $this_year ); ?>" selected><?php _e( 'This Year', 'tprm-theme' ); ?></option><?php
						foreach ( $classes_year as $classe_year ) {
							if( $classe_year != $this_year ) :
							?>
							<option value="<?php echo esc_attr( $classe_year ); ?>"><?php echo esc_attr( $classe_year ); ?></option>
							<?php
							endif;
						}
						?>
					</select>
					<span class="select-arrow" aria-hidden="true"></span>
				</div>
			</div>
		</div>
		<?php
	}

endif;
	
?>

<?php if( function_exists('is_tprm_manager') && is_tprm_manager() && function_exists('bp_is_group_subgroups') && bp_is_group_subgroups() ): ?>
<div class="manage-classrooms">
	<button class="all-classrooms">
		<span class="bb-icon-l bb-icon-home"></span>
	</button>
	<button class="create-classroom">
		<span class="bb-icon-l bb-icon-user-friends-plus"></span>
		<?php _e('Create Classroom', 'tprm-theme') ?>
	</button>
</div>
<?php endif; ?>