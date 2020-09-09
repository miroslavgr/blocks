/**
 * Select2 on order by in product archive
 *
 * @package BlockShop
 */

jQuery(
	function($) {

		"use strict";

		if ( typeof $.fn.select2 === 'function' ) {
			$( '.woocommerce-ordering select.orderby' ).select2(
				{
					minimumResultsForSearch: -1,
					placeholder: blockshop_js_var.select_placeholder,
					allowClear: true
				}
			);
		}
	}
);
