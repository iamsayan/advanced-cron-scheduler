<?php
/**
 * Migrate Crons to Action Scheduler.
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
 * Migration class.
 */
class MigrateActions
{
	use Hooker, HelperFunctions;

	/**
	 * Register functions.
	 */
	public function register()
	{
        $this->action( 'mwpcac/after_plugin_activate', 'migrate_old_crons', 5 );
        $this->action( 'mwpcac/single_action_set', 'insert_hook', 5, 4 );
        $this->action( 'mwpcac/after_plugin_deactivate', 'regenerate_crons', 99 );
	}

	/**
	 * Migrate old Crons.
	 */
	public function migrate_old_crons()
	{
        global $wp_version;
        if ( version_compare( $wp_version, '5.1.0', '<' ) ) {
            return;
        }

        // remove action scheduler schema if already exists.
        delete_option( 'schema-ActionScheduler_StoreSchema' );

		$crons = _get_cron_array();
	    foreach ( $crons as $timestamp => $data ) {
	    	foreach ( $data as $hook => $schedule ) {
	    		foreach ( $schedule as $id => $info ) {
                    if ( ! in_array( $hook, $this->get_protected_hooks() ) ) {
	    			    if ( empty( $this->get_next_action_by_data( $hook, $info['args'], $timestamp ) ) ) {
	    			    	if ( empty( $info['schedule'] ) ) {
                                $this->set_single_action( $timestamp, $hook, $info['args'] );
                            } else {
                                $this->set_recurring_action( $timestamp, $info['interval'], $hook, $info['args'] );
                            }
	    			    }

                        // remove pre scheduled crons
                        $this->remove_old_crons( $timestamp, $hook, $info['args'] );
                    }
	    		}    
	    	}
	    }
	}

    /**
     * Insert hooks into db for future reference.
     *
     * @param int       $timestamp Timestamp for when to run the event.
     * @param string    $hook      Action hook, the execution of which will be unscheduled.
     * @param array     $args      Arguments to pass to the hook's callback function.
     */
    public function insert_hook( $timestamp, $hook, $args, $job_id )
	{
		$data = unserialize( get_option( 'mwpcac_single_action_hooks' ) );
		if ( empty( $data ) ) $data = [];

        $data[$job_id] = $hook;

		update_option( 'mwpcac_single_action_hooks', maybe_serialize( $data ) );
	}

    /**
	 * Re-Generate crons.
	 */
    public function regenerate_crons()
	{
        global $wpdb, $wp_version;
        if ( version_compare( $wp_version, '5.1.0', '<' ) ) {
            return;
        }

		$data = unserialize( get_option( 'mwpcac_single_action_hooks' ) );
		if ( empty( $data ) ) return;
 
        $table_name = $wpdb->prefix . 'actionscheduler_actions';
        $status = 'pending';
         
        $statement = $wpdb->prepare( "SELECT hook, scheduled_date_gmt, args FROM {$table_name} WHERE status = %s", $status );
        $values = $wpdb->get_results( $statement, ARRAY_A );
        
        foreach ( $values as $value ) {
            foreach ( $data as $id => $hook ) {
                if ( in_array( $value['hook'], $hook ) ) {
                    $this->generate_new_single_cron( strtotime( $value['scheduled_date_gmt'] ), $value['hook'], json_decode( $value['args'], true ) );
                    $this->cancel_scheduled_action( $id );
                }
            }
        }

        delete_option( 'mwpcac_single_action_hooks' );
        delete_option( 'schema-ActionScheduler_StoreSchema' );
	}

    /**
     * Generate new single cron.
     *
     * @param int       $timestamp Timestamp for when to run the event.
     * @param string    $hook      Action hook, the execution of which will be unscheduled.
     * @param array     $args      Arguments to pass to the hook's callback function.
     */
    private function generate_new_single_cron( $timestamp, $hook, $args )
    {
        // get crons
        $crons = _get_cron_array();
    
        // get keys
        $key = md5( serialize( $args ) );
        
        $crons[ $timestamp ][ $hook ][ $key ] = [
            'schedule' => false,
            'args'     => $args,
        ];

        uksort( $crons, 'strnatcasecmp' );
    
        // set cron array
        _set_cron_array( $crons );
    }

	/**
     * Remove old cron events.
     *
     * @param stdClass  $event {
     *     An object containing an event's data.
     *
     *     @type string       $hook      Action hook to execute when the event is run.
     *     @type int          $timestamp Unix timestamp (UTC) for when to next run the event.
     *     @type string|false $schedule  How often the event should subsequently recur.
     *     @type array        $args      Array containing each separate argument to pass to the hook's callback function.
     *     @type int          $interval  The interval time in seconds for the schedule. Only present for recurring events.
     * }
     */
    private function remove_old_crons( $timestamp, $hook, $args ) {
        // get crons
        $crons = _get_cron_array();
    
        // get keys
        $key = md5( serialize( $args ) );
        
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