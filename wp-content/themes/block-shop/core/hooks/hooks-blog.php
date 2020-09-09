<?php
/**
 * This adds custom classes to posts in archive
 *
 * @package blockshop
 */

add_filter( 'post_class', 'blockshop_post_class', 10, 3 );

/**
 * This adds custom classes to posts ina rchive
 *
 * @param  [array]  $classes [The Post classes].
 * @param  [string] $class   [Pots class].
 * @param  [int]    $post_id [ID of the post].
 *
 * @return [array]          [An array of classes].
 */
function blockshop_post_class( $classes, $class, $post_id ) {
	$classes[] = 'post-item';

	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		$classes[] = 'shop-product-item';
	}

	return $classes;
}
