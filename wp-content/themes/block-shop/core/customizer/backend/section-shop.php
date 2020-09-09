<?php
/**
 * The Shop section options
 *
 * @package blockshop
 */

add_action( 'customize_register', 'blockshop_customizer_shop_controls' );

/**
 * Adds controls for shop section
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_shop_controls( $wp_customize ) {

	// Category Menu.
	$wp_customize->add_setting(
		'category_menu',
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
			'category_menu',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display &quot;Category Menu&quot;', 'block-shop' ),
				'section'  => 'shop',
				'priority' => 10,
			)
		)
	);

	// Product Image Border.
	$wp_customize->add_setting(
		'product_image_border',
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
			'product_image_border',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display Border for Product Images', 'block-shop' ),
				'section'  => 'shop',
				'priority' => 10,
			)
		)
	);

	// Image on Hover.
	$wp_customize->add_setting(
		'2nd_image',
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
			'2nd_image',
			array(
				'type'     => 'checkbox',
				'label'    => __( 'Display a 2nd Image on Hover', 'block-shop' ),
				'section'  => 'shop',
				'priority' => 10,
			)
		)
	);

	// Pagination.
	$wp_customize->add_setting(
		'shop_pagination',
		array(
			'type'              => 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'blockshop_sanitize_select',
			'default'           => 'infinite_scroll',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'shop_pagination',
			array(
				'type'     => 'select',
				'label'    => esc_attr__( 'Shop Pagination Style', 'block-shop' ),
				'section'  => 'shop',
				'priority' => 10,
				'choices'  => array(
					'default'          => esc_attr__( 'Page Numbering', 'block-shop' ),
					'load_more_button' => esc_attr__( 'Load More Button', 'block-shop' ),
					'infinite_scroll'  => esc_attr__( 'Infinite Scrolling', 'block-shop' ),
				),
			)
		)
	);
}
