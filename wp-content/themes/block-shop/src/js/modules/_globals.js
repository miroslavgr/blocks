/**
 * Global scroll functions
 *
 * @package BlockShop
 */

(function($){

	"use strict";

	// Global Debounce.
	window.gb_debounce = function(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later   = function() {
				timeout = null;
				if ( ! immediate) {
					func.apply( context, args );
				}
			};
			var callNow = immediate && ! timeout;
			clearTimeout( timeout );
			timeout = setTimeout( later, wait );
			if (callNow) {
				func.apply( context, args );
			}
		};
	};

	// Global throttle.
	window.gb_throttle = function(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later   = function() {
				timeout = null;
				if ( ! immediate) {
					func.apply( context, args );
				}
			};
			var callNow = immediate && ! timeout;
			if ( ! timeout ) {
				timeout = setTimeout( later, wait );
			}
			if (callNow) {
				func.apply( context, args );
			}
		};
	};

	// Scroll detection.
	window.scroll_position  = $( window ).scrollTop();
	window.scroll_direction = 'fixed';

	function scroll_detection() {
		var scroll = $( window ).scrollTop();
		if (scroll > window.scroll_position) {
			window.scroll_direction = 'down';
		} else {
			window.scroll_direction = 'up';
		}
		window.scroll_position = scroll;
	}

	$( window ).scroll(
		function() {
			scroll_detection();
		}
	);
})( jQuery );
