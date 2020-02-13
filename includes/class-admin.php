<?php
/**
 * Creates the admin interface for EmailOctopus.
 *
 * @package pmpro-emailoctopus
 */

namespace PMProEmailOctopus\Includes;

use PMProEmailOctopus\Includes\Options as Options;

/**
 * Class Admin
 */
class Admin {

	/**
	 * Store the lists for later use.
	 *
	 * @var array $lists EmailOctopus lists.
	 */
	private $lists = array();

	/**
	 * Whether we've checked for lists already.
	 *
	 * @var bool $list_check false if not checked, true if it has.
	 */
	private $list_check = false;

	/**
	 * Class Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Main class initializer.
	 */
	public function run() {
		// Admin Settings.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'init_admin_settings' ) );

		// Plugin settings.
		add_filter( 'plugin_action_links_' . plugin_basename( PMPROEMAILOCTOPUS_FILE ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialize options page
	 *
	 * Create plugin options page and callback
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see init
	 */
	public function add_admin_menu() {
		add_options_page( _x( 'PMPro EmailOctopus', 'Plugin Name - Settings Page Title', 'pmpro-emailoctopus' ), _x( 'PMPro EmailOctopus', 'Plugin Name - Menu Item', 'pmpro-emailoctopus' ), 'manage_options', 'pmpro-emailoctopus', array( $this, 'options_page' ) );
	}

	/**
	 * Initialize options
	 *
	 * Initialize page settings, fields, and sections and their callbacks
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see init
	 */
	public function init_admin_settings() {
		$args = array(
			'sanitize_callback' => array( $this, 'sanitize_options' ),
		);
		register_setting(
			'pmpro-emailoctopus',
			'pmpro-emailoctopus',
			$args
		);
		add_settings_section(
			'pmpro-emailoctopus-general',
			_x( 'General Settings', 'plugin settings heading', 'pmpro-emailoctopus' ),
			array( $this, 'settings_section' ),
			'pmpro-emailoctopus'
		);

		add_settings_field(
			'pmpro-emailoctopus-api-key',
			__( 'Enter your EmailOctopus API Key', 'pmpro-emailoctopus' ),
			array( $this, 'add_settings_field_api_key' ),
			'pmpro-emailoctopus',
			'pmpro-emailoctopus-general',
			array(
				'desc'      => __( 'You can find your API key in your EmailOctopus account.', 'pmpro-emailoctopus' ),
				'label_for' => 'emailoctopus-api-key',
			)
		);
		add_settings_field(
			'pmpro-emailoctopus-user-lists',
			__( 'Select EmailOctopus Lists', 'pmpro-emailoctopus' ),
			array( $this, 'add_settings_field_output_lists' ),
			'pmpro-emailoctopus',
			'pmpro-emailoctopus-general'
		);
	}

	/**
	 * Sanitize the API options.
	 *
	 * @param array $options Options to sanitize and check.
	 */
	public function sanitize_options( $options ) {
		foreach ( $options as $key => &$option ) {
			if ( is_array( $option ) ) {
				foreach ( $option as $option_key => &$option_value ) {
					$option_value = sanitize_text_field( $option_value );
				}
			} else {
				$option = sanitize_text_field( $option );
			}
		}
		$api_helper = new \PMProEmailOctopus\Includes\API();
		if ( ! $api_helper->validate_api( $options['api_key'] ) ) {
			add_settings_error(
				'pmpro-emailoctopus-api-key',
				'pmppro-emailoctopus-api-error',
				__( 'The API key is not valid.', 'pmpro-emailoctopus' ),
				'error'
			);
			return array();
		}
		return $options;
	}

	/**
	 * Retrieve EmailOctopus lists.
	 *
	 * @return array EmailOctopus lists.
	 */
	public function get_lists() {
		$lists = get_transient( 'pmpro-emailoctopus-lists' );
		if ( $lists ) {
			$this->lists = $lists;
			return $lists;
		}
		$options = Options::get_options();
		if ( empty( $this->lists ) && false === $this->list_check ) {
			$api_helper = new \PMProEmailOctopus\Includes\API();
			$lists      = $api_helper->get_lists( $options['api_key'] );
			if ( ! empty( $lists ) ) {
				$this->lists = $lists;
				set_transient( 'pmpro-emailoctopus-lists', $this->lists, 3600 );
			} else {
				$this->lists = array();
			}
		}
		$this->list_check = true;
		return $this->lists;
	}

	/**
	 * Output options page HTML.
	 *
	 * Output option page HTML and fields/sections.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see add_admin_menu
	 */
	public function options_page() {
		?>
		<div class="wrap">
			<h2><?php echo esc_html( _x( 'Paid Memberships Pro EmailOctopus Add On', 'Plugin Name - Settings Page Title', 'pmpro-emailoctopus' ) ); ?></h2>
			<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="POST">
				<?php settings_fields( 'pmpro-emailoctopus' ); ?>
				<?php do_settings_sections( 'pmpro-emailoctopus' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Add an API key field to the settings.
	 *
	 * @param array $args Setting arguments.
	 */
	public function add_settings_field_api_key( $args = array() ) {
		$options = Options::get_options();
		printf( '<p>%s</p>', esc_html( $args['desc'] ) );
		printf( '<input id="%s" class="regular-text" type="text" name="pmpro-emailoctopus[api_key]" value="%s" />', esc_attr( $args['label_for'] ), esc_attr( $options['api_key'] ) );
	}

	/**
	 * Add an list field to the settings.
	 *
	 * @param array $args Setting arguments.
	 */
	public function add_settings_field_output_lists( $args = array() ) {
		$options    = Options::get_options();
		$user_lists = $options['user_lists'];
		$lists      = $this->get_lists();
		if ( ! empty( $lists ) ) {
			echo "<select multiple='yes' name=\"pmpro-emailoctopus[user_lists][]\">";
			foreach ( $lists['data'] as $list ) {
				echo "<option value='" . esc_attr( $list['id'] ) . "' ";
				selected( in_array( $list['id'], $user_lists, true ), true, true );
				echo '>' . esc_html( $list['name'] ) . '</option>';
			}
			echo '</select>';
		} else {
			esc_html_e( 'No lists were found', 'pmpro-emailoctopus' );
		}
	}

	/**
	 * Output settings HTML
	 *
	 * Output any HTML required to go into a settings section
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see init_admin_settings
	 */
	public function settings_section() {
	}

	/**
	 * Add a settings link to the plugin's options.
	 *
	 * Add a settings link on the WordPress plugin's page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @see init
	 *
	 * @param array $links Array of plugin options.
	 * @return array $links Array of plugin options
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php?page=pmpro-emailoctopus' ) ), _x( 'Settings', 'Plugin settings link on the plugins page', 'pmpro-emailoctopus' ) );
			array_unshift( $links, $settings_link );
			return $links;
	}
}
