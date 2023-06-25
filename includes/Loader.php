<?php
/**
 * Register all classes
 *
 * @since      1.0.0
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Core
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin;

/**
 * Mwpcac Main Class.
 */
final class Loader
{
	/**
	 * Store all the classes inside an array
	 * 
	 * @return array Full list of classes
	 */
	public static function get_services() {
		$services = [
			Base\Actions::class,
			Base\AdminNotice::class,
			Base\Localization::class,
			Core\Connection::class,
			Core\MigrateActions::class,
			Core\Admin::class,
			Core\Settings::class,
		];

		return $services;
	}

	/**
	 * Loop through the classes, initialize them, 
	 * and call the register() method if it exists
	 */
	public static function register_services() {
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Initialize the class
	 * 
	 * @param  class $class    class from the services array
	 * @return class instance  new instance of the class
	 */
	private static function instantiate( $class ) {
		$service = new $class();

		return $service;
	}
}