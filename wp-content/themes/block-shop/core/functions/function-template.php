<?php
/**
 * Template functions
 *
 * @package blockshop
 */

if ( ! function_exists( 'blockshop_content_class' ) ) :
	/**
	 * Add content classes
	 */
	function blockshop_content_class() {
		if ( ! BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) {
			echo 'col';
			return;
		}

		if (
			( function_exists( 'is_checkout' ) && ! is_checkout() ) &&
			( function_exists( 'is_cart' ) && ! is_cart() ) &&
			( function_exists( 'is_account_page' ) && ! is_account_page() ) &&
			( function_exists( 'is_woocommerce' ) && ! is_woocommerce() ) ) {
			echo 'col';
		} else {
			echo 'col-woo';
		}
	}
endif;


if ( ! function_exists( 'blockshop_social_media' ) ) :
	/**
	 * Output social media options
	 */
	function blockshop_social_media() {

		if ( class_exists( 'BlockShop_Opt' ) ) {

			$social_media_encoded_list = BlockShop_Opt::get_option( 'social_media_repeater', json_encode( array() ) );
			$social_media_profiles 	   = json_decode( $social_media_encoded_list );

			if ( ! empty( $social_media_profiles ) && ( count($social_media_profiles) > 1 || ( !empty( $social_media_profiles[0]->link ) && !empty($social_media_profiles[0]->icon_slug) ) ) ) {
				foreach ( $social_media_profiles as $social ) {
					if( !empty( $social->link ) && !empty($social->icon_slug) ) {
						if( 'custom' === $social->icon_slug && !empty( $social->image_url ) ) {
							echo '<a target="_blank" href="' . $social->link . '"><img src="'.esc_url( $social->image_url ).'" class="icon-social-' . esc_attr( $social->icon_slug ) . '" width="20" /></a>';
						} else if( 'custom' !== $social->icon_slug ) {
							echo '<a target="_blank" href="' . $social->link . '"><i class="icon-social-' . esc_attr( $social->icon_slug ) . '"></i></a>';
						}
					}
				}
			}
		}
	}

	add_action( 'blockshop_social_media', 'blockshop_social_media' );
endif;
