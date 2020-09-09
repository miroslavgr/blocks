
jQuery( function($) {
	"use strict";

	if(afwssv_phpvars.afwssv_enable_toggle == 'yes') {
		$(".products .variations").wrap("<div class='showvar'></div>");
		if(afwssv_phpvars.afwssv_toggle_text!='') {

			$("<a class='exspands' href='javascript:void(0)'>"+afwssv_phpvars.afwssv_toggle_text+"</a>").insertBefore(".showvar");

		} else {

			$("<a class='exspands' href='javascript:void(0)'>Show/Hide Variations</a>").insertBefore(".showvar");
		}

		$('.exspands').on("click", function () {
			var id = $(this).parent().attr('data-product_id');
			$('.post-' + id + ' .showvar').toggle();

		});
	}


});


