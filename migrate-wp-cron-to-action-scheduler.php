<?php
/**
 * Plugin Name: Migrate WP Cron to Action Scheduler
 * Plugin URI: https://wordpress.org/plugins/migrate-wp-cron-to-action-scheduler/
 * Description: The Migrate WP Cron to Action Scheduler plugin helps to easily migrate Native WordPress Cron to the Action Scheduler Library.
 * Version: 1.0.1
 * Author: Sayan Datta
 * Author URI: https://www.sayandatta.in
 * License: GPLv3
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
function mwpcac_plugin_activation() {
	Mwpcac\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'mwpcac_plugin_activation' );

/**
 * The code that runs during plugin deactivation
 */
function mwpcac_plugin_deactivation() {
	Mwpcac\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'mwpcac_plugin_deactivation' );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Mwpcac\\MwpcacLoader' ) ) {
	Mwpcac\MwpcacLoader::register_services();
}