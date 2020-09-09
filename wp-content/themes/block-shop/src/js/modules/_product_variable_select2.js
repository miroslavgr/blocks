/**
 * Select2 on variations form
 *
 * @package BlockShop
 */

(function($) {

	"use strict";

	if ( typeof $.fn.select2 === 'function' ) {
		$( '.variations_form select' ).select2(
			{
				minimumResultsForSearch: -1,
				placeholder: blockshop_js_var.select_placeholder,
				allowClear: true
			}
		);
	}

})( jQuery );
