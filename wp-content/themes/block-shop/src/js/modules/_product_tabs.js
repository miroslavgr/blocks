/**
 * Single product tabs styles
 *
 * @package BlockShop
 */

jQuery(
	function($) {
		$( 'body' ).on(
			'click',
			'.wc-tabs li',
			function(){
				var id = $( this ).attr( 'aria-controls' );
				$( 'body' ).find( '.woocommerce-Tabs-panel' ).removeClass( 'active' );
				$( 'body' ).find( '#' + id ).addClass( 'active' );
			}
		);

		$( 'body.single-product' ).find( '.woocommerce-Tabs-panel' ).eq( 0 ).addClass( 'active' );
	}
);
