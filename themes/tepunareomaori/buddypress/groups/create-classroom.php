
<!-- multistep form -->
<form id="multistepsform">
  <!-- progressbar -->
  <ul id="progressbar">
    <li class="active"><?php _e('Classroom Details', 'tprm-theme') ?></li>
    <li><?php _e('Teachers Setup', 'tprm-theme') ?></li>
  </ul>
  <!-- fieldsets -->
  <?php 

  if( function_exists('bp_is_group_subgroups') && bp_is_group_subgroups()){
    $classroom_curriculum_nonce = wp_create_nonce('generate_classroom_curriculum');
    $classroom_create_nonce = wp_create_nonce('classroom_create_nonce');
    $school_id = bp_get_current_group_id();
  }
  global $bp;
  $group_link = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
  $teachers_tab = $group_link . 'teachers';
  ob_start();
  include TPRM_THEME_PATH . 'template-parts/preloader.php';
  $preloader = ob_get_clean();
  ?>

  <?php 
    // Classroom Details
  ?>
  <fieldset id="classroom_details">
    <div class="fieldset-header">
        <h2 class="fs-title"><?php _e('Classroom Details', 'tprm-theme') ?></h2>
        <h3 class="fs-subtitle"><?php _e('Select Classroom Name', 'tprm-theme') ?></h3>
    </div>
    <div class="fieldset-body">
        <div class="fieldset-body-component">
          <h2 class="bb-bp-group-title" id="classroom_school" data-school_id="<?php echo esc_attr($school_id) ?>"><?php echo wp_kses_post( bp_get_group_name() ); ?></h2>
        </div>
        <div class="fieldset-body-component">
            <label for="classroom_name"><?php _e( 'Enter the classroom Name', 'tprm-theme' ); ?></label>
            <input type="text" id="classroom_name" minlength="3" name="classroom_name" placeholder="<?php esc_attr_e( 'Classroom Name', 'tprm-theme' ); ?>" size="30" required>
        </div>
    </div>
    <div class="fieldset-footer">
        <input type="button" name="next" class="next action-button" value="<?php _e('Next ⮚', 'tprm-theme') ?>" />
    </div>
  </fieldset>

  <?php 
    // Teachers Setup
  ?>
  <fieldset id="teachers_setup">
    <div class="fieldset-header">
        <h2 class="fs-title"><?php _e('Teachers Setup', 'tprm-theme') ?></h2>
        <h3 class="fs-subtitle"><?php _e('Add a Teacher(s) to the Classroom:', 'tprm-theme') ?></h3>
    </div>
   
    <div class="fieldset-body">
          <div class="kwf-preloader" style="display: none;">
            <?php echo $preloader;  ?>
          </div>

          <div class="notice" style="display: none;"></div>
          <?php 
          $teachers = get_school_teachers($school_id);

          if( !empty($teachers) ) { // there is at least a teacher
          ?>
          <div class="teachers-notice">
            <?php
            _e(sprintf('Select a teacher from the list to assign to this classroom. If you want to add a new teacher, you can create one from <a target="_blank" href="%s">here</a>.', $teachers_tab), 'tprm-theme'); 
              ?>
          </div>      
          <div class="teachers-list">
            <?php include 'school-teachers-list.php'; ?>
          </div>
          <button class="button" id="confirm-teacher-selection" type="button" style="display: none;"><?php esc_html_e('Confirm Teacher Selection', 'tprm-theme') ?></button>
          <?php 
          } else{
            ?>
            <div class="no-teachers-list">
           <?php _e(sprintf('There are no teachers available for this school. You can continue creating this classroom, but you should create a teacher from <a target="_blank" href="%s">here</a> and assign it to this classroom later.', $teachers_tab), 'tprm-theme');           
          ?></div>  <?php
        }
          ?>
    </div>
    
    <div class="fieldset-footer">
        <input type="button" name="previous" class="previous action-button" value="<?php _e('⮘ Previous', 'tprm-theme') ?>" />
        <input type="button" style="display: none;" name="back" class="back action-button" value="<?php _e('⮘ Back', 'tprm-theme') ?>" />
        <button class="button" data-security="<?php esc_attr_e($classroom_create_nonce) ?>" id="submit-create-classroom" type="button"><?php esc_html_e('Create Classroom', 'tprm-theme') ?></button>
        <button class="button" style="display: none;" id="create-new-classroom" type="button"><?php esc_html_e('Create New Classroom', 'tprm-theme') ?></button>
    </div>
  </fieldset>
  <?php 
    // Teachers Setup
  ?>
</form>