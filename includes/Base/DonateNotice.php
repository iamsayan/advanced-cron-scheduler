<?php 
/**
 * Donation notice.
 *
 * @since      1.0.0
 * @package    WP Cron Action Schedular
 * @subpackage Mwpcac\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace Mwpcac\Base;

use Mwpcac\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Donation Notice class.
 */
class DonateNotice
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_notices', 'show_notice' );
		$this->action( 'admin_init', 'dismiss_notice' );
	}
	
	/**
	 * Show admin notices.
	 */
	public function show_notice() {
		// Show notice after 240 hours (10 days) from installed time.
		if ( $this->calculate_time() > strtotime( '-360 hours' )
			|| '1' === get_option( 'mwpcac_plugin_dismiss_donate_notice' )
			|| ! current_user_can( 'manage_options' )
			|| apply_filters( 'mwpcac/hide_sticky_donate_notice', false ) ) {
			return;
		}
	
		$dismiss = wp_nonce_url( add_query_arg( 'mwpcac_donate_notice_action', 'dismiss_donate_true' ), 'mwpcac_dismiss_donate_true' ); 
		$no_thanks = wp_nonce_url( add_query_arg( 'mwpcac_donate_notice_action', 'no_thanks_donate_true' ), 'mwpcac_no_thanks_donate_true' ); ?>
		
		<div class="notice notice-success">
			<p><?php esc_html_e( 'Hey, I noticed you\'ve been using WP Cron Action Schedular for more than 2 week – that’s awesome! If you like WP Cron Action Schedular and you are satisfied with the plugin, isn’t that worth a coffee or two? Please consider donating. Donations help me to continue support and development of this free plugin! Thank you very much!', 'migrate-wp-cron-to-action-scheduler' ); ?></p>
			<p><a href="https://www.paypal.me/iamsayan" target="_blank" class="button button-secondary"><?php esc_html_e( 'Donate Now', 'migrate-wp-cron-to-action-scheduler' ); ?></a>&nbsp;
			<a href="<?php echo esc_url( $dismiss ); ?>" class="already-did"><strong><?php esc_html_e( 'I already donated', 'migrate-wp-cron-to-action-scheduler' ); ?></strong></a>&nbsp;<strong>|</strong>
			<a href="<?php echo esc_url( $no_thanks ); ?>" class="later"><strong><?php esc_html_e( 'Nope&#44; maybe later', 'migrate-wp-cron-to-action-scheduler' ); ?></strong></a>&nbsp;<strong>|</strong>
			<a href="<?php echo esc_url( $dismiss ); ?>" class="hide"><strong><?php esc_html_e( 'I don\'t want to donate', 'migrate-wp-cron-to-action-scheduler' ); ?></strong></a></p>
		</div>
	<?php
	}
	
	/**
	 * Dismiss admin notices.
	 */
	public function dismiss_notice() {
		if ( get_option( 'mwpcac_plugin_no_thanks_donate_notice' ) === '1' ) {
			if ( get_option( 'mwpcac_plugin_dismissed_time_donate' ) > strtotime( '-360 hours' ) ) {
				return;
			}
			delete_option( 'mwpcac_plugin_dismiss_donate_notice' );
			delete_option( 'mwpcac_plugin_no_thanks_donate_notice' );
		}
	
		if ( ! isset( $_REQUEST['mwpcac_donate_notice_action'] ) ) {
			return;
		}
	
		if ( 'dismiss_donate_true' === $_REQUEST['mwpcac_donate_notice_action'] ) {
			check_admin_referer( 'mwpcac_dismiss_donate_true' );
			update_option( 'mwpcac_plugin_dismiss_donate_notice', '1' );
		}
	
		if ( 'no_thanks_donate_true' === $_REQUEST['mwpcac_donate_notice_action'] ) {
			check_admin_referer( 'mwpcac_no_thanks_donate_true' );
			update_option( 'mwpcac_plugin_no_thanks_donate_notice', '1' );
			update_option( 'mwpcac_plugin_dismiss_donate_notice', '1' );
			update_option( 'mwpcac_plugin_dismissed_time_donate', time() );
		}
	
		wp_redirect( remove_query_arg( 'mwpcac_donate_notice_action' ) );
		exit;
	}
	
	/**
	 * Calculate install time.
	 */
	private function calculate_time() {
		$installed_time = get_option( 'mwpcac_plugin_installed_time_donate' );
		
		if ( ! $installed_time ) {
			$installed_time = time();
			update_option( 'mwpcac_plugin_installed_time_donate', $installed_time );
		}

		return $installed_time;
	}
}