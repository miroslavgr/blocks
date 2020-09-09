/**
 * Posts animation on blog archives
 *
 * @package BlockShop
 */

jQuery(
	function($) {

		"use strict";
		window.blog_animation = function(action, delay) {

			if (typeof action === "undefined" || action === null) {
				action = '';
			}
			if (typeof delay === "undefined" || delay === null) {
				delay = 200;
			}

			$( '.articles-section' ).addClass( 'js_animated' );

			if (action == 'reset') {
				$( '.articles-section.js_animated .article-wrapper' ).removeClass( 'visible animation_ready animated' );
			}

			$( '.articles-section.js_animated .article-wrapper:not(.visible)' ).each(
				function() {
					if ( $( this ).visible( "partial" ) ) {
						$( this ).addClass( 'visible' );
					}
				}
			);

			$( '.articles-section.js_animated .article-wrapper.visible:not(.animation_ready)' ).each(
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

			$( '.articles-section.js_animated .article-wrapper.visible:first' ).prevAll().addClass( 'visible' ).addClass( 'animation_ready' ).addClass( 'animated' );

		}

		$( '.articles-section.js_animated' ).imagesLoaded(
			function() {
				blog_animation();
			}
		);

		$( window ).resize(
			function() {
				gb_throttle( blog_animation(), 300 );
			}
		);

		$( window ).scroll(
			function() {
				gb_throttle( blog_animation(), 300 );
			}
		);

		$( document ).ajaxComplete(
			function() {
				$( '.articles-section.js_animated' ).imagesLoaded(
					function() {
						blog_animation();
					}
				);
			}
		);

	}
);
