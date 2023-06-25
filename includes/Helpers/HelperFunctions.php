<?php
/**
 * Helper Functions.
 *
 * @since      1.0.0
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Helpers
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Meta & Option class.
 */
trait HelperFunctions
{

	/**
     * Get the WP Cron schedule names by interval.
     *
     * This is used as a fallback when Action Scheduler does not have the
     * schedule name stored in the database to make a best guest as
     * the schedules name.
     *
     * Interval collisions caused by two plugins registering the same
     * interval with different names are unified into a single name.
     *
     * @return array Cron Schedules indexed by interval.
     */
    protected function get_schedules_by_interval() {
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
    protected function get_schedule_by_interval( $interval = null ) {
    	if ( empty( $interval ) ) {
    		return false;
    	}
    
		$interval = (int) $interval;
    	$schedules = $this->get_schedules_by_interval();

    	if ( ! empty( $schedules ) && isset( $schedules[ $interval ] ) ) {
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
    protected function get_schedule( $job_id ) {
    	if ( ! \ActionScheduler::is_initialized() ) {
    		return false;
    	}
    
    	$job = \ActionScheduler::store()->fetch_action( $job_id );
		$schedule = $job->get_schedule();

    	return $schedule;
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
    	if ( ! \ActionScheduler::is_initialized() ) {
    		return false;
    	}
    
    	\ActionScheduler::store()->cancel_action( $job_id );
    
    	return $job_id;
    }

    /**
     * Check if Action Scheduler is actually initiated or not.
     * 
     * @since 1.0.8
     */
    protected function is_as_initialized() {
    	if ( ! did_action( 'action_scheduler_init' ) || ! \ActionScheduler::is_initialized() ) {
    		return false;
    	}
    
    	return true;
    }

	/**
     * Get Default WordPress Native Hooks.
     */
    protected function get_protected_hooks() {
    	$hooks = (array) $this->do_filter( 'protected_hooks', [
            'wp_privacy_delete_old_export_files',
            'wp_update_user_counts',
			'wp_version_check',
			'wp_update_plugins',
			'wp_update_themes',
			'wp_https_detection',
			'wp_site_health_scheduled_check',
			'recovery_mode_clean_expired_keys',
			'wp_scheduled_delete',
			'delete_expired_transients',
			'wp_scheduled_auto_draft_delete',
			'recovery_mode_clean_expired_keys',
        ] );
    
        array_push( $hooks, 'action_scheduler_run_queue' );

        return array_unique( $hooks );
    }
}