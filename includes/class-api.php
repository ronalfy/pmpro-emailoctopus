<?php
/**
 * EmailOctopus Helper API functions.
 *
 * @package pmpro-emailoctopus
 */

namespace PMProEmailOctopus\Includes;

/**
 * Class options
 */
class API {

	/**
	 * EmailOctopus API URL.
	 *
	 * @since  1.0
	 * @var    string $api_url EmailOctopus API URL.
	 */
	protected $api_url = 'https://emailoctopus.com/api/1.5/';

	/**
	 * Validate the EmailOctopus API
	 *
	 * @param string $api_key The EmailOctopus API Key.
	 *
	 * @return bool true if valid, false if not
	 */
	public function validate_api( $api_key ) {
		$request_url = add_query_arg(
			array(
				'api_key' => $api_key,
				'limit'   => 1,
				'page'    => 1,
			),
			$this->api_url . 'lists'
		);
		$response    = wp_remote_get( $request_url );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$response_body = json_decode( wp_remote_retrieve_body( $response ) );
		if ( isset( $response_body->error ) ) {
			return false;
		}
		return true;
	}

	public function get_lists( $api_key ) {

	}

}
