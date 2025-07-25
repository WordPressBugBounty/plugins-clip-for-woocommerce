<?php
/**
 * Class DebugTrait
 *
 * @package  Ecomerciar\Clip\Helper\DebugTrait
 */

namespace Ecomerciar\Clip\Helper;

/**
 * Database Trait
 */
trait DebugTrait {

	/**
	 * Log data if debug is enabled
	 *
	 * @param strin $log String to write in Log.
	 */
	public static function log( $log ) {
		if ( self::get_option( 'debug' ) !== 'no' ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				self::log_debug( wp_json_encode( $log, JSON_PRETTY_PRINT ) );
			} else {
				self::log_debug( $log );
			}
		}
	}
}
