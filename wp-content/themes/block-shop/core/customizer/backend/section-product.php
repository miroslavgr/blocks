<?php
/**
 * The Product section options
 *
 * @package blockshop
 */

add_action( 'customize_register', 'blockshop_customizer_product_controls' );
/**
 * Adds controls for product section
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_product_controls( $wp_customize ) {

	// Categories Navigation.
	$wp_customize->add_setting(
		'category_navigation',
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
			'category_navigation',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display &quot;Category Navigation&quot;', 'block-shop' ),
				'section'  => 'product',
				'priority' => 10,
			)
		)
	);

	// Number of Related Products.
	$wp_customize->add_setting(
		'number_related_products',
		array(
			'type'       => 'theme_mod',
			'capability' => 'edit_theme_options',
			'default'    => 6,
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'number_related_products',
			array(
				'type'        => 'number',
				'label'       => __( 'Number of Related Products', 'block-shop' ),
				'section'     => 'product',
				'priority'    => 10,
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 6,
					'step' => 1,
				),
			)
		)
	);
}
