<?php
/**
 * Shows link on admin bar.
 *
 * @since      1.0.7
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Core
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Core;

use ACSWP\Plugin\Helpers\Hooker;
use ACSWP\Plugin\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Bar class.
 */
class AdminBar
{
	use HelperFunctions, Hooker;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_bar_menu', 'admin_bar' );
	}

	/**
	 * Add admin bar content.
	 */
	public function admin_bar( $wp_admin_bar ) {
		$item = $this->do_filter( 'show_admin_bar_item', true );

		if ( true === $item ) {
			$args = array(
				'id'     => 'acswp-link',
				'parent' => 'top-secondary',
				'title'  => __( 'Tasks', 'migrate-wp-cron-to-action-scheduler' ),
				'href'   => admin_url( 'tools.php?page=action-scheduler' ),
			);
		
			$wp_admin_bar->add_node( $args );
		}
	}
}