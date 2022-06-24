<?php
/**
 * Plugin Name: WP Cron Action Schedular
 * Plugin URI: https://wordpress.org/plugins/migrate-wp-cron-to-action-scheduler/
 * Description: The WP Cron Action Schedular plugin helps to easily replace or migrate Native WordPress Cron to the Action Scheduler Library.
 * Version: 1.0.6
 * Author: Sayan Datta
 * Author URI: https://sayandatta.in
 * License: GPLv3
 * 
 * WP Cron Action Schedular is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * WP Cron Action Schedular is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Cron Action Schedular. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category Core
 * @package  WP Cron Action Schedular
 * @author   Sayan Datta <iamsayan@protonmail.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://wordpress.org/plugins/migrate-wp-cron-to-action-scheduler/
 * 
 */

// If this file is called firectly, abort!!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
if ( ! function_exists( 'mwpcac_plugin_activation' ) ) {
	function mwpcac_plugin_activation() {
		Mwpcac\Base\Activate::activate();
	}
}
register_activation_hook( __FILE__, 'mwpcac_plugin_activation' );

/**
 * The code that runs during plugin deactivation
 */
if ( ! function_exists( 'mwpcac_plugin_deactivation' ) ) {
	function mwpcac_plugin_deactivation() {
		Mwpcac\Base\Deactivate::deactivate();
	}
}
register_deactivation_hook( __FILE__, 'mwpcac_plugin_deactivation' );

/**
 * Initialize all the core classes of the plugin
 */
if ( ! function_exists( 'mwpcac_plugin_init' ) ) {
	function mwpcac_plugin_init() {
		if ( class_exists( 'Mwpcac\\Loader' ) ) {
			Mwpcac\Loader::register_services();
		}
	}
}
mwpcac_plugin_init();

add_action('init', function() {
    if ( ! wp_next_scheduled ( 'svd_cron' ) ) {
		error_log('fgfg');
        wp_schedule_event( time(), 'daily', 'svd_cron' );
    }
});