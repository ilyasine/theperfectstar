<?php

namespace RT\ThePostGridPro\Controllers\Api;

class ChatGPT {
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_post_route' ] );
	}

	public function register_post_route() {
		register_rest_route(
			'rttpg/v1',
			'chatgpt',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'chatgpt_callback' ],
				'permission_callback' => function () {
					return true;
				},
			]
		);
	}

	public function chatgpt_callback( $data ) {

		// Get Settings.
		$settings      = get_option( rtTPG()->options['settings'] );
		$api_key       = ! empty( $settings['chatgpt_secret_key'] ) ? $settings['chatgpt_secret_key'] : '';
		$model         = ! empty( $settings['chatgpt_model'] ) ? $settings['chatgpt_model'] : 'gpt-3.5-turbo';
		$response_time = ! empty( $settings['chatgpt_response_time'] ) ? esc_attr( $settings['chatgpt_response_time'] ) : 50;
		$content_limit = ! empty( $settings['chatgpt_max_tokens'] ) ? esc_attr( $settings['chatgpt_max_tokens'] ) : 1200;

		// Get Requested Data.
		$writingStyle  = ! empty( $data['writingStyle'] ) ? sanitize_text_field( $data['writingStyle'] ) : '';
		$language      = ! empty( $data['language'] ) ? sanitize_text_field( $data['language'] ) : '';
		$headingNumber = ! empty( $data['headingNumber'] ) ? sanitize_text_field( $data['headingNumber'] ) : '';
		$headingTag    = ! empty( $data['headingTag'] ) ? sanitize_text_field( $data['headingTag'] ) : '';
		$max_tokens    = $content_limit;

		$send_data   = [
			'status'  => 'ok',
			'content' => '',
		];
		$request_txt = ! empty( $data['request_txt'] ) ? sanitize_text_field( $data['request_txt'] ) : '';

		$direction = [];
		if ( 'html' == $writingStyle ) {
			$request_txt = sprintf( esc_html( 'Write a post content on this topic- %s' ), $request_txt );
			$direction[] = esc_html( 'write everything in html tag, do not add any style attribute' );

			if ( $headingNumber ) {
				$direction[] = sprintf( esc_html( 'and use %s %s html headings for the content' ), $headingNumber, $headingTag );
			}
		}

		if ( $language ) {
			$direction[] = sprintf( esc_html( 'write everything in %s language' ), $language );
		}

		$total_direction = count( $direction );
		foreach ( $direction as $index => $item ) {
			$first_parenthesis = $index == 0 ? '( ' : null;
			$last_parenthesis  = $index == ( $total_direction - 1 ) ? ' )' : null;

			$request_txt .= "$first_parenthesis $item $last_parenthesis";
		}

		$url = 'https://api.openai.com/v1/chat/completions';

		$data = wp_json_encode(
			[
				'max_tokens'  => intval( $max_tokens ), // Adjust this based on your needs.
				'model'       => $model,
				'messages'    => [
					[
						'role'    => 'user',
						'content' => $request_txt,
					],
				],
				'temperature' => 0.7,
			]
		);

		$args = [
			'headers' => [
				'Content-Type'  => 'application/json',
				'Authorization' => "Bearer $api_key",
			],
			'body'    => $data,
			'timeout' => intval( $response_time ), // Set the timeout to 10 seconds.
		];

		$response = wp_safe_remote_post( $url, $args );

		if ( ! $api_key ) {
			$send_data['content'] = '<h3>' . esc_html( 'Please Enter OpenAI API key to [ The Post Grid > Settings > ChatGPT ]' ) . '</h3>';
			$send_data['status']  = 'error';
		} elseif ( is_wp_error( $response ) ) {
			$send_data['status']  = 'error';
			$send_data['content'] = '<h3>' . esc_html( 'Something is wrong...' ) . '</h3>';
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
			if ( isset( $data['error'] ) ) {
				$content             = $data['error']['message'];
				$send_data['status'] = 'error';
			} else {
				$content = $data['choices'][0]['message']['content'];
			}
			if ( 'html' !== $writingStyle ) {
				$content = nl2br( $content );
			}
			$send_data['content'] = $content;
		}

		return rest_ensure_response( $send_data );
	}
}
