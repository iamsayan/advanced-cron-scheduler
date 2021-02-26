<?php
/**
 * Plugin Name: Migrate WP Cron to Action Scheduler
 * Plugin URI: https://wordpress.org/plugins/migrate-cron-to-action-scheduler/
 * Description: The Migrate WP Cron to Action Scheduler plugin helps to easily migrate Native WordPress Cron to the Action Scheduler Library.
 * Version: 1.0.0
 * Author: Sayan Datta
 * Author URI: https://www.sayandatta.in
 * License: GPLv3
 * Text Domain: migrate-cron-to-action-scheduler
 * Domain Path: /languages
 * 
 * Migrate WP Cron to Action Scheduler is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Migrate WP Cron to Action Scheduler is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Migrate WP Cron to Action Scheduler. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category Core
 * @package  Migrate WP Cron to Action Scheduler
 * @author   Sayan Datta <hello@sayandatta.in>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://wordpress.org/plugins/migrate-cron-to-action-scheduler/
 * 
 */

// If this file is called firectly, abort!!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include Action Schedular Library.
require_once dirname( __FILE__ ) . '/action-scheduler.php';

add_filter( 'pre_schedule_event', 'mwpcac_pre_schedule_event', 5, 2 );
add_filter( 'pre_unschedule_event', 'mwpcac_pre_unschedule_event', 5, 4 );
add_filter( 'pre_unschedule_hook', 'mwpcac_pre_unschedule_hook', 5, 2 );
add_filter( 'pre_clear_scheduled_hook', 'mwpcac_pre_clear_scheduled_hook', 5, 3 );
add_filter( 'pre_get_scheduled_event', 'mwpcac_pre_get_scheduled_event', 5, 4 );

/**
 * Schedule an event.
 *
 * @param null|bool $pre   Value to return instead. Default null to continue adding the event.
 * @param stdClass  $event {
 *     An object containing an event's data.
 *
 *     @type string       $hook      Action hook to execute when the event is run.
 *     @type int          $timestamp Unix timestamp (UTC) for when to next run the event.
 *     @type string|false $schedule  How often the event should subsequently recur.
 *     @type array        $args      Array containing each separate argument to pass to the hook's callback function.
 *     @type int          $interval  The interval time in seconds for the schedule. Only present for recurring events.
 * }
 * @return null|bool True if event successfully scheduled. False for failure.
 */
function mwpcac_pre_schedule_event( $pre, $event ) {
    // Allow other filters to do their thing.
	if ( $pre !== null ) {
		return $pre;
	}

	/**
     * Filter to exclude a hook from inclusion in Action Scheduler.
     *
     * @param array Array containing each separate argument to pass to the hook's callback function.
     */
    if ( in_array( $event->hook, mwpcac_get_protected_hooks() ) ) {
        return null;
    }

    if ( $event->schedule === false ) {
        // Search ten minute range to test for duplicate events.
	    if ( $event->timestamp < time() + 10 * MINUTE_IN_SECONDS ) {
	    	$min_timestamp = 0;
	    } else {
	    	$min_timestamp = $event->timestamp - 10 * MINUTE_IN_SECONDS;
	    }

	    if ( $event->timestamp < time() ) {
	    	$max_timestamp = time() + 10 * MINUTE_IN_SECONDS;
	    } else {
	    	$max_timestamp = $event->timestamp + 10 * MINUTE_IN_SECONDS;
	    }

        /**
         * Check for a duplicated event.
         *
         * Don't schedule an event if there's already an identical event
         * within 10 minutes.
         *
         * When scheduling events within ten minutes of the current time,
         * all past identical events are considered duplicates.
         *
         * When scheduling an event with a past timestamp (ie, before the
         * current time) all events scheduled within the next ten minutes
         * are considered duplicates.
         */
        $next_timestamp = as_next_scheduled_action( $event->hook, $event->args );
        $duplicate = false;
        if ( $next_timestamp !== false ) {
            if ( ( $next_timestamp > $min_timestamp ) && ( $next_timestamp < $max_timestamp ) ) {
                $duplicate = true;
            }
        }

        if ( $duplicate ) {
            return false;
        }

        /** This filter is documented in wordpress/wp-includes/cron.php */
	    $event = apply_filters( 'schedule_event', $event );

        // remove old cron events
        mwpcac_remove_old_crons( $event );

        return as_schedule_single_action( $event->timestamp, $event->hook, $event->args );
    } else {
        if ( false === as_next_scheduled_action( $event->hook, $event->args ) ) {
            /** This filter is documented in wordpress/wp-includes/cron.php */
	        $event = apply_filters( 'schedule_event', $event );

            // remove old cron events
            mwpcac_remove_old_crons( $event );

            return as_schedule_recurring_action( $event->timestamp, $event->interval, $event->hook, $event->args );
        }
    }

    return true;
}

/**
 * Unschedule a previously scheduled event.
 *
 * The $timestamp and $hook parameters are required so that the event can be
 * identified.
 *
 * @param null|bool $pre       Value to return instead. Default null to continue unscheduling the event.
 * @param int       $timestamp Timestamp for when to run the event.
 * @param string    $hook      Action hook, the execution of which will be unscheduled.
 * @param array     $args      Arguments to pass to the hook's callback function.
 * @return bool True if event successfully unscheduled. False for failure.
 */
function mwpcac_pre_unschedule_event( $pre, $timestamp, $hook, $args ) {
    // Allow other filters to do their thing.
	if ( $pre !== null ) {
		return $pre;
	}

	/**
     * Filter to exclude a hook from inclusion in Action Scheduler.
     *
     * @param array Array containing each separate argument to pass to the hook's callback function.
     */
    if ( in_array( $hook, mwpcac_get_protected_hooks() ) ) {
        return null;
    }

    $job = as_get_scheduled_actions( array(
        'hook' => $hook,
        'args' => $args,
        'date' => $timestamp,
        'status' => 'pending'
    ), 'ids' );

    if ( ! empty( $job ) ) {
        as_cancel_action( $job[0] );
    }

	return true;
}

/**
 * Unschedules all events attached to the hook.
 *
 * Can be useful for plugins when deactivating to clean up the cron queue.
 *
 * Warning: This function may return Boolean FALSE, but may also return a non-Boolean
 * value which evaluates to FALSE. For information about casting to booleans see the
 * {@link https://php.net/manual/en/language.types.boolean.php PHP documentation}. Use
 * the `===` operator for testing the return value of this function.
 *
 * @param null|array $pre  Value to return instead. Default null to continue unscheduling the hook.
 * @param string     $hook Action hook, the execution of which will be unscheduled.
 * @return bool On success an integer indicating number of events unscheduled (0 indicates no
 *              events were registered on the hook), false if unscheduling fails.
 */
function mwpcac_pre_unschedule_hook( $pre, $hook ) {
    // Allow other filters to do their thing.
	if ( $pre !== null ) {
		return $pre;
	}

	/**
     * Filter to exclude a hook from inclusion in Action Scheduler.
     *
     * @param array Array containing each separate argument to pass to the hook's callback function.
     */
    if ( in_array( $hook, mwpcac_get_protected_hooks() ) ) {
        return null;
    }

	as_unschedule_all_actions( $hook, array() );

	return true;
}

/**
 * Unschedules all events attached to the hook with the specified arguments.
 *
 * Warning: This function may return Boolean FALSE, but may also return a non-Boolean
 * value which evaluates to FALSE. For information about casting to booleans see the
 * {@link https://php.net/manual/en/language.types.boolean.php PHP documentation}. Use
 * the `===` operator for testing the return value of this function.
 *
 * @param null|array $pre  Value to return instead. Default null to continue unscheduling the event.
 * @param string     $hook Action hook, the execution of which will be unscheduled.
 * @param array|null $args Arguments to pass to the hook's callback function, null to clear all
 *                         events regardless of arugments.
 * @return bool      On success an integer indicating number of events unscheduled (0 indicates no
 *                   events were registered with the hook and arguments combination), false if
 *                   unscheduling one or more events fail.
*/
function mwpcac_pre_clear_scheduled_hook( $pre, $hook, $args ) {
	// Allow other filters to do their thing.
	if ( $pre !== null ) {
		return $pre;
	}

	/**
     * Filter to exclude a hook from inclusion in Action Scheduler.
     *
     * @param array Array containing each separate argument to pass to the hook's callback function.
     */
    if ( in_array( $hook, mwpcac_get_protected_hooks() ) ) {
        return null;
    }

	as_unschedule_all_actions( $hook, $args );

	return true;
}

/**
 * Retrieve a scheduled event.
 *
 * Retrieve the full event object for a given event, if no timestamp is specified the next
 * scheduled event is returned.
 *
 * @param null|bool $pre       Value to return instead. Default null to continue retrieving the event.
 * @param string    $hook      Action hook of the event.
 * @param array     $args      Array containing each separate argument to pass to the hook's callback function.
 *                             Although not passed to a callback, these arguments are used to uniquely identify the
 *                             event.
 * @param int|null  $timestamp Unix timestamp (UTC) of the event. Null to retrieve next scheduled event.
 * @return bool|object The event object. False if the event does not exist.
 */
function mwpcac_pre_get_scheduled_event( $pre, $hook, $args, $timestamp ) {
	// Allow other filters to do their thing.
	if ( $pre !== null ) {
		return $pre;
	}

    /**
     * Filter to exclude a hook from inclusion in Action Scheduler.
     *
     * @param array Array containing each separate argument to pass to the hook's callback function.
     */
    if ( in_array( $hook, mwpcac_get_protected_hooks() ) ) {
        return null;
    }

    if ( ! $timestamp ) {
        $timestamp = as_next_scheduled_action( $hook, $args );
    }

    $job = as_get_scheduled_actions( array(
        'hook' => $hook,
        'args' => $args,
        'date' => $timestamp,
        'status' => 'pending'
    ), 'ids' );

    if ( ! empty( $job ) ) {
        $job_id = $job[0];

        $job_object = as_fetch_action( $job_id );

        $value = (object) array(
            'hook'      => $hook,
            'timestamp' => $timestamp,
            'schedule'  => false,
            'args'      => $args,
        );
    
        if ( $job_object->get_schedule()->get_recurrence() !== null ) {
            $value->interval = (int) $job_object->get_schedule()->get_recurrence();
        }

        return $value;
    }

    return false;
}

/**
 * Retrieve a scheduled event.
 *
 * Retrieve the full event object for a given event, if no timestamp is specified the next
 * scheduled event is returned.
 *
 * @param null|bool $pre       Value to return instead. Default null to continue retrieving the event.
 * @param string    $hook      Action hook of the event.
 * @param array     $args      Array containing each separate argument to pass to the hook's callback function.
 *                             Although not passed to a callback, these arguments are used to uniquely identify the
 *                             event.
 * @param int|null  $timestamp Unix timestamp (UTC) of the event. Null to retrieve next scheduled event.
 * @return bool|object The event object. False if the event does not exist.
 */
function mwpcac_get_protected_hooks() {
	$hooks = array(
        'action_scheduler_run_queue',
        'wp_site_health_scheduled_check',
        'recovery_mode_clean_expired_keys',
        'wp_scheduled_auto_draft_delete',
        'wp_privacy_delete_old_export_files',
        'wp_version_check',
        'wp_update_plugins',
        'wp_update_themes',
        'wp_scheduled_delete',
        'delete_expired_transients'
    );

    return array_merge( apply_filters( 'mwpcac_exclude_event_hooks', array() ), $hooks );
}

function mwpcac_remove_old_crons( $event ) {
    $crons = _get_cron_array();
    $key   = md5( serialize( $event->args ) );
    unset( $crons[ $event->timestamp ][ $event->hook ][ $key ] );
    if ( empty( $crons[ $event->timestamp ][ $event->hook ] ) ) {
        unset( $crons[ $event->timestamp ][ $event->hook ] );
    }
    if ( empty( $crons[ $event->timestamp ] ) ) {
        unset( $crons[ $event->timestamp ] );
    }

    _set_cron_array( $crons );
}