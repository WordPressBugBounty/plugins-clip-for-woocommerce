<?php
/**
 * Class CountryCurrencyTrait
 *
 * @package  Ecomerciar\Clip\Helper\DebugTrait
 */

namespace Ecomerciar\Clip\Helper;

/**
 * Database Trait
 */
trait CountryCurrencyTrait {

	/**
	 * Return the current transaction currency
	 * - Supports WOOCS currency switcher
	 */
	public static function get_currency() {
		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			$currency = strtoupper( $WOOCS->storage->get_val( 'woocs_current_currency' ) );
		} else {
			$currency = get_woocommerce_currency();
		}
		return $currency;
	}

	/**
	 * Get supported currencies for Clip payment gateway
	 *
	 * @return array List of supported currencies
	 */
	public static function get_clip_supported_currencies() {
		return array(
			'MXN', // Mexican Peso
			'USD', // US Dollar
		);
	}

	/**
	 * Check if currency is supported by Clip payment gateway
	 *
	 * @param string $currency Currency Code.
	 * @return bool True if currency is supported, false otherwise
	 */
	public static function is_clip_currency_supported( $currency = null ) {
		if ( null === $currency ) {
			$currency = self::get_currency();
		}
		
		return in_array( strtoupper( $currency ), self::get_clip_supported_currencies(), true );
	}

}
