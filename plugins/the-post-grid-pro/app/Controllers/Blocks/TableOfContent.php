<?php

namespace RT\ThePostGridPro\Controllers\Blocks;

class TableOfContent {

	private $block_type;

	public function __construct() {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'tpg_postgrid_css_enqueue' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'block_editor_asstets' ] );
		$this->block_type = 'rttpg/tpg-table-of-content';
	}

	public function block_editor_asstets() {
		if ( is_admin() ) {
			wp_enqueue_script( 'rt-tpg-guten-editor' );
		}
	}

	public function tpg_postgrid_css_enqueue() {
		wp_enqueue_script( 'rttpg-pro-blocks-js' );
	}

	/**
	 * Register Block
	 * @return void
	 */
	public function register_blocks() {
		register_block_type( 'rttpg/tpg-table-of-content' );
	}

	public function render_block( $settings, $content ) {
		return $content;
	}


}