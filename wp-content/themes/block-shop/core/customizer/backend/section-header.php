<?php
/**
 * The Header section options
 *
 * @package blockshop
 */

add_action( 'customize_register', 'blockshop_customizer_header_controls' );
/**
 * Adds controls for header section
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_header_controls( $wp_customize ) {

	// Search.
	$wp_customize->add_setting(
		'header_search',
		array(
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'blockshop_bool_to_string',
			'sanitize_js_callback' => 'blockshop_string_to_bool',
			'default'              => 'yes',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'header_search',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display &quot;Search&quot; Icon', 'block-shop' ),
				'section'  => 'header',
				'priority' => 10,
			)
		)
	);

	// My Account.
	$wp_customize->add_setting(
		'header_user_account',
		array(
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'blockshop_bool_to_string',
			'sanitize_js_callback' => 'blockshop_string_to_bool',
			'default'              => 'yes',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'header_user_account',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display &quot;My Account&quot; Icon', 'block-shop' ),
				'section'  => 'header',
				'priority' => 10,
			)
		)
	);

	// Shopping Cart.
	$wp_customize->add_setting(
		'header_cart',
		array(
			'type'                 => 'theme_mod',
			'capability'           => 'edit_theme_options',
			'sanitize_callback'    => 'blockshop_bool_to_string',
			'sanitize_js_callback' => 'blockshop_string_to_bool',
			'default'              => 'yes',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'header_cart',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display &quot;Cart&quot; Icon', 'block-shop' ),
				'section'  => 'header',
				'priority' => 10,
			)
		)
	);
}
