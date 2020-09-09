/**
 * Various jQuery functions
 *
 * @package BlockShop
 */

( function( $ ) {
	'use strict';

	var blockshop = {
		init: function() {
			this.toggleMenu();
			this.toggleSearch();
			this.toggleCart();
			this.toggleAccount();
			this.toggleFilter();
			this.masonryLayout();
			this.scrollTop();
			this.blogPageNavigationPrevNext();
			this.getLabelValue();
			this.storeNotice();
			this.widgetTitlesHeight();
			this.styleSelects();
		},
		// FIXED MENU FUNCTION.
		toggleMenu: function () {
			// OPEN MOBILE MENU.
			$( '.mobile-menu-toggle' ).on(
				'click',
				function(){
					$( '.menu' ).addClass( 'active' );
					$( 'body' ).addClass( 'overlay' );
					$( '.left-menu-bar' ).addClass( 'active' );
					if ($( '.toggle-effect' ).hasClass( 'icon-menu-20x20' )) {
						$( '.toggle-effect' ).addClass( 'icon-close-20x20' );
						$( '.toggle-effect' ).removeClass( 'icon-menu-20x20' );
					} else {
						$( '.toggle-effect' ).addClass( 'icon-menu-20x20' );
						$( '.toggle-effect' ).removeClass( 'icon-close-20x20' );
					}
				}
			)

			// CLOSE MOBILE MENU.
			$( '.close-menu' ).on(
				'click',
				function(){
					$( '.menu' ).removeClass( 'active' );
					$( 'body' ).removeClass( 'overlay' );
					setTimeout(
						function() {
							$( '.nav .menu-item-has-children > a' ).parent().find( '> .sub-menu:visible' ).hide();
							$( '.nav .menu-item-has-children' ).removeClass( 'active' );
							$( '.left-menu-bar' ).removeClass( 'active' );
						},
						250
					);
				}
			)

			// OPEN DROPDOWN DESKTOP MENU.
			var hoverSubmenu = {
				over: function(){
					if (document.body.clientWidth >= 1200) {
						$( this ).closest( 'li' ).siblings().find( '> .sub-menu:visible' ).fadeOut();
						$( this ).closest( 'li' ).find( '> ul' ).fadeIn();
					}
				},
				out: function(){
					return false;
				}
			};

			$( '.nav .primary-menu > li > a' ).hoverIntent( hoverSubmenu );

			// OPEN DROPDOWN MOBILE MENU.
			$( '.nav .primary-menu > li > a' ).on(
				'click',
				function(){
					if (document.body.clientWidth < 1200) {
						if ( ! $( this ).parent().hasClass( 'active' ) && $( this ).parent().has( 'ul' ).length) {
							$( this ).parent().addClass( 'active' );
							$( this ).parent().siblings().removeClass( 'active' );
							$( this ).closest( 'li' ).siblings().find( '> .sub-menu:visible' ).slideUp();
							$( this ).closest( 'li' ).find( '> .sub-menu' ).slideDown();
							return false;
						}
					}
				}
			);
			$( '.nav .primary-menu > li > .plus-minus' ).on(
				'click',
				function(){
					if (document.body.clientWidth < 1200) {
						if ( ! $( this ).parent().hasClass( 'active' )) {
							$( this ).parent().addClass( 'active' );
							$( this ).parent().siblings().removeClass( 'active' );
							$( this ).closest( 'li' ).siblings().find( '> .sub-menu:visible' ).slideUp();
							$( this ).closest( 'li' ).find( 'ul' ).slideDown();
						} else {
							$( this ).parent().removeClass( 'active' );
							$( this ).parent().siblings().removeClass( 'active' );
							$( this ).closest( 'li' ).find( '> .sub-menu' ).slideUp();
						}
					}
				}
			);
			// TOGGLE DESKTOP MENU.
			$( '.toggle-menu' ).on(
				'click',
				function(){
					if ($( '.toggle-effect' ).hasClass( 'icon-menu-20x20' )) {
						$( '.menu' ).addClass( 'active' );
						$( '.left-menu-bar' ).addClass( 'active' );
						$( 'body' ).addClass( 'overlay' );
						$( '.toggle-effect' ).addClass( 'icon-close-20x20' );
						$( '.toggle-effect' ).removeClass( 'icon-menu-20x20' );
					} else {
						$( '.menu' ).removeClass( 'active' );
						$( 'body' ).removeClass( 'overlay' );
						$( '.toggle-effect' ).addClass( 'icon-menu-20x20' );
						$( '.toggle-effect' ).removeClass( 'icon-close-20x20' );
						$( '.menu' ).removeClass( 'active' );
						setTimeout(
							function() {
								$( '.nav .primary-menu > .menu-item-has-children > a' ).parent().find( '> .sub-menu:visible' ).hide();
								$( '.nav .menu-item-has-children' ).removeClass( 'active' );
								$( '.nav .left-menu-bar' ).removeClass( 'active' );
							},
							250
						);
					}
				}
			);
			// CLOSE MENU ON CLICK OUTSIDE.
			$( document ).on(
				"click",
				function(e){
					if (document.body.clientWidth >= 1200) {
						var target          = $( e.target );
						var targetClasses   = $( '.left-menu-bar, .toggle-effect, .toggle-menu span, .show-search .icon-search-20x20, .show-account .icon-login-20x20, .show-cart .icon-cart-20x20, .getbowtied_product_quickview_button' );
						var targetContainer = $( '.menu, .search-box, .shopping-cart, .account-cont' )
						if ( ! target.is( targetClasses ) && ! target.closest( targetContainer ).length ) {
							$( '.menu' ).removeClass( 'active' );
							$( 'body' ).removeClass( 'overlay' );
							$( '.toggle-effect' ).addClass( 'icon-menu-20x20' );
							$( '.toggle-effect' ).removeClass( 'icon-close-20x20' );
							$( '.search-box' ).removeClass( 'active' );
							$( '.shopping-cart' ).removeClass( 'active' );
							$( '.account-cont' ).removeClass( 'active' );
							$( '.nav .primary-menu > li' ).removeClass( 'active' );
							setTimeout(
								function() {
									$( '.nav .primary-menu > .menu-item-has-children > a' ).parent().find( '> .sub-menu:visible' ).hide();
									$( '.nav .menu-item-has-children' ).removeClass( 'active' );
									$( '.nav .left-menu-bar' ).removeClass( 'active' );
								},
								250
							);
						}
					}
				}
			);
		},
		toggleSearch: function(){
			// TOGGLE MOBILE SEARCH.
			$( '.mobile-search-toggle' ).on(
				'click',
				function(){
					$( '.search-box' ).addClass( 'active' );
					$( 'body' ).addClass( 'overlay' );

				}
			)
			// TOGGLE DESKTOP SEARCH.
			$( '.show-search' ).on(
				'click',
				function(){
					$( '.search-box' ).addClass( 'active' );
					$( 'body' ).addClass( 'overlay' );
				}
			)
			// CLOSE SEARCH.
			$( '.close-search' ).on(
				'click',
				function(){
					$( '.search-box' ).removeClass( 'active' );
					$( 'body' ).removeClass( 'overlay' );
				}
			)
		},
		toggleCart: function(){
			// OPEN CART.
			$( '.show-cart' ).on(
				'click',
				function(){
					$( '.shopping-cart' ).addClass( 'active' );
					$( 'body' ).addClass( 'overlay' );
				}
			)
			// CLOSE CART.
			$( '.close-cart' ).on(
				'click',
				function(){
					$( '.shopping-cart' ).removeClass( 'active' );
					$( 'body' ).removeClass( 'overlay' );
				}
			)
		},
		toggleAccount:function(){
			// OPEN ACCOUNT CANVAS.
			$( '.show-account' ).on(
				'click',
				function(){
					$( '.account-cont' ).addClass( 'active' );
					$( 'body' ).addClass( 'overlay' );
				}
			)
			// CLOSE ACCOUNT CANVAS.
			$( '.close-account' ).on(
				'click',
				function(){
					$( '.account-cont' ).removeClass( 'active' );
					$( 'body' ).removeClass( 'overlay' );
				}
			)
			// TOGGLE FORMS(LOGIN AND REGISTER).
			$( '#customer_login .u-column1' ).addClass( 'active' );

			$( '.toggle-forms .toggle-register' ).on(
				'click',
				function(e){
					e.preventDefault();
					$( '#customer_login .u-column1' ).removeClass( 'active' );
					$( '#customer_login .u-column2' ).addClass( 'active' );
					$( this ).css( 'display','none' );
					$( '.toggle-forms .toggle-login' ).css( 'display','block' );

				}
			)
			$( '.toggle-forms .toggle-login' ).on(
				'click',
				function(e){
					e.preventDefault();
					$( '#customer_login .u-column1' ).addClass( 'active' );
					$( '#customer_login .u-column2' ).removeClass( 'active' );
					$( this ).css( 'display','none' );
					$( '.toggle-forms .toggle-register' ).css( 'display','block' );

				}
			)
		},
		toggleFilter: function(){
			$( '.toggle-filter' ).on(
				'click',
				function(){
					if ($( this ).find( '.icon-filter' ).length == 1) {
						$( '.toggle-filter .icon-filter' ).removeClass( 'icon-filter' ).addClass( 'icon-close-20x20' );
						$( this ).addClass( 'active' );
					} else {
						$( '.toggle-filter .icon-close-20x20' ).removeClass( 'icon-close-20x20' ).addClass( 'icon-filter' );
						$( this ).removeClass( 'active' );
					}
					$( '.expanded-filter' ).slideToggle( 600, "swing" );
					if ($( '.woocommerce-widgets-wrapper' ).hasClass( 'active' )) {
						$( '.woocommerce-widgets-wrapper' ).removeClass( 'active' );
					} else {
						$( '.woocommerce-widgets-wrapper' ).addClass( 'active' );
					}
				}
			);
		},
		masonryLayout: function(){
			$( window ).on(
				'load',
				function(){
					// INIT SHOWCASE FILTER.
					var masonryCont = $( '.articles-masonry-layout .row' ).isotope(
						{
							itemSelector: '.ms-item',
							masonry: {
								percentPosition: true,
							}
						}
					);
				}
			);
		},
		scrollTop: function(){
			// SCROLL PAGE TO TOP.
			$( '.scroll-top' ).on(
				'click',
				function(){
					var $target = $( 'html,body' );
					$target.animate( {scrollTop: 0}, 500 );
					return false;
				}
			)
			// SCROLL TO COMMENTS.
			$( '.comments-count' ).on(
				'click',
				function(){
					var $target = $( 'body,html' );
					$target.animate( {scrollTop: $( '.comments-section' ).offset().top}, 800 );
					return false;
				}
			)

			function show_scroll_to_top() {
				if ( $( document ).scrollTop() > 400 ) {
					if ( ! $( '.right-menu-bar .scroll-top' ).hasClass( 'visible' )) {
						$( '.right-menu-bar .scroll-top' ).addClass( 'visible' )
					}
				} else {
					if ( $( '.right-menu-bar .scroll-top' ).hasClass( 'visible' )) {
						$( '.right-menu-bar .scroll-top' ).removeClass( 'visible' )
					}
				}
			}

			$( function(){
					show_scroll_to_top();
				}
			)

			$( document ).on(
				'scroll',
				function(){
					show_scroll_to_top();
				}
			);
		},
		blogPageNavigationPrevNext: function(){
			if ($( '.nav-links' ).children().length > 1) {
				$( '.blog-pagination-default .posts-navigation .nav-links' ).addClass( 'paginated' )
			}
		},
		getLabelValue: function(){
			$( ".form-row > label" ).each(
				function() {
					var labelValue = $( this ).text();
					var trimStr    = labelValue.trim().replace( /\s+/g, ' ' );
					$( this ).closest( ".form-row" ).find( "input, textarea" ).attr( "placeholder", trimStr );
				}
			);

			$( ".comment-respond p label" ).each(
				function() {
					var labelValue2 = $( this ).text();
					var trimStr2    = labelValue2.trim().replace( /\s+/g, ' ' );
					$( this ).closest( "p" ).find( "input, textarea" ).attr( "placeholder", trimStr2 );
				}
			);
		},
		storeNotice: function() {
			$( 'body' ).on(
				'click',
				'.blockshop-store-notice__dismiss-link',
				function(e){
					e.preventDefault();

					var noticeID   = $( '.woocommerce-store-notice' ).data( 'notice-id' ) || '',
						cookieName = 'store_notice' + noticeID;

					$( '.woocommerce-store-notice' ).addClass( 'hidden' );
					Cookies.set( cookieName, 'hidden', { path: '/' } );
				}
			);
		},
		widgetTitlesHeight: function() {
			if ($( '.woocommerce-widgets-wrapper' ).length || $( '.widgets-wrapper' ).length) {
				$( '.widget-title' ).each(
					function() {
						var cH = $( this ).parents( '.column' ).height();
					}
				)
			}
		},
		styleSelects: function() {
			if ( typeof $.fn.select2 === 'function' ) {
				$( '.widget select' ).select2(
					{
						minimumResultsForSearch: -1,
						allowClear: true,
						containerCssClass: "select2_no_border",
						dropdownCssClass: "select2_no_border",
					}
				);
			}
		}
	};
	$( function(){
				blockshop.init();
				setTimeout(
					function(){
						$( "html" ).addClass( "page-loaded" )
						$( '.shop-header-wrapper' ).addClass( 'visible' )
					},
					50
				);
		}
	);
}( jQuery ) );
