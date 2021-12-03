<?php
/**
 * Helper Functions.
 *
 * @since      1.0.0
 * @package    Migrate WP Cron to Action Scheduler
 * @subpackage Mwpcac\Helpers
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace Mwpcac\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Meta & Option class.
 */
trait HelperFunctions
{
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
	protected function set_recurring_action( $timestamp, $interval_in_seconds, $hook, $args = [], $group = 'mwpcac' )
    {
		$job_id = \as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );

		return $job_id;
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
	protected function set_single_action( $timestamp, $hook, $args = [], $group = 'mwpcac' )
    {
		$job_id = \as_schedule_single_action( $timestamp, $hook, $args, $group );

		$this->do_action( 'single_action_set', $timestamp, $hook, $args, $job_id );

		return $job_id;
	}

	/**
	 * Unschedule all action events.
	 *
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 */
	protected function unschedule_all_actions( $hook, $args = [], $group = 'mwpcac' )
    {
		\as_unschedule_all_actions( $hook, $args, $group );
	}

	/**
	 * Unschedule last action event.
	 *
	 * @param  string  $hook       Hook.
	 * @param  array   $arg        Parameter.
	 * @param  string  $group      Group Name.
	 */
	protected function unschedule_last_action( $hook, $args = [], $group = 'mwpcac' )
    {
		\as_unschedule_action( $hook, $args, $group );
	}

	/**
	 * Returns next action timestamp.
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function get_next_action( $hook, $args = [], $group = 'mwpcac' )
    {
		return \as_next_scheduled_action( $hook, $args, $group );
	}

	/**
	 * Check if next action is exists.
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function has_next_action( $hook, $args = [], $group = 'mwpcac' )
    {
		return \as_has_scheduled_action( $hook, $args, $group );
	}

	/**
	 * Check if next action is exists.
	 *
	 * @param  string  $hook   Action Hook.
	 * @param  array   $args   Parameters.
	 * @param  string  $group  Group Name.
	 * @return null|string
	 */
	protected function get_next_action_by_data( $hook, $args, $timestamp, $group = 'mwpcac' )
    {
		return \as_get_scheduled_actions( [
			'hook' 			=> $hook,
			'args' 			=> $args,
			'date' 			=> gmdate( 'U', $timestamp ),
			'date_compare' 	=> '=',
			'group' 		=> $group,
			'status' 		=> \ActionScheduler_Store::STATUS_PENDING,
			'per_page' 		=> 1,
			'orderby'  		=> 'date',
			'order' 		=> 'ASC'
		], 'ids' );
	}

		/**
     * Get the WP Cron schedule names by interval.
     *
     * This is used as a fallback when Cavalcade does not have the
     * schedule name stored in the database to make a best guest as
     * the schedules name.
     *
     * Interval collisions caused by two plugins registering the same
     * interval with different names are unified into a single name.
     *
     * @return array Cron Schedules indexed by interval.
     */
    protected function get_schedules_by_interval()
	{
    	$schedules = [];
    
    	foreach ( wp_get_schedules() as $name => $schedule ) {
    		$schedules[ (int) $schedule['interval'] ] = $name;
    	}
    
    	return $schedules;
    }
    
    /**
     * Helper function to get a schedule name from a specific interval.
     *
     * @param int $interval Cron schedule interval.
     * @return string Cron schedule name.
     */
    protected function get_schedule_by_interval( $interval = null )
	{
    	if ( empty( $interval ) ) {
    		return false;
    	}
    
		$interval = (int) $interval;
    	$schedules = $this->get_schedules_by_interval();

    	if ( ! empty ( $schedules ) && isset( $schedules[ $interval ] ) ) {
    		return $schedules[ $interval ];
    	}
    
    	return false;
    }

	/**
     * Check if there is an existing action in the queue with a given hook, args and group combination.
     *
     * An action in the queue could be pending, in-progress or async. If the is pending for a time in
     * future, its scheduled date will be returned as a timestamp. If it is currently being run, or an
     * async action sitting in the queue waiting to be processed, in which case boolean true will be
     * returned. Or there may be no async, in-progress or pending action for this hook, in which case,
     * boolean false will be the return value.
     *
     * @param string $hook
     * @param array $args
     * @param string $group
     *
     * @return int|bool The timestamp for the next occurrence of a pending scheduled action, true for an async or in-progress action or false if there is no matching action.
     */
    protected function get_recurrence( $job_id ) {
    	if ( ! \ActionScheduler::is_initialized( __FUNCTION__ ) ) {
    		return false;
    	}
    
    	$job = \ActionScheduler::store()->fetch_action( $job_id );
		$recurrence = $job->get_schedule();

    	return $recurrence;
    }

	/**
     * Cancel the next occurrence of a scheduled action by action id.
     *
     * @param string $hook The hook that the job will trigger.
     * @param array $args Args that would have been passed to the job.
     * @param string $group The group the job is assigned to.
     *
     * @return string|null The scheduled action ID if a scheduled action was found, or null if no matching action found.
     */
    protected function cancel_scheduled_action( $job_id ) {
    	if ( ! \ActionScheduler::is_initialized( __FUNCTION__ ) ) {
    		return false;
    	}
    
    	\ActionScheduler::store()->cancel_action( $job_id );
    
    	return $job_id;
    }

	/**
     * Get Default WordPress Native Hooks.
     */
    protected function get_protected_hooks()
	{
    	$hooks = apply_filters( 'mwpcac_protected_hooks', [
            'wp_site_health_scheduled_check',
            'recovery_mode_clean_expired_keys',
            'wp_scheduled_auto_draft_delete',
            'wp_privacy_delete_old_export_files',
            'wp_version_check',
            'wp_update_plugins',
            'wp_update_themes',
            'wp_scheduled_delete',
            'delete_expired_transients'
        ] );
    
        array_push( $hooks, 'action_scheduler_run_queue' );
    
        return array_merge( apply_filters( 'mwpcac_exclude_event_hooks', [] ), $hooks );
    }
}