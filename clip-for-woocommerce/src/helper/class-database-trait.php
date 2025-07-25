<?php
/**
 * Class DatabaseTrait
 *
 * @package  Ecomerciar\Clip\Helper\DatabaseTrait
 */

namespace Ecomerciar\Clip\Helper;

/**
 * Database Trait
 */
trait DatabaseTrait {

	/**
	 * Find an order id by itemmeta value
	 *
	 * @param string $meta_key Defines Key to looking for orders.
	 * @param string $meta_value Defines Values to looking for orders.
	 *
	 * @return int|false
	 */
	public static function find_order_by_itemmeta_value(
		string $meta_key,
		string $meta_value
	) {

		$args = array(
			'meta_key'     => $meta_key, /* phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key */
			'meta_value'   => $meta_value, /* phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value */
			'meta_compare' => '=',
			'return'       => 'ids',
		);

		$orders = wc_get_orders( $args );

		if ( ! empty( $orders ) ) {
			return (int) $orders[0];
		}

		return $orders;
	}
}
