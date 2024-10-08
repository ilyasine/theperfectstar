<?php
/**
 * LearnDash LD30 Theme Register.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Templates\LD30
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'LearnDash_Theme_Register' ) && ! class_exists( 'LearnDash_Theme_Register_LD30' ) ) {
	/**
	 * Class to create the settings section.
	 *
	 * @since 3.0.0
	 *
	 * @uses LearnDash_Theme_Register
	 */
	class LearnDash_Theme_Register_LD30 extends LearnDash_Theme_Register {
		/**
		 * Theme variation: Classic.
		 *
		 * @since 4.16.0
		 *
		 * @var string
		 */
		public static string $variation_classic = 'classic';

		/**
		 * Theme variation: Modern.
		 *
		 * @since 4.16.0
		 *
		 * @var string
		 */
		public static string $variation_modern = 'modern';

		/**
		 * Protected constructor for class
		 *
		 * @since 3.0.0
		 *
		 * @return void
		 */
		protected function __construct() {
			$this->theme_key          = 'ld30';
			$this->theme_name         = esc_html__( 'LearnDash 3.0', 'learndash' );
			$this->theme_base_dir     = trailingslashit( LEARNDASH_LMS_PLUGIN_DIR ) . 'themes/' . $this->theme_key;
			$this->theme_base_url     = trailingslashit( LEARNDASH_LMS_PLUGIN_URL ) . 'themes/' . $this->theme_key;
			$this->theme_template_dir = $this->theme_base_dir . '/templates';
			$this->theme_template_url = $this->theme_base_url . '/templates';
			$this->supports_views     = false;
			$this->variations         = [
				static::$variation_classic => _x( 'Classic', 'Theme variation: Classic', 'learndash' ),
				static::$variation_modern  => _x( 'Modern', 'Theme variation: Modern', 'learndash' ),
			];
			$this->default_variation  = static::$variation_classic;

			parent::__construct();
		}

		/**
		 * Load the theme files and assets.
		 *
		 * @since 4.0.0
		 *
		 * @return void
		 */
		public function load_theme() {
			include_once trailingslashit( $this->get_theme_base_dir() ) . 'includes/helpers.php';
		}

		/**
		 * Load the theme settings sections.
		 *
		 * @since 4.0.0
		 *
		 * @return void
		 */
		public function load_settings_sections() {
			include_once trailingslashit( $this->get_theme_base_dir() ) . 'includes/class-ld-settings-section-theme-ld30.php';
		}

		/**
		 * Returns an array of theme keys that inherit settings from this theme.
		 *
		 * @since 4.6.0
		 *
		 * @return string[]
		 */
		public function get_themes_inheriting_settings(): array {
			return array( 'breezy' );
		}
	}
}

add_action(
	'learndash_themes_init',
	function() {
		LearnDash_Theme_Register_LD30::add_theme_instance( 'ld30' );
	}
);
