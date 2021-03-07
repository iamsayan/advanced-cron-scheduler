<?php
/**
 * Action links.
 *
 * @since      1.0.0
 * @package    Migrate WP Cron to Action Scheduler
 * @subpackage Mwpcac\Base
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Mwpcac\Core;

use Mwpcac\Helpers\Hooker;
use Mwpcac\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Site cache class.
 */
class Connection
{
	use Hooker, HelperFunctions;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->filter( 'pre_schedule_event', 'pre_schedule_event', 5, 2 );
		$this->filter( 'pre_unschedule_event', 'pre_unschedule_event', 5, 4 );
		$this->filter( 'pre_unschedule_hook', 'pre_unschedule_hook', 5, 2 );
		$this->filter( 'pre_clear_scheduled_hook', 'pre_clear_scheduled_hook', 5, 3 );
		$this->filter( 'pre_get_scheduled_event', 'pre_get_scheduled_event', 5, 4 );
	}

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
    public function pre_schedule_event( $pre, $event )
	{
        // Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}
    
    	/**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         *
         * @param array Array containing each separate argument to pass to the hook's callback function.
         */
        if ( in_array( $event->hook, $this->get_protected_hooks() ) ) {
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

            return $this->set_single_action( $event->timestamp, $event->hook, $event->args );
        } else {
            if ( $this->get_next_action( $event->hook, $event->args ) ) {
                return false;
            }

            /** This filter is documented in wordpress/wp-includes/cron.php */
    	    $event = apply_filters( 'schedule_event', $event );
    
            return $this->set_recurring_action( $event->timestamp, $event->interval, $event->hook, $event->args );
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
    public function pre_unschedule_event( $pre, $timestamp, $hook, $args )
	{
        // Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}
    
    	/**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         *
         * @param array Array containing each separate argument to pass to the hook's callback function.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }
    
        $job = $this->get_next_action_by_data( $hook, $args, $timestamp );
    
        if ( ! empty( $job ) ) {
            $this->cancel_scheduled_action( $job[0] );
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
    public function pre_unschedule_hook( $pre, $hook )
	{
        // Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}
    
    	/**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         *
         * @param array Array containing each separate argument to pass to the hook's callback function.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }
    
    	$this->unschedule_all_actions( $hook, [] );
    
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
    public function pre_clear_scheduled_hook( $pre, $hook, $args )
	{
    	// Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}
    
    	/**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         *
         * @param array Array containing each separate argument to pass to the hook's callback function.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }
    
    	$this->unschedule_all_actions( $hook, $args );
    
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
    public function pre_get_scheduled_event( $pre, $hook, $args, $timestamp )
	{
    	// Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}
    
        /**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         *
         * @param array Array containing each separate argument to pass to the hook's callback function.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }
    
        if ( null !== $timestamp && ! is_numeric( $timestamp ) ) {
            return false;
        }
    
        if ( ! $timestamp ) {
            $timestamp = $this->get_next_action( $hook, $args );
        }
    
        if ( empty( $timestamp ) ) {
            return false;
        }
       
        $event = (object) array(
            'hook'      => $hook,
            'timestamp' => $timestamp,
            'schedule'  => false,
            'args'      => $args
        );

		$job = $this->get_next_action_by_data( $hook, $args, $timestamp );

		if ( ! empty( $job ) ) {
			$recurrence = $this->get_recurrence( $job[0] );
            if ( $recurrence->is_recurring() ) {
                if ( method_exists( $recurrence, 'get_recurrence' ) ) {
                    $event->schedule = $this->get_schedule_by_interval( $recurrence->get_recurrence() );
				    $event->interval = (int) $recurrence->get_recurrence();
                }
            }
		}
        
        return $event;
    }
}