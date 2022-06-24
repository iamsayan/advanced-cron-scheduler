<?php 
/**
 * Admin notices.
 *
 * @since      1.0.0
 * @package    WP Cron Action Schedular
 * @subpackage Mwpcac\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace Mwpcac\Base;

use Mwpcac\Helpers\Hooker;
use Mwpcac\Base\BaseController;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Notice class.
 */
class AdminNotice extends BaseController
{
	use Hooker;
	
	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_notices', 'install_notice' );
	}
	
	/**
	 * Show internal admin notices.
	 */
	public function install_notice() {
		global $wp_version;

		// Show a warning to sites running PHP < 5.6
		if ( version_compare( $wp_version, '5.2.0', '<' ) ) {
			deactivate_plugins( $this->plugin );
			/* translators: %s: Plugin Name */
			echo '<div class="error"><p>' . sprintf( __( 'Your version of WordPress is below the minimum version of WordPress required by %s plugin. Please upgrade WordPress to 5.2.0 or later.', 'migrate-wp-cron-to-action-scheduler' ), $this->name ) . '</p></div>';
		    return;
		}
		
		// Show a warning to sites running PHP < 5.6
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			deactivate_plugins( $this->plugin );
			/* translators: %s: Plugin Name */
			echo '<div class="error"><p>' . sprintf( __( 'Your version of PHP is below the minimum version of PHP required by %s plugin. Please contact your host and request that your version be upgraded to 5.6 or later.', 'migrate-wp-cron-to-action-scheduler' ), $this->name ) . '</p></div>';
			return;
		}

		// Check transient, if available display notice
		if ( get_transient( 'mwpcac-show-notice-on-activation' ) !== false ) { ?>
			<div class="notice notice-success">
				<p><strong><?php
				/* translators: %s: Plugin Name */ 
				printf( __( 'Thanks for installing %1$s v%2$s plugin. Click <a href="%3$s">here</a> to view Action Scheduler tasks.', 'migrate-wp-cron-to-action-scheduler' ), $this->name, $this->version, admin_url( 'tools.php?page=action-scheduler' ) ); ?></strong></p>
			</div> <?php
		    delete_transient( 'mwpcac-show-notice-on-activation' );
		}
	}
}