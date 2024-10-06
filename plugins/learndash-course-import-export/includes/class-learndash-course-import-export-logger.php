<?php

class Learndash_Course_Import_Export_Logger {

	public $is_writable = true;
	private $filename = '';
	private $file = '';

	/**
	 * Get things started
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Get things started
	 * @return void
	 */
	public function init() {

		$upload_dir     = wp_upload_dir( null, false );
		$this->filename = 'ld-cie-debug.log';
		$this->file     = trailingslashit( $upload_dir['basedir'] ) . $this->filename;

		if ( ! is_writeable( $upload_dir['basedir'] ) ) {
			$this->is_writable = false;
		}

	}

	/**
	 * Retrieve the log data
	 *
	 * @since 1.7.15
	 * @return string
	 */
	public function get_log() {
		return $this->get_file();
	}

	/**
	 * Retrieve the file data is written to
	 *
	 * @since 1.7.15
	 * @return string
	 */
	protected function get_file() {

		$file = '';

		if ( @file_exists( $this->file ) ) {

			if ( ! is_writeable( $this->file ) ) {
				$this->is_writable = false;
			}

			$file = @file_get_contents( $this->file );

		} else {

			@file_put_contents( $this->file, '' );
			@chmod( $this->file, 0664 );

		}

		return $file;
	}

	/**
	 * Write the log message
	 *
	 * @since 1.7.15
	 *
	 * @param string $message Message to write to the debug log.
	 * @return void
	 */
	protected function write_to_log( $message ) {
		$file = $this->get_file();
		$file .= $message;

		@file_put_contents( $this->file, $file );
	}

	/**
	 * Write the log message
	 *
	 * @since 1.7.15
	 * @return void
	 */
	public function clear_log() {
		@unlink( $this->file );
	}

	/**
	 * Log message to file
	 *
	 * @since 1.7.15
	 * @since 2.3 An optional `$data` parameter was added.
	 *
	 * @param string      $message Message to write to the debug log.
	 * @param array|mixed $data    Optional. Array of data or other output to send to the log.
	 *                             Default empty array.
	 * @return void
	 */
	public function log( $message, $data = array() ) {
		//[03-May-2021 06:10:21 Asia/Karachi]
		$message = '[' . date( 'd-M-Y H:i:s' ) . ' ' . get_option('timezone_string', 'UTC') . '] ' . $message . "\r\n";

		if ( ! empty( $data ) ) {
			if ( is_array( $data ) ) {
				$data = var_export( $data, true );
			} else {
				ob_start();

				var_dump( $data );

				$data = ob_get_clean();
			}

			$message .= $data;
		}


		$this->write_to_log( $message );
	}

	public function get_filename() {
		return $this->filename;
	}

	public function get_filesize() {
		$wp_filesystem = $this->get_filesystem();
		$size = $wp_filesystem->size( $this->file );
		if ( $size > 100000000 ) {
			echo 'huge'; exit;
		}
	}

	/**
	 * Instantiates the WordPress filesystem for use.
	 *
	 * @return object
	 */
	private function get_filesystem() {
		global $wp_filesystem;

		if ( ! defined( 'FS_METHOD' ) ) {
			define( 'FS_METHOD', 'direct' );
		}

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}


}