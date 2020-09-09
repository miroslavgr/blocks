<?php
/**
 * Classes to be added to the body of the template
 *
 * @package blockshop
 */

/**
 * Adds transparency classes
 *
 * @param  [array] $classes [array of classes].
 *
 * @return [array] [array of classes].
 */
function blockshop_transparency( $classes ) {

	if ( ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE && is_woocommerce() ) || ( ! is_page() ) ) {
		return $classes;
	}

	$transparent_header = get_post_meta( get_the_ID(), 'transparent_header_meta_box_check', true );
	$transparent_footer = get_post_meta( get_the_ID(), 'transparent_footer_meta_box_check', true );
	if ( isset( $transparent_header ) && 'on' === $transparent_header ) {
		$classes[] = 'transparent_header';
	}
	if ( isset( $transparent_footer ) && 'on' === $transparent_footer ) {
		$classes[] = 'transparent_footer';
	}

	return $classes;
}

/**
 * Adds shop classes
 *
 * @param  [array] $classes [array of classes].
 *
 * @return [array] [array of classes].
 */
function blockshop_shop( $classes ) {
	if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) {
		if ( is_shop() || is_product_category() || is_product_tag() || ( is_tax() && is_woocommerce() ) ) {
			$classes[] = 'woocommerce-shop';
		}
	}
	return $classes;
}

/**
 * Adds pagination classes
 *
 * @param  [array] $classes [array of classes].
 *
 * @return [array] [array of classes].
 */
function blockshop_shop_pagination( $classes ) {

	if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) {
		if ( is_shop() || is_product_category() || is_product_tag() || ( is_tax() && is_woocommerce() ) ) {
			$classes[] = 'shop-pagination-' . BlockShop_Opt::get_option( 'shop_pagination' );
		}
	}
	return $classes;
}

/**
 * Adds blog pagination classes
 *
 * @param  [array] $classes [array of classes].
 *
 * @return [array] [array of classes].
 */
function blockshop_blog_pagination( $classes ) {

	if ( is_home() || is_archive() || is_search() ) {
		$classes[] = 'blog-pagination-' . BlockShop_Opt::get_option( 'blog_pagination' );
	}

	return $classes;
}

/**
 * Adds body classes filters
 */
function blockshop_customiser_body_classes() {
	add_filter( 'body_class', 'blockshop_transparency' );
	add_filter( 'body_class', 'blockshop_shop' );
	add_filter( 'body_class', 'blockshop_shop_pagination' );
	add_filter( 'body_class', 'blockshop_blog_pagination' );
}

add_action( 'wp_head', 'blockshop_customiser_body_classes', 100 );

