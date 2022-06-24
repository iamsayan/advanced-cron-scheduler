<?php
/**
 * Deactivation.
 *
 * @since      1.0.0
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Base;

/**
 * Deactivation class.
 */
class Deactivate
{
	/**
	 * Run plugin deactivation process.
	 */
	public static function deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		delete_option( 'acswp_plugin_dismiss_rating_notice' );
		delete_option( 'acswp_plugin_no_thanks_rating_notice' );
		delete_option( 'acswp_plugin_installed_time' );

		// action
		do_action( 'acswp/plugin_deactivate' );
	}
}