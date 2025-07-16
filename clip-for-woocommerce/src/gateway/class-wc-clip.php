<?php
/**
 * Class WC_Clip
 *
 * @package Ecomerciar\Clip\Gateway\WC_Clip
 */

namespace Ecomerciar\Clip\Gateway;

use Ecomerciar\Clip\Helper\Helper;
use Ecomerciar\Clip\Sdk\ClipSdk;

defined( 'ABSPATH' ) || class_exists( '\WC_Payment_Gateway' ) || exit();

/**
 * Main Class Clip Payment.
 */
class WC_Clip extends \WC_Payment_Gateway {

	/**
	 * Instrucciones de pago para el usuario.
	 *
	 * @var string
	 */
	public string $instructions;

	/**
	 * Indica si el banner está habilitado.
	 *
	 * @var string
	 */
	public string $banner_enabled;

	/**
	 * URL del banner de Clip.
	 *
	 * @var string
	 */
	public string $banner_clip;

	/**
	 * Opciones de pago disponibles.
	 *
	 * @var string
	 */
	public string $payment_options;

	/**
	 * Indica si el log está habilitado.
	 *
	 * @var bool
	 */
	public bool $log_enabled;

	/**
	 * API Key de Clip.
	 *
	 * @var string
	 */
	public string $api_key;

	/**
	 * API Secret de Clip.
	 *
	 * @var string
	 */
	public string $api_secret;

	/**
	 * Instancia del SDK de Clip.
	 *
	 * @var ClipSdk
	 */
	public ClipSdk $sdk;

	/**
	 * Indica si el pago debe ser personalizado.
	 *
	 * @var string
	 */
	public string $payment_override;

	/**
	 * Indica si el pago por crédito está habilitado.
	 *
	 * @var array
	 */
	public array $payment_type;

	/**
	 * Payment types default.
	 *
	 * @var array
	 */
	public array $payment_types_default;

	/**
	 * Payment card brands.
	 *
	 * @var array
	 */
	public array $payment_card_brands;

	/**
	 * Payment card brands default.
	 *
	 * @var array
	 */
	public array $payment_card_brands_default;

	/**
	 * Payment tips.
	 *
	 * @var string
	 */
	public string $payment_tips;

	/**
	 * Payment installments.
	 *
	 * @var string
	 */
	public string $payment_installments_enabled;

	/**
	 * Payment installments.
	 *
	 * @var array
	 */
	public array $payment_installments;

	/**
	 * Payment installments default.
	 *
	 * @var array
	 */
	public array $payment_installments_default;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = \Clip::GATEWAY_ID;
		$this->has_fields         = false;
		$this->method_title       = __( 'Clip', 'clip' );
		$this->method_description = __( 'Accept payments using Clip.', 'clip' );

		// Define user set variables.
		$this->title        = __( 'Clip', 'clip' );
		$this->instructions = $this->get_option(
			$this->description,
			$this->method_description
		);
		$this->icon         = Helper::get_assets_folder_url() . '/img/logotype_clip_primary.svg';

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		$this->supports[] = 'products';
		$this->supports[] = 'refunds';

		$this->description                  = __( 'The No. 1 payment platform in Mexico', 'clip' );
		$this->payment_options              = $this->get_option( 'wc_clip_payment_options' );
		$this->log_enabled                  = $this->get_option( 'wc_clip_log_enabled' );
		$this->api_key                      = $this->get_option( 'wc_clip_api_key' );
		$this->api_secret                   = $this->get_option( 'wc_clip_api_secret' );
		$this->sdk                          = new ClipSdk( $this->api_key, $this->api_secret );
		$this->payment_override             = $this->get_option( 'wc_clip_payment_override', 'no' );
		$this->payment_types_default        = array(
			'cards'         => 'no',
			'credit'        => 'no',
			'debit'         => 'no',
			'cash'          => 'no',
			'bank_transfer' => 'no',
		);
		$this->payment_type                 = $this->get_option( 'wc_clip_payment_type', $this->payment_types_default );
		$this->payment_card_brands_default  = array(
			'visa'       => 'no',
			'mastercard' => 'no',
			'amex'       => 'no',
			'carnet'     => 'no',
			'discover'   => 'no',
			'diners'     => 'no',
		);
		$this->payment_card_brands          = $this->get_option( 'wc_clip_payment_card_brands', $this->payment_card_brands_default );
		$this->payment_tips                 = $this->get_option( 'wc_clip_payment_tips', 'yes' );
		$this->payment_installments_enabled = $this->get_option( 'wc_clip_payment_installments_enabled', 'yes' );
		$this->payment_installments_default = array(
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
		);
		$this->payment_installments         = $this->get_option( 'wc_clip_payment_installments', $this->payment_installments_default );

		$this->banner_enabled = $this->get_option( 'wc_clip_banner_enabled' );
		$this->banner_clip    = $this->get_clip_banner_url();

		global $current_section;
		if ( \Clip::GATEWAY_ID === $current_section ) {
			$this->enqueue_settings_css();
			$this->enqueue_settings_js();
		}

		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);
		add_action(
			'woocommerce_thankyou_' . $this->id,
			array(
				$this,
				'thankyou_page',
			)
		);
	}

	/**
	 * Get the banner URL to display based on the enabled payment methods.
	 *
	 * @return string Banner image URL.
	 */
	private function get_clip_banner_url() {
		$clip_types = $this->payment_type;

		// Determine which payment methods are enabled.
		$has_cash          = isset( $clip_types['cash'] ) && 'yes' === $clip_types['cash'];
		$has_card          = isset( $clip_types['cards'] ) && 'yes' === $clip_types['cards'];
		$has_bank_transfer = isset( $clip_types['bank_transfer'] ) && 'yes' === $clip_types['bank_transfer'];

		// Detect current language.
		$current_language = get_bloginfo( 'language' );
		$language_folder  = ( 0 === strpos( $current_language, 'es' ) ) ? 'spanish' : 'english';

		// Check if the user is on a mobile device.
		$is_mobile = wp_is_mobile();

		$banner_map = array(
			array(
				'conditions' => array( $has_cash, $has_card, $has_bank_transfer ),
				'file'       => 'cards_spei_cash.png',
			),
			array(
				'conditions' => array( $has_card, $has_bank_transfer ),
				'file'       => 'cards_spei.png',
			),
			array(
				'conditions' => array( $has_cash, $has_bank_transfer ),
				'file'       => 'spei_cash.png',
			),
			array(
				'conditions' => array( $has_cash, $has_card ),
				'file'       => 'cards_cash.png',
			),
			array(
				'conditions' => array( $has_bank_transfer ),
				'file'       => 'spei.png',
			),
			array(
				'conditions' => array( $has_card ),
				'file'       => 'cards.png',
			),
			array(
				'conditions' => array( $has_cash ),
				'file'       => 'cash.png',
			),
		);

		foreach ( $banner_map as $banner ) {
			$matches = true;

			foreach ( $banner['conditions'] as $condition ) {
				if ( ! $condition ) {
					$matches = false;
					break;
				}
			}

			if ( $matches ) {
				$file = $is_mobile ? str_replace( '.png', '_mobile.png', $banner['file'] ) : $banner['file'];
				return esc_url( Helper::get_assets_folder_url() . "/img/banners/{$language_folder}/{$file}" );
			}
		}

		// Default banner if no conditions matched.
		return esc_url( Helper::get_assets_folder_url() . "/img/banners/{$language_folder}/cards_spei_cash.png" );
	}



	/**
	 * Add fields to pre-select method of payment
	 */
	public function payment_fields() {
		?>
		<style>
			.banner_clip {
				width: 600px !important;
				max-height: 600px !important;
			}
			.p_clip {
				font-size: 15px;
			}
			.woocommerce .wc_payment_method.payment_method_wc_clip label img{
				max-width: 25px;
			}
		</style> 			
		<fieldset>
			<legend>
				<p class="p_clip"><?php echo esc_html( $this->description ); ?> </p>
			</legend>
			<?php if ( 'yes' === $this->banner_enabled ) : ?>
				<img class="banner_clip" src="<?php echo esc_url( $this->banner_clip ); ?>" alt="">
			<?php endif; ?>
		</fieldset>
		<?php
	}


	/**
	 * Output for the order received page.
	 *
	 * @param string $order_id Order Id.
	 */
	public function thankyou_page( $order_id ) {
		// Nothing to add, but required to avoid Warnings.

		Helper::handle_payment( $order_id );
	}

	/**
	 * Enqueue_settings_js
	 */
	private function enqueue_settings_js() {
		?>
		<style>
			.logotype_clip {
				width: 30px;
				height: auto;
				position: relative;
				bottom: -8px;
				border-right: 10px solid transparent;
			}
		</style>
		<?php

		wp_register_script(
			'my-clip-admin-js',
			Helper::get_assets_folder_url() . '/js/admin-settings.js',
			array( 'jquery' ),
			\Clip::VERSION . ( \Clip::CLIP_ENVIRONMENT !== 'prod' ? wp_rand() : '' ),
			true
		);
		wp_enqueue_script( 'my-clip-admin-js' );

		wp_localize_script(
			'my-clip-admin-js',
			'ClipSettings',
			array(
				'logotypeUrl' => esc_url( Helper::get_assets_folder_url() . '/img/logotype_clip_primary.svg' ),
			)
		);
	}

	/**
	 * Initialize Gateway Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = Settings::get_settings();
	}

	/**
	 * Generate payment installments html
	 *
	 * @param string $key Key of the payment installments.
	 * @param array  $settings Settings of the payment installments.
	 *
	 * @return string
	 */
	public function generate_payment_installments_html( $key, $settings ) {
		$html = '';
		$data = array(
			'key'                  => $key,
			'settings'             => $settings,
			'payment_installments' => $this->payment_installments,
			'labels'               => array(
				'3'  => __( '3 months without interest', 'clip' ),
				'6'  => __( '6 months without interest', 'clip' ),
				'9'  => __( '9 months without interest', 'clip' ),
				'12' => __( '12 months without interest', 'clip' ),
				'18' => __( '18 months without interest', 'clip' ),
				'21' => __( '21 months without interest', 'clip' ),
				'24' => __( '24 months without interest', 'clip' ),
			),
		);
		ob_start();
		$html .= Helper::get_template_part( 'setting', 'payment-installments', $data );
		$html .= ob_get_clean();
		return $html;
	}

	/**
	 * Validate payment installments
	 *
	 * @param string $key Key of the payment installments.
	 * @param array  $value Value of the payment installments.
	 *
	 * @return array
	 */
	public function validate_payment_installments_field( $key, $value ) {
		if ( ( is_null( $value ) || ! is_array( $value ) ) ) {
			$value = $this->payment_installments_default;
		} else {
			foreach ( $value as $key => $item ) {
				$value[ $key ]['enabled'] = isset( $item['enabled'] ) ? $item['enabled'] : 'no';
			}
		}
		return $value;
	}

	/**
	 * Generate payment card brands html
	 *
	 * @param string $key Key of the payment card brands.
	 * @param array  $settings Settings of the payment card brands.
	 *
	 * @return string
	 */
	public function generate_payment_card_brands_html( $key, $settings ) {
		$html = '';
		$data = array(
			'key'                 => $key,
			'settings'            => $settings,
			'payment_card_brands' => $this->payment_card_brands,
			'brands'              => array(
				array(
					'value' => 'visa',
					'label' => 'Visa',
					'icon'  => Helper::get_assets_folder_url() . '/img/brands/visa.png',
				),
				array(
					'value' => 'mastercard',
					'label' => 'MasterCard',
					'icon'  => Helper::get_assets_folder_url() . '/img/brands/mastercard.png',
				),
				array(
					'value' => 'amex',
					'label' => 'Amex',
					'icon'  => Helper::get_assets_folder_url() . '/img/brands/amex.png',
				),
				array(
					'value' => 'carnet',
					'label' => 'Carnet',
					'icon'  => Helper::get_assets_folder_url() . '/img/brands/carnet.png',
				),
				array(
					'value' => 'discover',
					'label' => 'Discover',
					'icon'  => Helper::get_assets_folder_url() . '/img/brands/discover.png',
				),
				array(
					'value' => 'diners',
					'label' => 'Diners Club',
					'icon'  => Helper::get_assets_folder_url() . '/img/brands/diners.png',
				),
			),
		);
		ob_start();
		$html .= Helper::get_template_part( 'setting', 'payment-card-brands', $data );
		$html .= ob_get_clean();
		return $html;
	}

	/**
	 * Validate payment card brands
	 *
	 * @param string $key Key of the payment card brands.
	 * @param array  $value Value of the payment card brands.
	 *
	 * @return array
	 */
	public function validate_payment_card_brands_field( $key, $value ) { /*phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed */
		return ( is_null( $value ) || ! is_array( $value ) ) ? $this->payment_card_brands_default : array_merge( $this->payment_card_brands_default, $value );
	}

	/**
	 * Generate payment types html
	 *
	 * @param string $key Key of the payment type.
	 * @param array  $settings Settings of the payment type.
	 *
	 * @return string
	 */
	public function generate_payment_types_html( $key, $settings ) {
		$html = '';
		$data = array(
			'key'          => $key,
			'settings'     => $settings,
			'payment_type' => $this->payment_type,
		);
		ob_start();
		$html .= Helper::get_template_part( 'setting', 'payment-type', $data );
		$html .= ob_get_clean();
		return $html;
	}

	/**
	 * Validate payment types
	 *
	 * @param string $key Key of the payment type.
	 * @param string $value Value of the payment type.
	 *
	 * @return bool
	 */
	public function validate_payment_types_field( $key, $value ) { /*phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed */
		return ( is_null( $value ) || ! is_array( $value ) ) ? $this->payment_types_default : array_merge( $this->payment_types_default, $value );
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id ID of Woo Order.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		$payment_nonce = wp_create_nonce( \Clip::GATEWAY_ID );
		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => add_query_arg( 'clip_nonce', $payment_nonce, add_query_arg( 'clip_cta', true, $order->get_checkout_payment_url( true ) ) ),
		);
	}


	/**
	 * Set if Clip must be available or not
	 *
	 * @param Array $available_gateways Array of Available Gateways.
	 *
	 * @return Array
	 */
	public static function available_payment_method( $available_gateways ) {
		if ( ! WC()->customer ) {
			return $available_gateways;
		}

		if ( ! Helper::is_clip_currency_supported() && isset( $available_gateways[ \Clip::GATEWAY_ID ] ) ) {
			unset( $available_gateways[ \Clip::GATEWAY_ID ] );
		}

		return $available_gateways;
	}

	/**
	 * Process Refunds
	 *
	 * @param int    $order_id Order to Refund.
	 * @param float  $amount Amount to Refund.
	 * @param string $reason Reason for the refund.
	 *
	 * @return bool
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {

		if ( empty( $order_id ) ) {
			return false;
		}

		if ( empty( $amount ) ) {
			/* translators: %s: Order ID */
			return new \WP_Error( 'wc-order', sprintf( __( 'Clip: Skipping ZERO value refund for Order ID %s.', 'clip' ), $order_id ) );
		}

		$order      = wc_get_order( $order_id );
		$order_data = $order->get_data();

		$response = $this->sdk->request_refund( $order_id, $amount, $reason );
		if ( isset( $response['code_message'] ) ) {
			$message = '';
			if ( 'AI1801' === $response['code_message'] ) {
				$message = __( 'Clip: The refund amount is greater that the original.' );
			}
			if ( 'AI1802' === $response['code_message'] ) {
				$message = __( 'Clip: The refund amount, plus previous refunds, is greater than the original amount.' );
			}
			if ( 'AI1803' === $response['code_message'] ) {
				$message = __( 'Clip: The refund date has expired.', 'clip' );
			}
			if ( 'AI1804' === $response['code_message'] ) {
				$message = __( 'Clip: Refund declined.', 'clip' );
			}
			if ( 'AI1805' === $response['code_message'] ) {
				$message = __( 'Clip: Refunds are disabled.', 'clip' );
			}
			if ( 'AI1806' === $response['code_message'] ) {
				$message = __( 'Clip: Refund is disabled for payments with MSI and MCI.', 'clip' );
			}
			if ( 'AI1807' === $response['code_message'] ) {
				$message = __( 'Clip: Refund in process for this transaction. Please try again later.', 'clip' );
			}
			if ( 'AI1400' === $response['code_message'] ) {
				$message = __( 'Clip: Insufficient funds to make the refund.', 'clip' );
			}
			return new \WP_Error( 'wc-order', $message );
		} elseif ( ! isset( $response['id'] ) ) {
			return new \WP_Error( 'wc-order', __( 'Clip: Unauthorized.', 'clip' ) );
		} else {
			/* translators: %s: Order ID */
			$order->add_order_note( sprintf( __( 'Clip: Refund requested. Id: %s .', 'clip' ), $response['id'] ) );
		}
		$order->save();

		return true;
	}

	/**
	 * Enqueue settings css
	 */
	public function enqueue_settings_css() {
		wp_enqueue_style( 'clip-settings' );
	}
}
