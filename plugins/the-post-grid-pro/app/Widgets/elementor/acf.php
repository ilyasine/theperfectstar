<?php
/**
 * Elementor: ACF Widget.
 *
 * @package RT_TPG_PRO
 */

use Elementor\Controls_Manager;
use RT\ThePostGrid\Helpers\Fns;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor: ACF Widget.
 */
class TPGAdvanceCustomField extends Custom_Widget_Base {

	/**
	 * GridLayout constructor.
	 *
	 * @param  array $data
	 * @param  null  $args
	 *
	 * @throws \Exception
	 */


	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->tpg_name     = esc_html__( 'TPG - Advance Custom Field', 'the-post-grid-pro' );
		$this->tpg_base     = 'tpg-single-acf';
		$this->tpg_icon     = 'eicon-custom tpg-grid-icon'; // .tpg-grid-icon class for just style
		$this->tpg_category = $this->tpg_archive_category;
	}

	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );
			array_push( $style, 'rt-tpg-common' );
			array_push( $style, 'rt-tpg-block' );
		}

		return $style;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'tpg_acf_section',
			[
				'label' => esc_html__( 'TPG Advance Custom Field', 'the-post-grid-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		rtTPGElementorHelper::get_tpg_acf_settings( $this, true );

		$this->add_control(
			'acf_style_hading',
			[
				'label'   => esc_html__( 'ACF Style', 'the-post-grid-pro' ),
				'type'    => \Elementor\Controls_Manager::HEADING,
				'classes' => 'tpg-control-type-heading',
			]
		);

		rtTPGElementorHelper::get_tpg_acf_style( $this, false );

		$this->add_control(
			'acf_spacing',
			[
				'label'              => esc_html__( 'Wrapper Spacing', 'the-post-grid-pro' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => [ 'px' ],
				'selectors'          => [
					'{{WRAPPER}} .tpg-single-post-share' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'vertical',
				'default'            => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'isLinked' => false,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$data             = $this->get_settings();
		$data['show_acf'] = 'show';
		?>
		<div class="tpg-single-post-share">
			<?php Fns::tpg_get_acf_data_elementor( $data, $this->last_post_id, true ); ?>
		</div>
		<?php
	}

}
