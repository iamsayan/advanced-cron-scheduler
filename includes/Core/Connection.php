<?php
/**
 * Action links.
 *
 * @since      1.0.0
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Core;

use ACSWP\Plugin\Helpers\Scheduler;
use ACSWP\Plugin\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Site cache class.
 */
class Connection
{
	use HelperFunctions, Scheduler;

    /**
	 * List of Events.
	 *
	 * @var array
	 */
    protected $events = [];

	/**
	 * Register functions.
	 */
	public function register() {
		$this->filter( 'pre_schedule_event', 'pre_schedule_event', 10, 2 );
		$this->filter( 'pre_reschedule_event', 'pre_reschedule_event', 10, 2 );
		$this->filter( 'pre_unschedule_event', 'pre_unschedule_event', 10, 4 );
		$this->filter( 'pre_unschedule_hook', 'pre_unschedule_hook', 10, 2 );
		$this->filter( 'pre_clear_scheduled_hook', 'pre_clear_scheduled_hook', 10, 3 );
		$this->filter( 'pre_get_scheduled_event', 'pre_get_scheduled_event', 10, 4 );
        $this->filter( 'pre_get_ready_cron_jobs', 'pre_get_ready_cron_jobs' );
        $this->action( 'action_scheduler_init', 'register_crons' ); 
        $this->action( 'shutdown', 'clear_crons' ); 
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
    public function pre_schedule_event( $pre, $event ) {
        // Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}

        /**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         */
        if ( in_array( $event->hook, $this->get_protected_hooks() ) ) {
            return null;
        }

        if ( ! $this->is_as_initialized() ) {
            $this->events[ $event->hook ] = $event;

    		return false;
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
            $next_timestamp = $this->get_next_action( $event->hook, $event->args );
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
            if ( $this->has_next_action( $event->hook, $event->args ) ) {
                return false;
            }

            /** This filter is documented in wordpress/wp-includes/cron.php */
    	    $event = apply_filters( 'schedule_event', $event );
    
            return $this->set_recurring_action( $event->timestamp, $event->interval, $event->hook, $event->args );
        }
    }

    /**
     * Reschedules a recurring event.
     *
     * Note: The Action Scheduler reschedule behaviour is intentionally different to WordPress's.
     * To avoid drift of cron schedules, Action Scheduler adds the interval to the next scheduled
     * run time without checking if this time is in the past.
     *
     * To ensure the next run time is in the future, it is recommended you delete and reschedule
     * a job.
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
     * @return bool True if event successfully rescheduled. False for failure.
     */
    public function pre_reschedule_event( $pre, $event ) {
        // Allow other filters to do their thing.
        if ( $pre !== null ) {
            return $pre;
        }

        /**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         */
        if ( in_array( $event->hook, $this->get_protected_hooks() ) ) {
            return null;
        }

        if ( ! $this->is_as_initialized() ) {
    		return false;
    	}

        $job = $this->get_next_action_by_data( $event->hook, $event->args, $event->timestamp );
    
        if ( empty( $job ) ) {
            return false;
        }

        $this->cancel_scheduled_action( $job[0] );

        $now = time();
        $timestamp = $event->timestamp;
        $interval = $event->interval;

        if ( $timestamp >= $now ) {
            $timestamp = $now + $interval;
        } else {
            $timestamp = $now + ( $interval - ( ( $now - $timestamp ) % $interval ) );
        }
     
        return \wp_schedule_event( $timestamp, $event->schedule, $event->hook, $event->args );
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
    public function pre_unschedule_event( $pre, $timestamp, $hook, $args ) {
        // Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}

    	/**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }

        if ( ! $this->is_as_initialized() ) {
    		return false;
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
    public function pre_unschedule_hook( $pre, $hook ) {
        // Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}
    
    	/**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }

        if ( ! $this->is_as_initialized() ) {
    		return false;
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
    public function pre_clear_scheduled_hook( $pre, $hook, $args ) {
    	// Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}

    	/**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }

        if ( ! $this->is_as_initialized() ) {
    		return false;
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
    public function pre_get_scheduled_event( $pre, $hook, $args, $timestamp ) {
        // Allow other filters to do their thing.
    	if ( $pre !== null ) {
    		return $pre;
    	}

        /**
         * Filter to exclude a hook from inclusion in Action Scheduler.
         */
        if ( in_array( $hook, $this->get_protected_hooks() ) ) {
            return null;
        }

        if ( ! $this->is_as_initialized() ) {
    		return false;
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
            'args'      => $args,
        );

		$job = $this->get_next_action_by_data( $hook, $args, $timestamp );

		if ( ! empty( $job ) ) {
			$schedule = $this->get_schedule( $job[0] );
            if ( $schedule->is_recurring() ) {
                if ( method_exists( $schedule, 'get_recurrence' ) ) {
                    $event->schedule = $this->get_schedule_by_interval( $schedule->get_recurrence() );
				    $event->interval = (int) $schedule->get_recurrence();
                }
            }
		}
        
        return $event;
    }

    /**
     * Retrieve cron jobs ready to be run.
     *
     * Returns the results of _get_cron_array() limited to events ready to be run,
     * ie, with a timestamp in the past.
     *
     * @param null|array $pre Array of ready cron tasks to return instead. Default null
     *                        to continue using results from _get_cron_array().
     * @return array Cron jobs ready to be run.
     */
    public function pre_get_ready_cron_jobs( $pre ) {
        // Allow other filters to do their thing.
        if ( $pre !== null ) {
            return $pre;
        }

        $crons = [];
        $proceed = true;
        $wp_crons = _get_cron_array();
        if ( is_array( $wp_crons ) ) {
            $gmt_time = microtime( true );
            $keys     = array_keys( $wp_crons );
            if ( isset( $keys[0] ) && $keys[0] > $gmt_time ) {
                $proceed = false;
            }

            if ( $proceed ) {
                foreach ( $wp_crons as $timestamp => $cronhooks ) {
                    if ( $timestamp > $gmt_time ) {
                        break;
                    }
                    $crons[ $timestamp ] = $cronhooks;
                }
            }
        }

        if ( ! $this->is_as_initialized() ) {
    		return $crons;
    	}

        $results = $this->get_next_actions( [
			'date'         => gmdate( 'U' ),
			'date_compare' => '<=',
			'per_page'     => 100,
			'orderby'      => 'none',
		] );

        foreach ( $results as $action_id ) {
            $action = \ActionScheduler::store()->fetch_action( $action_id );

            $hook = $action->get_hook();
            $key = md5( serialize( $action->get_args() ) ); // PHPCS:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
            $value = [
                'args' => $action->get_args(),
                '_job' => $action,
            ];

            $timestamp = $action->get_schedule();
            if ( method_exists( $timestamp, 'get_recurrence' ) ) {
                $value['schedule'] = $this->get_schedule_by_interval( $timestamp->get_recurrence() );
                $value['interval'] = (int) $timestamp->get_recurrence();
            }

            $next = $timestamp->get_date();
            if ( $next ) {
                $timestamp = $next->getTimestamp();
            } else {
                $timestamp = strtotime( '0000-00-00 00:00:00' );
            }

            // Build the array up.
            if ( ! isset( $crons[ $timestamp ] ) ) {
                $crons[ $timestamp ] = [];
            }
            if ( ! isset( $crons[ $timestamp ][ $hook ] ) ) {
                $crons[ $timestamp ][ $hook ] = [];
            }
            $crons[ $timestamp ][ $hook ][ $key ] = $value;
        }

        ksort( $crons, SORT_NUMERIC );
        
        return $crons;
    }

    /**
     * Register scheduled events which are called before init hook.
     */
    public function register_crons() {
        foreach ( $this->events as $event ) {
            \wp_schedule_event( $event->timestamp, $event->schedule, $event->hook, $event->args );
        }
    }

    /**
     * Clean crons
     */
    public function clear_crons() {
        $this->events = [];
    }
}