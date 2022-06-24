<?php
/**
 * Plugin Name: Advanced Cron Scheduler
 * Plugin URI: https://wordpress.org/plugins/migrate-wp-cron-to-action-scheduler/
 * Description: The Advanced Cron Scheduler plugin helps to easily replace or migrate Native WordPress Cron to the Action Scheduler Library.
 * Version: 1.0.7
 * Author: Sayan Datta
 * Author URI: https://sayandatta.in
 * License: GPLv3
 * 
 * Advanced Cron Scheduler is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Advanced Cron Scheduler is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Advanced Cron Scheduler. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category Core
 * @package  Advanced Cron Scheduler
 * @author   Sayan Datta <iamsayan@protonmail.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://wordpress.org/plugins/migrate-wp-cron-to-action-scheduler/
 * 
 */

// If this file is called firectly, abort!!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ACS_PLUGIN_VERSION' ) ) {
	define( 'ACS_PLUGIN_VERSION', '1.0.7' );
}

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
if ( ! function_exists( 'acswp_plugin_activation' ) ) {
	function acswp_plugin_activation() {
		ACSWP\Plugin\Base\Activate::activate();
	}
}
register_activation_hook( __FILE__, 'acswp_plugin_activation' );

/**
 * The code that runs during plugin deactivation
 */
if ( ! function_exists( 'acswp_plugin_deactivation' ) ) {
	function acswp_plugin_deactivation() {
		ACSWP\Plugin\Base\Deactivate::deactivate();
	}
}
register_deactivation_hook( __FILE__, 'acswp_plugin_deactivation' );

/**
 * Initialize all the core classes of the plugin
 */
if ( ! function_exists( 'acswp_plugin_init' ) ) {
	function acswp_plugin_init() {
		if ( class_exists( 'ACSWP\Plugin\\Loader' ) ) {
			ACSWP\Plugin\Loader::register_services();
		}
	}
}
acswp_plugin_init();