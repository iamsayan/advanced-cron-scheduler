<?php
/**
 * Migrate Crons to Action Scheduler.
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
 * Migration class.
 */
class MigrateActions
{
	use HelperFunctions;
    use Scheduler;

	/**
	 * Register functions.
	 */
	public function register() {
        $this->action( 'acswp/plugin_activate', 'migrate_old_crons' );
        $this->action( 'acswp/plugin_deactivate', 'regenerate_crons' );
	}

	/**
	 * Migrate old Crons.
	 */
	public function migrate_old_crons() {
        global $wp_version;
        if ( version_compare( $wp_version, '5.2.0', '<' ) ) {
            return;
        }

		$crons = _get_cron_array();
        if ( empty( $crons ) || ! is_array( $crons ) ) {
            return;
        }

        $protected_hooks = $this->get_protected_hooks();
        
        foreach ( $crons as $timestamp => $hooks ) {
            if ( ! is_array( $hooks ) ) {
                continue;
            }

            foreach ( $hooks as $hook => $schedules ) {
                // Skip protected hooks early
                if ( in_array( $hook, $protected_hooks, true ) ) {
                    continue;
                }

                foreach ( $schedules as $info ) {
                    if ( ! is_array( $info ) || ! isset( $info['args'] ) ) {
                        continue;
                    }

                    // Handle recurring events
                    if ( ! empty( $info['schedule'] ) && isset( $info['interval'] ) ) {
                        $this->maybe_set_recurring_action( $timestamp, $hook, $info );
                        continue;
                    }

                    // Handle one-time events
                    $this->maybe_set_single_action( $timestamp, $hook, $info );
                }
            }
        }
	}

    /**
	 * Re-Generate crons.
	 */
    public function regenerate_crons() {
        global $wpdb, $wp_version;
        if ( version_compare( $wp_version, '5.2.0', '<' ) ) {
            return;
        }

        $statement = $wpdb->prepare( "SELECT a.action_id, a.hook, a.scheduled_date_gmt, g.slug AS `group` FROM {$wpdb->actionscheduler_actions} a LEFT JOIN {$wpdb->actionscheduler_groups} g ON a.group_id=g.group_id WHERE a.status=%s AND g.slug=%s", 'pending', 'mwpcac' );
        $values    = $wpdb->get_results( $statement, ARRAY_A ); // PHPCS:ignore WordPress.DB.PreparedSQL.NotPrepared
        
        foreach ( $values as $key => $value ) {
            $action = $this->get_action( $value['action_id'] );
            if ( ! $action ) {
                continue;
            }

            $values[ $key ]['args']     = $action->get_args();
            $values[ $key ]['schedule'] = false;
            $values[ $key ]['interval'] = 0;

            $schedule = $action->get_schedule();
            if ( $schedule && method_exists( $schedule, 'get_recurrence' ) ) {
                $recurrence                 = (int) $schedule->get_recurrence();
                $values[ $key ]['schedule'] = $this->get_schedule_by_interval( $recurrence );
                $values[ $key ]['interval'] = $recurrence;
            }
        }

        foreach ( $values as $value ) {
            $this->generate_wp_cron( strtotime( $value['scheduled_date_gmt'] ), $value['hook'], $value['args'], $value['schedule'], $value['interval'] );
            $this->cancel_scheduled_action( $value['action_id'] );
        }
	}

    /**
     * Generate new single cron.
     *
     * @param int            $timestamp Timestamp for when to run the event.
     * @param string         $hook      Action hook, the execution of which will be unscheduled.
     * @param array          $args      Arguments to pass to the hook's callback function.
     * @param string|false   $schedule  Schedule.
     * @param int|null       $interval  Interval.
     */
    private function generate_wp_cron( $timestamp, $hook, $args, $schedule, $interval ) {
        // get crons
        $crons = _get_cron_array();
        if ( ! is_array( $crons ) ) {
            $crons = [];
        }
    
        // get keys
        $key = md5( serialize( $args ) ); // PHPCS:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize

        $cron = [
            'schedule' => $schedule,
            'args'     => $args,
        ];

        if ( ! empty( $interval ) ) {
            $cron['interval'] = $interval;
        }
        
        $crons[ $timestamp ][ $hook ][ $key ] = $cron;

        uksort( $crons, 'strnatcasecmp' );
    
        // set cron array
        _set_cron_array( $crons );
    }

	/**
     * Remove WP Cron Event.
     *
     * @param int       $timestamp Timestamp for when to run the event.
     * @param string    $hook      Action hook, the execution of which will be unscheduled.
     * @param array     $args      Arguments to pass to the hook's callback function.
     */
    private function remove_wp_cron( $timestamp, $hook, $args ) {
        // get crons
        $crons = _get_cron_array();
    
        // get keys
        $key = md5( serialize( $args ) ); // PHPCS:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
        
        unset( $crons[ $timestamp ][ $hook ][ $key ] );
        
        if ( empty( $crons[ $timestamp ][ $hook ] ) ) {
            unset( $crons[ $timestamp ][ $hook ] );
        }

        if ( empty( $crons[ $timestamp ] ) ) {
            unset( $crons[ $timestamp ] );
        }
    
        // set cron array
        _set_cron_array( $crons );
    }

    /**
     * Handle recurring action migration
     */
    private function maybe_set_recurring_action( $timestamp, $hook, $info ) {
        if ( ! $this->has_next_action( $hook, $info['args'] ) ) {
            $this->set_recurring_action( $timestamp, $info['interval'], $hook, $info['args'] );
            $this->remove_wp_cron( $timestamp, $hook, $info['args'] );
        }
    }

    /**
     * Handle single action migration
     */
    private function maybe_set_single_action( $timestamp, $hook, $info ) {
        if ( empty( $this->get_next_action_by_data( $hook, $info['args'], $timestamp ) ) ) {
            $this->set_single_action( $timestamp, $hook, $info['args'] );
            $this->remove_wp_cron( $timestamp, $hook, $info['args'] );
        }
    }
}