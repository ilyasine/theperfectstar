
<?php if( is_tprm_manager() && bp_is_group()  && strpos($_SERVER['REQUEST_URI'], "teachers") !== false ): 
  /* start .teachers-management */
  ?>
  <div class="teachers-management">

    <!-- teacher management header -->
    <div class="teachers-management-header">
        <!-- search -->
        <div class="subnav-search members-search">
          <?php bp_nouveau_search_form(); ?>
        </div>
    <!-- manage -->
      <div class="manage-teachers">
        <button class="all-teachers">
          <span class="bb-icon-l bb-icon-home"></span>
        </button>
        <button class="create-teacher">
          <span class="bb-icon-l bb-icon-user-plus"></span>
          <?php _e('Create teacher', 'tprm-theme') ?>
        </button>
      </div>
    </div>

    <!-- start create teacher form -->
    <div id="create-teacher" class="teachers-management-content" style="display: none;">
       
        <form id="multistepsform">
          <!-- progressbar -->
          <ul id="progressbar">
            <li class="active"><?php _e('Teacher Details', 'tprm-theme') ?></li>
            <li><?php _e('Credentials Setup', 'tprm-theme') ?></li>
            <li><?php _e('Classrooms Setup', 'tprm-theme') ?></li>
          </ul>
          <!-- fieldsets -->
          <?php 

          global $bp;
          $classrooms_tab = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
          $teacher_create_nonce = wp_create_nonce('teacher_create_nonce');
          $school_id = bp_get_current_group_id();

          ob_start();
          include TPRM_THEME_PATH . 'template-parts/preloader.php';
          $preloader = ob_get_clean();
          ?>

          <?php 
            // teacher Details
          ?>
          <fieldset id="teacher_details">
            <div class="fieldset-header">
                <h2 class="fs-title"><?php _e('teacher Details', 'tprm-theme') ?></h2>
                <h3 class="fs-subtitle"><?php _e('Fill in Teacher First Name & Last Name', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
                <div class="fieldset-body-component">
                  <h2 class="bb-bp-group-title" id="teacher_school" data-school_id="<?php echo esc_attr($school_id) ?>"><?php echo wp_kses_post( bp_get_group_name() ); ?></h2>
                </div>
                <div class="fieldset-body-component">
                    <label for="teacher_firstname"><?php _e( 'Enter the teacher First Name', 'tprm-theme' ); ?></label>
                    <input type="text" id="teacher_firstname" minlength="3" name="teacher_firstname" placeholder="<?php esc_attr_e( 'First Name', 'tprm-theme' ); ?>" size="30" required>
                </div>
                <div class="fieldset-body-component">
                    <label for="teacher_lastname"><?php _e( 'Enter the teacher Last Name', 'tprm-theme' ); ?></label>
                    <input type="text" id="teacher_lastname" minlength="3" name="teacher_lastname" placeholder="<?php esc_attr_e( 'Last Name', 'tprm-theme' ); ?>" size="30" required>
                </div>
            </div>
            <div class="fieldset-footer">
                <input type="button" name="next" class="next action-button" value="<?php _e('Next ⮚', 'tprm-theme') ?>" />
            </div>
          </fieldset>

          <?php 
            // Curriculum Setup
          ?>
          <fieldset id="teacher_credentials">
            <div class="fieldset-header">
                <h2 class="fs-title"><?php _e('Credentials Setup', 'tprm-theme') ?></h2>
                <h3 class="fs-subtitle"><?php _e('Create the credentials for the teacher', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
                <div class="fieldset-body-component">
                    <label for="teacher_email"><?php _e( 'Enter the teacher Email', 'tprm-theme' ); ?></label>
                    <input type="email" id="teacher_email" name="teacher_email" placeholder="<?php esc_attr_e( 'Ex. teacher@tepunareomaori.com', 'tprm-theme' ); ?>" size="30" required>
                </div>
                <div class="fieldset-body-component">
                    <label for="teacher_password"><?php _e( 'Generate a password for this teacher', 'tprm-theme' ); ?></label>
                    <input type="text" id="teacher_password" minlength="3" name="teacher_password" value="" placeholder="*****************" size="30" required>
                    <input type="button" class="button" id="generate_teacher_password" value="<?php esc_attr_e( 'Generate', 'tprm-theme' ); ?>">
                    <div id="password_strength"></div>
                </div>
           </div>
            <div class="fieldset-footer">
                <input type="button" name="previous" class="previous action-button" value="<?php _e('⮘ Previous', 'tprm-theme') ?>" />
                <input type="button" name="next" class="next action-button" value="<?php _e('Next ⮚', 'tprm-theme') ?>" />
            </div>
          </fieldset>

          <?php 
            // Classrooms Setup
          ?>
          <fieldset id="classrooms_setup">
            <div class="fieldset-header">
                <h2 class="fs-title"><?php _e('Classrooms Setup', 'tprm-theme') ?></h2>
                <h3 class="fs-subtitle"><?php _e('Assign Classroom(s) to the teacher:', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
                  <div class="tprm-preloader" style="display: none;">
                    <?php echo $preloader;  ?>
                  </div>
                  <div class="notice" style="display: none;">
                  </div>
                  <?php /* check for classrooms */
                  $classrooms = get_school_classrooms_for_year($school_id);
                  if(!empty($classrooms)){           
                  ?>
                  <div class="classrooms-notice">         
                    <?php
                    _e(sprintf('Select a classroom from the list to assign to this teacher. If you want to add a new classroom, you can create one from <a target="_blank" href="%s">here</a>.', $classrooms_tab), 'tprm-theme'); 
                      ?>
                  </div>      
                  <div class="classrooms-list">
                    <?php include 'school-classrooms-list.php'; ?>
                  </div>
                  <button class="button" id="confirm-classroom-selection" type="button" style="display: none;"><?php esc_html_e('Confirm Classroom Selection', 'tprm-theme') ?></button>
                  <?php 
                  }else{
                    ?>
                    <div class="no-classrooms-list">
                   <?php _e(sprintf('No classrooms were found for this year. You may continue creating this teacher, but remember to assign him/her to a classroom later. You can create classrooms from <a target="_blank" href="%s">here</a>.', $classrooms_tab), 'tprm-theme');           
                  ?></div>  <?php
                }
                 
                  ?>
                  
            </div>
            <div class="fieldset-footer">
                <input type="button" name="previous" class="previous action-button" value="<?php _e('⮘ Previous', 'tprm-theme') ?>" />
                <input type="button" style="display: none;" name="back" class="back action-button" value="<?php _e('⮘ Back', 'tprm-theme') ?>" />
                <button class="button" data-security="<?php esc_attr_e($teacher_create_nonce) ?>" id="submit-create-teacher" type="button"><?php esc_html_e('Create teacher', 'tprm-theme') ?></button>
                <button class="button" style="display: none;" id="create-new-teacher" type="button"><?php esc_html_e('Create New teacher', 'tprm-theme') ?></button>
            </div>
          </fieldset>
          <?php 
            // Teachers Setup
          ?>
        </form>
    </div>
    <!-- end create teacher form -->
  
  <?php endif;  ?>