<?php

if ( ! function_exists( 'action_scheduler_register_3_dot_1_dot_6' ) ) {

	if ( ! class_exists( 'ActionScheduler_Versions' ) ) {
		require_once( 'classes/ActionScheduler_Versions.php' );
		add_action( 'plugins_loaded', array( 'ActionScheduler_Versions', 'initialize_latest_version' ), 1, 0 );
	}

	add_action( 'plugins_loaded', 'action_scheduler_register_3_dot_1_dot_6', 0, 0 );

	function action_scheduler_register_3_dot_1_dot_6() {
		$versions = ActionScheduler_Versions::instance();
		$versions->register( '3.1.6', 'action_scheduler_initialize_3_dot_1_dot_6' );
	}

	function action_scheduler_initialize_3_dot_1_dot_6() {
		require_once( 'classes/abstracts/ActionScheduler.php' );
		ActionScheduler::init( __FILE__ );
	}

	// Support usage in themes - load this version if no plugin has loaded a version yet.
	if ( did_action( 'plugins_loaded' ) && ! class_exists( 'ActionScheduler' ) ) {
		action_scheduler_initialize_3_dot_1_dot_6();
		do_action( 'action_scheduler_pre_theme_init' );
		ActionScheduler_Versions::initialize_latest_version();
	}
}
