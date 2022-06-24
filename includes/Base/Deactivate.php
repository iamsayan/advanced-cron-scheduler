<?php
/**
 * Deactivation.
 *
 * @since      1.0.0
 * @package    WP Cron Action Schedular
 * @subpackage Mwpcac\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
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
		do_action( 'mwpcac/plugin_deactivate' );
	}
}