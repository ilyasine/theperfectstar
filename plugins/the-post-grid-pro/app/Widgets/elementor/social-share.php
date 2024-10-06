<?php
/**
 * Elementor: Social Share Widget.
 *
 * @package RT_TPG_PRO
 */

use Elementor\Controls_Manager;
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGridPro\Helpers\Functions;

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

/**
 * Elementor: Social Share Widget.
 */
class TPGSocialShare extends Custom_Widget_Base {

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
		$this->tpg_name     = esc_html__( 'TPG - Social Share', 'the-post-grid-pro' );
		$this->tpg_base     = 'tpg-single-social-share';
		$this->tpg_icon     = 'eicon-social-icons tpg-grid-icon'; // .tpg-grid-icon class for just style
		$this->tpg_category = $this->tpg_archive_category;
	}

	public function get_style_depends() {
		$settings = get_option( rtTPG()->options['settings'] );
		$style    = [];

		if ( isset( $settings['tpg_load_script'] ) ) {
			array_push( $style, 'rt-fontawsome' );
			array_push( $style, 'rt-flaticon' );
			array_push( $style, 'rt-tpg-block' );
		}

		return $style;
	}


	protected function register_controls() {
		$this->start_controls_section(
			'tpg_social_share',
			[
				'label' => esc_html__( 'TPG Social Share', 'the-post-grid-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		rtTPGElementorHelper::get_social_share_control( $this );

		$this->end_controls_section();
	}

	protected function render() {
		?>
		<div class="tpg-single-post-share">
		<?php Fns::print_html( Functions::rtShare( $this->last_post_id ), true ); ?>
		</div>
		<?php
	}
}
