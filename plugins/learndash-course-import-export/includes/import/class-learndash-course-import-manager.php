<?php

/**
 * LearnDash Course Import/Export Add-on Importer
 */
defined('ABSPATH') || exit;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * LearnDash_Course_Import_Manager Class.
 */
if (!class_exists('LearnDash_Course_Import_Manager')) {

    class LearnDash_Course_Import_Manager {

        public $plugin_settings = [];

        /**
         * Class constructor.
         */
        public function __construct() {
            // Add AJAX action for uploading Excel file.
            add_action('wp_ajax_ld_cie_upload_excel_file', [$this, 'ld_cie_upload_excel_file_cb']);

            // Add action to filter post type order.
            add_action('pre_get_posts', [$this, 'ld_cie_filter_post_type_order'], 10, 1);

            // Retrieve and assign the 'ldcie_settings' option to $this->settings.
            $this->plugin_settings = get_option('__ldcie_plugin_global_settings', array());

        }


        /**
         * Filters the post type order in the admin area.
         *
         * @param WP_Query $query The WP_Query object.
         */
        public function ld_cie_filter_post_type_order($query) {
            // Check if the current context is in the admin area and the post type is 'sfwd-courses', 'sfwd-lessons', or 'sfwd-topic'.
            if (is_admin() && in_array($query->get('post_type'), array('sfwd-courses', 'sfwd-lessons', 'sfwd-topic'))) {
                // Set the orderby parameter to 'ID' for the query.
                $query->set('orderby', 'ID');

                // Set the order parameter to 'ASC' (ascending) for the query.
                $query->set('order', 'ASC');
            }
        }

        /**
         * Log an event for importing a post of a specific type.
         *
         * @param string $post_type The post type being imported.
         * @param int    $post_id   The ID of the imported post.
         */
        public function log_event($post_type, $post_id) {
            // Get the title of the post being imported.
            $post_title = get_the_title($post_id);

            // Prepare the log message.
            $log_title = sprintf(__('Importing %s', 'learndash-course-import-export'), ucfirst($post_type));
            $log_message = sprintf(__('Importing %s: %s', 'learndash-course-import-export'), ucfirst($post_type), $post_title);

            // Log the event using LearnDash_Course_Import_Export_WP_Logging::add method.
            LearnDash_Course_Import_Export_WP_Logging::add(
                    $log_title, // Log title.
                    $log_message, // Log message.
                    $post_id, // Post parent.
                    'event'       // Log type.
            );
        }

        /**
         * Retrieves the status of a course.
         *
         * @return string The course status ('publish' or 'pending').
         */
        public function get_course_status() {
            // Retrieve the global plugin settings.
            $settings = $this->plugin_settings;

            // Check if the 'publish_course' setting exists and assign its value to $publish_course_on_import.
            $publish_course_on_import = isset($settings['publish_course']) ? $settings['publish_course'] : '';

            // Check if the 'publish_course' value is "1" and return 'publish' if true.
            if ("1" === $publish_course_on_import) {
                return 'publish';
            } else {
                // Return 'pending' if 'publish_course' value is not "1".
                return 'pending';
            }
        }

        /**
         * Checks if update duplicates is enabled.
         *
         * @return bool Whether update duplicates is enabled or not.
         */
        public function is_update_duplicate() {
            // Retrieve the global plugin settings.
            $settings = $this->plugin_settings;

            // Check if the 'update_duplicate' setting exists and assign its value to $update_duplicate_on_import.
            $update_duplicate_on_import = isset($settings['update_duplicate']) ? $settings['update_duplicate'] : '';

            // Check if the 'update_duplicate' value is "1" and return true if true.
            if ("1" === $update_duplicate_on_import) {
                return true;
            } else {
                // Return false if 'update_duplicate' value is not "1".
                return false;
            }
        }

        /**
         * Callback function for uploading Excel file.
         *
         * @return int 1 if the file format is invalid, 0 if the file is invalid.
         */
        public function ld_cie_upload_excel_file_cb() {
            $file_formats = ['xls', 'xlsx'];

            $file = isset($_FILES['ldcie_import_file']) ? $_FILES['ldcie_import_file'] : null;
            if (!empty($file)) {
                $ext = strtolower(pathinfo(sanitize_text_field($_FILES['ldcie_import_file']['name']), PATHINFO_EXTENSION));
                if (in_array($ext, $file_formats)) {
                    global $current_user;
                    $upload_dir = wp_upload_dir();
                    $user_dirname = $upload_dir['basedir'] . '/ld_cie_uploads/' . $current_user->ID;
                    if (!file_exists($user_dirname)) {
                        wp_mkdir_p($user_dirname);
                    }

                    $file_path = trailingslashit($user_dirname) . $file['name'];
                    move_uploaded_file($file["tmp_name"], $file_path);

                    $this->file_loaded = true;
                    $this->start_import($file_path, $ext);
                }
                return 1; // Invalid file format
            }
            return 0; // Invalid file
        }

        /**
         * Import post type categories from Excel.
         *
         * @param string   $categories The categories string.
         * @param int      $post_id    The ID of the post.
         * @param string   $taxonomy   The taxonomy name.
         */
        public function learndash_course_import_export_post_categories($categories, $post_id, $taxonomy) {
            Learndash_Course_Import_Export_Helper::ld_course_import_export_category_parse_categories_field($categories, $post_id, $taxonomy);
        }

        /**
         * Import the featured image for a post from a given URL.
         *
         * @param int    $post_id Post ID.
         * @param string $url     URL of the image to import.
         */
        public function import_feature_image($post_id, $url) {

            if (empty($url)) {
                return false;
            }

            if ($this->check_if_image_exist($post_id, $url)) {
                return false;
            }

            // Add Featured Image to Post
            $image_url = $url;              // Define the image URL here
            $extension = pathinfo($url, PATHINFO_EXTENSION);     // Extension of file
            $image_name = 'IMG' . strtotime('now') . '.' . $extension;   // Filename
            $upload_dir = wp_upload_dir();           // Set upload folder
            $image_data = file_get_contents($image_url);        // Get image data
            $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name); // Generate unique name
            $filename = basename($unique_file_name);        // Create image file name
            // Check folder permission and define file location
            if (wp_mkdir_p($upload_dir['path'])) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            // Create the image  file on the server
            file_put_contents($file, $image_data);

            // Check image file type
            $wp_filetype = wp_check_filetype($filename, null);

            // Set attachment data
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            // Create the attachment
            $attach_id = wp_insert_attachment($attachment, $file, $post_id);

            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);

            // Assign metadata to attachment
            wp_update_attachment_metadata($attach_id, $attach_data);
            update_post_meta($post_id, 'image_url', $url);

            // And finally assign featured image to post
            set_post_thumbnail($post_id, $attach_id);
        }

        private function check_if_image_exist($post_id, $image_url) {

            $existing_image_url = get_post_meta($post_id, 'image_url', true);

            if (!empty($existing_image_url) && $existing_image_url === $image_url) {
                // The image URL already exists for this post
                return true;
            }

            // No match found
            return false;
        }

        /**
         * Starts the import process
         *
         * @param string $filepath The path of the file to import
         * @param string $ext The file extension
         */
        private function start_import($filepath, $ext) {

            // Create a reader based on the file extension
            if ($ext == 'xlsx') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } elseif ($ext == 'xls') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                // Add error handling for unsupported file types if necessary
            }

            // Load the spreadsheet from the file
            $spreadsheet = $reader->load($filepath);

            // Get the number of sheets in the spreadsheet
            $sheetCount = $spreadsheet->getSheetCount();

            // If the spreadsheet has less than 4 sheets, import data
            if ($sheetCount < 4) {
                // Create a variable to store the imported data
                $data = null;

                // Get the course sheet and save its data
                $spreadsheet->setActiveSheetIndexByName('Courses');
                $course_data = $spreadsheet->getActiveSheet()->toArray();
                $imported_course_ids = $this->save_courses($course_data);

                // Get the lesson sheet and save its data
                $spreadsheet->setActiveSheetIndexByName('Lessons');
                $lesson_data = $spreadsheet->getActiveSheet()->toArray();
                $imported_lesson_ids = $this->save_lessons($lesson_data);

                // Get the topic sheet and save its data
                $spreadsheet->setActiveSheetIndexByName('Topics');
                $topic_data = $spreadsheet->getActiveSheet()->toArray();
                $imported_topic_ids = $this->save_topics($topic_data);

                // Set the data variable to indicate a successful import and return it
                $data = array('success' => true, 'c_ids' => $imported_course_ids);
                wp_send_json($data, 200);
            } else {
                // If the spreadsheet has 4 or more sheets, return 0 (or add error handling if necessary)
                return 0;
            }
        }

        /**
         * Save the imported courses.
         *
         * @param array $courses Array of courses to save.
         * @return array|integer Array of saved courses or 0 if no courses to save.
         */
        private function save_courses($courses) {
            $course_ids = array();

            // Check if there are more than one courses to import.
            if (count($courses) > 1) {
                $header_row = [];
                $row_data = [];

                // Loop through each course row.
                foreach ($courses as $row => $columns) {
                    if ($row === 0) {
                        // Process header row to get column names.
                        foreach ($columns as $column) {
                            $col_name = strtolower(preg_replace('/\s+/', '_', trim($column)));
                            $header_row[] = $col_name;
                        }
                    } else {
                        // Combine header row with current row data.
                        $row_data = array_combine($header_row, $columns);

                        //echo $row_data['content'];
                        //exit;
                        // Check if the title is provided.
                        if (!empty($row_data['title'])) {
                            // Prepare post data for insertion.
                            $post = array(
                                'post_title' => wp_strip_all_tags($row_data['title']),
                                'post_type' => 'sfwd-courses',
                                'post_content' => isset($row_data['content']) ? html_entity_decode($row_data['content'], ENT_QUOTES, 'UTF-8') : 'content',
                                'post_status' => $this->get_course_status(),
                                'post_author' => get_current_user_id(),
                            );

                            // Check if course already exists.
                            $course_exist = get_page_by_title($row_data['title'], OBJECT, 'sfwd-courses');

                            //$course_exist = get_post($row_data['course_id'], OBJECT, 'sfwd-courses');
                            // Insert or update the course.
                            // $course_id = ( $course_exist !== null ) ? $course_exist->ID : wp_insert_post( $post );
                            if ($course_exist !== null) {
                                $post = array(
                                    'post_status' => $this->get_course_status(),
                                );
                                // Add the ID to the $lesson_post array to update the existing post
                                $post['ID'] = $course_exist->ID;
                                $course_id = wp_update_post($post);
                            } else {
                                $course_id = wp_insert_post($post);
                            }


                            $this->elementormeta_decode($row_data, $course_id);
                            // Log course import event.
                            if ('1' === $this->plugin_settings['ldcie_wp_log']) {
                                $this->log_event('course', $course_id);
                            }

                            // Link course to a Quiz if needed.
                            if (isset($row_data['course_id'])) {
                                set_transient('ld_cie_course_' . $row_data['course_id'], $course_id, DAY_IN_SECONDS);
                            }

                            $course_settings = array();

                            // Get available course settings.
                            $settings = Learndash_Course_Import_Export_Helper::get_course_settings();

                            // Loop through settings and update metadata.
                            foreach ($settings as $setting) {
                                if ('course_prerequisite' === $setting) {
                                    update_post_meta($course_id, '_course_prerequisite', $row_data[$setting]);
                                } else {
                                    $course_settings['sfwd-courses_' . $setting] = $row_data[$setting];
                                }

                                // Handle specific settings.
                                $specific_settings = array(
                                    'course_price_billing_p3',
                                    'course_price_billing_t3',
                                    'course_trial_duration_t1',
                                    'course_trial_duration_p1',
                                    'course_price_type',
                                );

                                if (in_array($setting, $specific_settings, true)) {
                                    update_post_meta($course_id, $setting, $row_data[$setting]);
                                }
                            }

                            // Update course settings metadata.
                            update_post_meta($course_id, '_sfwd-courses', $course_settings, true);

                            // Update course sections.
                            if (isset($row_data['course_sections'])) {
                                update_post_meta($course_id, 'course_sections', $row_data['course_sections']);
                            }

                            // Import featured image.
                            if (!empty($row_data['course_image'])) {
                                $this->import_feature_image($course_id, $row_data['course_image']);
                            }

                            // Import post categories and tags.
                            if (!empty($row_data['category'])) {
                                $this->learndash_course_import_export_post_categories($row_data['category'], $course_id, 'ld_course_category');
                            }
                            if (!empty($row_data['tag'])) {
                                $this->learndash_course_import_export_post_categories($row_data['tag'], $course_id, 'ld_course_tag');
                            }

                            // Build course link.
                            $link = add_query_arg(array('post' => $course_id, 'action' => 'edit'), esc_url(admin_url('post.php')));

                            // Add course data to the list.
                            $course_ids[] = array('id' => $course_id, 'name' => $row_data['title'], 'link' => $link);
                        }
                    }
                }

                // Set course prerequisites.
                $this->set_course_prerequisite($course_ids);

                // Return saved courses.
                return $course_ids;
            }

            // No courses to save.
            return 0;
        }

        function elementormeta_decode($data, $post_id) {
            global $wpdb;
            if (!function_exists('is_plugin_active')) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            if (is_plugin_active('elementor/elementor.php')) {
                foreach ($data as $key => $value) {

                    if (strpos($key, "_elementor") > -1) {
                        $dd = $wpdb->get_row("select * from {$wpdb->prefix}postmeta where post_id={$post_id} and meta_key='{$key}'");
                        if ($dd) {
                            $wpdb->update($wpdb->prefix . 'postmeta', ['post_id' => $post_id, 'meta_key' => $key, 'meta_value' => $value], ['meta_id' => $dd->meta_id]);
                        } else {

                            $wpdb->insert($wpdb->prefix . 'postmeta', ['post_id' => $post_id, 'meta_key' => $key, 'meta_value' => $value]);
                        }
                    }
                }
            }
        }

        /**
         * Save the imported lessons.
         *
         * @param array $lessons Array of lessons to save.
         */
        private function save_lessons($lessons) {
            if (count($lessons) > 1) {
                $header_row = [];
                $row_data = [];
                $lesson_ids = [];

                foreach ($lessons as $row => $columns) {
                    if ($row === 0) {
                        foreach ($columns as $column) {
                            $col_name = strtolower(preg_replace('/\s+/', '_', trim($column)));
                            $header_row[] = $col_name;
                        }
                    } else {
                        $row_data = array_combine($header_row, $columns);

                        if (!empty($row_data['title'])) {
                            $lesson_post = array(
                                'post_title' => wp_strip_all_tags($row_data['title']),
                                'post_type' => 'sfwd-lessons',
                                'post_content' => isset($row_data['content']) ? html_entity_decode($row_data['content'], ENT_QUOTES, 'UTF-8') : 'content',
                                'post_status' => $this->get_course_status(),
                                'post_author' => get_current_user_id(),
                            );

                            $lesson_exist = get_page_by_title($row_data['title'], OBJECT, 'sfwd-lessons');
                            // Insert or update the lesson based on the setting.
                            if ($this->is_update_duplicate()) {
                                
                                if ($lesson_exist !== null) {
                                    // Add the ID to the $lesson_post array to update the existing post
                                    $lesson_post['ID'] = $lesson_exist->ID;
                                    
                                    $lesson_id = wp_update_post($lesson_post);
                                    $old_lesson_id = $lesson_id;
                                } else {
                                    $lesson_id = wp_insert_post($lesson_post);
                                }
                            } else {
                                $lesson_id = 0; // Avoid using uninitialized variable.
                                $lesson_id = wp_insert_post($lesson_post);
                            }
                            $this->elementormeta_decode($row_data, $lesson_id);

                            if (!empty($lesson_id)) {
                                // Log lesson import event.
                                if ('1' === $this->plugin_settings['ldcie_wp_log']) {
                                    $this->log_event('Lesson', $lesson_id);
                                }

                                // Link lesson to a Quiz if needed.
                                if (isset($row_data['lesson_id'])) {
                                    set_transient('ld_cie_lesson_' . $row_data['lesson_id'], $lesson_id, DAY_IN_SECONDS);
                                }

                                // Get course object based on shared steps setting.
                                $course_title_key = learndash_is_course_shared_steps_enabled() ? 'shared_course' : 'course';
                                $post = get_page_by_title($row_data[$course_title_key], OBJECT, 'sfwd-courses');

                                if (empty($post)) {
                                    // If the initial lookup failed, use the other course title key
                                    $alternate_course_title_key = $course_title_key === 'shared_course' ? 'course' : 'shared_course';
                                    $post = get_page_by_title($row_data[$alternate_course_title_key], OBJECT, 'sfwd-courses');
                                }

                                $lesson_settings = array();

                                $settings = Learndash_Course_Import_Export_Helper::get_lesson_settings();

                                foreach ($settings as $setting) {
                                    $lesson_settings['sfwd-lessons_' . $setting] = $row_data[$setting];
                                }

                                if (!empty($post->ID) && !$old_lesson_id) {
                                    $lesson_settings['sfwd-lessons_course'] = $post->ID;
                                    update_post_meta($lesson_id, 'course_id', $post->ID);
                                }

                                // Import & Set featured image.
                                if (!empty($row_data['lesson_image'])) {
                                    $this->import_feature_image($lesson_id, $row_data['lesson_image']);
                                }

                                // Import post categories and tags.
                                if (!empty($row_data['category'])) {
                                    $this->learndash_course_import_export_post_categories($row_data['category'], $lesson_id, 'ld_lesson_category');
                                }
                                if (!empty($row_data['tag'])) {
                                    $this->learndash_course_import_export_post_categories($row_data['tag'], $lesson_id, 'ld_lesson_tag');
                                }

                                // Update lesson settings metadata.
                                update_post_meta($lesson_id, '_sfwd-lessons', $lesson_settings);

                                // Update course-specific settings.
                                if (!empty($post->ID)) {
                                    if (learndash_is_course_shared_steps_enabled()) {

                                        if(!$old_lesson_id){
                                            update_post_meta($lesson_id, 'ld_course_' . $post->ID, $post->ID);
                                        }

                                        $course_id = $post->ID;
                                        $course_steps_meta = get_post_meta($course_id, 'ld_course_steps', true);
                                        if ($course_steps_meta){
                                            if (!isset($course_steps_meta['steps']['h']['sfwd-lessons'][$lesson_id])) {
                                                $course_steps_meta['steps']['h']['sfwd-lessons'][$lesson_id] = array(
                                                    'sfwd-topic' => array(),
                                                    'sfwd-quiz' => array()
                                                );
                                                // Increment steps count
                                                if (isset($course_steps_meta['steps_count'])) {
                                                    $course_steps_meta['steps_count'] += 1;
                                                } else {
                                                    $course_steps_meta['steps_count'] = 1; // Set to 1 if steps_count is not set
                                                }

                                                update_post_meta($course_id, 'ld_course_steps', $course_steps_meta);
                                            }
                                        } else {
                                            // Initialize course steps meta with the new lesson
                                            $course_steps_meta = array(
                                                'steps' => array(
                                                    'h' => array(
                                                        'sfwd-lessons' => array(
                                                            $lesson_id => array(
                                                                'sfwd-topic' => array(),
                                                                'sfwd-quiz' => array()
                                                            )
                                                        ),
                                                        'sfwd-quiz' => array()
                                                    )
                                                ),
                                                'course_id' => $course_id,
                                                'course_builder_enabled' => 1,
                                                'course_shared_steps_enabled' => 1,
                                                'steps_count' => 1 // Initialize steps count to 1
                                            );

                                            // Add the new course steps meta
                                            update_post_meta($course_id, 'ld_course_steps', $course_steps_meta);
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         * Save the imported topics.
         *
         * @param array $topics Array of topics to save.
         */
        private function save_topics($topics) {
            if (count($topics) > 1) {
                $header_row = [];
                $row_data = [];

                foreach ($topics as $row => $columns) {
                    if ($row === 0) {
                        foreach ($columns as $column) {
                            $col_name = strtolower(preg_replace('/\s+/', '_', trim($column)));
                            $header_row[] = $col_name;
                        }
                    } else {
                        $row_data = array_combine($header_row, $columns);

                        if (!empty($row_data['title'])) {
                            $topic_post = array(
                                'post_title' => wp_strip_all_tags($row_data['title']),
                                'post_type' => 'sfwd-topic',
                                'post_content' => isset($row_data['content']) ? html_entity_decode($row_data['content'], ENT_QUOTES, 'UTF-8') : 'content',
                                'post_status' => $this->get_course_status(),
                                'post_author' => get_current_user_id(),
                            );

                            $topic_exist = get_page_by_title($row_data['title'], OBJECT, 'sfwd-topic');
                            
                            // Insert or update the topic based on the setting.
                            if ($this->is_update_duplicate()) {
                                if ($topic_exist !== null) {
                                    // Add the ID to the $lesson_post array to update the existing post
                                    $topic_post['ID'] = $topic_exist->ID;
                                    $topic_id = wp_update_post($topic_post);
                                    $old_topic_id = $topic_id;
                                } else {
                                    $topic_id = wp_insert_post($topic_post);
                                }
                            } else {
                                $topic_id = 0; // Avoid using uninitialized variable.
                                $topic_id = wp_insert_post($topic_post);
                            }

                            $this->elementormeta_decode($row_data, $topic_id);

                            if (!empty($topic_id)) {
                                // Log topic import event.
                                if ('1' === $this->plugin_settings['ldcie_wp_log']) {
                                    $this->log_event('Topic', $topic_id);
                                }

                                // Link topic to a Quiz if needed.
                                if (isset($row_data['topic_id'])) {
                                    set_transient('ld_cie_topic_' . $row_data['topic_id'], $topic_id, DAY_IN_SECONDS);
                                }

                                if (!empty($topic_id)) {
                                    $c_post = null;
                                    $l_post = null;
                                    // Get course and lesson objects based on shared steps setting.

                                    if (learndash_is_course_shared_steps_enabled()) {

                                        $c_post = get_page_by_title($row_data['shared_course'], OBJECT, 'sfwd-courses');
                                        if (empty($c_post)) {
                                            // If the initial lookup failed, use the other course title key
                                            $c_post = get_page_by_title($row_data['course'], OBJECT, 'sfwd-courses');
                                        }

                                        $l_post = get_page_by_title($row_data['shared_lesson'], OBJECT, 'sfwd-lessons');
                                        if (empty($l_post)) {
                                            // If the initial lookup failed, use the other lesson title key
                                            $l_post = get_page_by_title($row_data['lesson'], OBJECT, 'sfwd-lessons');
                                        }

                                    } elseif (isset($row_data['course'])) {

                                        $c_post = get_page_by_title($row_data['course'], OBJECT, 'sfwd-courses');
                                        if (empty($c_post)) {
                                            // If the initial lookup failed, use the other course title key
                                            $c_post = get_page_by_title($row_data['shared_course'], OBJECT, 'sfwd-courses');
                                        }

                                        $l_post = get_page_by_title($row_data['lesson'], OBJECT, 'sfwd-lessons');
                                        if (empty($l_post)) {
                                            // If the initial lookup failed, use the other lesson title key
                                            $l_post = get_page_by_title($row_data['shared_lesson'], OBJECT, 'sfwd-lessons');
                                        }

                                    }

                                    $topic_settings = array();

                                    $settings = Learndash_Course_Import_Export_Helper::get_topic_settings();

                                    foreach ($settings as $setting) {
                                        $topic_settings['sfwd-topic_' . $setting] = $row_data[$setting];
                                    }

                                    if(!$old_topic_id){
                                        // Save course and lesson IDs to topic metadata.
                                        $topic_settings['sfwd-topic_course'] = $c_post->ID;
                                        $topic_settings['sfwd-topic_lesson'] = $l_post->ID;
                                    }

                                    // Import & Set featured image.
                                    if (!empty($row_data['topic_image'])) {
                                        $this->import_feature_image($topic_id, $row_data['topic_image']);
                                    }

                                    // Import post categories and tags.
                                    if (!empty($row_data['category'])) {
                                        $this->learndash_course_import_export_post_categories($row_data['category'], $topic_id, 'ld_topic_category');
                                    }
                                    if (!empty($row_data['tag'])) {
                                        $this->learndash_course_import_export_post_categories($row_data['tag'], $topic_id, 'ld_topic_tag');
                                    }

                                    // Update course and lesson settings metadata.
                                    update_post_meta($topic_id, '_sfwd-topic', $topic_settings);

                                    // Update course and lesson specific settings.
                                    if (!empty($c_post->ID) && !$old_topic_id) {
                                        if (learndash_is_course_shared_steps_enabled()) {
                                            update_post_meta($topic_id, 'ld_course_' . $c_post->ID, $c_post->ID);
                                        }
                                        add_post_meta($topic_id, 'course_id', $c_post->ID);
                                    }

                                    if (!empty($l_post->ID) && !$old_topic_id) {
                                        if (learndash_is_course_shared_steps_enabled()) {
                                            update_post_meta($topic_id, 'ld_lesson_' . $l_post->ID, $l_post->ID);
                                        }
                                        add_post_meta($topic_id, 'lesson_id', $l_post->ID);
                                    }

                                    if (!empty($c_post->ID) && !empty($l_post->ID) && learndash_is_course_shared_steps_enabled()){
                                        $course_id = $c_post->ID;
                                        $lesson_id = $l_post->ID;
                                        $course_steps_meta = get_post_meta($course_id, 'ld_course_steps', true);
                                        if ($course_steps_meta){
                                            if (isset($course_steps_meta['steps']['h']['sfwd-lessons'][$lesson_id]) && !isset($course_steps_meta['steps']['h']['sfwd-lessons'][$lesson_id]['sfwd-topic'][$topic_id])) {
                                                
                                                $course_steps_meta['steps']['h']['sfwd-lessons'][$lesson_id]['sfwd-topic'][$topic_id] = array(
                                                    'sfwd-quiz' => array()
                                                );
                        
                                                // Increment steps count
                                                if (isset($course_steps_meta['steps_count'])) {
                                                    $course_steps_meta['steps_count'] += 1;
                                                } else {
                                                    $course_steps_meta['steps_count'] = 1; // Set to 1 if steps_count is not set
                                                }
                        
                                                update_post_meta($course_id, 'ld_course_steps', $course_steps_meta);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         * Set the course prerequisites for given course IDs
         *
         * @param array $course_ids The array of course IDs
         */
        private function set_course_prerequisite($course_ids) {

            // Loop through each course ID in the array
            foreach ($course_ids as $id) {

                // Get the metadata for the current course
                $meta = get_post_meta($id['id'], '_sfwd-courses', true);

                // Get the course prerequisite from metadata
                $prerequisite = get_post_meta($id['id'], '_course_prerequisite', true);

                // Initialize an empty array to store prerequisite course IDs
                $prerequisite_ids = [];

                // If the prerequisite is not empty, process the names and get their corresponding IDs
                if (!empty($prerequisite)) {

                    // Split the prerequisite course names by comma
                    $prerequisite_names = explode(',', $prerequisite);

                    // Loop through each prerequisite course name
                    foreach ($prerequisite_names as $p_name) {

                        // Get the prerequisite course object by title
                        $p_obj = get_page_by_title($p_name, OBJECT, 'sfwd-courses');

                        // If the prerequisite course object exists and is of type 'sfwd-courses', add its ID to the array
                        if (!empty($p_obj) && $p_obj->post_type === 'sfwd-courses') {
                            $prerequisite_ids[] = $p_obj->ID;
                        }
                    }

                    // Update the course prerequisite IDs in the metadata array
                    $meta['sfwd-courses_course_prerequisite'] = $prerequisite_ids;
                }

                // Update the course metadata with the new prerequisite IDs
                update_post_meta($id['id'], '_sfwd-courses', $meta);
            }
        }
    }

    new LearnDash_Course_Import_Manager();
}