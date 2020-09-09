/**
 * Load shop/blog/product pages when viewing sections in customizer
 *
 * @package BlockShop
 */

jQuery(
	function($) {

		"use strict";

		var in_customizer = false;

		if ( typeof wp !== 'undefined' ) {
			if ( typeof wp.customize !== 'undefined' ) {
				in_customizer = typeof wp.customize.section !== 'undefined' ? true : false;
			}
		}

		if ( in_customizer ) {

			wp.customize.section(
				'shop',
				function( section ) {
					go_to_page( section, 'shop' );
				}
			);

			wp.customize.section(
				'blog',
				function( section ) {
					go_to_page( section, 'blog' );
				}
			);

			wp.customize.section(
				'product',
				function( section ) {
					go_to_page( section, 'product' );
				}
			);

			wp.customize.section(
				'fonts',
				function( section ) {
					fonts_min_max( section );
				}
			);

		}

		function go_to_page( section, page ) {
			section.expanded.bind(
				function( isExpanded ) {
					if ( isExpanded ) {
						var data = {
							'action' : 'get_section_url',
							'page'	 : page,
							'security': gotopage.gotopage_nonce
						};

						jQuery.post(
							'admin-ajax.php',
							data,
							function(response) {
								wp.customize.previewer.previewUrl.set( response );
							}
						);
					}
				}
			);
		}

		function fonts_min_max(section) {
			section.expanded.bind(
				function( isExpanded ) {
					if ( isExpanded ) {
						$( 'input[type="number"]' ).keyup(
							function(e) {
								var iVal = $( this );
								if ($( this ).val() > 24) {
									$( this ).val( 24 );
								}
								if ($( this ).val() < 12) {
									setTimeout(
										function() {
											if ( $( 'input#_customize-input-font_size' ).val() < 12 ) {
												$( 'input#_customize-input-font_size' ).val( 12 );
											}
										},
										1000
									);

								}
							}
						);
					}
				}
			);
		}

	}
);
