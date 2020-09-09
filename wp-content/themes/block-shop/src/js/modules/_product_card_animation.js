/**
 * Product card jQuery animations
 *
 * @package BlockShop
 */

jQuery(
	function($) {

		"use strict";
		window.product_card_animation = function(action, delay) {

			if (typeof action === "undefined" || action === null) {
				action = '';
			}
			if (typeof delay === "undefined" || delay === null) {
				delay = 200;
			}

			$( 'ul.products' ).addClass( 'js_animated' );

			if (action == 'reset') {
				$( 'ul.products.js_animated li.product' ).removeClass( 'visible animation_ready animated' );
			}

			$( 'ul.products.js_animated li.product:not(.visible)' ).each(
				function() {
					if ( $( this ).visible( "partial" ) ) {
						$( this ).addClass( 'visible' );
					}
				}
			);

			$( 'ul.products.js_animated li.product.visible:not(.animation_ready)' ).each(
				function(i) {
					$( this ).addClass( 'animation_ready' );
					$( this ).delay( i * delay ).queue(
						function(next) {
							$( this ).addClass( 'animated' );
							next();
						}
					);
				}
			);

			$( 'ul.products.js_animated li.product.visible:first' ).prevAll().addClass( 'visible' ).addClass( 'animation_ready' ).addClass( 'animated' );

		}

		window.category_card_animation = function(action, delay) {

			if (typeof action === "undefined" || action === null) {
				action = '';
			}
			if (typeof delay === "undefined" || delay === null) {
				delay = 200;
			}

			$( 'section.section-categories' ).addClass( 'js_animated' );

			if (action == 'reset') {
				$( 'section.section-categories.js_animated .category-grid-item' ).removeClass( 'visible animation_ready animated' );
			}

			$( 'section.section-categories.js_animated .category-grid-item:not(.visible)' ).each(
				function() {
					if ( $( this ).visible( "partial" ) ) {
						$( this ).addClass( 'visible' );
					}
				}
			);

			$( 'section.section-categories.js_animated .category-grid-item.visible:not(.animation_ready)' ).each(
				function(i) {
					$( this ).addClass( 'animation_ready' );
					$( this ).delay( i * delay ).queue(
						function(next) {
							$( this ).addClass( 'animated' );
							next();
						}
					);
				}
			);

			$( 'section.section-categories.js_animated .category-grid-item.visible:first' ).prevAll().addClass( 'visible' ).addClass( 'animation_ready' ).addClass( 'animated' );

		}

		$( 'ul.products.js_animated' ).imagesLoaded(
			function() {
				product_card_animation();
			}
		);

		$( 'section.section-categories.js_animated' ).imagesLoaded(
			function() {
				category_card_animation();
			}
		);

		$( window ).resize(
			function() {
				gb_throttle( product_card_animation(), 300 );
				gb_throttle( category_card_animation(), 300 );
			}
		);

		$( window ).scroll(
			function() {
				gb_throttle( product_card_animation(), 300 );
				gb_throttle( category_card_animation(), 300 );
			}
		);

		$( document ).ajaxComplete(
			function() {
				$( 'ul.products.js_animated' ).imagesLoaded(
					function() {
						product_card_animation();
					}
				);
			}
		);

	}
);
