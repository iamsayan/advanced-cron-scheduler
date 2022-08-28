<?php
/**
 * Action Scheduler functions.
 *
 * @since      1.0.6
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Helpers
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Helpers;

use ACSWP\Plugin\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Action Scheduler Class
 */
trait Scheduler
{
	use Hooker;

	/**
	 * Create the recurring action event.
	 *
	 * @param  integer $timestamp            Timestamp.
	 * @param  integer $interval_in_seconds  Interval in Seconds.
	 * @param  string  $hook                 Action Hook.
	 * @param  array   $args                 Parameters.
	 * @param  string  $group                Group Name.
	 * @return string
	 */
	protected function set_recurring_action( $timestamp, $interval_in_seconds, $hook, $args = [], $group = 'mwpcac' ) {
		$unique = (bool) $this->do_filter( 'unique_action', get_option( 'acswp_unique_action' ) );
		$action_id = \as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group, $unique ); // @phpstan-ignore-line

		return $action_id;
	}

	/**
	 * Create the single action event.
	 *
	 * @param  integer $timestamp  Timestamp.
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 * @return string
	 */
	protected function set_single_action( $timestamp, $hook, $args = [], $group = 'mwpcac' ) {
		$unique = (bool) $this->do_filter( 'unique_action', get_option( 'acswp_unique_action' ) );
		$action_id = \as_schedule_single_action( $timestamp, $hook, $args, $group, $unique ); // @phpstan-ignore-line

		return $action_id;
	}

	/**
	 * Unschedule all action events.
	 *
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 */
	protected function unschedule_all_actions( $hook, $args = [], $group = 'mwpcac' ) {
		\as_unschedule_all_actions( $hook, $args, $group ); // @phpstan-ignore-line
	}

	/**
	 * Unschedule last action event.
	 *
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 */
	protected function unschedule_last_action( $hook, $args = [], $group = 'mwpcac' ) {
		\as_unschedule_action( $hook, $args, $group ); // @phpstan-ignore-line
	}

	/**
	 * Net next scheduled action.
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function get_next_action( $hook, $args = [], $group = 'mwpcac' ) {
		return \as_next_scheduled_action( $hook, $args, $group ); // @phpstan-ignore-line
	}

	/**
	 * Check if next action is exists.
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function has_next_action( $hook, $args = [], $group = 'mwpcac' ) {
		if ( ! function_exists( 'as_has_scheduled_action' ) ) {
			return \boolval( $this->get_next_action( $hook, $args, $group ) );  // @phpstan-ignore-line
		}
		return \as_has_scheduled_action( $hook, $args, $group ); // @phpstan-ignore-line
	}

	/**
	 * Get next scheduled actions
	 *
	 * @param  array   $args   Parameters.
	 * @return null|string
	 */
	protected function get_next_actions( $args, $return_format = 'ids' ) {
		$args = \wp_parse_args( $args, [
			'status'       => \ActionScheduler_Store::STATUS_PENDING,
			'per_page'     => 1,
			'orderby'      => 'date',
			'order'        => 'ASC',
			'group'        => 'mwpcac',
			'date_compare' => '=',
		] );

		return \as_get_scheduled_actions( $args, $return_format ); // @phpstan-ignore-line
	}

	/**
	 * Get next scheduled actions by data
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function get_next_action_by_data( $hook, $args, $timestamp ) {
		$actions = $this->get_next_actions( [
			'hook' => $hook,
			'args' => $args,
			'date' => gmdate( 'U', $timestamp ),
		] );

		return $actions;
	}
}