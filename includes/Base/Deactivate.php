<?php
/**
 * Deactivation.
 *
 * @since      1.0.0
 * @package    Migrate WP Cron to Action Scheduler
 * @subpackage Mwpcac\Base
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Mwpcac\Base;

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

		delete_option( 'mwpcac_plugin_dismiss_rating_notice' );
		delete_option( 'mwpcac_plugin_no_thanks_rating_notice' );
		delete_option( 'mwpcac_plugin_installed_time' );

		// action
		do_action( 'mwpcac/after_plugin_deactivate' );
	}
}