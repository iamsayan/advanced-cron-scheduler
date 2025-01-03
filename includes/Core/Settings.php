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
use ACSWP\Plugin\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Settings class.
 */
class Settings
{
	use Hooker;
    use HelperFunctions;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_init', 'register_fields' );
		$this->action( 'admin_menu', 'add_settings_page' );
	}

	public function style() {
		?>
		<style>
			.acswp-settings {
				display: flex;
				flex-direction: column;
				gap: 6px;
			}
			.acswp-settings small {
				font-size: 12px;
				color: #666;
			}
		</style>
		<?php
	}

	/**
	 * Add settings page to menu
	 */
	public function add_settings_page() {
		$page = add_options_page(
			__( 'Advanced Cron Scheduler', 'migrate-wp-cron-to-action-scheduler' ),
			__( 'Advanced Scheduler', 'migrate-wp-cron-to-action-scheduler' ),
			'manage_options',
			'advanced-cron-scheduler',
			[ $this, 'render_settings_page' ]
		);

		$this->action( 'admin_print_styles-' . $page, 'style' );
	}

	/**
	 * Render the settings page
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'acswp_settings' );
				do_settings_sections( 'advanced-cron-scheduler' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register custom settings.
	 */
	public function register_fields() {
		$fields = [
			'admin_bar'                 => __( 'Show Admin Bar', 'migrate-wp-cron-to-action-scheduler' ),
			'unique_action'             => __( 'Enable Unique Actions', 'migrate-wp-cron-to-action-scheduler' ),
			'disable_past_due_checking' => __( 'Disable Past-Due Checking', 'migrate-wp-cron-to-action-scheduler' ),
			'data_retention_period'     => __( 'Data Retention Period', 'migrate-wp-cron-to-action-scheduler' ),
			'data_retention_unit'       => '',
			'queue_batch_size'          => __( 'Queue Batch Size', 'migrate-wp-cron-to-action-scheduler' ),
			'concurrent_batches'        => __( 'Concurrent Batches', 'migrate-wp-cron-to-action-scheduler' ),
			'timeout'                   => __( 'Timeout', 'migrate-wp-cron-to-action-scheduler' ),
			'time_limit'                => __( 'Time Limit', 'migrate-wp-cron-to-action-scheduler' ),
		];

		add_settings_section(
			'acswp_section',
			__( 'Settings', 'migrate-wp-cron-to-action-scheduler' ),
			[ $this, 'description' ],
			'advanced-cron-scheduler'
		);

		register_setting( 'acswp_settings', 'acswp_settings' );

		foreach ( $fields as $field => $name ) {
			if ( ! empty( $name ) ) {
				add_settings_field(
					$field,
					$name,
					[ $this, $field . '_field' ],
					'advanced-cron-scheduler',
					'acswp_section'
				);
			}
		}
	}

	/* 
	 * Description field
	 */
	public function description() { ?>
		<div id="acswp-settings"><?php esc_html_e( 'Customize Advanced Cron Scheduler Plugin settings here.', 'migrate-wp-cron-to-action-scheduler' ); ?></div>
		<?php
	}

	/* 
	 * Admin bar field
	 */
	public function admin_bar_field() { ?>
		<div class="acswp-settings">
			<label><input type="checkbox" name="acswp_settings[admin_bar]" value="1" <?php checked( $this->get_settings( 'admin_bar' ), 1 ); ?> /> <?php esc_html_e( 'Show Action Schedular Link in Admin Bar', 'migrate-wp-cron-to-action-scheduler' ); ?></label>
		</div>
		<?php
	}

	/* 
	 * Unique actions field
	 */
	public function unique_action_field() { ?>
		<div class="acswp-settings">
			<label><input type="checkbox" name="acswp_settings[unique_action]" value="1" <?php checked( $this->get_settings( 'unique_action' ), 1 ); ?> /> <?php esc_html_e( 'Enabling Unique Actions will prevent actions from being duplicated', 'migrate-wp-cron-to-action-scheduler' ); ?></label>
		</div>
		<?php
	}

	/* 
	 * Disable past-due actions checking field
	 */
	public function disable_past_due_checking_field() { ?>
		<div class="acswp-settings">
			<label><input type="checkbox" name="acswp_settings[disable_past_due_checking]" value="1" <?php checked( $this->get_settings( 'disable_past_due_checking' ), 1 ); ?> /> <?php esc_html_e( 'Disable Past-Due Actions Checking', 'migrate-wp-cron-to-action-scheduler' ); ?></label>
		</div>
		<?php
	}

	/* 
	 * Data retention period field
	 */
	public function data_retention_period_field() { ?>
		<div class="acswp-settings">
			<div style="display: flex;">
				<input type="number" name="acswp_settings[data_retention_period]" placeholder="30" value="<?php echo esc_attr( $this->get_settings( 'data_retention_period' ) ); ?>" style="width: 100px;" />
				<select name="acswp_settings[data_retention_unit]">
					<?php
					$units = [
						'minutes' => __( 'Minutes', 'migrate-wp-cron-to-action-scheduler' ),
						'hours'   => __( 'Hours', 'migrate-wp-cron-to-action-scheduler' ),
						'days'    => __( 'Days', 'migrate-wp-cron-to-action-scheduler' ),
						'weeks'   => __( 'Weeks', 'migrate-wp-cron-to-action-scheduler' ),
						'months'  => __( 'Months', 'migrate-wp-cron-to-action-scheduler' ),
						'years'   => __( 'Years', 'migrate-wp-cron-to-action-scheduler' ),
					];
					$selected_unit = $this->get_settings( 'data_retention_unit', 'days' );
					
					foreach ( $units as $value => $label ) {
						printf(
							'<option value="%s" %s>%s</option>',
							esc_attr( $value ),
							selected( $selected_unit, $value, false ),
							esc_html( $label )
						);
					}
					?>
				</select>
			</div>
			<small>By default, Action Scheduler deletes completed actions every 30 days. Use this field to set a default actions delete duration.</small>
		</div>
		<?php
	}

	/* 
	 * Queue batch size field
	 */
	public function queue_batch_size_field() { ?>
		<div class="acswp-settings">
			<input type="number" min="25" name="acswp_settings[queue_batch_size]" placeholder="25" value="<?php echo esc_attr( $this->get_settings( 'queue_batch_size' ) ); ?>" style="width: 100px;" />
			<small><?= esc_html__( 'By default, Action Scheduler claims a batch of 25 actions. This small batch size is because the default time limit is only 30 seconds. However, if your actions are processing very quickly use this field to increase the batch size.', 'migrate-wp-cron-to-action-scheduler' ); ?></small>
		</div>
		<?php
	}

	/* 
	 * Concurrent batches field
	 */
	public function concurrent_batches_field() { ?>
		<div class="acswp-settings">
			<input type="number" min="1" name="acswp_settings[concurrent_batches]" placeholder="1" value="<?php echo esc_attr( $this->get_settings( 'concurrent_batches' ) ); ?>" style="width: 100px;" />
			<small><?= esc_html__( 'By default, Action Scheduler will run only one concurrent batches of actions. This is to prevent consuming a lot of available connections or processes on your webserver.', 'migrate-wp-cron-to-action-scheduler' ); ?></small>
		</div>
		<?php
	}

	/* 
	 * Timeout field
	 */
	public function timeout_field() {
		?>
		<div class="acswp-settings">
			<input type="number" min="5" name="acswp_settings[timeout]" placeholder="5" value="<?php echo esc_attr( $this->get_settings( 'timeout' ) ); ?>" style="width: 100px;" />
			<small><?= esc_html__( 'By default Action scheduler reset actions claimed for more than 5 minutes (300 seconds). Because we are increasing the batch size, we also want to increase the amount of time given to queues before reseting claimed actions.', 'migrate-wp-cron-to-action-scheduler' ); ?></small>
		</div>
		<?php
	}

	/* 
	 * Time limit field
	 */
	public function time_limit_field() {
		?>
		<div class="acswp-settings">
			<input type="number" min="30" name="acswp_settings[time_limit]" placeholder="30" value="<?php echo esc_attr( $this->get_settings( 'time_limit' ) ); ?>" style="width: 100px;" />
			<small><?= esc_html__( 'By default, Action Scheduler will only process actions for a maximum of 30 seconds in each request. This time limit minimises the risk of a script timeout on unknown hosting environments, some of which enforce 30 second timeouts. If your host supports time limits longer than this for web requests, use this field to increase this time limit. This allows more actions to be processed in each request and reduces the lag between processing each queue, greatly speeding up the processing rate of scheduled actions.', 'migrate-wp-cron-to-action-scheduler' ); ?></small>
		</div>
		<?php
	}
}