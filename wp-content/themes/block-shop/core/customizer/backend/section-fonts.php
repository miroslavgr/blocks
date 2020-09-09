<?php
/**
 * The Fonts section options
 *
 * @package blockshop
 */

add_action( 'customize_register', 'blockshop_customizer_fonts_controls' );
/**
 * Adds controls for fonts section
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_fonts_controls( $wp_customize ) {

	// Main Font.
	$wp_customize->add_setting(
		'main_font_family',
		array(
			'default' 			=> 'Archivo',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
			'type'				=> 'theme_mod',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'main_font_family',
			array(
				'type'			=> 'text',
				'label' 		=> __( 'Base Font Family', 'block-shop' ),
				'description'	=> BlockShop_Fonts::get_suggested_fonts_list() . __( 'Block Shop supports all fonts on <a href="https://fonts.google.com" target="_blank">Google Fonts</a> and all <a href="https://www.w3schools.com/cssref/css_websafe_fonts.asp" target="_blank">web safe fonts</a>.', 'block-shop' ),
				'section' 		=> 'fonts',
				'input_attrs' 	=> array(
					'placeholder' 		=> __( 'Enter the font name', 'block-shop' ),
					'class'				=> 'blockshop-font-suggestions',
					'list'  			=> 'blockshop-suggested-fonts',
					'autocapitalize'	=> 'off',
					'autocomplete'		=> 'off',
					'autocorrect'		=> 'off',
					'spellcheck'		=> 'false',
				),
			)
		)
	);

	// Base Font Size.
	$wp_customize->add_setting(
		'font_size',
		array(
			'type'       => 'theme_mod',
			'capability' => 'edit_theme_options',
			'default'    => 20,
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'font_size',
			array(
				'type'        => 'number',
				'label'       => __( 'Base Font Size', 'block-shop' ),
				'description' => esc_attr__( 'The Base Font Size refers to the size applied to the paragraph text. All other elements, such as headings, links, buttons, etc will adjusted automatically to keep the hierarchy of font sizes based on this one size. Easy-peasy!', 'block-shop' ),
				'section'     => 'fonts',
				'priority'    => 10,
				'input_attrs' => array(
					'min'  => 12,
					'max'  => 24,
					'step' => 1,
				),
			)
		)
	);

	// Background Color.
	$wp_customize->add_setting(
		'main_background_color',
		array(
			'type'       => 'theme_mod',
			'capability' => 'edit_theme_options',
			'default'    => '#fff',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'main_background_color',
			array(
				'label'    => esc_attr__( 'Background Color', 'block-shop' ),
				'section'  => 'fonts',
				'priority' => 10,
			)
		)
	);

	// Text Color.
	$wp_customize->add_setting(
		'main_font_color',
		array(
			'type'       => 'theme_mod',
			'capability' => 'edit_theme_options',
			'default'    => '#000',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'main_font_color',
			array(
				'label'    => esc_attr__( 'Text Color', 'block-shop' ),
				'section'  => 'fonts',
				'priority' => 10,
			)
		)
	);
}
