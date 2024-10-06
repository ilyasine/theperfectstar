<?php
/**
 * Action Hooks Class.
 *
 * @package RT_TPG_PRO
 */

namespace RT\ThePostGridPro\Controllers\Hooks;

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Action Hooks Class.
 */
class ActionHooks {
	/**
	 * Class constructor
	 */

	public function __construct() {
		add_action( 'tpg_settings_tab_title', [ $this, 'settings_tab_title' ] );
		add_action( 'tpg_settings_tab_content', [ $this, 'settings_tab_content' ] );
		add_action( 'rt_tpg_sc_style_group_field', [ $this, 'sc_style_tab_content_wrap_settings' ], 10 );
		add_action( 'rt_tpg_sc_style_group_field', [ $this, 'sc_style_tab_category_settings' ], 20 );
	}

	/**
	 * Settings tab
	 *
	 * @param string $last_tab Last tab.
	 *
	 * @return void
	 */
	public function settings_tab_title( $last_tab ) {
		printf(
			'<li%s><a href="#plugin-license">%s</a></li>',
			'plugin-license' === $last_tab ? ' class="active"' : '',
			esc_html__( 'Plugin License', 'the-post-grid-pro' )
		);
	}

	/**
	 * Tab content
	 *
	 * @param string $last_tab Last tab.
	 *
	 * @return void
	 */
	public function settings_tab_content( $last_tab ) {
		$html = sprintf( '<div id="plugin-license" class="rt-tab-content"%s>', 'plugin-license' === $last_tab ? ' style="display:block"' : '' );
		$html .= Fns::rtFieldGenerator( Options::rtTPGLicenceField() );
		$html .= '</div>';

		Fns::print_html( $html, true );
	}

	/**
	 * Metabox - Content
	 *
	 * @return void
	 */
	public function sc_style_tab_content_wrap_settings() {
		?>
        <div class="field-holder content-style-wrapper">
            <div class="field-label"><?php esc_html_e( 'Content Wrap', 'the-post-grid-pro' ); ?></div>
            <div class="field">
                <div class="tpg-multiple-field-group">
					<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGStyleContentWrap() ), true ); ?>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Metabox - Category
	 *
	 * @return void
	 */
	public function sc_style_tab_category_settings() {
		?>
        <div class="field-holder category-style-wrapper">
            <div class="field-label"><?php esc_html_e( 'Category', 'the-post-grid-pro' ); ?></div>
            <div class="field">
                <div class="tpg-multiple-field-group">
					<?php Fns::print_html( Fns::rtFieldGenerator( Options::rtTPGStyleCategory() ), true ); ?>
                </div>
            </div>
        </div>
		<?php
	}

}
