<?php
/**
 * Set the customizer sections
 *
 * @package blockshop
 */

add_action( 'customize_register', 'blockshop_customizer_sections' );

/**
 * Sets the customizer sections
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_sections( $wp_customize ) {

	$wp_customize->add_section(
		'header',
		array(
			'title'    => esc_attr__( 'Header', 'block-shop' ),
			'priority' => 21,
		)
	);

	$wp_customize->add_section(
		'footer',
		array(
			'title'    => esc_attr__( 'Footer', 'block-shop' ),
			'priority' => 22,
		)
	);

	$wp_customize->add_section(
		'fonts',
		array(
			'title'    => esc_attr__( 'Fonts and Colors', 'block-shop' ),
			'priority' => 23,
		)
	);

	$wp_customize->add_section(
		'shop',
		array(
			'title'    => esc_attr__( 'Shop', 'block-shop' ),
			'priority' => 24,
		)
	);

	$wp_customize->add_section(
		'product',
		array(
			'title'    => esc_attr__( 'Product Page', 'block-shop' ),
			'priority' => 25,
		)
	);

	$wp_customize->add_section(
		'blog',
		array(
			'title'    => esc_attr__( 'Blog', 'block-shop' ),
			'priority' => 26,
		)
	);

	$wp_customize->add_section(
		'social_media',
		array(
			'title'       => esc_attr__( 'Social Media', 'block-shop' ),
			'priority'    => 27,
		)
	);
}

require_once 'section-header.php';
require_once 'section-footer.php';
require_once 'section-fonts.php';
require_once 'section-shop.php';
require_once 'section-product.php';
require_once 'section-blog.php';
require_once 'section-social-media.php';
