<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

add_action( 'elementor_pro/init', function() {
	// Here its safe to include our action class file
	include_once( dirname(__FILE__).'/includes/class-birdsend-integration-action.php' );

	// Instantiate the action class
	$birdsend_integration_action = new BirdSend_Integration_Action_After_Submit();

	// Register the action with form widget
	\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $birdsend_integration_action->get_name(), $birdsend_integration_action );
});