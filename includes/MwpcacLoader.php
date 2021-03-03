<?php
/**
 * Register all classes
 *
 * @since      1.0.0
 * @package    Migrate WP Cron to Action Scheduler
 * @subpackage Mwpcac\Core
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Mwpcac;

/**
 * Mwpcac Main Class.
 */
final class MwpcacLoader
{
	/**
	 * Store all the classes inside an array
	 * 
	 * @return array Full list of classes
	 */
	public static function get_services() 
	{
		$services = [
			Base\Actions::class,
			Base\AdminNotice::class,
			Base\DonateNotice::class,
			Base\Localization::class,
			Base\RatingNotice::class,
			Core\Connection::class,
			Core\MigrateActions::class,
			Core\SiteCache::class,
			Tools\DatabaseTable::class
		];

		return $services;
	}

	/**
	 * Loop through the classes, initialize them, 
	 * and call the register() method if it exists
	 */
	public static function register_services() 
	{
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
	private static function instantiate( $class )
	{
		$service = new $class();

		return $service;
	}
}