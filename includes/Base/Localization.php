<?php
/**
 * Localization loader.
 *
 * @since      1.0.0
 * @package    Migrate WP Cron to Action Scheduler
 * @subpackage Mwpcac\Base
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Mwpcac\Base;

use Mwpcac\Helpers\Hooker;
use Mwpcac\Base\BaseController;

defined( 'ABSPATH' ) || exit;

/**
 * Localizationclass.
 */
class Localization extends BaseController
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register() 
	{
		$this->action( 'plugins_loaded', 'load_textdomain' );
	}

	/**
     * Initialize plugin for localization.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     */
	public function load_textdomain()
	{
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'migrate-wp-cron-to-action-scheduler' ); // phpcs:ignore

		unload_textdomain( 'migrate-wp-cron-to-action-scheduler' );
		if ( false === load_textdomain( 'migrate-wp-cron-to-action-scheduler', WP_LANG_DIR . '/plugins/migrate-wp-cron-to-action-scheduler-' . $locale . '.mo' ) ) {
			load_textdomain( 'migrate-wp-cron-to-action-scheduler', WP_LANG_DIR . '/migrate-wp-cron-to-action-scheduler/migrate-wp-cron-to-action-scheduler-' . $locale . '.mo' );
		}
		load_plugin_textdomain( 'migrate-wp-cron-to-action-scheduler', false, dirname( $this->plugin ) . '/languages/' ); 
	}
}