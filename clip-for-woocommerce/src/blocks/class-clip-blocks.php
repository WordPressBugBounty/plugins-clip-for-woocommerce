<?php
/**
 * Class ClipBlocks
 *
 * @package  Ecomerciar\Clip\Blocks\ClipBlocks
 */

namespace Ecomerciar\Clip\Blocks;

use Ecomerciar\Clip\Helper\Helper;
use Ecomerciar\Clip\Gateway\WC_Clip;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/**
 * Clip Blocks
 *
 * @extends AbstractPaymentMethodType
 */
final class ClipBlocks extends AbstractPaymentMethodType {

	/**
	 * Gateway
	 *
	 * @var WC_Clip
	 */
	private $gateway;

	/**
	 * Name
	 *
	 * @var string
	 */
	protected $name = 'wc_clip';

	/**
	 * Initialize
	 */
	public function initialize() {
		$this->settings = get_option( 'woocommerce_wc_clip_settings', array() );
		$this->gateway  = new WC_Clip();
	}

	/**
	 * Is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->gateway->is_available();
	}

	/**
	 * Get payment method script handles
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {

		wp_register_script(
			'wc_clip-blocks-integration',
			Helper::get_assets_folder_url() . '/js/checkout-blocks.js',
			array(
				'react',
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
				'wp-i18n',
			),
			'1.0.1',
			true
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc_clip-blocks-integration' );

		}
		return array( 'wc_clip-blocks-integration' );
	}

	/**
	 * Get payment method data
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return array(
			'title'          => $this->gateway->title,
			'description'    => $this->gateway->description,
			'icon_clip'      => $this->gateway->icon,
			'banner_clip'    => $this->gateway->banner_clip,
			'banner_enabled' => $this->gateway->banner_enabled,
		);
	}
}
