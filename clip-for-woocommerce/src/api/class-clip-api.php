<?php
/**
 * Class CLipApi
 *
 * @package  Ecomerciar\Clip\Api\ClipApi
 */

namespace Ecomerciar\Clip\Api;

use Ecomerciar\Clip\Helper\Helper;
defined( 'ABSPATH' ) || exit();
/**
 * Clip API Class
 */
class ClipApi extends ApiConnector implements ApiInterface {

	/**
	 * API Key de Clip.
	 *
	 * @var string
	 */
	private string $api_key;

	/**
	 * API Secret de Clip.
	 *
	 * @var string
	 */
	private string $api_secret;

	/**
	 * Modo depuración (debug).
	 *
	 * @var bool
	 */
	private bool $debug;

	/**
	 * Class Constructor
	 *
	 * @param array $settings Clip Settings Object.
	 */
	public function __construct( array $settings = array() ) {
		$this->api_key    = $settings['api_key'];
		$this->api_secret = $settings['api_secret'];
		$this->debug      = $settings['debug'];
	}

	/**
	 * Get the base URL based on the environment and whether the gateway is required.
	 *
	 * @param bool $use_gateway Indicates whether to use the gateway (true) or the normal URL (false).
	 * @return string The base URL based on the environment and the parameter.
	 */
	public function get_base_url( bool $use_gateway = false ) {
		$environment = \Clip::CLIP_ENVIRONMENT;
		if ( $use_gateway ) {
			return \Clip::CLIP_API[ $environment . '-gw' ];
		}
		return \Clip::CLIP_API[ $environment ];
	}
}
