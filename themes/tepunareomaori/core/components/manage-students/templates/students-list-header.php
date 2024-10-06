
<div class="manage-students-header">
	<div class="student_avatar <?php echo $is_school ? 'school' : ''; ?>">
		<div class="kwf-checkbox">
			<div class="round">
				<input type="checkbox" id="check-all-students" />
				<label for="check-all-students"></label>
			</div>
		</div>
	</div>
	<div class="student_name">
		<?php _e('Name', 'tprm-theme') ?>
	</div>
	<div class="student_login">
		<?php _e('Login', 'tprm-theme') ?>
	</div>
	<div class="student_password">
		<?php _e('Password', 'tprm-theme') ?>
	</div>
    <?php
    if ($is_school) { ?>
	<div class="student_classroom">
		<?php _e('Classroom', 'tprm-theme') ?>
	</div>
    <?php  }
   /*  if ($is_school) { ?>
	<div class="student_classroom">
        <?php
         $this_year = get_option('school_year');
         $previous_year = get_previous_year();
         ?>
            <div class="student_classroom-title">
                <?php _e('Classroom', 'tprm-theme') ?>
            </div>
            <div class="sub-cell-container">
                <div class="sub-cell previous-year"> <?php echo esc_html($previous_year) ?></div>
                <div class="sub-cell next-year"><?php echo esc_html($this_year) ?></div>
            </div>   
	</div>
    <?php  } */
    ?>
	<div class="student_account_status">
		<?php _e('Account Status', 'tprm-theme') ?>
	</div>
	<div class="student_actions">
		<?php _e('Actions', 'tprm-theme') ?>
	</div>
</div>