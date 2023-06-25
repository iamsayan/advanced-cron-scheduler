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
		$fields = [
			'acswp_admin_bar'                 => __( 'Show Admin Bar', 'migrate-wp-cron-to-action-scheduler' ),
			'acswp_unique_action'             => __( 'Enable Unique Actions', 'migrate-wp-cron-to-action-scheduler' ),
			'acswp_disable_past_due_checking' => __( 'Disable Past-Due Checking', 'migrate-wp-cron-to-action-scheduler' ),
		];

		add_settings_section( 'acswp-settings', __( 'Advanced Cron Scheduler', 'migrate-wp-cron-to-action-scheduler' ), [ $this, 'description' ], 'general' );

		foreach ( $fields as $field => $name ) {
			register_setting( 'general', $field );
			add_settings_field( $field, $name, [ $this, str_replace( 'acswp_', '', $field ) . '_field' ], 'general', 'acswp-settings' );
		}
	}

	/* 
	 * Print settings field
	 */
	public function description() { ?>
		<div id="acswp-settings"><?php esc_html_e( 'Customize Advanced Cron Scheduler Plugin settings here.', 'migrate-wp-cron-to-action-scheduler' ); ?></div>
		<?php
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

	/* 
	 * Print settings field
	 */
	public function disable_past_due_checking_field() { ?>
		<label><input type="checkbox" name="acswp_disable_past_due_checking" value="1" <?php checked( get_option( 'acswp_disable_past_due_checking' ), 1 ); ?> /> <?php esc_html_e( 'Disable Past-Due Actions Checking', 'migrate-wp-cron-to-action-scheduler' ); ?></label>
		<?php
	}
}