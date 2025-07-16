<?php
/**
 * Class SettingsTrait
 *
 * @package  Ecomerciar\Clip\Helper\SettingsTrait
 */

namespace Ecomerciar\Clip\Helper;

/**
 * Settings Trait
 */
trait SettingsTrait {

	/**
	 * Gets a plugin option
	 *
	 * @param string  $key Key value searching for.
	 * @param boolean $default A dafault value in case Key is not founded.
	 *
	 * @return mixed
	 */
	public static function get_option( string $key, $default = false ) {
		return isset( self::get_options()[ $key ] ) &&
			! empty( self::get_options()[ $key ] )
			? self::get_options()[ $key ]
			: $default;
	}

	/**
	 * Get options
	 *
	 * @param string $gateway Gateway Name.
	 *
	 * @return Array
	 */
	public static function get_options( $gateway = 'wc_clip' ) {
		$option = get_option( 'woocommerce_' . $gateway . '_settings' );
		return array(
			'enabled'                      => isset( $option['enabled'] ) ? $option['enabled'] : 'no',
			'title'                        => isset( $option['title'] ) ? $option['title'] : __( 'Pay with Clip', 'clip' ),
			'description'                  => isset( $option['description'] ) ? $option['description'] : __( 'Accept payments using Clip.', 'clip' ),

			'api_key'                      => isset( $option['wc_clip_api_key'] )
				? $option['wc_clip_api_key']
				: '',

			'api_secret'                   => isset( $option['wc_clip_api_secret'] )
				? $option['wc_clip_api_secret']
				: '',

			'wc_clip_payment_options'      => isset( $option['wc_clip_payment_options'] )
			? $option['wc_clip_payment_options']
			: 'ocash',

			'wc_clip_expiration_hours'     => isset( $option['wc_clip_expiration_hours'] )
				? $option['wc_clip_expiration_hours']
				: '',

			'wc_clip_banner_enabled'       => isset( $option['wc_clip_banner_enabled'] )
			? $option['wc_clip_banner_enabled']
			: 'no',

			'debug'                        => isset( $option['wc_clip_log_enabled'] )
				? $option['wc_clip_log_enabled']
				: 'no',

			'payment_override'             => isset( $option['wc_clip_payment_override'] )
				? $option['wc_clip_payment_override']
				: 'no',

			'payment_type'                 => isset( $option['wc_clip_payment_type'] )
				? $option['wc_clip_payment_type']
				: array(
					'card'          => 'yes',
					'credit'        => 'yes',
					'debit'         => 'yes',
					'cash'          => 'yes',
					'bank_transfer' => 'yes',
					'spei'          => 'yes',
				),

			'payment_card_brands'          => isset( $option['wc_clip_payment_card_brands'] )
				? $option['wc_clip_payment_card_brands']
				: array(
					'visa'       => 'yes',
					'mastercard' => 'yes',
					'amex'       => 'yes',
					'carnet'     => 'yes',
					'discover'   => 'yes',
					'diners'     => 'yes',
				),

			'payment_installments_enabled' => isset( $option['wc_clip_payment_installments_enabled'] )
				? $option['wc_clip_payment_installments_enabled']
				: 'yes',

			'payment_installments'         => isset( $option['wc_clip_payment_installments'] )
				? $option['wc_clip_payment_installments']
				: array(
					array(
						'installment' => 3,
						'enabled'     => 'yes',
						'value'       => '',
					),
					array(
						'installment' => 6,
						'enabled'     => 'yes',
						'value'       => '',
					),
					array(
						'installment' => 9,
						'enabled'     => 'yes',
						'value'       => '',
					),
					array(
						'installment' => 12,
						'enabled'     => 'yes',
						'value'       => '',
					),
					array(
						'installment' => 18,
						'enabled'     => 'yes',
						'value'       => '',
					),
					array(
						'installment' => 21,
						'enabled'     => 'yes',
						'value'       => '',
					),
					array(
						'installment' => 24,
						'enabled'     => 'yes',
						'value'       => '',
					),
				),
			'payment_tips'                 => isset( $option['wc_clip_payment_tips'] )
				? $option['wc_clip_payment_tips']
				: 'yes',
		);
	}

	/**
	 * Set options
	 *
	 * @param string $key Key value searching for.
	 * @param string $value A value to be setted.
	 * @param string $gateway Gateway Name.
	 */
	public static function set_option( string $key, string $value, string $gateway = 'wc_clip' ) {
		$option                      = get_option( 'woocommerce_' . $gateway . '_settings' );
		$option[ 'wc_clip_' . $key ] = $value;
		update_option( 'woocommerce_' . $gateway . '_settings', $option );
	}
}
