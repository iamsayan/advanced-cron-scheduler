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
	use HelperFunctions, Scheduler;

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
	    foreach ( $crons as $timestamp => $data ) {
	    	foreach ( $data as $hook => $schedule ) {
	    		foreach ( $schedule as $id => $info ) {
                    if ( in_array( $hook, $this->get_protected_hooks() ) ) {
                        continue;
                    }

                    if ( ! empty( $info['schedule'] ) && isset( $info['interval'] ) ) {
                        if ( ! $this->has_next_action( $hook, $info['args'] ) ) {
                            $this->set_recurring_action( $timestamp, $info['interval'], $hook, $info['args'] );
                        }
                    } else {
                        if ( empty( $this->get_next_action_by_data( $hook, $info['args'], $timestamp ) ) ) {
                            $this->set_single_action( $timestamp, $hook, $info['args'] );
                        }
                    }

                    // remove pre scheduled crons
                    $this->remove_wp_cron( $timestamp, $hook, $info['args'] );
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

        $statement = $wpdb->prepare( "SELECT a.action_id, a.hook, a.scheduled_date_gmt, a.args, g.slug AS `group` FROM {$wpdb->actionscheduler_actions} a LEFT JOIN {$wpdb->actionscheduler_groups} g ON a.group_id=g.group_id WHERE a.status=%s AND g.slug=%s", 'pending', 'mwpcac' );
        $values = $wpdb->get_results( $statement, ARRAY_A ); // PHPCS:ignore WordPress.DB.PreparedSQL.NotPrepared
        foreach ( $values as $value ) {
            $this->generate_wp_cron( strtotime( $value['scheduled_date_gmt'] ), $value['hook'], json_decode( $value['args'], true ) );
            $this->cancel_scheduled_action( $value['action_id'] );
        }
	}

    /**
     * Generate new single cron.
     *
     * @param int       $timestamp Timestamp for when to run the event.
     * @param string    $hook      Action hook, the execution of which will be unscheduled.
     * @param array     $args      Arguments to pass to the hook's callback function.
     */
    private function generate_wp_cron( $timestamp, $hook, $args ) {
        // get crons
        $crons = _get_cron_array();
        if ( ! is_array( $crons ) ) {
            $crons = [];
        }
    
        // get keys
        $key = md5( serialize( $args ) ); // PHPCS:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
        
        $crons[ $timestamp ][ $hook ][ $key ] = [
            'schedule' => false,
            'args'     => $args,
        ];

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
}