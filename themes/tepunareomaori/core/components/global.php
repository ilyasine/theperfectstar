<?php 

/* 
* *** Global and helper functions & hooks ***
*/

add_action('after_setup_theme', 'TPRM_theme_textdomain_languages' );
add_filter('body_class', 'TPRM_body_class');
//add_action('wp_footer', 'add_button_to_footer');
//add_action('login_footer', 'add_button_to_footer' );

/**
 * Sets up theme for translation
 *
 * @since V2
 */

function TPRM_theme_textdomain_languages(){

    load_theme_textdomain( 'tprm-theme', get_stylesheet_directory() . '/languages' );
  
}

/**
 * Add tepunareomaori-theme class to body
 *
 * @since V2
 */

function TPRM_body_class($classes) {
    $classes[] = 'tepunareomaori-theme';
    return $classes;
}

/**
 * @since V2
 *
 * Add hard refresh button to the footer
 */

 function add_button_to_footer() {

	// show only for plateform pages
	if( ! TPRM_is_public() ) {

        $button_markup = '<button id="tprm-refresh"';

        if (is_user_logged_in()) {
            // Add attributes if user is logged in
            $button_markup .= ' data-balloon-pos="left"';
            $button_markup .= ' data-balloon="' . esc_attr__('Click here if you have a problem', 'tprm-theme') . '"';
        }
        
        $button_markup .= '>
            <i class="bb-icon-l bb-icon-radio"></i>
        </button>';

        echo wp_kses(
            $button_markup,
            array(
                'button' => array(
                    'id' => array(),
                    'class' => array(),
                    'data-balloon-pos' => array(),
                    'data-balloon' => array(),
                ),
                'i' => array(
                    'class' => array(),
                ),
            )
        );

        $current_wpml_lang = apply_filters('wpml_current_language', NULL);

        $browser = '';
        $label_browser = '';
        
        // Get the user agent string
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        // Detect the user's browser
        if (strpos($user_agent, 'Firefox') !== false) {
            $browser = 'firefox';
            $label_browser = 'Mozilla Firefox';
        } elseif (strpos($user_agent, 'Edg') !== false) {
            $browser = 'edge';
            $label_browser = 'Microsoft Edge';
        } elseif (strpos($user_agent, 'Chrome') !== false) {
            $browser = 'chrome';
            $label_browser = 'Google Chrome';
        } elseif (strpos($user_agent, 'Safari') !== false) {
            $browser = 'safari';
            $label_browser = 'Safari';
        }

        $images_dir = TPRM_IMG_ABS_PATH . 'clear_cache/' . $browser . '/' . $current_wpml_lang;

        $image_paths = glob($images_dir . '/*.jpg');
        
        // HTML template
        $html_template = '
        <a class="TPRM_refresh popup-modal-login popup-refresh" style="display:none;" href="%s">%s</a>
        <div id="TPRM_refresh" class="mfp-hide tprm-refresh-popup login-popup bb-modal">
            <div class="header-container">
                <span class="bb-icon-l bb-icon-exclamation-triangle"></span>
                <h1 class="TPRM_troubleshooting">%s</h1>
            </div>
            <div class="tprm-refresh-popup-content">
                <div class="popup-scroll">                  
                    <p>%s</p>
                    <p>%s</p>
                    <ul class="steps-list">';
        $step = 1;
        // Loop through each image path and add it to the HTML template
        foreach ($image_paths as $image_path) {
            // Convert absolute file path to web URL
            $html_template .= '<li class="step_item">';
            $image_url = str_replace(TPRM_IMG_ABS_PATH, TPRM_IMG_PATH, $image_path);
            $html_template .= '<img src="' . $image_url . '" alt=""></li>';
            //$step++;
        }
        
        $html_template .= '</ul>
                </div>
                <div class="button-container">
                    <button type="button" class="button popup-troubleshooting-dismiss" id="continue-btn">%s</button>
                </div>
            </div>
            
        </div>';
        
        
        // Other variables
        $href = '#TPRM_refresh';
        $link_text = __('tepunareomaori troubleshooting', 'tprm-theme');
        $alert_title = __('tepunareomaori troubleshooting', 'tprm-theme');
        $popup_content_1 = sprintf(
            __('If you have any trouble navigating tepunareomaori on %s browser', 'tprm-theme'),
            $label_browser
        );
        $popup_content_2 = __('Please try the following steps as described in the pictures below', 'tprm-theme');
        $continue_button_text = __('I understand', 'tprm-theme');
        
        
        // Generate the output
        $troubleshooting_output = sprintf(
            $html_template,
            $href,
            $link_text,
            $alert_title,
            $popup_content_1,
            $popup_content_2,
            //$popup_content_3, // Make sure $popup_content_3 is defined
            $continue_button_text,
        );
        
        echo $troubleshooting_output;

	}	
	
}



/**
 * 
 * @global function to check if running a mobile version
 * 
 * @since V2
 */

 function TPRM_is_mobile() {
    // Check if the user agent indicates a mobile device
    if (wp_is_mobile()) {
        return true;
    }

    // Check if the "mobile" query parameter is set in the URL
    if (isset($_GET['mobile']) && $_GET['mobile'] === 'true') {
        return true;
    }

    // Check if the "responsive" cookie is set (for testing in desktop browser inspect tool)
    if (isset($_COOKIE['responsive']) && $_COOKIE['responsive'] === 'true') {
        return true;
    }

   // Check the width of the screen using JavaScript
   $script_markup = '<script>
    if (window.innerWidth <= 480) {
        document.cookie = "responsive=true; path=/;";
    }
    </script>';

    echo wp_kses(
        $script_markup,
        array(
            'script' => array(
                'type' => array(),
            ),
        )
    );

    // If none of the conditions are met, assume it's not a mobile device
    return false;
}

/**
 * 
 * @global function to get all schools ( parent groups and exclude demo)
 * 
 * @since V2
 * @return array 
 */

function get_tprm_schools() {
    global $wpdb;
    $schools_sql = "
        SELECT g.*
        FROM {$wpdb->prefix}bp_groups g
        INNER JOIN {$wpdb->prefix}term_relationships tr ON g.id = tr.object_id
        INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->prefix}terms t ON tt.term_id = t.term_id
        WHERE tt.taxonomy = 'bp_group_type' AND t.slug = 'tprm-school'
        AND NOT EXISTS (
            SELECT 1
            FROM {$wpdb->prefix}bp_groups_groupmeta gm
            WHERE g.id = gm.group_id
            AND gm.meta_key = 'ecole_demo'
            AND gm.meta_value = 'on'
        );
    ";

    $schools = $wpdb->get_results( $schools_sql, ARRAY_A );

    return $schools;
}

/**
 * 
 * @global function to get all groups having specific type
 * 
 * @since V2
 * @param string group type which we want to get the groups of
 * @return array 
 */

function get_all_groups_of_type($type){
    global $wpdb;
    $groups_sql = "
        SELECT g.* FROM {$wpdb->prefix}bp_groups g
        INNER JOIN {$wpdb->prefix}term_relationships tr ON g.id = tr.object_id
        INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->prefix}terms t ON tt.term_id = t.term_id
        WHERE tt.taxonomy = 'bp_group_type' AND t.slug = '{$type}';
    ";

    $groups = $wpdb->get_results( $groups_sql, ARRAY_A );

    return $groups;
}

/**
 * 
 * @global function to check if a given group id is a demo group
 * 
 * @since V2
 * @param int group id to check
 * @return boolean
 */

function is_demo($group_id){
    //$demo_meta = get_metadata( 'group', $group_id, 'ecole_demo' , true );
    $demo_meta = groups_get_groupmeta( $group_id, 'ecole_demo' );
    if( isset($demo_meta) && $demo_meta == 'on' ){
        return true;
    }else{
        return false;
    }
}