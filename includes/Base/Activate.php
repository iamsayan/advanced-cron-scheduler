<?php
/**
 * Activation.
 *
 * @since      1.1.0
 * @package    Migrate WP Cron to Action Scheduler
 * @subpackage Mwpcac\Base
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Mwpcac\Base;

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
		
		set_transient( 'mwpcac-show-notice-on-activation', true, 15 );

		// action
		do_action( 'mwpcac/after_plugin_activate' );
	}
}