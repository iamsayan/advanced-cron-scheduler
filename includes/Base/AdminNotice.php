<?php 
/**
 * Admin notices.
 *
 * @since      1.0.0
 * @package    Advanced Cron Scheduler
 * @subpackage ACSWP\Plugin\Base
 * @author     Sayan Datta <iamsayan@protonmail.com>
 */

namespace ACSWP\Plugin\Base;

use ACSWP\Plugin\Helpers\Hooker;
use ACSWP\Plugin\Base\BaseController;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Notice class.
 */
class AdminNotice extends BaseController
{
	use Hooker;
	
	/**
	 * Register functions.
	 */
	public function register() {
		$this->action( 'admin_notices', 'notice' );
		$this->action( 'admin_init', 'dismiss_notice' );
	}
	
	/**
	 * Show internal admin notices.
	 */
	public function notice() {
		// Check transient, if available display notice
		if ( get_transient( 'acswp-show-notice-on-activation' ) !== false ) { ?>
			<div class="notice notice-success">
				<p><strong><?php
				/* translators: %s: Plugin Name */ 
				printf( wp_kses_post( __( 'Thanks for installing %1$s v%2$s plugin. Click <a href="%3$s">here</a> to view Action Scheduler tasks.', 'migrate-wp-cron-to-action-scheduler' ) ), 'Advanced Cron Scheduler', esc_html( ACSWP_VERSION ), esc_url( admin_url( 'tools.php?page=action-scheduler' ) ) ); ?></strong></p>
			</div> <?php
		    delete_transient( 'acswp-show-notice-on-activation' );
		}

		$show_rating = true;
		if ( $this->calculate_time() > strtotime( '-7 days' )
	    	|| '1' === get_option( 'acswp_plugin_dismiss_rating_notice' )
			|| apply_filters( 'acswp/hide_sticky_rating_notice', false ) ) {
			$show_rating = false;
        }
    
		if ( $show_rating ) {
			$dismiss = wp_nonce_url( add_query_arg( 'acswp_notice_action', 'dismiss_rating' ), 'acswp_notice_nonce' );
			$no_thanks = wp_nonce_url( add_query_arg( 'acswp_notice_action', 'no_thanks_rating' ), 'acswp_notice_nonce' ); ?>

			<div class="notice notice-success">
				<p><?php echo wp_kses_post( 'Hey, I noticed you\'ve been using Advanced Cron Scheduler for more than 1 week – that’s awesome! Could you please do me a BIG favor and give it a <strong>5-star</strong> rating on WordPress? Just to help us spread the word and boost my motivation.', 'migrate-wp-cron-to-action-scheduler' ); ?></p>
            	<p><a href="https://wordpress.org/support/plugin/migrate-wp-cron-to-action-scheduler/reviews/?filter=5#new-post" target="_blank" class="button button-secondary" rel="noopener"><?php esc_html_e( 'Ok, you deserve it', 'migrate-wp-cron-to-action-scheduler' ); ?></a>&nbsp;
				<a href="<?php echo esc_url( $dismiss ); ?>" class="already-did"><strong><?php esc_html_e( 'I already did', 'migrate-wp-cron-to-action-scheduler' ); ?></strong></a>&nbsp;<strong>|</strong>
				<a href="<?php echo esc_url( $no_thanks ); ?>" class="later"><strong><?php esc_html_e( 'Nope&#44; maybe later', 'migrate-wp-cron-to-action-scheduler' ); ?></strong></a></p>
			</div>
			<?php
		}

		$show_donate = true;
		if ( $this->calculate_time() > strtotime( '-240 hours' )
			|| '1' === get_option( 'acswp_plugin_dismiss_donate_notice' )
			|| apply_filters( 'acswp/hide_sticky_donate_notice', false ) ) {
			$show_donate = false;
		}

		if ( $show_donate ) {
			$dismiss = wp_nonce_url( add_query_arg( 'acswp_notice_action', 'dismiss_donate' ), 'acswp_notice_nonce' );
			$no_thanks = wp_nonce_url( add_query_arg( 'acswp_notice_action', 'no_thanks_donate' ), 'acswp_notice_nonce' ); ?>

			<div class="notice notice-success">
				<p><?php echo wp_kses_post( 'Hey, I noticed you\'ve been using Advanced Cron Scheduler for more than 2 week – that’s awesome! If you like Advanced Cron Scheduler and you are satisfied with the plugin, isn’t that worth a coffee or two? Please consider donating. Donations help me to continue support and development of this free plugin! Thank you very much!', 'migrate-wp-cron-to-action-scheduler' ); ?></p>
				<p><a href="https://www.paypal.me/iamsayan" target="_blank" class="button button-secondary" rel="noopener"><?php esc_html_e( 'Donate Now', 'migrate-wp-cron-to-action-scheduler' ); ?></a>&nbsp;
				<a href="<?php echo esc_url( $dismiss ); ?>" class="already-did"><strong><?php esc_html_e( 'I already donated', 'migrate-wp-cron-to-action-scheduler' ); ?></strong></a>&nbsp;<strong>|</strong>
				<a href="<?php echo esc_url( $no_thanks ); ?>" class="later"><strong><?php esc_html_e( 'Nope&#44; maybe later', 'migrate-wp-cron-to-action-scheduler' ); ?></strong></a></p>
			</div>
			<?php
		}
	}

		/**
	 * Dismiss admin notices.
	 */
	public function dismiss_notice() {
		// Check for Rating Notice
		if ( get_option( 'acswp_plugin_no_thanks_rating_notice' ) === '1'
			&& get_option( 'acswp_plugin_dismissed_time' ) <= strtotime( '-7 days' ) ) {
			delete_option( 'acswp_plugin_dismiss_rating_notice' );
			delete_option( 'acswp_plugin_no_thanks_rating_notice' );
		}

		// Check for Donate Notice
		if ( get_option( 'acswp_plugin_no_thanks_donate_notice' ) === '1'
			&& get_option( 'acswp_plugin_dismissed_time_donate' ) <= strtotime( '-14 days' ) ) {
			delete_option( 'acswp_plugin_dismiss_donate_notice' );
			delete_option( 'acswp_plugin_no_thanks_donate_notice' );
		}

		if ( ! isset( $_REQUEST['acswp_notice_action'] ) || empty( $_REQUEST['acswp_notice_action'] ) ) {
			return;
		}

		check_admin_referer( 'acswp_notice_nonce' );

		$notice = sanitize_text_field( wp_unslash( $_REQUEST['acswp_notice_action'] ) );
		$notice = explode( '_', $notice );
		$notice_type = end( $notice );
		array_pop( $notice );
		$notice_action = join( '_', $notice );

		if ( 'dismiss' === $notice_action ) {
			update_option( 'acswp_plugin_dismiss_' . $notice_type . '_notice', '1' );
		}
	
		if ( 'no_thanks' === $notice_action ) {
			update_option( 'acswp_plugin_no_thanks_' . $notice_type . '_notice', '1' );
			update_option( 'acswp_plugin_dismiss_' . $notice_type . '_notice', '1' );
			if ( 'donate' === $notice_type ) {
				update_option( 'acswp_plugin_dismissed_time_donate', time() );
			} else {
				update_option( 'acswp_plugin_dismissed_time', time() );
			}
		}
	
		wp_safe_redirect( remove_query_arg( [ 'acswp_notice_action', '_wpnonce' ] ) );
		exit;
	}

	/**
	 * Calculate install time.
	 */
	private function calculate_time() {
		$installed_time = get_option( 'acswp_plugin_installed_time' );
		
		if ( ! $installed_time ) {
            $installed_time = time();
            update_option( 'acswp_plugin_installed_time', $installed_time );
		}
		
        return $installed_time;
	}
}