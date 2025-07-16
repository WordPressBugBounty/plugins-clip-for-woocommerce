<?php
/**
 * Class ClipSdk
 *
 * @package  Ecomerciar\Clip\Helper\ClipSdk
 */

namespace Ecomerciar\Clip\Sdk;

use Ecomerciar\Clip\Api\ClipApi;
use Ecomerciar\Clip\Helper\Helper;

/**
 * Main Class Clip Sdk.
 */
class ClipSdk {

	/**
	 * Defines Clip API Key
	 *
	 * @var string $api_key
	 */
	private string $api_key;
	/**
	 * Defines Clip API Secret
	 *
	 * @var string $api_secret API Secret;
	 */
	private string $api_secret;
	/**
	 * Defines Clip API Token
	 *
	 * @var string $api_token API Token;
	 */
	private string $api_token;
	/**
	 * Defines Debug flag
	 *
	 * @var bool $debug Debug flag ;
	 */
	private bool $debug;

	/**
	 * Defines Clip API instance
	 *
	 * @var ClipApi $api API instance;
	 */
	private ClipApi $api;

	const JSON = 'application/json';

	/**
	 * Constructor.
	 *
	 * @param string  $api_key Clip API Key.
	 * @param string  $api_secret Clip API Secret.
	 * @param boolean $debug Debug Switch.
	 */
	public function __construct(
		string $api_key,
		string $api_secret,
		bool $debug = false
	) {
		$this->api_key    = $api_key;
		$this->api_secret = $api_secret;
		$this->api        = new ClipApi(
			array(
				'api_key'    => $api_key,
				'api_secret' => $api_secret,
				'debug'      => $debug,
			)
		);
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$this->api_token = 'Basic ' . base64_encode( $api_key . ':' . $api_secret );
		$this->debug     = $debug;
	}



	/**
	 * Validate_receipt
	 *
	 * @return bool
	 */
	public function validate_receipt() {
		try {
			$res = $this->api->get(
				'/payments/receipt-no/5mUV5Dt',
				array(),
				array(
					'accept'        => self::JSON,
					'content-type'  => self::JSON,
					'Authorization' => $this->api_token,
				),
				true
			);

		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		if ( ! empty( $this->handle_response( $res, __FUNCTION__ )['query'] ) ) {
			return true;
		}
		Helper::set_option( 'api_key', '' );
		Helper::set_option( 'api_secret', '' );
		return false;
	}

	/**
	 * Create dummy deposit just to validate credentias (only first time)
	 *
	 * @return array
	 */
	public function request_first_deposit() {

		$data_to_send = array(
			'amount'               => floatval( number_format( floatval( 3 ), 2, '.', '' ) ),
			'currency'             => 'MXN',
			'purchase_description' => 'Authenticate woocommerce',
			'redirection_url'      => array(
				'success' => get_site_url(),
				'error'   => get_site_url(),
				'default' => get_site_url(),
			),
			'metadata'             => array(
				'me_reference_id' => 'authenticate woocommerce',
				'customer_info'   => array(
					'name'  => 'Alejandro Lee',
					'email' => 'buyer@hotmail.com',
					'phone' => 5520686868,
				),
				'source'          => 'woocommerce',
			),
			'override_settings'    => array(
				'payment_method' => array( 'CASH', 'CARD' ),
			),
			'webhook_url'          => get_site_url() . '/wc-api/wc-clip',
		);

		try {
			$res = $this->api->post(
				'/v2/checkout',
				$data_to_send,
				array(
					'content-type'  => self::JSON,
					'accept'        => self::JSON,
					'Authorization' => $this->api_token,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Get payment data
	 *
	 * @param string $payment_request_id Clip Payment Intention.
	 *
	 * @return array
	 */
	public function get_payment_data( $payment_request_id ) {
		try {
			$res = $this->api->get(
				'/v2/checkout/' . $payment_request_id,
				array(),
				array(
					'content-type'  => self::JSON,
					'accept'        => self::JSON,
					'Authorization' => $this->api_token,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Request Refund
	 *
	 * @param string $id ID of the order.
	 * @param float  $amount Amount to refund.
	 * @param string $reason Reason for the refund.
	 *
	 * @return array
	 */
	public function request_refund( string $id, float $amount, string $reason = '' ) {
		$order        = wc_get_order( $id );
		$payment_id   = $order->get_meta( \Clip::META_CLIP_RECEIPT_NO );
		$data_to_send = array(
			'reference' => array(
				'type' => 'receipt',
				'id'   => $payment_id,
			),
			'amount'    => floatval( number_format( floatval( $amount ), 2, '.', '' ) ),
			'reason'    => $reason,
		);
		try {
			$res = $this->api->post(
				'/refunds/',
				$data_to_send,
				array(
					'content-type'  => self::JSON,
					'accept'        => self::JSON,
					'Authorization' => $this->api_token,
				),
				true // -GW
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return array();
		}
		return $this->handle_response( $res, __FUNCTION__ );
	}

	/**
	 * Obtener datos para el depósito
	 *
	 * @param int $order_id ID para WC Order.
	 *
	 * @return array
	 */
	public function get_deposit_data( $order_id ) {
		$order = wc_get_order( $order_id );

		$option = get_option( 'woocommerce_wc_clip_settings', array() );

		$clip_override = ( isset( $option['wc_clip_payment_override'] ) && ! empty( $option['wc_clip_payment_override'] ) )
		? $option['wc_clip_payment_override']
		: 'no';

		$clip_tips = ( isset( $option['wc_clip_payment_tips'] ) && ! empty( $option['wc_clip_payment_tips'] ) )
			? $option['wc_clip_payment_tips']
			: 'no';

		$payment_options = Helper::get_option( 'wc_clip_payment_options', false );
		$expiration      = Helper::get_option( 'wc_clip_expiration_hours', 72 );

		$current_datetime = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		$new_datetime     = $current_datetime->modify( "+$expiration hours" );
		$expires_at       = $new_datetime->format( 'Y-m-d\TH:i:s\Z' );

		switch ( $payment_options ) {
			case 'CASH':
				$payment_options = array( 'CASH' );
				break;
			case 'CARD':
				$payment_options = array( 'CARD' );
				break;
			default:
				$payment_options = array( 'CASH', 'CARD' );
				break;
		}

		$billing_states  = WC()->countries->get_states( $order->get_billing_country() );
		$billing_state   = ! empty( $billing_states[ $order->get_billing_state() ] ) ? $billing_states[ $order->get_billing_state() ] : '';
		$billing_country = ! empty( WC()->countries->countries[ $order->get_billing_country() ] ) ? WC()->countries->countries[ $order->get_billing_country() ] : '';

		$shipping_states  = WC()->countries->get_states( $order->get_shipping_country() );
		$shipping_state   = ! empty( $shipping_states[ $order->get_shipping_state() ] ) ? $shipping_states[ $order->get_shipping_state() ] : '';
		$shipping_country = ! empty( WC()->countries->countries[ $order->get_shipping_country() ] ) ? WC()->countries->countries[ $order->get_shipping_country() ] : '';

		// Preparar los datos para enviar.
		$data = array(
			'amount'               => floatval( number_format( floatval( $order->get_total() ), 2, '.', '' ) ),
			'currency'             => $order->get_currency(),
			'purchase_description' => __( 'Compra en tienda', 'clip' ),
			'redirection_url'      => array(
				'success' => $order->get_checkout_order_received_url(),
				'error'   => $order->get_checkout_order_received_url(),
				'default' => $order->get_checkout_order_received_url(),
			),
			'expires_at'           => $expires_at,
			'metadata'             => array(
				'me_reference_id'  => 'WC' . $order_id . '-' . wp_generate_uuid4(),
				'customer_info'    => array(
					'name'  => $order->get_billing_first_name(),
					'email' => $order->get_billing_email(),
					'phone' => $order->get_billing_phone(),
				),
				'source'           => 'woocommerce',
				'billing_address'  => array(
					'street'          => ! empty( $order->get_billing_address_1() ) ? $order->get_billing_address_1() : '',
					'outdoor_number'  => '',
					'interior_number' => '',
					'locality'        => '',
					'city'            => ! empty( $order->get_billing_city() ) ? $order->get_billing_city() : '',
					'state'           => $billing_state,
					'zip_code'        => ! empty( $order->get_billing_postcode() ) ? $order->get_billing_postcode() : '',
					'country'         => $billing_country,
					'reference'       => '',
					'between_streets' => '',
					'floor'           => '',
				),
				'shipping_address' => array(
					'street'          => ! empty( $order->get_shipping_address_1() ) ? $order->get_shipping_address_1() : '',
					'outdoor_number'  => '',
					'interior_number' => '',
					'locality'        => '',
					'city'            => ! empty( $order->get_shipping_city() ) ? $order->get_shipping_city() : '',
					'state'           => $shipping_state,
					'zip_code'        => ! empty( $order->get_shipping_postcode() ) ? $order->get_shipping_postcode() : '',
					'country'         => $shipping_country,
					'reference'       => '',
					'between_streets' => '',
					'floor'           => '',
				),
			),
		);

		if ( 'yes' === $clip_override ) {

			$custom_payment_options = $this->payment_options( $order->get_total(), $order->get_currency() );
			if ( null !== $custom_payment_options ) {
				$data['custom_payment_options'] = $custom_payment_options;
			}
			if ( 'yes' === $clip_tips ) {
				$data['override_settings']['tip_enabled'] = true;
			}
		}

		$data['webhook_url'] = get_site_url() . '/wc-api/wc-clip';

		$data = $this->remove_empty_fields( $data );

		Helper::log( 'data' );
		Helper::log( $data );

		return $data;
	}

	/**
	 * Retrieves custom payment options based on WooCommerce settings.
	 *
	 * @param float  $order_total    The total amount of the order.
	 * @param string $order_currency The currency of the order.
	 *
	 * @return array|null Custom payment options or null if no options are available.
	 */
	public static function payment_options( $order_total, $order_currency ) {

		$option      = get_option( 'woocommerce_wc_clip_settings', array() );
		$clip_brands = isset( $option['wc_clip_payment_card_brands'] ) && is_array( $option['wc_clip_payment_card_brands'] )
		? $option['wc_clip_payment_card_brands']
		: array();

		$clip_payments = isset( $option['wc_clip_payment_type'] ) && is_array( $option['wc_clip_payment_type'] )
			? $option['wc_clip_payment_type']
			: array();

		$clip_installments_enabled = isset( $option['wc_clip_payment_installments_enabled'] ) && ! empty( $option['wc_clip_payment_installments_enabled'] )
			? $option['wc_clip_payment_installments_enabled']
			: 'no';

		$clip_installments = isset( $option['wc_clip_payment_installments'] ) && is_array( $option['wc_clip_payment_installments'] )
			? $option['wc_clip_payment_installments']
			: array();

		$custom_payment_options = array();

		if ( ! empty( $clip_brands ) && is_array( $clip_brands ) ) {
			$enabled_brands = array();
			foreach ( $clip_brands as $brand => $status ) {
				if ( 'yes' === $status ) {
					$enabled_brands[] = $brand;
				}
			}
			$custom_payment_options['payment_method_brands'] = $enabled_brands;
		}

		if ( ! empty( $clip_payments ) && is_array( $clip_payments ) ) {
			$enabled_types = array();

			foreach ( $clip_payments as $type => $status ) {
				if ( 'yes' === $status && 'cards' !== $type ) {
					$enabled_types[] = $type;
				}
			}

			$custom_payment_options['payment_method_types'] = $enabled_types;
		}

		$installments_msi = array();

		if ( 'yes' === $clip_installments_enabled && ! empty( $clip_installments ) ) {
			foreach ( $clip_installments as $installment => $details ) {
				$is_enabled = $details['enabled'] ?? 'no';
				$min_amount = isset( $details['value'] ) ? (float) $details['value'] : 0;

				if ( 'yes' === $is_enabled && $order_total >= $min_amount ) {
					$installments_msi[] = (int) $installment;
				}
			}
		}

		if ( ! empty( $installments_msi ) ) {
			$custom_payment_options['installments_msi'] = $installments_msi;
		}

		if ( 'USD' === $order_currency ) {
			$custom_payment_options['international_enabled'] = true;
		}
		return ! empty( $custom_payment_options ) ? $custom_payment_options : null;
	}


	/**
	 * Elimina campos vacíos de las direcciones
	 *
	 * @param array $data Los datos a limpiar.
	 * @return array
	 */
	private function remove_empty_fields( $data ) {
		foreach ( array( 'billing_address', 'shipping_address' ) as $address_key ) {
			if ( isset( $data[ $address_key ] ) ) {
				foreach ( $data[ $address_key ] as $field => $value ) {
					if ( empty( $value ) ) {
						unset( $data[ $address_key ][ $field ] );
					}
				}
			}
		}
		return $data;
	}

	/**
	 * Crear depósito
	 *
	 * @param int $order_id ID para WC Order.
	 *
	 * @return array
	 */
	public function request_deposit( $order_id ) {
		try {
			$data_to_send = $this->get_deposit_data( $order_id );

			$res = $this->api->post(
				'/v2/checkout',
				$data_to_send,
				array(
					'content-type'  => self::JSON,
					'accept'        => self::JSON,
					'Authorization' => $this->api_token,
				)
			);
		} catch ( \Exception $e ) {
			Helper::log_error( __FUNCTION__ . ': ' . $e->getMessage() );
			return false;
		}

		return $this->handle_response( $res, __FUNCTION__ );
	}


	/**
	 * Handle Response
	 *
	 * @param array  $response Response data.
	 * @param string $function_name Function function is calling from.
	 *
	 * @return array
	 */
	protected function handle_response(
		$response = array(),
		string $function_name = ''
	) {
		if ( 'request_first_deposit' === $function_name ) {
			return ( isset( $response['status'] ) && 'CHECKOUT_CREATED' === $response['status'] );
		}
		return $response;
	}
}
