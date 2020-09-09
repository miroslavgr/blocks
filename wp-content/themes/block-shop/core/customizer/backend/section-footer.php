<?php
/**
 * The Footer section options
 *
 * @package blockshop
 */

add_action( 'customize_register', 'blockshop_customizer_footer_controls' );
/**
 * Adds controls for footer section
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_footer_controls( $wp_customize ) {

	// Bottom Left Text.
	$wp_customize->add_setting(
		'footer_text',
		array(
			'type'       => 'theme_mod',
			'capability' => 'edit_theme_options',
			'default'    => esc_html__( 'Powered by ', 'block-shop' ) . '<a href="https://blockshop.getbowtied.com" title="eCommerce WordPress Theme for WooCommerce">'.esc_html__( 'Block Shop', 'block-shop' ).'</a>.',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'footer_text',
			array(
				'type'     => 'textarea',
				'label'    => __( 'Footer Text Note', 'block-shop' ),
				'section'  => 'footer',
				'priority' => 10,
			)
		)
	);

	// Back To Top.
	$wp_customize->add_setting(
		'footer_back_to_top',
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
			'footer_back_to_top',
			array(
				'type'        => 'checkbox',
				'label'       => esc_attr__( 'Display &quot;Back To Top&quot;', 'block-shop' ),
				'description' => __( 'An arrow icon will be displayed in the bottom right corner of the screen as soon as you start scrolling. Available on screens larger than 1024px', 'block-shop' ),
				'section'     => 'footer',
				'priority'    => 10,
			)
		)
	);
}
