/**
 * Animate offcanvas after ajax add to cart
 *
 * @package BlockShop
 */

jQuery(function($){
	"use strict";

	$( 'body' ).on(
		'added_to_cart',
		function(){
			$( '.shopping-cart.offcanvas' ).addClass( 'active' );
			$( 'body' ).addClass( 'overlay' );
		}
	);
});
