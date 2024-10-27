<?php // start send message
if (bp_is_active('messages')) {
    bp_send_message_button($message_button_args);
}
/* End Send Message */
$school_id = is_school(bp_get_current_group_id()) ? bp_get_current_group_id() : bp_get_parent_group_id(bp_get_current_group_id());
$school_name = bp_get_group_name(groups_get_group($school_id));
$student_classroom = get_student_classroom_for_year($student_id);
ob_start();
include TPRM_THEME_PATH . 'template-parts/preloader.php';
$preloader = ob_get_clean();

/* Start Manage Classrooms */
$student_name = bp_core_get_user_displayname($student_id); ?>
<a data-balloon-pos="left"
    data-balloon="<?php esc_attr_e('Manage Classrooms for this student', 'tprm-theme'); ?>"
    href="#manage-student-classrooms-<?php echo esc_attr($student_id); ?>"
    id="manage-student-classrooms" class="manage-student-classrooms-btn"
    data-student_id="<?php echo esc_attr($student_id); ?>"
    data-student_name="<?php echo esc_attr($student_name); ?>"
    target="_blank">
    <span class="bb-icon-exchange"></span>
</a>
<div id="manage-student-classrooms-<?php echo esc_attr($student_id); ?>" class="manage-student-classrooms mfp-with-anim mfp-hide white-popup">
    <?php if (!empty($classrooms)) {  ?>
        <div class="manage-student-classrooms-content-title">
            <span class="bb-icon-exchange"></span>
            <?php _e(sprintf('<span class="title-text">Manage Classrooms for</span> <strong>%s</strong>', $student_name), 'tprm-theme');  ?>
        </div>
        <!-- Popup content here -->
        <div class="manage-student-classrooms-content-body">

            <div class="classrooms-list">
                <div class="tprm-preloader" style="display: none"><?php echo $preloader; ?></div>
                <?php
                include 'school-classrooms-list.php';
                ?>
            </div>
        </div>
        <div class="manage-student-classrooms-content-footer">
            <button
                data-balloon-pos="up"
                data-balloon="<?php _e('Confirm Classrooms Selection for this student ', 'tprm-theme'); ?>"
                data-security="<?php esc_attr_e($manage_student_classrooms_nonce); ?>"
                data-student_id="<?php echo esc_attr($student_id); ?>"
                data-student_name="<?php echo esc_attr($student_name); ?>"
                type="button"
                id="confirm_student_classrooms"
                class="confirm_student_classrooms">
                <?php _e('Confirm Classrooms Selection', 'tprm-theme'); ?>
            </button>
            <button
                data-balloon-pos="up"
                data-balloon="<?php esc_attr_e('Cancel change, The Classrooms for this student will remain the same before you click on Manage Classrooms Button', 'tprm-theme'); ?>"
                class="button"
                type="button"
                id="cancel_student_classrooms">
                <?php _e('Cancel', 'tprm-theme'); ?>
            </button>
        </div>
    <?php
    } else {
    ?>
        <div class="no-classrooms">
            <div class="no-classrooms-body">
                <?php
                _e(sprintf('There are no classrooms found for this year. Please create a new classroom from <a target="_blank" href="%s">here</a>.', $classrooms_tab), 'tprm-theme'); ?>
            </div>
            <button
                class="button"
                type="button"
                id="cancel_student_classrooms">
                <?php _e('OK', 'tprm-theme'); ?>
            </button>
        </div>

    <?php }

    ?>
</div>
<!-- End Manage Classrooms -->

<!-- Start Edit student -->
<?php $student_name = bp_core_get_user_displayname($student_id); ?>
<a data-balloon-pos="left"
    data-balloon="<?php esc_attr_e('Edit student Details', 'tprm-theme'); ?>"
    href="#edit-student-details-<?php echo esc_attr($student_id); ?>"
    id="edit-student-details" class="edit-student-details-btn"
    data-student_id="<?php echo esc_attr($student_id); ?>"
    data-student_name="<?php echo esc_attr($student_name); ?>"
    target="_blank">
    <span class="bb-icon-user-edit"></span>
</a>
<div id="edit-student-details-<?php echo esc_attr($student_id); ?>" class="edit-student-details mfp-with-anim mfp-hide white-popup">
    <div class="edit-student-details-content-title">
        <span class="bb-icon-user-edit"></span>
        <?php _e(sprintf('Edit Details for <strong>%s</strong>', $student_name), 'tprm-theme');  ?>
    </div>
    <!-- Popup content here -->
    <?php
    global $wpdb;
    $student_object = get_userdata($student_id);
    $username = sanitize_text_field($student_object->user_login);
    $email = sanitize_text_field($student_object->user_email);
    $first_name = get_user_meta($student_id, 'first_name', true);
    $last_name = get_user_meta($student_id, 'last_name', true);
    $std_cred_tbl = $wpdb->prefix . "students_credentials";
    $sql = $wpdb->prepare("SELECT stdcred FROM " . esc_sql($std_cred_tbl) . " WHERE username = %s", esc_sql($username));
    $stdcred_object = $wpdb->get_results($sql, OBJECT);
    if (!empty($stdcred_object)) {
        $password = $stdcred_object[0]->stdcred;
    } ?>

    <div class="edit-student-details-content-body">
        <ul>
            <li><a href="#student-name-<?php echo esc_attr($student_id); ?>"><?php _e('Change Name', 'tprm-theme') ?></a></li>
            <li><a href="#student-email-<?php echo esc_attr($student_id); ?>"><?php _e('Change Email', 'tprm-theme') ?></a></li>
            <li><a href="#student-password-<?php echo esc_attr($student_id); ?>"><?php _e('Change Password', 'tprm-theme') ?></a></li>
        </ul>
        <div id="student-name-<?php echo esc_attr($student_id); ?>">
            <form>
                <div class="student-first-name">
                    <label for="first-name"><?php _e('First Name: ', 'tprm-theme') ?></label>
                    <input type="text" id="first-name-<?php echo esc_attr($student_id); ?>" name="first-name" value="<?php echo esc_attr($first_name); ?>">
                </div>
                <div class="student-last-name">
                    <label for="last-name"><?php _e('Last Name: ', 'tprm-theme') ?></label>
                    <input type="text" id="last-name-<?php echo esc_attr($student_id); ?>" name="last-name" value="<?php echo esc_attr($last_name); ?>">
                </div>
            </form>
        </div>
        <div id="student-email-<?php echo esc_attr($student_id); ?>">
            <form>
                <label for="email"><?php _e('Email: ', 'tprm-theme') ?></label>
                <input type="email" id="email-<?php echo esc_attr($student_id); ?>" name="email" value="<?php echo esc_attr($email); ?>">
            </form>
        </div>
        <div id="student-password-<?php echo esc_attr($student_id); ?>">
            <?php
            // Get the current password type (text or picture) from user meta
            $password_type = get_user_meta($student_id, 'password_type', true);
            $current_password = get_user_meta($student_id, 'text_password', true);
            $current_picture_password = get_user_meta($student_id, 'picture_password_image', true);
            ?>

            <form id="password-selection-form">
                <label><?php _e('Select Password Type:', 'tprm-theme'); ?></label>

                <!-- Radio buttons to switch between Text and Picture Password -->
                <input type="radio" id="text-password-radio" name="password_type" value="text"
                    <?php echo ($password_type === 'text') ? 'checked' : ''; ?>>
                <label for="text-password-radio"><?php _e('Text Password', 'tprm-theme'); ?></label>

                <input type="radio" id="picture-password-radio" name="password_type" value="picture"
                    <?php echo ($password_type === 'picture') ? 'checked' : ''; ?>>
                <label for="picture-password-radio"><?php _e('Picture Password', 'tprm-theme'); ?></label>
            </form>

            <!-- Text Password Section -->
            <div id="text-password-section" style="display: <?php echo ($password_type === 'text') ? 'block' : 'none'; ?>;">
                <form>
                    <label for="password"><?php _e('Current Text Password:', 'tprm-theme'); ?></label>
                    <input type="text" id="password-<?php echo esc_attr($student_id); ?>" name="password"
                        value="<?php echo esc_attr($current_password); ?>" placeholder="<?php _e('Enter new text password', 'tprm-theme'); ?>">
                </form>
            </div>
            <center>
                <!-- Picture Password Section -->
                <div id="picture-password-section" style="display: <?php echo ($password_type === 'picture') ? 'block' : 'none'; ?>;">
                    <label><?php _e('Current Picture Password:', 'tprm-theme'); ?></label>
                    <div id="current-picture-password">
                        <?php

                        if (!empty($current_picture_password)) {


                            $image_dir = wp_upload_dir()['baseurl'] . '/picture-passwords/';

                            $imageUrl = $image_dir . $current_picture_password;
                            echo '<img src="' . esc_url($imageUrl) . '" alt="Current Picture Password" class="current-picture-password-image">';
                        } else {
                            echo '<p>' . __('No picture password set.', 'tprm-theme') . '</p>';
                        }
                        ?>
                    </div>
                    <label><?php _e('Select New Picture Password:', 'tprm-theme'); ?></label>
                    <div id="image-gallery" class="image-gallery-grid images-grid">
                        <!-- Picture Password Images will be dynamically loaded here -->
                    </div>
                    <input type="hidden" id="picture-password-url-<?php echo esc_attr($student_id); ?>"
                        class="picture-password-url" name="picture_password_url" value=""
                        data-student-id="<?php echo esc_attr($student_id); ?>">
                </div>
            </center>

        </div>

        <style>
            #password-selection-form {
                display: flex;
                flex-direction: row;
            }

            #password-selection-form input {
                width: unset !important;
            }

            .image-gallery-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                /* 4 columns */
                grid-gap: 10px;
                /* Space between images */
                max-height: 400px;
                /* Limit the height */
                width: 450px;
                overflow-y: auto;
                /* Enable vertical scroll for overflow */
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }

            #current-picture-password img {
                border: 0;
                font-style: italic;
                height: 100px;
                max-width: 100px;
                border-radius: 10px;
                vertical-align: middle;
            }




            /* Style for each image container */
            .gallery-image-container {
                position: relative;
                cursor: pointer;
                transition: transform 0.2s ease;
            }

            .gallery-image-container.selected {
                border: 2px solid #007cba;
                /* Add border for selected images */
            }

            /* Image inside the container */
            .gallery-image-container img {
                width: 100px !important;
                /* Make the image fill the container */
                height: 100px !important;
                display: block;
                border-radius: 5px;
            }

            /* Add hover effect */
            .gallery-image-container:hover {
                transform: scale(1.05);
                /* Slight zoom on hover */
            }

            /* Style for the scrollbar */
            .image-gallery-grid::-webkit-scrollbar {
                width: 8px;
            }

            .image-gallery-grid::-webkit-scrollbar-thumb {
                background-color: #888;
                border-radius: 4px;
            }

            .image-gallery-grid::-webkit-scrollbar-thumb:hover {
                background-color: #555;
            }

            /* Ensure that the images are responsive */
            @media screen and (max-width: 768px) {
                .image-gallery-grid {
                    grid-template-columns: repeat(2, 1fr);
                    /* Adjust to 2 columns for smaller screens */
                }
            }
        </style>




    </div>


    <div class="edit-student-details-content-footer">
        <button
            data-balloon-pos="up"
            data-balloon="<?php _e('Save and Update student Details for this student ', 'tprm-theme'); ?>"
            data-security="<?php esc_attr_e($manage_student_classrooms_nonce); ?>"
            data-student_id="<?php echo esc_attr($student_id); ?>"
            data-student_name="<?php echo esc_attr($student_name); ?>"
            type="button"
            id="confirm_edit_student"
            class="confirm_edit_student">
            <?php _e('Update student Details', 'tprm-theme'); ?>
        </button>
        <button
            data-balloon-pos="up"
            data-balloon="<?php esc_attr_e('Cancel change, The Details for this student will remain the same before you click on Edit student Details Button', 'tprm-theme'); ?>"
            class="button"
            type="button"
            id="cancel_edit_student">
            <?php _e('Cancel', 'tprm-theme'); ?>
        </button>
    </div>
</div>
<!-- End Edit student -->

<!-- Start Delete student -->
<a data-balloon-pos="left"
    data-balloon="<?php esc_attr_e('Suspend this student', 'tprm-theme'); ?>"
    href="#student-delete-content-<?php echo esc_attr(bp_get_group_member_id()); ?>" data-student_id="<?php echo esc_attr(bp_get_group_member_id()); ?>" id="delete-student" class="delete-student" target="_blank">
    <span class="bb-icon-trash"></span>
</a>
<div id="student-delete-content-<?php echo esc_attr(bp_get_group_member_id()); ?>" class="student-delete-content mfp-with-anim mfp-hide white-popup">
    <div class="student-delete-content-title">
        <span class="bb-icon-l bb-icon-exclamation-triangle"></span>
        <div class="title-text"><?php _e('Confirm Suspend this student', 'tprm-theme') ?></div>
    </div>
    <!-- Popup content here -->
    <div class="student-delete-content-body">
        <div class="tprm-preloader" style="display: none"><?php echo $preloader; ?></div>
        <p><?php _e('Please be aware that this action will suspend the student from the school and all classrooms and will not have access to it’s account.', 'tprm-theme'); ?></p>
        <p><?php _e('This should only be done when a student is no longer part of the school.', 'tprm-theme'); ?></p>
        <p><?php _e('If you only wish to remove the student from a classroom(s), please press cancel and use the button with the following icon <span class="bb-icon-exchange"></span> .', 'tprm-theme'); ?></p>
    </div>
    <div class="student-delete-content-footer">
        <button
            data-balloon-pos="up"
            data-balloon="<?php _e('I confirm suspending this student', 'tprm-theme'); ?>"
            data-security="<?php esc_attr_e($suspend_student_nonce); ?>"
            data-student_id="<?php echo esc_attr(bp_get_group_member_id()); ?>"
            data-student_name="<?php echo esc_attr(bp_core_get_user_displayname(bp_get_group_member_id())); ?>"
            data-school_id="<?php echo esc_attr($school_id); ?>"
            data-classrooms_id="<?php echo esc_attr($student_classroom); ?>"
            type="button"
            id="confirm_delete_student"
            class="confirm_delete_student">
            <?php _e('Confirm', 'tprm-theme') ?>
        </button>
        <button
            data-balloon-pos="up"
            data-balloon="<?php esc_attr_e('Cancel change', 'tprm-theme'); ?>"
            class="button"
            type="button"
            id="cancel_delete_student">
            <?php _e('Cancel', 'tprm-theme') ?>
        </button>
    </div>
</div>


<!-- End Delete student -->