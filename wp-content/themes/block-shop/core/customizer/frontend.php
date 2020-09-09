<?php
/**
 * Applies styles to theme based on Customizer options
 *
 * @package blockshop
 */

/**
 * Applies styles
 */
function blockshop_theme_customiser_styles() {
	$styles = '
	html, body {
		font-size: ' . esc_attr( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
		color: ' . esc_attr( BlockShop_Opt::get_option( 'main_font_color' ) ) . ';
		font-family: ' . BlockShop_Fonts::get_font( BlockShop_Opt::get_option( 'main_font_family' ) ) . ';
		background-color: ' . esc_attr( BlockShop_Opt::get_option( 'main_background_color' ) ) . ';
	}
	.wp-block-quote cite,
	.wp-block-pullquote cite {
		font-size: ' . esc_attr( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
	}

	@media screen and (max-width: 575px ) {
		body.woocommerce-cart .entry-content .woocommerce .cart-empty {
			font-size: ' . esc_attr( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
		}
	}
	';

	if ( 'no' === BlockShop_Opt::get_option( 'product_image_border' ) ) :
		$styles .= 'ul.products li .shop-product-box .ft_image {
			border: none !important;
			background: transparent !important;
		}';
	endif;

	wp_add_inline_style( 'blockshop-main', $styles );
}
add_action( 'wp_enqueue_scripts', 'blockshop_theme_customiser_styles' );

require_once 'frontend/offcanvas-colors.php';
require_once 'frontend/colors.php';
