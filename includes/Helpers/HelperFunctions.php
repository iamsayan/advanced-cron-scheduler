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
    
		$interval  = (int) $interval;
    	$schedules = $this->get_schedules_by_interval();

    	if ( ! empty( $schedules ) && isset( $schedules[ $interval ] ) ) {
    		return $schedules[ $interval ];
    	}
    
    	return false;
    }

    /**
     * Get the action object for a specific action by its ID.
     *
     * Retrieves the action object for an action from the Action Scheduler store.
     * Returns false if Action Scheduler is not initialized or if the action doesn't exist.
     *
     * @param int $action_id The ID of the scheduled action
     * @return object|false Action object if found, false otherwise
     */
    protected function get_action( $action_id ) {
    	if ( ! \ActionScheduler::is_initialized() ) {
    		return false;
    	}
    
    	return \ActionScheduler::store()->fetch_action( $action_id );
    }

	/**
     * Get the schedule object for a specific action by its ID.
     *
     * Retrieves the schedule object for an action from the Action Scheduler store.
     * Returns false if Action Scheduler is not initialized or if the action doesn't exist.
     *
     * @param int $action_id The ID of the scheduled action
     * @return object|false Schedule object if found, false otherwise
     */
    protected function get_schedule( $action_id ) {
        $action = $this->get_action( $action_id );
        if ( ! $action ) {
            return false;
        }

        return $action->get_schedule();
    }

	/**
     * Cancel the next occurrence of a scheduled action by action id.
     *
     * @param int $action_id The ID of the scheduled action
     * @return int|null|false The scheduled action ID if a scheduled action was found, or false if no matching action found.
     */
    protected function cancel_scheduled_action( $action_id ) {
    	if ( ! \ActionScheduler::is_initialized() ) {
    		return false;
    	}
    
    	\ActionScheduler::store()->cancel_action( $action_id );
    
    	return $action_id;
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

    /**
	 * Get settings.
	 */
	protected function get_settings( $key, $default = null ) {
		$settings = get_option( 'acswp_settings', [] );
        if ( empty( $settings ) || ! is_array( $settings ) ) {
            $settings = [];
        }

        if ( ! empty( $settings[ $key ] ) ) {
            return $settings[ $key ];
        }

		return $default;
	}
}