<?php
/**
 * Activation.
 *
 * @since      1.1.0
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Base;

/**
 * Activation class.
 */
class Activate
{
	/**
	 * Run plugin activation process.
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		
		set_transient( 'acswp-show-notice-on-activation', true, 15 );

		// action
		do_action( 'acswp/plugin_activate' );
	}
}