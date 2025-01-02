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
class Admin
{
	use Hooker, HelperFunctions;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_bar_menu', 'admin_bar' );
		$this->filter( 'action_scheduler_check_pastdue_actions', 'past_due_actions', 100 );
		$this->filter( 'action_scheduler_retention_period', 'retention_period', 100 );
		$this->filter( 'action_scheduler_queue_runner_batch_size', 'queue_batch_size', 10000 );
		$this->filter( 'action_scheduler_queue_runner_concurrent_batches', 'concurrent_batches', 10000 );
		$this->filter( 'action_scheduler_timeout_period', 'timeout', 100 );
		$this->filter( 'action_scheduler_failure_period', 'timeout', 100 );
		$this->filter( 'action_scheduler_queue_runner_time_limit', 'time_limit', 100 );
	}

	/**
	 * Add admin bar content.
	 */
	public function admin_bar( $wp_admin_bar ) {
		$item = (bool) $this->do_filter( 'show_admin_bar_item', $this->get_settings( 'admin_bar' ) );

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
	 * Disable past-due actions checking.
	 */
	public function past_due_actions( $check ) {
		$is_disabled = (bool) $this->get_settings( 'disable_past_due_checking' );
		
		return $is_disabled ? false : $check;
	}

	/**
	 * Change retention period.
	 */
	public function retention_period( $retention_period ) {
		$units = [
			'minutes' => MINUTE_IN_SECONDS,
			'hours'   => HOUR_IN_SECONDS,
			'days'    => DAY_IN_SECONDS,
			'weeks'   => WEEK_IN_SECONDS,
			'months'  => MONTH_IN_SECONDS,
			'years'   => YEAR_IN_SECONDS,
		];
		$retention_period = $this->get_settings( 'data_retention_period', 0 );
		$selected_unit = $this->get_settings( 'data_retention_unit', 'days' );

		if ( $retention_period > 0 && isset( $units[ $selected_unit ] ) ) {
			return $retention_period * $units[ $selected_unit ];
		}

		return $retention_period;
	}

	/**
	 * Change queue batch size.
	 */
	public function queue_batch_size( $batch_size ) {
		return $this->get_settings( 'queue_batch_size', $batch_size );
	}

	/**
	 * Change concurrent batches.
	 */
	public function concurrent_batches( $concurrent_batches ) {
		return $this->get_settings( 'concurrent_batches', $concurrent_batches );
	}

	/**
	 * Change timeout.
	 */
	public function timeout( $timeout ) {
		return $this->get_settings( 'timeout', $timeout );
	}

	/**
	 * Change time limit.
	 */
	public function time_limit( $time_limit ) {
		return $this->get_settings( 'time_limit', $time_limit );
	}
}