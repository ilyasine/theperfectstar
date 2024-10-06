<?php
/**
 * @package tepunareomaori
 * 
 * The core theme functions and dependencies are located at wp-content/themes/tepunareomaori/core
 * 
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since V1
 */
function TPRM_theme_languages()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   */
  load_theme_textdomain( 'tprm-theme', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'TPRM_theme_languages' );

/****************************** Define constants and global variables ******************************/

require_once 'core/kwf-global-variables.php';

/****************************** Enqueue styles and scripts ******************************/

require_once 'core/kwf-styles-scripts.php';

/****************************** Components ******************************/

require_once 'core/kwf-components.php';