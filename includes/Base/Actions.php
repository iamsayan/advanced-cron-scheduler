<?php
/**
 * Action links.
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
 * Action links class.
 */
class Actions extends BaseController
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( "plugin_action_links_{$this->plugin}", 'settings_link', 10, 1 );
		$this->action( 'plugin_row_meta', 'meta_links', 10, 2 );
	}

	/**
	 * Register settings link.
	 */
	public function settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'tools.php?page=action-scheduler' ) . '">' . __( 'Action Scheduler', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';

		return $links;
	}

	/**
	 * Register meta links.
	 */
	public function meta_links( $links, $file ) {
		if ( $file === $this->plugin ) { // only for this plugin
			$links[] = '<a href="https://actionscheduler.org/api/" target="_blank">' . __( 'Usage', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
			$links[] = '<a href="https://actionscheduler.org/faq/" target="_blank">' . __( 'FAQ', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
			$links[] = '<a href="https://github.com/iamsayan/migrate-wp-cron-to-action-scheduler" target="_blank">' . __( 'GitHub', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
		}
		
		return $links;
	}
}