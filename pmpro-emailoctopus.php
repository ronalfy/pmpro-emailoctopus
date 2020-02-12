<?php // phpcs:ignore
/**
 * Paid Memberships Pro - EmailOctopus Add On
 *
 * @package   pmpro-emailoctopus
 * @copyright Copyright(c) 2020, MediaRon LLC
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: Paid Memberships Pro - EmailOctopus Add On
 * Plugin URI: https://github.com/ronalfy/pmppro-emailoctopus
 * Description: Integrate the EmailOctopus service with Paid Memberships Pro
 * Version: 1.0.0
 * Author: MediaRon LLC
 * Author URI: https://mediaron.com
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pmpro-emailoctopus
 * Domain Path: languages
 */

define( 'PMPROEMAILOCTOPUS_VERSION', '1.0.0' );
define( 'PMPROEMAILOCTOPUS_PLUGIN_NAME', 'Paid Memberships Pro EmailOctopus Add On' );
define( 'PMPROEMAILOCTOPUS_DIR', plugin_dir_path( __FILE__ ) );
define( 'PMPROEMAILOCTOPUS_URL', plugins_url( '/', __FILE__ ) );
define( 'PMPROEMAILOCTOPUS_SLUG', plugin_basename( __FILE__ ) );
define( 'PMPROEMAILOCTOPUS_FILE', __FILE__ );

// Setup the plugin auto loader.
require_once 'autoloader.php';

/**
 * The EmailOctopus base class.
 */
class PMPro_EmailOctopus {

	/**
	 * PMPro_EmailOctopus instance.
	 *
	 * @var PMPro_EmailOctopus $instance
	 */
	private static $instance = null;

	/**
	 * Return a class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Class Constructor
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 20 );
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Fired when the init action for WordPress is triggered.
	 */
	public function init() {
		load_plugin_textdomain( 'pmpro-emailoctopus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Fired when the plugins for WordPress have finished loading.
	 */
	public function plugins_loaded() {
		// Create the admin interface.
		$this->admin = new PMProEmailOctopus\Includes\Admin();
		$this->admin->run();
	}
}
PMPro_EmailOctopus::get_instance();
