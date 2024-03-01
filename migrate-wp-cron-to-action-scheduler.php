<?php
/**
 * Plugin Name: Advanced Cron Scheduler
 * Plugin URI: https://wordpress.org/plugins/migrate-wp-cron-to-action-scheduler/
 * Description: The Advanced Cron Scheduler plugin helps to easily replace or migrate Native WordPress Cron to the Action Scheduler Library.
 * Version: 1.1.1
 * Author: Sayan Datta
 * Author URI: https://www.sayandatta.co.in
 * License: GPLv3
 * 
 * Advanced Cron Scheduler is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Advanced Cron Scheduler is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Advanced Cron Scheduler. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category Core
 * @package  Advanced Cron Scheduler
 * @author   Sayan Datta <iamsayan@protonmail.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://wordpress.org/plugins/migrate-wp-cron-to-action-scheduler/
 * 
 */

// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) || exit;

/**
 * ACSWP class.
 *
 * @class Main class of the plugin.
 */
final class ACSWP {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.1.1';

	/**
	 * Minimum version of WordPress required to run ACSWP.
	 *
	 * @var string
	 */
	private $wordpress_version = '5.2';

	/**
	 * Minimum version of PHP required to run ACSWP.
	 *
	 * @var string
	 */
	private $php_version = '5.6';

	/**
	 * Hold install error messages.
	 *
	 * @var bool
	 */
	private $messages = [];

	/**
	 * The single instance of the class.
	 *
	 * @var ACSWP
	 */
	protected static $instance = null;

	/**
	 * Retrieve main ACSWP instance.
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @see acswp()
	 * @return ACSWP
	 */
	public static function get() {
		if ( is_null( self::$instance ) && ! ( self::$instance instanceof ACSWP ) ) {
			self::$instance = new ACSWP();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the plugin.
	 */
	private function setup() {
		// Define plugin constants.
		$this->define_constants();

		if ( ! $this->is_requirements_meet() ) {
			return;
		}

		// Include required files.
		$this->includes();

		// Instantiate services.
		$this->instantiate();

		// Loaded action.
		do_action( 'acswp/loaded' );
	}

	/**
	 * Check that the WordPress and PHP setup meets the plugin requirements.
	 *
	 * @return bool
	 */
	private function is_requirements_meet() {

		// Check WordPress version.
		if ( version_compare( get_bloginfo( 'version' ), $this->wordpress_version, '<' ) ) {
			/* translators: WordPress Version */
			$this->messages[] = sprintf( esc_html__( 'You are using the outdated WordPress, please update it to version %s or higher.', 'migrate-wp-cron-to-action-scheduler' ), $this->wordpress_version );
		}

		// Check PHP version.
		if ( version_compare( phpversion(), $this->php_version, '<' ) ) {
			/* translators: PHP Version */
			$this->messages[] = sprintf( esc_html__( 'Advanced Cron Scheduler for WordPress requires PHP version %s or above. Please update PHP to run this plugin.', 'migrate-wp-cron-to-action-scheduler' ), $this->php_version );
		}

		if ( empty( $this->messages ) ) {
			return true;
		}

		// Auto-deactivate plugin.
		add_action( 'admin_init', [ $this, 'auto_deactivate' ] );
		add_action( 'admin_notices', [ $this, 'activation_error' ] );

		return false;
	}

	/**
	 * Auto-deactivate plugin if requirements are not met, and display a notice.
	 */
	public function auto_deactivate() {
		deactivate_plugins( ACSWP_BASENAME );
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore
			unset( $_GET['activate'] ); // phpcs:ignore
		}
	}

	/**
	 * Error notice on plugin activation.
	 */
	public function activation_error() {
		?>
		<div class="notice acswp-notice notice-error">
			<p>
				<?php echo join( '<br>', $this->messages ); // phpcs:ignore ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Define the plugin constants.
	 */
	private function define_constants() {
		define( 'ACSWP_VERSION', $this->version );
		define( 'ACSWP_FILE', __FILE__ );
		define( 'ACSWP_PATH', dirname( ACSWP_FILE ) . '/' );
		define( 'ACSWP_URL', plugins_url( '', ACSWP_FILE ) . '/' );
		define( 'ACSWP_BASENAME', plugin_basename( ACSWP_FILE ) );
	}

	/**
	 * Include the required files.
	 */
	private function includes() {
		include dirname( __FILE__ ) . '/vendor/autoload.php';
	}

	/**
	 * Instantiate services.
	 */
	private function instantiate() {
		// Activation hook.
		register_activation_hook( ACSWP_FILE, 
			function () {
				ACSWP\Plugin\Base\Activate::activate();
			} 
		);

		// Deactivation hook.
		register_deactivation_hook( ACSWP_FILE, 
			function () {
				ACSWP\Plugin\Base\Deactivate::deactivate();
			} 
		);

		// Init ACSWP Classes.
		ACSWP\Plugin\Loader::register_services();
	}
}

/**
 * Returns the main instance of ACSWP to prevent the need to use globals.
 *
 * @return ACSWP
 */
function acswp() {
	return ACSWP::get();
}

// Start it.
acswp();