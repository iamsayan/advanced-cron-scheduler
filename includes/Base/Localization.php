<?php
/**
 * Localization loader.
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
 * Localizationclass.
 */
class Localization extends BaseController
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'plugins_loaded', 'load_textdomain' );
	}

	/**
     * Initialize plugin for localization.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     */
	public function load_textdomain() {
		load_plugin_textdomain( 'migrate-wp-cron-to-action-scheduler', false, dirname( $this->plugin ) . '/languages' ); 
	}
}