/**
 * Load posts for infinte / load more pagination
 *
 * @package BlockShop
 */

jQuery(
	function($) {

		"use strict";

		var listing_class     = ".articles-grid-layout";
		var item_class        = ".articles-grid-layout .article-wrapper";
		var pagination_class  = ".posts-navigation";
		var next_page_class   = ".posts-navigation .nav-previous a";
		var ajax_button_class = ".load-more";

		var ajax_load_items = {

			init: function() {

				if (blockshop_js_var.blog_pagination_type == 'load_more_button' || blockshop_js_var.blog_pagination_type == 'infinite_scroll') {

					$( function() {

							if ($( pagination_class ).length) {

								$( pagination_class ).before( '<div class="load-more" data-processing="0"><span class="load-text">' + blockshop_js_var.load_more_locale + '</span><div class="loader"></div></div>' );

							}

						}
					);

					$( 'body:not(.woocommerce)' ).on(
						'click',
						ajax_button_class,
						function() {

							if ($( next_page_class ).length) {

								$( ajax_button_class ).attr( 'data-processing', 1 ).addClass( 'loading' );

								var href = $( next_page_class ).attr( 'href' );

								if ( ! ajax_load_items.msieversion() ) {
									history.pushState( null, null, href );
								}

								ajax_load_items.onstart();

								$.get(
									href,
									function(response) {

										$( pagination_class ).html( $( response ).find( pagination_class ).html() );

										$( response ).find( item_class ).each(
											function() {
												if ( $( '.articles-grid-layout > .row > .article-wrapper:last' ).length ) {
													$( '.articles-grid-layout > .row > .article-wrapper:last' ).after( $( this ) );
												} else {
													$( '.articles-grid-layout > .row' ).html( $( this ) );
												}

											}
										);

										$( ajax_button_class ).attr( 'data-processing', 0 ).removeClass( 'loading' );

										ajax_load_items.onfinish();

										if ($( next_page_class ).length == 0) {
											$( ajax_button_class ).addClass( 'disabled' );
										} else {

										}

									}
								);

							} else {

								$( ajax_button_class ).addClass( 'disabled' );

							}

						}
					);

				}

				if (blockshop_js_var.blog_pagination_type == 'infinite_scroll') {

					var buffer_pixels = Math.abs( 0 );

					$( window ).scroll(
						function() {

							if ($( listing_class ).length) {

								var a = $( listing_class ).offset().top + $( listing_class ).outerHeight();
								var b = a - $( window ).scrollTop();

								if ((b - buffer_pixels) < $( window ).height()) {
									if ($( ajax_button_class ).attr( 'data-processing' ) == 0) {
										$( ajax_button_class ).trigger( 'click' );
									}
								}

							}

						}
					);
				}
			},

			onstart: function() {
			},

			onfinish: function() {
			},

			msieversion: function() {
				var ua   = window.navigator.userAgent;
				var msie = ua.indexOf( "MSIE " );

				if (msie > 0) {
					return parseInt( ua.substring( msie + 5, ua.indexOf( ".", msie ) ) );
				}

				return false;
			},

		};

		ajax_load_items.init();
	}
);
