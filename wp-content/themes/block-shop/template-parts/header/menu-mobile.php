<?php
/**
 * The template for the mobile menu
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<div class="mobile-menu-bar">
	<div class="mobile-menu-toggle">
		<i class="icon-menu-20x20"></i>
		<span><?php esc_html_e( 'Menu', 'block-shop' ); ?></span>
	</div>
	<div class="mobile-logo-wrapper">
		<span class="mobile-logo">
		<?php
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			the_custom_logo();
		} else {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home" class="logo">
					<span>' . esc_html( get_bloginfo( 'name' ) ) . '</span><br/>
					<span class="tagline">' . esc_html( get_bloginfo( 'description' ) ) . '</span>
				</a>';
		}
		?>
		</span>
	</div>
	<div class="mobile-icons-box">
		<?php if ( 'yes' === BlockShop_Opt::get_option( 'header_search' ) ) : ?>
		<span class="mobile-search-toggle">
			<i class="icon-search-20x20"></i>
		</span>
		<?php endif; ?>
		<?php if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE && 'yes' === BlockShop_Opt::get_option( 'header_cart' ) ) : ?>
		<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_cart_page_id' ) ) ); ?>" class="mobile-cart">
			<i class="icon-cart-20x20"></i>
			<span class="items-count shopping_bag_items_number"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
		</a>
		<?php endif; ?>
	</div>
</div>
