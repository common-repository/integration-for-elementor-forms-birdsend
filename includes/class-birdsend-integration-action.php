<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class BirdSend_Integration_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'birdsend integration';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'Birdsend', 'birdsend-elementor-integration' );
	}

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_birdsend-elementor-integration',
			[
				'label' => __( 'Birdsend', 'birdsend-elementor-integration' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'birdsend_api',
			[
				'label' => __( 'Birdsend API key', 'birdsend-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'apikeyherexxxxxxxx',
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter your API key from Birdsend', 'birdsend-elementor-integration' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'birdsend_email_field',
			[
				'label' => __( 'Email Field ID', 'birdsend-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'email',
				'separator' => 'before',
				'description' => __( 'Enter the email field id - you can find this under the email field advanced tab', 'birdsend-elementor-integration' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'birdsend_name_field',
			[
				'label' => __( 'Name Field ID (Optional)', 'birdsend-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'name',
				'description' => __( 'Enter the name field id - you can find this under the name field advanced tab', 'birdsend-elementor-integration' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'birdsend_last_name_field',
			[
				'label' => __( 'Lastname Field ID (Optional)', 'birdsend-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'lastname',
				'description' => __( 'Enter the lastname field id - you can find this under the lastname field advanced tab', 'birdsend-elementor-integration' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$widget->add_control(
			'need_help_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __('Need help? <a href="https://plugins.webtica.be/support/?ref=plugin-widget" target="_blank">Check out our support page.</a>', 'birdsend-elementor-integration'),
			]
		);

		$widget->end_controls_section();

	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */
	public function on_export( $element ) {
		unset(
			$element['birdsend_api'],
			$element['birdsend_email_field'],
			$element['birdsend_name_field'],
			$element['birdsend_last_name_field']
		);

		return $element;
	}

	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );

		// Get submitted Form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		$dataarray = [
			"email" => $fields[$settings['birdsend_email_field']], 
			"fields" => [
				  "first_name" => $fields[$settings['birdsend_name_field']], 
				  "last_name" => $fields[$settings['birdsend_last_name_field']]
			   ] 
		 ]; 
		  
		//Send data to Sendinblue Double optin
		wp_remote_post( 'https://api.birdsend.co/v1/contacts', array(
				'method'      => 'POST',
			    'timeout'     => 45,
			    'httpversion' => '1.0',
			    'blocking'    => false,
			    'headers'     => [
		            'accept' => 'application/json',
			    	'content-Type' => 'application/json',
					'Authorization' => 'Bearer '. $settings['birdsend_api'],
			    ],
			    'body'        => json_encode(["email" => $fields[$settings['birdsend_email_field']], "fields" => [ "first_name" => $fields[$settings['birdsend_name_field']], "last_name" => $fields[$settings['birdsend_last_name_field']]] ])
			)
		);
	}
}