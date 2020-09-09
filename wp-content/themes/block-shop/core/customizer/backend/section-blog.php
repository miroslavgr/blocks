<?php
/**
 * The Blog section options
 *
 * @package blockshop
 */

add_action( 'customize_register', 'blockshop_customizer_blog_controls' );
/**
 * Adds controls for blog section
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_blog_controls( $wp_customize ) {

	// Blog Categories.
	$wp_customize->add_setting(
		'blog_categories',
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
			'blog_categories',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display &quot;Blog Category Navigation&quot;', 'block-shop' ),
				'section'  => 'blog',
				'priority' => 10,
			)
		)
	);

	// Highlight First 5 Posts.
	$wp_customize->add_setting(
		'blog_highlights',
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
			'blog_highlights',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Highlight First 5 Posts', 'block-shop' ),
				'section'  => 'blog',
				'priority' => 10,
			)
		)
	);

	// Horizontal Widget Area.
	$wp_customize->add_setting(
		'blog_widget_area',
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
			'blog_widget_area',
			array(
				'type'     => 'checkbox',
				'label'    => esc_attr__( 'Display &quot;Horizontal Widget Area&quot;', 'block-shop' ),
				'section'  => 'blog',
				'priority' => 10,
			)
		)
	);

	// Pagination.
	$wp_customize->add_setting(
		'blog_pagination',
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
			'blog_pagination',
			array(
				'type'     => 'select',
				'label'    => esc_attr__( 'Pagination Style', 'block-shop' ),
				'section'  => 'blog',
				'priority' => 10,
				'choices'  => array(
					'default'          => esc_attr__( 'Page Numbering', 'block-shop' ),
					'load_more_button' => esc_attr__( 'Load More Button', 'block-shop' ),
					'infinite_scroll'  => esc_attr__( 'Infinite Scrolling', 'block-shop' ),
				),
			)
		)
	);

	// Featured Image Size.
	$wp_customize->add_setting(
		'single_featured_img_size',
		array(
			'type'              => 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'blockshop_sanitize_select',
			'default'           => 'full',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'single_featured_img_size',
			array(
				'type'     => 'select',
				'label'    => esc_attr__( 'Featured Image on Single Posts', 'block-shop' ),
				'section'  => 'blog',
				'priority' => 10,
				'choices'  => array(
					'none'   => esc_attr__( 'None', 'block-shop' ),
					'full'   => esc_attr__( 'Full Size', 'block-shop' ),
					'large'  => esc_attr__( 'Large Size', 'block-shop' ),
					'medium' => esc_attr__( 'Medium Size', 'block-shop' ),
				),
			)
		)
	);
}
