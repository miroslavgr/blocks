<?php
/**
 * Globals and helper functions
 *
 * @package blockshop
 */

/**
 * Returns the theme version
 *
 * @return [string] [theme version].
 */
function blockshop_version() {
	global $theme;
	$theme = wp_get_theme( get_template() );
	return $theme->get( 'Version' );
}

// Define Constants.
define( 'BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE', class_exists( 'WooCommerce' ) );


/**
 * Converts hex color to rgb
 *
 * @param  [string] $hex [the hex code].
 *
 * @return [string]      [the rgb output].
 */
function blockshop_hex2rgb( $hex ) {
	$hex = str_replace( '#', '', $hex );

	if ( 3 === strlen( $hex ) ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}
	$rgb = array( $r, $g, $b );
	return implode( ',', $rgb );
}


/**
 * Returns page ID or posts page
 *
 * @return [int] [ID of the page].
 */
function blockshop_page_id() {
	$page_id = '';
	if ( is_single() || is_page() ) {
		$page_id = get_the_ID();
	} else {
		$page_id = get_option( 'page_for_posts' );
	}
	return $page_id;
}

/**
 * Returns an array of all WooCommerge page ID's
 *
 * @return [array] [the ID's].
 */
function blockshop_woo_page_ids() {
	if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE) {
		$woo_page_ids = array(
			wc_get_page_id( 'shop' ),
			wc_get_page_id( 'cart' ),
			wc_get_page_id( 'checkout' ),
			wc_get_page_id( 'myaccount' ),

		);
	} else {
		$woo_page_ids = array();
	}

	return $woo_page_ids;
}
