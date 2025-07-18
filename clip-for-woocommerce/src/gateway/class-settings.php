<?php
/**
 * Settings.php
 *
 * @package  Ecomerciar\Clip\Gateway\
 */

namespace Ecomerciar\Clip\Gateway;

use Ecomerciar\Clip\Helper\Helper;

/**
 * Clase para manejar la configuración de WooCommerce.
 */
class Settings {

	/**
	 * Obtiene los campos de configuración de WooCommerce.
	 *
	 * @return array $args Campos de configuración de WooCommerce.
	 */
	public static function get_settings() {

		/**
		 * Clip Form Fields.
		 *
		 * Filter to Edit Clip Settings Fields.
		 *
		 * @since 2023.05.01
		 *
		* @param array $args {
		*     Array of settings fields for clip payment gateway
		* }
		* @return array $args Clip Settings.
		*/
		return apply_filters(
			'wc_clip_form_fields',
			array(
				'wc_clip_gateway_section'              => array(
					'title'       => '',
					'type'        => 'title',
					'description' => '',
				),

				'enabled'                              => array(
					'title'   => __( 'Enable/Disable', 'clip' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Clip Payment Gateway', 'clip' ),
					'default' => 'yes',
				),

				'wc_clip_credentials_section'          => array(
					'title'       => __( 'Credentials', 'clip' ),
					'type'        => 'title',
					'description' => __( "If you still do not have your credentials to operate with Clip, click <a href='https://dashboard.clip.mx/md/users/sign_in?pathname=%1\$2Fusers%2\$2Fsign_in' target='_bank'>here</a>.", 'clip' ),
				),
				'wc_clip_api_key'                      => array(
					'title' => __( 'Client Api Key', 'clip' ),
					'type'  => 'text',
				),
				'wc_clip_api_secret'                   => array(
					'title' => __( 'Client Api Secret', 'clip' ),
					'type'  => 'password',
				),
				'wc_clip_validations_section'          => array(
					'title'       => '',
					'type'        => 'title',
					'description' => Helper::validate_all_html(),
				),

				'wc_clip_checkout_section'             => array(
					'title'       => __( 'Checkout', 'clip' ),
					'type'        => 'title',
					'description' => __( 'Here you can configure the checkout banner and the expiration time for the checkout.', 'clip' ),
				),
				'wc_clip_banner_enabled'               => array(
					'title' => __( 'Checkout Banner', 'clip' ),
					'type'  => 'checkbox',
					'label' => __( 'Activate', 'clip' ),
				),
				'wc_clip_expiration_hours'             => array(
					'title'             => __( 'Checkout expiration time (hours)', 'clip' ),
					'type'              => 'number',
					'description'       => sprintf(
						/* translators: %s: System Flag */
						__(
							'This field will determine the maximum checkout time to receive your payment with Clip while the order remains pending payment and only one integer value is allowed. If no value is added, the default maximum time will be 72 hours.',
							'clip'
						),
					),
					'sanitize_callback' => function ( $input ) {
						// Limitar el valor a un rango específico, por ejemplo, de 0 a 72 horas.
						$input = intval( $input );
						$input = max( 0, min( 72, $input ) );
						return $input;
					},
				),

				'wc_clip_advance_section'              => array(
					'title' => __( 'Advance Settings', 'clip' ),
					'type'  => 'title',
				),
				'wc_clip_log_enabled'                  => array(
					'title'       => __( 'Debug Logs', 'clip' ),
					'type'        => 'checkbox',
					'label'       => __( 'Activate', 'clip' ),
					'description' => sprintf(
					/* translators: %s: System Flag */
						__(
							'You can enable plugin debugging to track communication between the plugin and Clip API. You will be able to view the record from the <a href="%s">WooCommerce > Status > Records</a> menu.',
							'clip'
						),
						esc_url( get_admin_url( null, 'admin.php?page=wc-status&tab=logs' ) )
					),
					'default'     => 'yes',
				),

				'wc_clip_link_section'                 => array(
					'title' => __( 'Link Settings', 'clip' ),
					'type'  => 'title',
				),
				'wc_clip_msi'                          => array(
					'title'       => '',
					'type'        => 'title',
					'description' => __( "If you want to activate MSI you can configure them in the <a href='https://dashboard.clip.mx/md/marketing_tools' target='_bank'>Clip panel</a>.", 'clip' ),
				),
				'wc_clip_payment_override'             => array(
					'title'   => __( 'Customize your link', 'clip' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'label'   => ' ',
				),
				'wc_clip_payment_type'                 => array(
					'title' => __( 'Payment Types', 'clip' ),
					'type'  => 'payment_types',
					'label' => ' ',
				),
				'wc_clip_payment_card_brands'          => array(
					'title' => __( 'Brands', 'clip' ),
					'type'  => 'payment_card_brands',
					'label' => ' ',
				),
				'wc_clip_payment_installments_enabled' => array(
					'title'   => __( 'Installments', 'clip' ),
					'type'    => 'checkbox',
					'default' => 'yes',
					'label'   => __( 'Customize months without interest', 'clip' ),
				),
				'wc_clip_payment_installments'         => array(
					'title' => '',
					'type'  => 'payment_installments',
				),
				'wc_clip_payment_tips'                 => array(
					'title'       => __( 'Tips', 'clip' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'label'       => ' ',
					'description' => __( 'Enable this option to allow customers to add tips during checkout. Tips are accepted with card payments, remote payments, or cash.', 'clip' ),
				),
			)
		);
	}
}
