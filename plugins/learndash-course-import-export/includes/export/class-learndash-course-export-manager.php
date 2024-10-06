<?php

// namespace LDCIE;

if (!defined('ABSPATH')) {
    exit;
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * LearnDash_Course_Export Class.
 */
if (!class_exists('LearnDash_Course_Export_Manager')) {

    class LearnDash_Course_Export_Manager {

        /**
         * Class Constructor
         */
        public function __construct() {
            add_filter('manage_sfwd-courses_posts_columns', [$this, 'course_post_columns']);
            add_action('manage_posts_custom_column', [$this, 'course_post_columns_data'], 10, 2);
            add_action('admin_init', [$this, 'export_course_from_link']);
            add_filter('bulk_actions-edit-sfwd-courses', [$this, 'ldcie_register_export_bulk_actions']);
            add_filter('handle_bulk_actions-edit-sfwd-courses', [$this, 'ldcie_export_bulk_action_handler'], 10, 3);

            // Retrieve and assign the 'ldcie_settings' option to $this->settings.
            $this->plugin_settings = get_option('__ldcie_plugin_global_settings', array());
        }

        /**
         * Log an event for importing a post of a specific type.
         *
         * @param string $post_type The post type being imported.
         * @param int    $post_id   The ID of the imported post.
         */
        public function log_event($post_type, $post_id) {
            // Get the title of the post being imported.
            $post = get_post($post_id);
            $post_title = $post->post_title;

            // Prepare the log message.
            $log_title = sprintf(__('Exporting %s', 'learndash-course-import-export'), ucfirst($post_type));
            $log_message = sprintf(__('Exporting %s: %s', 'learndash-course-import-export'), ucfirst($post_type), $post_title);

            // Log the event using LearnDash_Course_Import_Export_WP_Logging::add method.
            LearnDash_Course_Import_Export_WP_Logging::add(
                    $log_title, // Log title.
                    $log_message, // Log message.
                    $post_id, // Post parent.
                    'event'       // Log type.
            );
        }

        /**
         * Add Export column
         *
         * @param $columns
         * @return array
         */
        public function course_post_columns($columns) {
            if (isset($_GET['post_type']) && ( $_GET['post_type'] == 'sfwd-courses' )) {
                $new_columns = array(
                    'export_as_xls' => __('Export as XLS', 'learndash-course-import-export'),
                    'export_as_xlsx' => __('Export as XLSX', 'learndash-course-import-export')
                );
                return array_merge($columns, $new_columns);
            }
            return $columns;
        }

        /**
         * Add export button to Courses
         *
         * @param $column
         * @param $post_id
         */
        public function course_post_columns_data($column, $post_id) {
            if (isset($_GET['post_type']) && ( $_GET['post_type'] == 'sfwd-courses' )) {
                switch ($column) {
                    case 'export_as_xls':
                        self::get_export_xls_button($post_id);
                        break;
                    case 'export_as_xlsx':
                        self::get_export_xlsx_button($post_id);
                        break;
                }
            }
        }

        /**
         * Export XLS button HTML
         *
         * @param $post_id
         */
        public function get_export_xls_button($post_id) {
            global $wp, $post;
            $download_url = add_query_arg(array('post_type' => $post->post_type, 'course_export_xls' => true, 'c_post_id' => $post_id), admin_url());
            echo '<a href="' . $download_url . '" class="ldcie-btn">' . __('Export', 'learndash-course-import-export') . '</a>';
        }

        /**
         * Export XLSX button HTML
         *
         * @param $post_id
         */
        public function get_export_xlsx_button($post_id) {
            global $wp, $post;
            $download_url = add_query_arg(array('post_type' => $post->post_type, 'course_export_xlsx' => true, 'c_post_id' => $post_id), admin_url());
            echo '<a href="' . $download_url . '" class="ldcie-btn">' . __('Export', 'learndash-course-import-export') . '</a>';
        }

        /**
         * Add a new action button in bottom bar
         *
         * @param $which
         * @return none
         */
        public function admin_course_list_top_bar_button($which) {
            global $typenow;
            if ('sfwd-courses' === $typenow && 'bottom' === $which) {
                echo '<div class="alignleft actions"><a href="admin.php?page=ldcie-quiz-import" class="button action">' . __('Import Courses', 'learndash-course-import-export') . '</a></div>';
            }
        }

        /**
         * Add a new action in bulk export
         *
         * @param $bulk_actions
         * @return array
         */
        public function ldcie_register_export_bulk_actions($bulk_actions) {
            if (isset($_GET['post_type']) && ( $_GET['post_type'] == 'sfwd-courses' )) {
                $bulk_actions['ldcie_bulk_export_xls'] = __('Export course as XLS', 'learndash-course-import-export');
                $bulk_actions['ldcie_bulk_export_xlsx'] = __('Export course as XLSX', 'learndash-course-import-export');
                return $bulk_actions;
            }
            return $bulk_actions;
        }

        /**
         * Handle bulk export action for courses.
         *
         * @param string $redirect_to The URL to redirect to after the action.
         * @param string $doaction The action being performed.
         * @param array $post_ids The IDs of the posts to be exported.
         * @return string Updated URL to redirect to after the action.
         */
        public function ldcie_export_bulk_action_handler($redirect_to, $doaction, $post_ids) {
            // Check if the action is not related to bulk export.
            if ($doaction !== 'ldcie_bulk_export_xls' && $doaction !== 'ldcie_bulk_export_xlsx') {
                return $redirect_to;
            }

            // Perform the appropriate export action.
            if ($doaction == 'ldcie_bulk_export_xls') {
                $this->export_course('xls', $post_ids);
            } elseif ($doaction == 'ldcie_bulk_export_xlsx') {
                $this->export_course('xlsx', $post_ids);
            }

            // Update the redirect URL with export information.
            $redirect_to = add_query_arg('bulk_courses_export', count($post_ids), $redirect_to);
            return $redirect_to;
        }

        /**
         * Handle course export based on query parameters.
         */
        public function export_course_from_link() {
            if (isset($_REQUEST['course_export_xls']) && $_REQUEST['course_export_xls'] == '1' && isset($_REQUEST['c_post_id'])) {
                $c_post_id = intval($_REQUEST['c_post_id']);
                $this->export_course('xls', array($c_post_id));
            } elseif (isset($_REQUEST['course_export_xlsx']) && $_REQUEST['course_export_xlsx'] == '1' && isset($_REQUEST['c_post_id'])) {
                $c_post_id = intval($_REQUEST['c_post_id']);
                $this->export_course('xlsx', array($c_post_id));
            }
        }

        /**
         * Get categories associated with a post.
         *
         * @param int    $post_id   The ID of the post.
         * @param string $taxonomy  The taxonomy to retrieve categories from.
         * @return array|string  An array of category names or an empty string.
         */
        public function get_post_categories($post_id, $taxonomy) {
            global $wpdb;

            $term_relationship = $wpdb->prefix . 'term_relationships';
            $term_taxonomy = $wpdb->prefix . 'term_taxonomy';

            // Retrieve term IDs associated with the post.
            $term_ids = $wpdb->get_col(
                    $wpdb->prepare(
                            "SELECT term_taxonomy_id
				FROM {$term_relationship}
				WHERE object_id = %d",
                            $post_id
                    )
            );

            if (empty($term_ids)) {
                return '';
            }

            // Retrieve term names based on term IDs.
            $term_names = Learndash_Course_Import_Export_Helper::ld_course_import_export_category_format_term_ids($term_ids, $taxonomy);

            return $term_names;
        }

        private function get_course_data($ids) {

            $header_row_courses = array(
                'post_type' => __('Type', 'learndash-course-import-export'),
                'course_id' => __('Course ID', 'learndash-course-import-export'),
                'category' => __('Category', 'learndash-course-import-export'),
                'tag' => __('Tag', 'learndash-course-import-export'),
                'course_title' => __('Title', 'learndash-course-import-export'),
                'course_content' => __('Content', 'learndash-course-import-export'),
                'course_image' => __('Course Image', 'learndash-course-import-export'),
                'course_sections' => __('Course Sections', 'learndash-course-import-export'),
            );

            $course_meta_keys = Learndash_Course_Import_Export_Helper::get_course_meta_keys();
            $elementer_meta_keys = $this->getElementorKeys();
            $merged_data = array_merge($header_row_courses, $course_meta_keys, $elementer_meta_keys);
            $data_rows_courses[] = $merged_data;

            foreach ($ids as $i => $course_id) {
                // Retrieve post object
                $post = get_post($course_id);
                $course_section = !empty(get_post_meta($course_id, 'course_sections', true)) ? get_post_meta($course_id, 'course_sections', true) : '';

                // Get course data
                $course_data = $this->get_learndash_course_price_format($course_id);
                $course_material = $this->get_learndash_course_material($course_id);
                $course_meta = get_post_meta($course_id, '_sfwd-courses', true);
                $elementor_meta_arr = $this->export_elementor_meta($course_id);
                $ld_post = 'sfwd-courses_';

                // Create an array to hold course-specific data
                $rows_courses_data = array(
                    'post_type' => learndash_get_post_type_key('sfwd-courses'),
                    'course_id' => $course_id,
                    'category' => $this->get_post_categories($course_id, 'ld_course_category'),
                    'tag' => $this->get_post_categories($course_id, 'ld_course_tag'),
                    'course_title' => $post->post_title,
                    'course_content' => $post->post_content,
                    'course_image' => get_the_post_thumbnail_url($course_id, 'post-thumbnail'),
                    'course_sections' => $course_section
                        // 'course_type' => $course_data,
                );

                // Get Course meta keys
                $rows_courses_meta_data = Learndash_Course_Import_Export_Helper::get_course_settings();

                // Extract and sanitize course-specific metadata
                $rows_courses_general_data = array();
                foreach ($rows_courses_meta_data as $key => $value) {
                    $meta_key = $ld_post . $value;
                    if (isset($course_meta[$meta_key])) {
                        if ('course_prerequisite' === $value) {
                            $rows_courses_general_data[$value] = $this->check_if_prerequisite($course_meta[$meta_key]);
                        } else {
                            $rows_courses_general_data[$value] = $this->sanitize_keys($course_meta[$meta_key]);
                        }
                    } else {
                        $rows_courses_general_data[$value] = '';
                    }
                }

                if ('1' === $this->plugin_settings['ldcie_wp_log']) {
                    $this->log_event('course', $course_id);
                }

                // Combine data and metadata into the final array

                $data_rows_courses[] = array_merge($rows_courses_data, $rows_courses_general_data, $elementor_meta_arr);
            }


            // Return Course data that will be exported
            return $data_rows_courses;
        }

        function export_elementor_meta($post_id) {
            if (!function_exists('is_plugin_active')) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            global $wpdb;
            $new_arr = [];
            if (is_plugin_active('elementor/elementor.php')) {
                $data = $wpdb->get_results(" select * from {$wpdb->prefix}postmeta where post_id='$post_id' and meta_key like '%_elementor%'");
                $arr = [];

                foreach ($data as $key => $value) {

                    $arr[$value->meta_key] = $value->meta_value;
                }

                $keys = $this->getElementorKeys();

                foreach ($keys as $key => $value) {
                    $new_arr[] = $arr[$key];
                }
            }

            return $new_arr;
        }

        function elementormeta_decode($string) {
            $rows = explode("<!-elementormeta_row>", $string);
            foreach ($rows as $key => $value) {
                $cols = explode("<!-elementormeta_value>", $value);
            }
        }

        private function get_lesson_data($lesson_ids, $lesson_course_names) {
            $header_row_lessons = array(
                'post_type' => __('Type', 'learndash-course-import-export'),
                'lesson_id' => __('Lesson ID', 'learndash-course-import-export'),
                'category' => __('Category', 'learndash-course-import-export'),
                'tag' => __('Tag', 'learndash-course-import-export'),
                'lesson_title' => __('Title', 'learndash-course-import-export'),
                'lesson_content' => __('Content', 'learndash-course-import-export'),
                'lesson_image' => __('lesson Image', 'learndash-course-import-export'),
                'course_title' => __('Course', 'learndash-course-import-export'),
                'shared_course' => __('Shared Course', 'learndash-course-import-export'),
            );

            $lesson_meta = Learndash_Course_Import_Export_Helper::get_lesson_meta_keys();
            $elementer_meta_keys = $this->getElementorKeys();
            $merged_data = array_merge($header_row_lessons, $lesson_meta, $elementer_meta_keys);
            $data_rows_lessons[] = $merged_data;

            foreach ($lesson_ids as $key => $lesson_id) {

                $lesson_post = get_post($lesson_id);

                $lesson_data = get_post_meta($lesson_id, '_sfwd-lessons', true);

                $ld_post = 'sfwd-lessons_';

                $shared_course = '';
                if (learndash_is_course_shared_steps_enabled()) {
                    $shared_course = isset($lesson_course_names[$lesson_id]) ? $lesson_course_names[$lesson_id] : '';
                }

                $course_id = isset($lesson_data['sfwd-lessons_course']) ? $lesson_data['sfwd-lessons_course'] : '';
                
                if (!empty($course_id)) {
                    $course_post = get_post($course_id);
                    $course_title = $course_post->post_title;
                } else {
                    $course_title = $shared_course;
                }
                $elementor_meta_arr = $this->export_elementor_meta($lesson_id);
                $rows_lessons_data = array(
                    'post_type' => learndash_get_post_type_key('sfwd-lessons'),
                    'lesson_id' => $lesson_id,
                    'category' => $this->get_post_categories($lesson_id, 'ld_lesson_category'),
                    'tag' => $this->get_post_categories($lesson_id, 'ld_lesson_tag'),
                    'lesson_title' => $lesson_post->post_title,
                    'lesson_content' => $lesson_post->post_content,
                    'lesson_image' => get_the_post_thumbnail_url($lesson_post->ID, 'post-thumbnail'),
                    'course_title' => $course_title,
                    'shared_course' => learndash_is_course_shared_steps_enabled() ? $shared_course : '',
                );

                // Get Lesson meta keys
                $rows_lessons_meta_data = Learndash_Course_Import_Export_Helper::get_lesson_settings();

                // Extract and sanitize lesson-specific metadata
                $rows_lessons_general_data = array();
                foreach ($rows_lessons_meta_data as $key => $value) {
                    $meta_key = $ld_post . $value;
                    if (isset($lesson_data[$meta_key])) {
                        $rows_lessons_general_data[$value] = $this->sanitize_keys($lesson_data[$meta_key]);
                    } else {
                        $rows_lessons_general_data[$value] = '';
                    }
                }

                if ('1' === $this->plugin_settings['ldcie_wp_log']) {
                    $this->log_event('lesson', $lesson_id);
                }

                $data_rows_lessons[] = array_merge($rows_lessons_data, $rows_lessons_general_data, $elementor_meta_arr);
            }
            return $data_rows_lessons;
        }

        function getElementorKeys() {
            if (!function_exists('is_plugin_active')) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }
            $elementer_meta_keys = [];
            if (is_plugin_active('elementor/elementor.php')) {
                $elementer_meta_keys = [
                    '_elementor_edit_mode' => __('_elementor_edit_mode', 'learndash-course-import-export'),
                    '_elementor_template_type' => __('_elementor_template_type', 'learndash-course-import-export'),
                    '_elementor_version' => __('_elementor_version', 'learndash-course-import-export'),
                    '_elementor_pro_version' => __('_elementor_pro_version', 'learndash-course-import-export'),
                    '_wp_page_template' => __('_wp_page_template', 'learndash-course-import-export'),
                    '_elementor_data' => __('_elementor_data', 'learndash-course-import-export'),
                    '_elementor_page_assets' => __('_elementor_page_assets', 'learndash-course-import-export'),
                    '_elementor_controls_usage' => __('_elementor_controls_usage', 'learndash-course-import-export'),
                ];
            }
            return $elementer_meta_keys;
        }

        private function get_topic_data($topic_ids, $topic_course_names, $topic_lesson_names) {

            $header_row_topics = array(
                'post_type' => __('Type', 'learndash-course-import-export'),
                'topic_id' => __('Topic ID', 'learndash-course-import-export'),
                'category' => __('Category', 'learndash-course-import-export'),
                'tag' => __('Tag', 'learndash-course-import-export'),
                'topic_title' => __('Title', 'learndash-course-import-export'),
                'topic_content' => __('Content', 'learndash-course-import-export'),
                'topic_image' => __('Topic Image', 'learndash-course-import-export'),
                'course_title' => __('Course', 'learndash-course-import-export'),
                'lesson_title' => __('Lesson', 'learndash-course-import-export'),
                'shared_course' => __('Shared Course', 'learndash-course-import-export'),
                'shared_lesson' => __('Shared Lesson', 'learndash-course-import-export'),
            );

            $topic_meta = Learndash_Course_Import_Export_Helper::get_topic_meta_keys();
            $elementer_meta_keys = $this->getElementorKeys();
            $merged_data = array_merge($header_row_topics, $topic_meta, $elementer_meta_keys);
            $data_rows_topics[] = $merged_data;

            foreach ($topic_ids as $key => $topic_id) {

                $topic_post = get_post($topic_id);
                $topic_title = $topic_post->post_title;
                $topic_data = get_post_meta($topic_id, '_sfwd-topic', true);
                $ld_post = 'sfwd-topic_';

                if (learndash_is_course_shared_steps_enabled()) {
                    $shared_course_title = isset($topic_course_names[$topic_id]) ? $topic_course_names[$topic_id] : '';
                    $shared_lesson_title = isset($topic_lesson_names[$topic_id]) ? $topic_lesson_names[$topic_id] : '';
                }

                // $course_title = isset( $topic_data['sfwd-topic_course'] ) ? get_the_title($topic_data['sfwd-topic_course']) : '';
                // $lesson_title = isset( $topic_data['sfwd-topic_lesson'] ) ? get_the_title($topic_data['sfwd-topic_lesson']) : '';

                $course_id = isset($topic_data['sfwd-topic_course']) ? $topic_data['sfwd-topic_course'] : '';
                if (!empty($course_id)) {
                    $course_post = get_post($course_id);
                    $course_title = $course_post->post_title;
                } else {
                    $course_title = '';
                }

                $lesson_id = isset($topic_data['sfwd-topic_lesson']) ? $topic_data['sfwd-topic_lesson'] : '';
                if (!empty($lesson_id)) {
                    $lesson_post = get_post($lesson_id);
                    $lesson_title = $lesson_post->post_title;
                } else {
                    $lesson_title = '';
                }

                $elementor_meta_arr = $this->export_elementor_meta($topic_id);

                $rows_topics_data = array(
                    'post_type' => learndash_get_post_type_key('sfwd-topic'),
                    'topic_id' => $topic_id,
                    'category' => $this->get_post_categories($topic_id, 'ld_topic_category'),
                    'tag' => $this->get_post_categories($topic_id, 'ld_topic_tag'),
                    'topic_title' => $topic_title,
                    'topic_content' => $topic_post->post_content,
                    'topic_image' => get_the_post_thumbnail_url($topic_post->ID, 'post-thumbnail'),
                    'course_title' => $course_title,
                    'lesson_title' => $lesson_title,
                    'shared_course' => learndash_is_course_shared_steps_enabled() ? $shared_course_title : '',
                    'shared_lesson' => learndash_is_course_shared_steps_enabled() ? $shared_lesson_title : '',
                );

                // Get Lesson meta keys
                $rows_topics_meta_data = Learndash_Course_Import_Export_Helper::get_topic_settings();

                // Extract and sanitize topic-specific metadata
                $rows_topics_general_data = array();
                foreach ($rows_topics_meta_data as $key => $value) {
                    $meta_key = $ld_post . $value;
                    if (isset($topic_data[$meta_key])) {
                        $rows_topics_general_data[$value] = $this->sanitize_keys($topic_data[$meta_key]);
                    } else {
                        $rows_topics_general_data[$value] = '';
                    }
                }

                if ('1' === $this->plugin_settings['ldcie_wp_log']) {
                    $this->log_event('topic', $topic_id);
                }

                $data_rows_topics[] = array_merge($rows_topics_data, $rows_topics_general_data, $elementor_meta_arr);
            }
            return $data_rows_topics;
        }

        /**
         * Export quiz in XLS/XLSX format
         */
        public function export_course($exp_type, $ids) {
            if (!isset($exp_type) || !isset($ids)) {
                return;
            }

            // Create a PHPExcel object
            $spreadsheet = new Spreadsheet();

            // Set active sheet
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setTitle(addslashes('Courses'));

            $data_rows_courses = array();
            $data_rows_lessons = array();
            $data_rows_topics = array();

            // Create sheets for Lessons and Topics
            $lesson_sheet = new Worksheet($spreadsheet, addslashes('Lessons'));
            $spreadsheet->addSheet($lesson_sheet);

            $topic_sheet = new Worksheet($spreadsheet, addslashes('Topics'));
            $spreadsheet->addSheet($topic_sheet);

            $lesson_course_names = [];
            $topic_course_names = [];
            $topic_lesson_names = [];

            // Determine whether shared steps are enabled
            if (learndash_is_course_shared_steps_enabled()) {
                $shared_post_data = $this->get_shared_lesson_ids($ids);
                $lesson_ids = $shared_post_data['lesson_ids'];

                $topic_ids = $shared_post_data['topic_ids'];
                $lesson_course_names = $shared_post_data['lesson_course_names'];

                $topic_course_names = $shared_post_data['topic_course_names'];
                $topic_lesson_names = $shared_post_data['topic_lesson_names'];
            } else {
                $lesson_ids = $this->get_course_lesson_ids($ids);

                $topic_ids = $this->get_course_lesson_topic_ids($ids);
            }

            // Retrieve data for Courses, Lessons, and Topics
            if (count($data_rows_courses) == 0) {
                $data_rows_courses = $this->get_course_data($ids);
            }

            if (count($data_rows_lessons) == 0) {
                $data_rows_lessons = $this->get_lesson_data($lesson_ids, $lesson_course_names);
            }

            if (count($data_rows_topics) == 0) {
                $data_rows_topics = $this->get_topic_data($topic_ids, $topic_course_names, $topic_lesson_names);
            }

            // Set data in the sheets
            $spreadsheet->setActiveSheetIndexByName('Courses');
            $spreadsheet->getActiveSheet()->fromArray($data_rows_courses);
            // Set each column size to auto
            for ($i = 'A'; $i != $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
            }

            $spreadsheet->setActiveSheetIndexByName('Lessons');
            $spreadsheet->getActiveSheet()->fromArray($data_rows_lessons);
            // Set each column size to auto
            for ($i = 'A'; $i != $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
            }

            $spreadsheet->setActiveSheetIndexByName('Topics');
            $spreadsheet->getActiveSheet()->fromArray($data_rows_topics);

            // Set each column size to auto
            for ($i = 'A'; $i != $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
                $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
            }

            // Save the workbook as a file
            $filename = 'course-' . date('Y-m-d h-i-s') . '.' . $exp_type;

            if (trim($exp_type) == 'xlsx') {
                $content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            } else {
                $content_type = 'application/vnd.ms-excel';
            }

            header('Content-Type: ' . $content_type);
            header(sprintf('Content-Disposition: attachment; filename="%s"', $filename));
            header('Cache-Control: max-age=0');

            // Create a writer based on the export type
            if (trim($exp_type) == 'xlsx') {
                $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $objWriter->setOffice2003Compatibility(true);
            } else {
                $objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
            }

            // Update exports total
            $exports_total = get_option('ld_course_exports_total');
            if (intval($exports_total) == 0 || empty($exports_total)) {
                $exports_total = 1;
            } else {
                $exports_total += count($ids);
            }

            update_option('ld_course_exports_total', $exports_total);

            ob_clean();
            $objWriter->save('php://output');
            exit;
        }

        /**
         * Retrieve lesson and topic IDs for given course IDs.
         *
         * @param array $course_ids Array of course IDs.
         * @return array Associative array containing lesson and topic IDs, along with their corresponding course, lesson, and topic names.
         */
        function get_lesson_and_topic_ids($course_ids): array {
            $lesson_ids = [];
            $topic_ids = [];
            $lesson_course_names = [];
            $topic_course_names = [];
            $topic_lesson_names = [];

            // Iterate through each course ID.
            foreach ($course_ids as $course_id) {
                $data = get_post_meta($course_id, 'ld_course_steps', true);

                if (isset($data['steps']['h']['sfwd-lessons'])) {
                    // Iterate through each lesson within the course.
                    foreach ($data['steps']['h']['sfwd-lessons'] as $lesson_id => $lesson_data) {
                        $lesson_ids[] = $lesson_id;

                        // Get the title of the course.
                        $course_post = get_post($course_id);
                        $lesson_course_names[$lesson_id] = $course_post->post_title;

                        if (isset($lesson_data['sfwd-topic'])) {
                            // Iterate through each topic within the lesson.
                            foreach ($lesson_data['sfwd-topic'] as $topic_id => $topic_data) {
                                $topic_ids[] = $topic_id;

                                // Get the title of the course and lesson.
                                $topic_course_names[$topic_id] = $course_post->post_title;
                                $lesson_post = get_post($lesson_id);
                                $topic_lesson_names[$topic_id] = $lesson_post->post_title;
                            }
                        }
                    }
                }
            }

            return [
                'lesson_ids' => $lesson_ids,
                'topic_ids' => $topic_ids,
                'lesson_course_names' => $lesson_course_names,
                'topic_course_names' => $topic_course_names,
                'topic_lesson_names' => $topic_lesson_names,
            ];
        }

        /**
         * Get an array of lesson IDs associated with a course.
         *
         * @param int $course_id The ID of the course.
         *
         * @return array The array of lesson IDs.
         */
        private function get_course_lesson_ids($course_id) {
            $lesson_ids = array();
            $lesson_data = array();

            $args = array(
                'post_type' => 'sfwd-lessons',
                'post_status' => array('draft', 'pending', 'publish'),
                'numberposts' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => 'course_id',
                        'value' => $course_id,
                        'compare' => 'IN',
                    ),
                ),
            );

            $lesson_data = get_posts($args);

            foreach ($lesson_data as $key => $data) {
                $lesson_ids[] = $data;
            }

            return $lesson_ids;
        }

        /**
         * Get an array of lesson IDs associated with multiple course IDs.
         *
         * @param array $course_id The array of course IDs.
         *
         * @return array The array of lesson IDs.
         */
        private function get_course_builder_lesson_ids($course_id) {
            $lesson_ids = array();
            $lesson_data = array();

            foreach ($course_id as $id) {
                $args = array(
                    'post_type' => 'sfwd-lessons',
                    'post_status' => array('draft', 'pending', 'publish'),
                    'numberposts' => -1,
                    'orderby' => 'ID',
                    'order' => 'ASC',
                    'fields' => 'ids',
                    'meta_query' => array(
                        array(
                            'key' => 'ld_course_' . $id,
                            'value' => $id,
                            'compare' => '=',
                        ),
                    ),
                );

                $temp = get_posts($args);
                $lesson_data = array_merge($lesson_data, $temp);
            }

            foreach ($lesson_data as $key => $data) {
                $lesson_ids[] = $data;
            }

            return $lesson_ids;
        }

        /**
         * Get an array of topic IDs associated with multiple course IDs.
         *
         * @param array $course_id The array of course IDs.
         *
         * @return array The array of topic IDs.
         */
        private function get_course_builder_topic_ids($course_id) {
            $topic_ids = array();
            $topic_data = array();

            foreach ($course_id as $id) {
                $args = array(
                    'post_type' => 'sfwd-topic',
                    'post_status' => array('draft', 'pending', 'publish'),
                    'numberposts' => -1,
                    'orderby' => 'ID',
                    'order' => 'ASC',
                    'fields' => 'ids',
                    'meta_query' => array(
                        array(
                            'key' => 'ld_course_' . $id,
                            'value' => $id,
                            'compare' => '=',
                        ),
                    ),
                );

                $temp = get_posts($args);
                $topic_data = array_merge($topic_data, $temp);
            }

            foreach ($topic_data as $key => $data) {
                $topic_ids[] = $data;
            }

            return $topic_ids;
        }

        /**
         * Get an array of topic IDs associated with a course ID.
         *
         * @param int $course_id The ID of the course.
         *
         * @return array The array of topic IDs.
         */
        private function get_course_lesson_topic_ids($course_id) {
            $topic_ids = array();
            $topic_data = array();
            $lesson_ids = $this->get_course_lesson_ids($course_id);

            $args = array(
                'post_type' => 'sfwd-topic',
                'post_status' => array('draft', 'pending', 'publish'),
                'numberposts' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'fields' => 'ids',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'course_id',
                        'value' => $course_id,
                        'compare' => 'IN',
                    ),
                    array(
                        'key' => 'lesson_id',
                        'value' => $lesson_ids,
                        'compare' => 'IN',
                    ),
                ),
            );

            $topic_data = get_posts($args);

            foreach ($topic_data as $key => $data) {
                $topic_ids[] = $data;
            }

            return $topic_ids;
        }

        /**
         * Retrieve the shared lesson IDs across multiple courses.
         *
         * @param array $course_ids Array of course IDs.
         * @return array $lesson_ids Array of shared lesson IDs.
         */
        private function get_shared_lesson_ids($course_ids) {
            $lesson_ids = array(); // Initialize an array to store shared lesson IDs.
            $topic_ids = array(); // Initialize an array to store shared lesson IDs.

            $lesson_course_names = array();
            $topic_course_names = array();
            $topic_lesson_names = array();

            // Loop through each course ID to retrieve lessons.
            foreach ($course_ids as $course_id) {  
                // Retrieve lessons for the current course and store them in the $lesson_posts array.
                $lesson_posts = learndash_course_get_lessons($course_id, array('return_type' => 'WP_Post', 'per_page' => 0,));
                if (!empty($lesson_posts)) {
                    $course_post = get_post($course_id);
                    $course_title = $course_post->post_title;
                    // Loop through each lesson post and extract the lesson ID.
                    foreach ($lesson_posts as $lesson_post) {
                        $lesson_id = $lesson_post->ID;
                        $lesson_ids[] = $lesson_id;
                        $lesson_course_names[$lesson_id] = $course_title;
                        $topic_posts = learndash_course_get_topics($course_id, $lesson_id, array('return_type' => 'WP_Post', 'per_page' => 0,));

                        foreach ($topic_posts as $topic_post) {
                            $topic_id = $topic_post->ID;
                            $topic_ids[] = $topic_id;
                            $lesson_post = get_post($lesson_id);
                            $lesson_title = $lesson_post->post_title;
                            $topic_course_names[$topic_id] = $course_title;
                            $topic_lesson_names[$topic_id] = $lesson_title;
                        }
                    }
                }
            }

            // Use array_unique to remove duplicate entries.
            $lesson_ids = !empty($lesson_ids) ? array_unique($lesson_ids) : $lesson_ids;
            $topic_ids = !empty($topic_ids) ? array_unique($topic_ids) : $topic_ids;
            
            // $lesson_course_names = !empty($lesson_course_names) ? array_unique($lesson_course_names) : $lesson_course_names;
            // $topic_course_names = !empty($topic_course_names) ? array_unique($topic_course_names) : $topic_course_names;
            // $topic_lesson_names = !empty($topic_lesson_names) ? array_unique($topic_lesson_names) : $topic_lesson_names;

            return [
                'lesson_ids' => $lesson_ids,
                'topic_ids' => $topic_ids,
                'lesson_course_names' => $lesson_course_names,
                'topic_course_names' => $topic_course_names,
                'topic_lesson_names' => $topic_lesson_names,
            ];
        }

        /**
         * Get the price format for a LearnDash course.
         *
         * @param int $course_id The ID of the course.
         *
         * @return string The price format.
         */
        private function get_learndash_course_price_format($course_id) {
            $data = get_post_meta($course_id, '_sfwd-courses', true);
            $billing_period = get_post_meta($course_id, 'course_price_billing_p3', true);
            $billing_period_type = get_post_meta($course_id, 'course_price_billing_t3', true);
            $type = isset($data['sfwd-courses_course_price_type']) ? $data['sfwd-courses_course_price_type'] : '';
            $type_str = '';

            $price = isset($data['sfwd-courses_course_price']) ? $data['sfwd-courses_course_price'] : 0;
            $button_url = isset($data['sfwd-courses_custom_button_url']) ? $data['sfwd-courses_custom_button_url'] : '';

            if ($type === 'open' || $type === 'free') {
                return $type;
            } elseif ($type === 'paynow') {
                $type_str = $type . '|' . $price;
                return $type_str;
            } elseif ($type === 'subscribe') {
                $type_str = 'recurring |' . $price . '|' . $billing_period . '|' . $billing_period_type;
                return $type_str;
            } elseif ($type === 'closed') {
                $type_str = $type . '|' . $price . '|' . $button_url;
                return $type_str;
            } else {
                return 'free';
            }
        }

        /**
         * Get the course material for a LearnDash course.
         *
         * @param int $course_id The ID of the course.
         *
         * @return string The course material.
         */
        private function get_learndash_course_material($course_id) {
            $data = get_post_meta($course_id, '_sfwd-courses', true);
            $material = '';

            if (isset($data['sfwd-courses_course_materials_enabled']) && $data['sfwd-courses_course_materials_enabled'] === 'on') {
                $material = isset($data['sfwd-courses_course_materials']) ? html_entity_decode($data['sfwd-courses_course_materials'], ENT_QUOTES, 'UTF-8') : '';
            }

            return $material;
        }

        /**
         * Check if a value is an array and convert it to a comma-separated string if necessary.
         *
         * @param mixed $value The value to check.
         *
         * @return string The converted string value.
         */
        private function check_if_array($value) {
            if (isset($value)) {
                if (is_array($value)) {
                    $string_value = implode(',', array_map('sanitize_text_field', $value));
                } else {
                    $string_value = sanitize_text_field($value);
                }

                return $string_value;
            }

            return '';
        }

        /**
         * Sanitize keys by converting arrays to comma-separated strings or using sanitize_text_field().
         *
         * @param mixed $value The value to sanitize.
         *
         * @return string The sanitized string value.
         */
        private function sanitize_keys($value) {
            if (isset($value)) {
                if (is_array($value)) {
                    $string_value = implode(',', array_map('sanitize_text_field', $value));
                } else {
                    $string_value = sanitize_text_field($value);
                }

                return $string_value;
            }

            return '';
        }

        /**
         * Check if a value is an array of prerequisite IDs and retrieve their titles as a comma-separated string.
         *
         * @param mixed $value The value to check.
         *
         * @return string The comma-separated string of prerequisite titles.
         */
        private function check_if_prerequisite($value) {
            $titles = [];

            if (isset($value) && is_array($value)) {
                foreach ($value as $id) {
                    $post = get_post(absint($id));
                    $titles[] = sanitize_text_field($post->post_title);
                }
            }

            return implode(',', $titles);
        }
    }

    new LearnDash_Course_Export_Manager();
}