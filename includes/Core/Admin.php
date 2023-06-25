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

defined( 'ABSPATH' ) || exit;

/**
 * Admin Bar class.
 */
class Admin
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_bar_menu', 'admin_bar' );
		$this->filter( 'action_scheduler_check_pastdue_actions', 'past_due_actions', 100 );
	}

	/**
	 * Add admin bar content.
	 */
	public function admin_bar( $wp_admin_bar ) {
		$item = (bool) $this->do_filter( 'show_admin_bar_item', get_option( 'acswp_admin_bar' ) );

		if ( $item ) {
			$args = array(
				'id'     => 'acswp-link',
				'parent' => 'top-secondary',
				'title'  => __( 'Tasks', 'migrate-wp-cron-to-action-scheduler' ),
				'href'   => admin_url( 'tools.php?page=action-scheduler' ),
			);
		
			$wp_admin_bar->add_node( $args );
		}
	}

	/**
	 * Add admin bar content.
	 */
	public function past_due_actions( $check ) {
		$is_disabled = (bool) get_option( 'acswp_disable_past_due_checking' );
		
		return $is_disabled ? false : $check;
	}
}