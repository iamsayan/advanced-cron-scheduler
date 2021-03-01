<?php
/**
 * Action links.
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
 * Action links class.
 */
class Actions extends BaseController
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register() 
	{
		$this->action( "plugin_action_links_{$this->plugin}", 'settings_link', 10, 1 );
		$this->action( 'plugin_row_meta', 'meta_links', 10, 2 );
		$this->action( 'upgrader_process_complete', 'run_upgrade_action', 10, 2 );
	}

	/**
	 * Register settings link.
	 */
	public function settings_link( $links ) 
	{
		$wparlinks = [
			'<a href="' . admin_url( 'tools.php?page=action-scheduler' ) . '">' . __( 'Action Scheduler', 'migrate-wp-cron-to-action-scheduler' ) . '</a>',
		];
		return array_merge( $links, $wparlinks );
	}

	/**
	 * Register meta links.
	 */
	public function meta_links( $links, $file )
	{
		if ( $file === $this->plugin ) { // only for this plugin
			$links[] = '<a href="https://actionscheduler.org/api/" target="_blank">' . __( 'Usage', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
			$links[] = '<a href="https://actionscheduler.org/faq/" target="_blank">' . __( 'FAQ', 'migrate-wp-cron-to-action-scheduler' ) . '</a>';
		}
		
		return $links;
	}

	/**
	 * Run process after plugin update.
	 */
	public function run_upgrade_action( $upgrader_object, $options )
	{
		// If an update has taken place and the updated type is plugins and the plugins element exists
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
	        // Iterate through the plugins being updated and check if ours is there
		    foreach ( $options['plugins'] as $plugin ) {
		        if ( $plugin === $this->plugin ) {
					$this->do_action( 'plugin_updated', $options, $this->version, $upgrader_object );
				}
		    }
		}
	}
}