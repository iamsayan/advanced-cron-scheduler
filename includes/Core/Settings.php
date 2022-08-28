<?php
/**
 * Settings fields.
 *
 * @since      1.0.8
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Core
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Core;

use ACSWP\Plugin\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Bar class.
 */
class Settings
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_init', 'register_fields' );
	}

	/**
	 * Register custom settings.
	 */
	public function register_fields() {
		register_setting( 'general', 'acswp_admin_bar' );
		add_settings_field( 'acswp_admin_bar', __( 'Show Admin Bar', 'migrate-wp-cron-to-action-scheduler' ), [ $this, 'admin_bar_field' ], 'general' );

		register_setting( 'general', 'acswp_unique_action' );
		add_settings_field( 'acswp_unique_action', __( 'Enable Unique Actions', 'migrate-wp-cron-to-action-scheduler' ), [ $this, 'unique_action_field' ], 'general' );
	}

	/* 
	 * Print settings field
	 */
	public function admin_bar_field() { ?>
		<label><input type="checkbox" name="acswp_admin_bar" value="1" <?php checked( get_option( 'acswp_admin_bar' ), 1 ); ?> /> <?php esc_html_e( 'Show Action Schedular Link in Admin Bar', 'migrate-wp-cron-to-action-scheduler' ); ?></label>
		<?php
	}

	/* 
	 * Print settings field
	 */
	public function unique_action_field() { ?>
		<label><input type="checkbox" name="acswp_unique_action" value="1" <?php checked( get_option( 'acswp_unique_action' ), 1 ); ?> /> <?php esc_html_e( 'Enabling Unique Actions will prevent actions from being duplicated', 'migrate-wp-cron-to-action-scheduler' ); ?></label>
		<?php
	}
}