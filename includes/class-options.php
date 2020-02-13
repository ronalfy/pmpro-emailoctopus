<?php
/**
 * Stores the options for EmailOctopus.
 *
 * @package pmpro-emailoctopus
 */

namespace PMProEmailOctopus\Includes;

/**
 * Class options
 */
class Options {

	/**
	 * Store the options.
	 *
	 * @var array $options
	 */
	private static $options;

	/**
	 * Get options for the plugin.
	 *
	 * @param bool $force_reload Whether to skip caching and get options from the database.
	 *
	 * @return array Options.
	 */
	public static function get_options( $force_reload = false ) {
		// Try to get cached options.
		$options = self::$options;
		if ( empty( $options ) || true === $force_reload ) {
			$options = get_option( 'pmpro-emailoctopus', array() );
		}

		// Store options.
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$defaults = array(
			'api_key' => '',
		);

		if ( empty( $options ) || count( $options ) < count( $defaults ) ) {
			$options = wp_parse_args(
				$options,
				$defaults
			);
		}

		self::$options = $options;
		return $options;
	}
}
