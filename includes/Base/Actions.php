<?php
/**
 * Action links.
 *
 * @since      1.0.0
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Base;

use ACSWP\Plugin\Helpers\Hooker;
use ACSWP\Plugin\Base\BaseController;

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
		$links[] = '<a href="' . admin_url( 'options-general.php#acswp-settings' ) . '">' . __( 'Settings', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
		$links[] = '<a href="' . admin_url( 'tools.php?page=action-scheduler' ) . '">' . __( 'Action Scheduler', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
		
		return $links;
	}

	/**
	 * Register meta links.
	 */
	public function meta_links( $links, $file ) {
		if ( $file === $this->plugin ) { // only for this plugin
			$links[] = '<a href="https://actionscheduler.org/api/" target="_blank" rel="noopener">' . __( 'Usage', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
			$links[] = '<a href="https://actionscheduler.org/faq/" target="_blank" rel="noopener">' . __( 'FAQ', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
			$links[] = '<a href="https://github.com/iamsayan/advanced-cron-scheduler" target="_blank" rel="noopener">' . __( 'GitHub', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
			$links[] = '<a href="https://www.paypal.me/iamsayan/" target="_blank" rel="noopener">' . __( 'Donate', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
			$links[] = '<a href="https://wordpress.org/support/plugin/migrate-wp-cron-to-action-scheduler/reviews/?filter=5#new-post" target="_blank" rel="noopener" style="color: #ff2000;">★★★★★</a>';
		}
		
		return $links;
	}
}