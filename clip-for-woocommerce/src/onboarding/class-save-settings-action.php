<?php
/**
 * Main SaveSettingsAction Class
 *
 * @package Ecomerciar\Clip\Onboarding
 */

namespace Ecomerciar\Clip\Onboarding;

use Ecomerciar\Clip\Helper\Helper;

/**
 * Main SaveSettingsAction Class
 */
class SaveSettingsAction {

	/**
	 * Run Action
	 *
	 * @return boolean
	 */
	public static function run() {

		if ( isset( $_POST['wpClipNonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpClipNonce'] ) ), \Clip::GATEWAY_ID ) ) {
				return false;
			}
		}
		if ( isset( $_POST['apiKey'] ) ) {
			Helper::set_option( 'api_key', sanitize_text_field( wp_unslash( $_POST['apiKey'] ) ) );
		}
		if ( isset( $_POST['apiSecret'] ) ) {
			Helper::set_option( 'api_secret', sanitize_text_field( wp_unslash( $_POST['apiSecret'] ) ) );
		}

		// Validate Credentials.
		if ( false === Helper::validate_credentials() ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook received invalid credentials.', 'clip' )
			);
			return false;
		}

		return true;
	}

	/**
	 * Validates Post parameters for Ajax Request
	 *
	 * @return bool/string
	 */
	public static function validate_ajax_request() {
		$error_cd = '';

		if ( ! isset( $_POST['wpClipNonce'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook received without nonce.', 'clip' )
			);
			$error_cd = 'missing nonce';
		} elseif ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpClipNonce'] ) ), \Clip::GATEWAY_ID ) ) {
				Helper::log(
					__FUNCTION__ .
						__( '- Webhook received with invalid nonce.', 'clip' )
				);
				$error_cd = 'nonce';
		}
		if ( ! isset( $_POST['apiKey'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook received without api_key.', 'clip' )
			);
			$error_cd = 'missing apiKey';
		}
		if ( ! isset( $_POST['apiSecret'] ) ) {
			Helper::log(
				__FUNCTION__ .
					__( '- Webhook received without api_secret.', 'clip' )
			);
			$error_cd = 'missing apiSecret';
		}

		Helper::log( $_POST );

		if ( ! empty( $error_cd ) ) {
			return $error_cd;
		}

		return true;
	}

	/**
	 * Ajax Callback
	 */
	public static function ajax_callback_wp() {
		Helper::log(
			__FUNCTION__ .
				__( '- Webhook received for saving credentials', 'clip' )
		);
		$ret_validate = static::validate_ajax_request();
		if ( true !== $ret_validate ) {
			if ( defined( 'TEST_CLIP_RUNNING' ) && TEST_CLIP_RUNNING ) {
				return false;
			} else {
				wp_send_json_error( $ret_validate );
			}
		}

		$ret = static::run();
		if ( $ret ) {
			if ( defined( 'TEST_CLIP_RUNNING' ) && TEST_CLIP_RUNNING ) {
				return true;
			} else {
				wp_send_json_success( $ret );
			}
		} elseif ( defined( 'TEST_CLIP_RUNNING' ) && TEST_CLIP_RUNNING ) {
				return false;
		} else {
			$res = __( 'WooCommerce Clip Webhook not valid.', 'clip' );
			wp_send_json_error( $res );
		}
		return false;
	}
}
