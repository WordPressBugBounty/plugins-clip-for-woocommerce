<?php
/**
 * Main Onboarding Class
 *
 * @package Ecomerciar\Clip\Onboarding
 */

namespace Ecomerciar\Clip\Onboarding;

use Ecomerciar\Clip\Helper\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Main Onboarding Class
 */
class Main {


	/**
	 * Register Onboarding Page
	 */
	public static function register_onboarding_page() {
		add_submenu_page(
			'woocommerce',
			__( 'Clip', 'clip' ),
			__( 'Clip', 'clip' ),
			'manage_woocommerce',
			'wc-clip-onboarding',
			array( __CLASS__, 'content' )
		);
		return true;
	}

	/**
	 * Get content
	 */
	public static function content() {
		$data     = array();
		$site_url = get_site_url();
		$nonce    = wp_create_nonce( \Clip::GATEWAY_ID );

		$current_language = get_bloginfo( 'language' );
		$language_slug    = '/en/';
		if ( 0 === strpos( $current_language, 'es' ) ) {
			$language_slug = '/es/';
		}

		$front_url = \Clip::CLIP_ONBOARDING[ \Clip::CLIP_ENVIRONMENT ] . $language_slug;

		$front_url = add_query_arg( 'ecommerce', 'woo', $front_url );
		$front_url = add_query_arg( 'wp-nonce', $nonce, $front_url );
		$front_url = add_query_arg( 'wp-base-url', $site_url, $front_url );

		$data['settings_url']     = $front_url;
		$data['woo_settings_url'] = esc_url( get_admin_url( null, 'admin.php?page=wc-settings&tab=checkout&section=wc_clip' ) );

		helper::get_template_part( 'page', 'onboarding', $data );
		return true;
	}
}
