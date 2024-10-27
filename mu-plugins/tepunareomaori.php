<?php

/**
 * Plugin Name: tepunareomaori
 * Plugin URI:  https://tepunareomaori.com
 * Description: Must-Use plugin for tepunareomaori
 * Version:     2.0.0
 * Author:      KWF
 *
 * @package tepunareomaori
 */

/**
 * 
 * @global function to check if user has an english access
 * 
 * @since V2
 */

 function is_en_user($user_id){

	global $blog_id, $wpdb;

	// If $user_id is not provided, use the current user.
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }

    $user_lang = $wpdb->get_blog_prefix($blog_id) . 'lang';

	$user_language = get_user_meta($user_id, $user_lang, true);

	if($user_language === 'en' && ! is_TPRM_admin()){
		return true;
	}

	return false;
}

/**
 * 
 * @global function to check if user has a french access
 * 
 * @since V2
 */

function is_fr_user($user_id){

	global $blog_id, $wpdb;

    // If $user_id is not provided, use the current user.
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }

    $user_lang = $wpdb->get_blog_prefix($blog_id) . 'lang';

	$user_language = get_user_meta($user_id, $user_lang, true);

	if($user_language === 'fr' && ! is_TPRM_admin()){
		return true;
	}

	return false;
}

/**
 * 
 * @global function to check if user has access to the two languages
 * 
 * @since V2
 */

function is_bilingual_user($user_id){

	global $blog_id, $wpdb;

    // If $user_id is not provided, use the current user.
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }

    $user_lang = $wpdb->get_blog_prefix($blog_id) . 'lang';

	$user_language = get_user_meta($user_id, $user_lang, true);

	if($user_language === 'bi' && ! is_TPRM_admin()){
		return true;
	}

	return false;
}


function redirect_after_login($redirect_to, $request, $user) {
  // Ensure $user is a WP_User object
  if (is_wp_error($user)) {
      return $redirect_to;
  }

  $roles = array();
  if (isset($user) && !is_wp_error($user)) {
      $roles = $user->roles;
      if (isset($roles) && is_array($roles)) {
          // Check for admins
          if (in_array('administrator', $roles)) {
              $redirect_to = site_url('/fr/dashboard/');
          } else if (in_array('library', $roles) || in_array('libraries_manager', $roles)) {
              $redirect_to = site_url('/fr/library/');
          } else if (in_array('school_principal', $roles) || in_array('school_leader', $roles) || in_array('school_staff', $roles)) {
              $selected_school_url = get_user_meta($user->ID, 'selected_school_url', true);
              if ($selected_school_url && get_user_school()[0] != get_last_user_school()) {
                  $redirect_to = esc_url($selected_school_url);
              } else {
                  $redirect_to = bp_get_group_permalink(groups_get_group(get_last_user_school()));
              }
          } else {
              $redirect_to = home_url('/members/me/my-course/');
          }
      }
  }

  return $redirect_to;
}

add_filter('login_redirect', 'redirect_after_login', 99, 3);





/**
 * If current user is a super admin
 *
 * @since V2
 */

function is_yskwf(){
	return get_current_user_id() == 2023;	
}

//plugin organizer & super admin

function hide_metabox() {
	if (is_admin() && function_exists('is_yskwf') && ! is_yskwf() ) {
	  $post_types = get_post_types(); // Get all post types
	  foreach ($post_types as $type) { // Loop through each post type
		remove_meta_box('plugin_organizer', $type, 'normal'); // Remove the metabox
	  }
	}
  }
add_action('admin_init', 'hide_metabox');

function hide_menu() {

	// restrict_plugin_menu
	if (is_admin() && function_exists('is_yskwf') && ! is_yskwf()) {
		remove_menu_page('Plugin_Organizer');
	}
	$plugin_urls = array(
		'Plugin_Organizer',
		'PO_global_plugins',
		'PO_search_plugins',
		'PO_pt_plugins',
		'PO_group_and_order_plugins',
	);

	// restrict_plugin_urls
	if (is_admin() && isset($_GET['page']) && in_array($_GET['page'], $plugin_urls)) {
		if (function_exists('is_yskwf') && ! is_yskwf()) {
		wp_die('Sorry, you are not allowed to access this page.');
		}
	}

	// restrict_custom_post_type_and_taxonomy
	if (is_admin() && isset($_SERVER['REQUEST_URI'])) {
		$url = parse_url($_SERVER['REQUEST_URI']);
		if (isset($url['query'])) {
			parse_str($url['query'], $params);
			if (isset($params['post_type']) && $params['post_type'] == 'plugin_filter' && function_exists('is_yskwf') && ! is_yskwf()) {
				wp_die('Sorry, you are not allowed to access this page.');
			}
		}
	}
	
}
add_action('admin_menu', 'hide_menu', 11);

function enable_edit_plugin_filter_cap($allcaps, $caps, $args, $user) {
    // Check if the user is a super admin
    if (isset($user->roles) && is_array($user->roles) && in_array('administrator', $user->roles) && $user->ID === 2023) {
        // Allow the user to edit the 'plugin_filter' post type
        $allcaps['edit_plugin_filter'] = true;
    }

    return $allcaps;
}

add_filter('user_has_cap', 'enable_edit_plugin_filter_cap', 10, 4);

function replace_row_actions( $actions, $user ) {
	global $pagenow;

	if ($pagenow!=='users.php') {
		return $actions;
	}
	// Check if is super admin
	if ( $user->ID == 2023 ) {

	 	$actions = array( 'super_tepunareomaori_admin' => 'Super Admin' );

		if ( isset( $actions['capabilities'] ) ) {
			unset( $actions['capabilities'] );
		}
		if ( isset( $actions['edit-profile'] ) ) {
			unset( $actions['edit-profile'] );
		}
	}
	// Return the modified actions
	return $actions;
  }

add_filter( 'user_row_actions', 'replace_row_actions', 99, 2 );


  // Hide the checkbox for the user with the ID 2023 using JavaScript
function hide_checkbox_for_super_admin() {
	// Check if the current screen is the user list
	if (get_current_screen()->id === 'users') {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var user2023Checkbox = document.querySelector('#user-2023 .check-column input');
                var user2023Label = document.querySelector('#user-2023 .check-column label');

                if (user2023Checkbox && user2023Label) {
                    user2023Checkbox.remove();
                    user2023Label.remove();
                }
            });
        </script>
        <?php
    }
}
add_action( 'admin_footer', 'hide_checkbox_for_super_admin' );


function restrict_super_admin_profile_access() {
   /** @global string $pagenow */
	global $pagenow;

    if ($pagenow && 'user-edit.php' == $pagenow) {
        // Check if a user ID is specified in the URL
        if (isset($_GET['user_id']) && $_GET['user_id'] == 2023) {
            // Redirect users away from the user profile page
            wp_die('You are not allowed to view or access the super admin profile.');
            exit();
        }
    }
}
add_action('admin_init', 'restrict_super_admin_profile_access');


/* 
* *** Access url ***
*/

function TPRM_add_nav_menu_metabox() {
  add_meta_box('kwf-login', __('Login/Logout', 'tepunareomaori'), 'TPRM_nav_menu_metabox', 'nav-menus', 'side', 'default');
}
add_action('admin_head-nav-menus.php', 'TPRM_add_nav_menu_metabox');

function TPRM_nav_menu_metabox($object) {
  global $nav_menu_selected_id;

  $elems = array(
    '#kwflogin#' => __('Log In', 'tepunareomaori'),
    '#kwflogout#' => __('Log Out', 'tepunareomaori'),
    '#kwfloginout#' => __('Log In', 'tepunareomaori').'|'.__('Log Out', 'tepunareomaori')
  );
  
  class kwfLogItems {
    public $db_id = 0;
    public $object = 'kwflog';
    public $object_id;
    public $menu_item_parent = 0;
    public $type = 'custom';
    public $title;
    public $url;
    public $target = '';
    public $attr_title = '';
    public $classes = array();
    public $xfn = '';
  }

  $elems_obj = array();

  foreach($elems as $value => $title) {
    $elems_obj[$title]             		= new kwfLogItems();
    $elems_obj[$title]->object_id		= esc_attr($value);
    $elems_obj[$title]->title			= esc_attr($title);
    $elems_obj[$title]->url			    = esc_attr($value);
  }

  $walker = new Walker_Nav_Menu_Checklist(array());

  ?>
  <div id="login-links" class="loginlinksdiv">
    <div id="tabs-panel-login-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
      <ul id="login-linkschecklist" class="list:login-links categorychecklist form-no-clear">
        <?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $elems_obj), 0, (object) array('walker' => $walker)); ?>
      </ul>
    </div>
    <p class="button-controls">
      <span class="add-to-menu">
        <input type="submit"<?php disabled($nav_menu_selected_id, 0); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu', 'tepunareomaori'); ?>" name="add-login-links-menu-item" id="submit-login-links" />
        <span class="spinner"></span>
      </span>
    </p>
  </div>
  <script>
        jQuery(document).ready(function ($) {
			jQuery( '#menu-to-edit' ).on( 'click', 'a.item-edit', function () {
			var settings = jQuery( this ).closest( '.menu-item-bar' ).next( '.menu-item-settings' );
			var css_class = settings.find( '.edit-menu-item-classes' );
			if ( css_class.val().indexOf( 'kwf-access-nav' ) === 1 ) {
				css_class.attr( 'readonly', 'readonly' );
				settings.find( '.field-url' ).css( 'display', 'none' );
			}
		} );
        });
    </script>
  <?php
}

function TPRM_nav_menu_type_label($menu_item) {
  $elems = array('#kwflogin#', '#kwflogout#', '#kwfloginout#');
  if(isset($menu_item->object, $menu_item->url) && 'custom' == $menu_item->object && in_array($menu_item->url, $elems)) {
    $menu_item->type_label = __('Access Link', 'tepunareomaori');
	$menu_item->classes[] = 'kwf-access-nav';
  }

  return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'TPRM_nav_menu_type_label');

function TPRM_loginout_title($title) {
	$titles = explode('|', $title);

	if(!is_user_logged_in()) {
		return esc_html(isset($titles[0])?$titles[0]:__('Log In', 'tepunareomaori'));
	} else {
		return esc_html(isset($titles[1]) ? $titles[1] : __('Log Out', 'tepunareomaori'));
	}
}

function TPRM_setup_nav_menu_item($item) {
	global $pagenow;

	// Check if we are in the nav-menus.php page or doing an AJAX request
	if($pagenow != 'nav-menus.php' && !defined('DOING_AJAX') && isset($item->url) && strstr($item->url, '#kwf') != '') {
		$login_page_url       =  wp_login_url();
    	$logout_redirect_url  =  home_url();

		// Handle login and logout menu items based on user login status
		switch($item->url) {
			case '#kwflogin#':
				// Only show the login link if the user is not logged in
				if (!is_user_logged_in()) {
					$item->url = $login_page_url;
				} else {
					$item->_invalid = true; // Mark item as invalid if logged in (hide it)
				}
				break;

			case '#kwflogout#':
				// Only show the logout link if the user is logged in
				if (is_user_logged_in()) {
					$item->url = wp_logout_url($logout_redirect_url);
				} else {
					$item->_invalid = true; // Mark item as invalid if not logged in (hide it)
				}
				break;

			default: // Should be #kwfloginout#
				$item->url = (is_user_logged_in()) ? wp_logout_url($logout_redirect_url) : $login_page_url;
				$item->title = TPRM_loginout_title($item->title);
		}
	}

	return $item;
}
add_filter('wp_setup_nav_menu_item', 'TPRM_setup_nav_menu_item');

// l'URL de connexion
add_filter ( 'login_url', 'TPRM_login_url' );
function TPRM_login_url ( $access_url ) {
  $access_url = home_url('/access/');
  return $access_url;
}

// l'URL de déconnexion
add_filter ( 'logout_url', 'TPRM_logout_url' );
function TPRM_logout_url ( $access_url ) {
  $access_url = home_url( '/access?action=logout&_wpnonce=' . wp_create_nonce( 'log-out' ) );
  return $access_url;
}

// Remove logout menu item from header profile menu added by parent theme when BP is disabled.
if ( ! function_exists( 'buddyboss_theme_add_logout_link' ) ) {

	function buddyboss_theme_add_logout_link() {
		if ( ! function_exists( 'bp_is_active' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'header-my-account',
					'menu_id'        => 'header-my-account-menu',
					'container'      => false,
					'fallback_cb'    => '',
					'depth'          => 2,
					'walker'         => new BuddyBoss_SubMenuWrap(),
					'menu_class'     => 'bb-my-account-menu',
				)
			);		
		}
	}

	add_action( 'buddyboss_theme_header_user_menu_items', 'buddyboss_theme_add_logout_link' );
}


