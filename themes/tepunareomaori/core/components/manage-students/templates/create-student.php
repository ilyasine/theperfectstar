
<?php
$is_school = is_school(bp_get_current_group_id()) ? true : false;
$school_seats = !is_null(get_school_seats($school_id)) ? intval(get_school_seats($school_id)) : 0;
$this_year = get_option('school_year');
$students_with_classroom = count(get_students_classroom_for_year($school_id, $this_year));
// Define the path to the helper class
$helper_class_path = WP_PLUGIN_DIR . '/kwf-membership-coupon/includes/classes/helper-class.php';

// Check if the file exists and include it
if ( file_exists( $helper_class_path ) ) {
  require_once $helper_class_path;
}

if( is_students_page() ) : ?>

<div class="students-management">
  <div class="account-stats-container">   
      <div class="account-stat-box" id="school_seats">
          <h2><?php echo esc_html($school_seats); ?></h2>
          <p><?php _e('Available Seats', 'tprm-theme') ?></p>
      </div>
      <div class="account-stat-box" id="enrolled_students_count">
          <h2><?php echo esc_html($students_with_classroom) ?></h2>
          <p><?php _e('Enrolled Students', 'tprm-theme') ?></p>
      </div>
      <div class="account-stat-box" id="active_count">
          <h2><?php echo esc_html($active_count) ?></h2>
          <p><?php _e('Accounts Activated', 'tprm-theme') ?></p>
      </div>
      <div class="account-stat-box" id="inactive_count">
          <h2><?php echo esc_html($inactive_count) ?></h2>
          <p><?php _e('Accounts inactivated', 'tprm-theme') ?></p>
      </div>
      <div class="account-stat-box" id="total_count">
          <h2><?php echo esc_html($total_count) ?></h2>
          <p><?php _e('Total Accounts', 'tprm-theme') ?></p>
      </div>
      <div class="account-stat-box" id="activation_rate">
          <h2><span><?php echo esc_html($activation_rate) ?></span> %</h2>
          <p><?php _e('Activation rate', 'tprm-theme') ?></p>
      </div>
  </div>
  <!-- student management header -->
  <div class="students-management-header">
        <!-- print students credentials -->
    <div class="print_students_credentials">
        <button 
            data-balloon-pos="up"
            data-balloon="<?php esc_attr_e('Print Students Credentials to a PDF file', 'tprm-theme'); ?>"
            data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>"
            id="print_pdf" class="print_students_pdf">
            <div class="print_students_inner">
                <?php _e('PDF')?>
                <span class="bb-icon-l bb-icon-file-pdf"></span>
            </div>
            <span class="bb-icon-l bb-icon-download"></span>
        </button>
        <button 
            data-balloon-pos="up"
            data-balloon="<?php esc_attr_e('Print Students Credentials to an Excel file', 'tprm-theme'); ?>"
            data-security="<?php esc_attr_e($TPRM_ajax_nonce); ?>"
            id="print_excel" class="print_students_inner">       
            <div class="print_students_excel_inner">
                <?php _e('Excel'); ?>
                <span class="bb-icon-l bb-icon-file-excel"></span>
            </div>
            <span class="bb-icon-l bb-icon-download"></span>
        </button>
    </div>
    <!-- manage -->
     <?php if ( is_TPRM_manager() && $is_school) : ?>
    <div class="manage-students">
      <button class="all-students">
        <span class="bb-icon-l bb-icon-home"></span>
      </button>
      <button class="create-student">
        <span class="bb-icon-l bb-icon-user-plus"></span>
        <?php _e('Create student', 'tprm-theme'); ?>
      </button>
      <button class="bulk-create-student">
        <span class="bb-icon-l bb-icon-upload"></span>
        <?php _e('Create Students from File', 'tprm-theme'); ?>
      </button>
    </div>
    <?php endif; ?>

  </div>
  <!-- end student management header -->
  <?php if (is_TPRM_manager() && $is_school) : /* start create student form */ ?>
    <div id="create-student" class="students-management-content" style="display: none;">
       
        <form id="multistepsform">
          <!-- progressbar -->
          <ul id="progressbar">
            <li class="active"><?php _e('student Details', 'tprm-theme') ?></li>
            <li><?php _e('Credentials Setup', 'tprm-theme') ?></li>
            <li><?php _e('Classroom Setup', 'tprm-theme') ?></li>
          </ul>
          <!-- fieldsets -->
          <?php 
          global $bp;
          $classrooms_tab = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
          $student_create_nonce = wp_create_nonce('student_create_nonce');
          $school_id = bp_get_current_group_id();

          ob_start();
          include TPRM_THEME_PATH . 'template-parts/preloader.php';
          $preloader = ob_get_clean();

          // student Details
          ?>
          <fieldset id="student_details">
            <div class="fieldset-header">
                <h2 class="fs-title"><?php _e('student Details', 'tprm-theme') ?></h2>
                <h3 class="fs-subtitle"><?php _e('Fill in student First Name & Last Name', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
                <div class="fieldset-body-component">
                  <h2 class="bb-bp-group-title" id="student_school" data-school_id="<?php echo esc_attr($school_id) ?>"><?php echo wp_kses_post( bp_get_group_name() ); ?></h2>
                </div>
                <div class="fieldset-body-component">
                    <label for="student_firstname"><?php _e( 'Enter the student First Name', 'tprm-theme' ); ?></label>
                    <input type="text" id="student_firstname" minlength="3" name="student_firstname" placeholder="<?php esc_attr_e( 'First Name', 'tprm-theme' ); ?>" size="30" required>
                </div>
                <div class="fieldset-body-component">
                    <label for="student_lastname"><?php _e( 'Enter the student Last Name', 'tprm-theme' ); ?></label>
                    <input type="text" id="student_lastname" minlength="3" name="student_lastname" placeholder="<?php esc_attr_e( 'Last Name', 'tprm-theme' ); ?>" size="30" required>
                </div>
            </div>
            <div class="fieldset-footer">
                <input type="button" name="next" class="next action-button" value="<?php _e('Next ⮚', 'tprm-theme') ?>" />
            </div>
          </fieldset>

          <?php 
            // Credentials Setup
          ?>
          <fieldset id="student_credentials">
            <div class="fieldset-header">
              <h2 class="fs-title"><?php _e('Credentials Setup', 'tprm-theme') ?></h2>
              <h3 class="fs-subtitle"><?php _e('Create the credentials for the student', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
              <div class="fieldset-body-component">
                <label for="password_type"><?php _e('Choose a password type', 'tprm-theme'); ?></label>
                <select id="password_type" name="password_type" required>
                  <option value=""><?php _e('Choose a password type', 'tprm-theme'); ?></option>
                  <option value="<?php echo esc_attr('text') ?>"><?php esc_html_e('Text Password', 'tprm-theme'); ?></option>
                  <option value="<?php echo esc_attr('picture') ?>"><?php esc_html_e('Picture Password', 'tprm-theme'); ?></option>
                </select>
              </div>
              <div class="fieldset-body-component">
                <div class="text-password" style="display: none;">
                  <label for="student_password"><?php _e('Generate a password for this student', 'tprm-theme'); ?></label>
                  <input type="text" id="student_password" minlength="3" name="student_password" value="" placeholder="*****************" size="30" required>
                  <input type="button" class="button" id="generate_student_password" value="<?php esc_attr_e('Generate', 'tprm-theme'); ?>">
                  <div id="password_strength"></div>
                </div>
                <div class="picture-password" style="display: none;">
                  <div class="images-grid">
                    <?php 
                      $upload_dir = wp_upload_dir();
                      $image_dir = $upload_dir['basedir'] . '/picture-passwords/';
                      if(file_exists($image_dir) && is_dir($image_dir)){
                        $image_files = glob($image_dir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
                        $image_urls = array_map(function($file) use ($upload_dir) {
                          return $upload_dir['baseurl'] . '/picture-passwords/' . basename($file);
                        }, $image_files);
                      
                        foreach($image_urls as $index => $imageUrl){
                          $escapedImageUrl = esc_url($imageUrl); ?>
                          <div class="picture-container">
                            <img src="<?php echo $escapedImageUrl ?>">
                            <span class="bb-icon-thumbtack-star"></span>
                          </div>
                          <?php 
                        }
                      }
                    ?>
                  </div>
                  <div id="password_strength"></div>               
                </div>
                <button class="button" id="confirm-picture-password" type="button" style="display: none;"><?php esc_html_e('Confirm Picture Selection', 'tprm-theme') ?></button>
              </div>
            </div>
            <div class="fieldset-footer">
              <input type="button" name="previous" class="previous action-button" value="<?php _e('⮘ Previous', 'tprm-theme') ?>" />
              <input type="button" name="next" class="next action-button" value="<?php _e('Next ⮚', 'tprm-theme') ?>" />
            </div>
          </fieldset>

          <?php 
            // Classroom Setup
          ?>
          <fieldset id="classrooms_setup">
            <div class="fieldset-header">
                <h2 class="fs-title"><?php _e('Classroom Setup', 'tprm-theme') ?></h2>
                <h3 class="fs-subtitle"><?php _e('Assign Classroom to the student:', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
                  <div class="kwf-preloader" style="display: none;">
                    <?php echo $preloader;  ?>
                  </div>
                  <div class="notice" style="display: none;"></div>            
                  <?php /* check for classrooms */
                  $classrooms = get_school_classrooms_for_year($school_id);
                  if(!empty($classrooms)){           
                  ?>
                  <div class="classrooms-notice">         
                    <?php
                    _e(sprintf('Select a classroom ( only one can be selected ) from the list to assign to this student. If you want to add a new classroom, you can create one from <a target="_blank" href="%s">here</a>.', $classrooms_tab), 'tprm-theme'); 
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
                   <?php _e(sprintf('No classrooms were found for this year. You may continue creating this student, but remember to assign him/her to a classroom later. You can create classrooms from <a target="_blank" href="%s">here</a>.', $classrooms_tab), 'tprm-theme');           
                  ?></div>  <?php
                }
                 
                  ?>
                  
            </div>
            <div class="fieldset-footer">
                <input type="button" name="previous" class="previous action-button" value="<?php _e('⮘ Previous', 'tprm-theme') ?>" />
                <input type="button" style="display: none;" name="back" class="back action-button" value="<?php _e('⮘ Back', 'tprm-theme') ?>" />
                <button class="button" data-security="<?php esc_attr_e($student_create_nonce) ?>" id="submit-create-student" type="button"><?php esc_html_e('Create student', 'tprm-theme') ?></button>
                <button class="button" style="display: none;" id="create-new-student" type="button"><?php esc_html_e('Create New student', 'tprm-theme') ?></button>
            </div>
          </fieldset>
          <?php 
            // students Setup
          ?>
        </form>
    </div>
    <?php /* end create student form */ ?>
    <?php /* start bulk create student form */ ?>
    <div id="bulk-create-student" class="students-management-content" style="display: none;">
       
        <form id="bulkmultistepsform">
          <!-- progressbar -->
          <ul id="progressbar">
            <li class="active"><?php _e('Template File', 'tprm-theme') ?></li>
            <li><?php _e('Classroom Setup', 'tprm-theme') ?></li>
            <li><?php _e('Submit', 'tprm-theme') ?></li>
            
          </ul>
          <!-- fieldsets -->
          <?php 
          global $bp;
          $classrooms_tab = $bp->root_domain . '/' . bp_get_groups_root_slug() . '/' . $bp->groups->current_group->slug . '/';
          $bulk_student_create_nonce = wp_create_nonce('bulk_student_create_nonce');
          $school_id = bp_get_current_group_id();              

          ob_start();
          include TPRM_THEME_PATH . 'template-parts/preloader.php';
          $preloader = ob_get_clean();

          // student Details
          ?>
          <fieldset id="template_file">
            <div class="fieldset-header">
                <h2 class="fs-title"><?php _e('Template File', 'tprm-theme') ?></h2>
                <h3 class="fs-subtitle"><?php _e('Download and Fill in Template File', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
                <div class="fieldset-body-component">
                  <h2 class="bb-bp-group-title" id="student_school" data-school_id="<?php echo esc_attr($school_id) ?>"><?php _e('Notes :', 'tprm-theme') ?></h2>
                </div>
                <div class="fieldset-body-component">
                  <div class="template-file-text">
                      <p><?php _e('1. Download the Excel template file and fill in the student information: First Name, Last Name, and Password Type.', 'tprm-theme') ?></p>
                      <p><?php _e('2. Ensure that the Excel file contains only students for a single classroom.', 'tprm-theme') ?></p>
                      <p><?php _e('3. In the next step, select the classroom where you want to import the students from the dropdown menu.', 'tprm-theme') ?></p>
                      <p><?php _e('4. Avoid using special characters (e.g., *, @, /) in any cell.', 'tprm-theme') ?></p>
                      <p><?php _e('5. Specify the Password Type as either « <strong>text</strong> » or « <strong>picture</strong> » : ', 'tprm-theme') ?></p>
                      <ul>
                          <li><?php _e('If « <strong>text</strong> », a text password will be generated for the student.', 'tprm-theme') ?></li>
                          <li><?php _e('If « <strong>picture</strong> », a random picture password will be assigned to the student.', 'tprm-theme') ?></li>
                      </ul>                     
                  </div>                
                  <a class="button download_template" href="<?php echo esc_url( MST_URL_PATH . 'templates/students_template_file.xlsx'); ?>" download="students_template_file.xlsx">
                      <?php _e('Download Template', 'tprm-theme') ?>
                  </a>

              </div>
            </div>
            <div class="fieldset-footer">
              <input type="button" name="next" class="next action-button" value="<?php _e('Next ⮚', 'tprm-theme') ?>" />
            </div>
          </fieldset>
          
          <?php 
            // Classroom Setup
          ?>
          <fieldset id="classroom_setup">
            <div class="fieldset-header">
              <h2 class="fs-title"><?php _e('Classroom Setup', 'tprm-theme') ?></h2>
              <h3 class="fs-subtitle"><?php _e('Choose a Classroom ', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
              <div class="fieldset-body-component">
                <div class="template-file-text">
                    <ul>
                        <li><?php _e('Select the classroom where you want to import the students from the dropdown menu.', 'tprm-theme') ?></li>
                        <li><?php _e('Ensure that the <strong>Excel file contains only students for the selected classroom.</strong>', 'tprm-theme') ?></li>
                    </ul>
                </div>
                <?php $classrooms = get_school_classrooms_for_year($school_id); ?>       
                <div id="groups-dropdown" class="last filter">
                    <label class="bp-screen-reader-text" for="classroom_select">
                        <span><?php _e('Select a Classroom where you want to import the students', 'tprm-theme') ?></span>
                    </label>
                    <div class="select-wrap">
                    <select id="classroom_select" required class="classroom_select" name="classroom_select">
                        <option value=""><?php _e('Select a classroom', 'tprm-theme') ?></option>
                        <?php
                        if (!empty($classrooms)) {                                    
                            foreach ($classrooms as $classroom_id) {
                                $classroom = groups_get_group($classroom_id); 
                                $classroom_slug = bp_get_group_slug(groups_get_group($classroom_id));
                                ?>                               
                                <option value="<?php echo esc_attr($classroom_id) ?>" data-slug="<?php echo esc_attr($classroom_slug) ?>" data-name="<?php echo esc_attr($classroom->name) ?>"><?php echo esc_html($classroom->name) ?></option>
                        <?php }
                        } else { ?>
                            <option value=""><?php _e('No Classroom found for this year', 'tprm-theme') ?></option>
                        <?php  }
                        ?>
                    </select>
                    </div>
                </div>
              </div>
            </div>
            <div class="fieldset-footer">
              <input type="button" name="previous" class="previous action-button" value="<?php _e('⮘ Previous', 'tprm-theme') ?>" />
              <input type="button" name="next" class="next action-button" value="<?php _e('Next ⮚', 'tprm-theme') ?>" />
            </div>
          </fieldset>

          <?php 
            // Submit File
          ?>
          <fieldset id="file_submit">
            <div class="fieldset-header">
                <h2 class="fs-title"><?php _e('Submit File', 'tprm-theme') ?></h2>
                <h3 class="fs-subtitle"><?php _e('Upload and Submit Students File', 'tprm-theme') ?></h3>
            </div>
            <div class="fieldset-body">
                  <div class="kwf-preloader" style="display: none;">
                    <?php echo $preloader; ?>
                    <div class="proccessed-data" style="display: none">
                      <?php _e('Importing', 'tprm-theme') ?> 
                      <span id="process_data">0</span> 
                      <?php _e('of', 'tprm-theme') ?> 
                      <span id="total_data"></span>
                      <?php _e('students to « <strong id="imported-students-classroom"></strong> »', 'tprm-theme') ?> 
                    </div>                 
                    <div class="progress-container" style="display: none">
                        <div class="progress-bar" style="width: 0%;"></div>
                    </div>
                  </div>
                  <div class="template-file-text">
                    <ul>
                        <li class="selected_classroom_text"><?php _e('You have Selected the « <strong><span><span></strong> » classroom to import the students to.', 'tprm-theme') ?></li>
                        <li class="selected_classroom_text"><?php _e('Ensure that the Excel file contains only students for « <strong><span><span></strong> ».', 'tprm-theme') ?></li>
                    </ul>
                </div>
                  <div class="notice" style="display: none;"></div>
                  <div class="skipped_student_notice error" style="display: none;"><span id="ignored_students_count"></span><?php _e('Students Ignored', 'tprm-theme') ?></div>
                  <div class="file_upload_Container">
                    <input type="file" id="excel_file" name="excel_file" accept=".xls, .xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required hidden />
                  </div>
                  
            </div>
            <div class="fieldset-footer">
                <input type="button" name="previous" class="previous action-button" value="<?php _e('⮘ Previous', 'tprm-theme') ?>" />
                <input type="button" style="display: none;" name="back" class="back action-button" value="<?php _e('⮘ Back', 'tprm-theme') ?>" />
                <button class="button" data-security="<?php esc_attr_e($bulk_student_create_nonce) ?>" id="submit-students-file" type="button"><?php esc_html_e('Submit Students File', 'tprm-theme') ?></button>
                <button class="button" style="display: none;" id="submit-new-students-file" type="button"><?php esc_html_e('Submit New Students File', 'tprm-theme') ?></button>
            </div>
          </fieldset>
          <?php 
            // students Setup
          ?>
        </form>
    </div>
  <?php endif; /* end bulk create student form */ ?>
<?php endif;  ?>
