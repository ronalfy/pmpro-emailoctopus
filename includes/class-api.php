<?php
/**
 * EmailOctopus Helper API functions.
 *
 * @package pmpro-emailoctopus
 */

namespace PMProEmailOctopus\Includes;

use PMProEmailOctopus\Includes\Options as Options;

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

	/**
	 * Retrieve lists from the EmailOctopus API
	 *
	 * @param string $api_key The EmailOctopus API Key.
	 *
	 * @return mixed false if valid, object if not.
	 */
	public function get_lists( $api_key ) {
		$request_url = add_query_arg(
			array(
				'api_key' => $api_key,
				'limit'   => 100,
				'page'    => 1,
			),
			$this->api_url . 'lists'
		);
		$response    = wp_remote_get( $request_url );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $response_body['error'] ) ) {
			return false;
		}
		if ( empty( $response_body ) ) {
			return false;
		}
		return $response_body;
	}

	/**
	 * Subscribe a user to an EmailOctopus list.
	 *
	 * @param string $email Email Address.
	 * @param array  $lists Lists to subscribe to.
	 */
	public function subscribe( $email, $lists ) {
		$options = Options::get_options();
		if ( ! isset( $options['api_key'] ) || empty( $options['api_key'] ) ) {
			return;
		}
		$api_key = $options['api_key'];
		if ( is_array( $lists ) ) {
			foreach ( $lists as $list ) {
				$path                          = $this->api_url . sprintf( 'lists/%s/contacts', $list );
				$args                          = array();
				$args['body']['api_key']       = $api_key;
				$args['body']['email_address'] = $email;
				$response                      = wp_remote_post( $path, $args );
			}
		}
	}

}
