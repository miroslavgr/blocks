<?php
/**
 * The template for the vertical menu
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<div class="vertical-menu">
	<div class="left-menu-bar">
		<?php
			wp_nav_menu(
				array(
					'theme_location' => 'vertical',
					'container'      => false,
					'menu_class'     => 'secondary-menu',
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'link_before'    => '<span>',
					'link_after'     => '</span>',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
		<div class="toggle-menu">
			<span><?php esc_html_e( 'Menu', 'block-shop' ); ?></span>
			<i class="toggle-effect icon-menu-20x20"></i>
		</div>
		<?php endif; ?>
	</div>
	<div class="top-bar">
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
	</div>
	<div class="right-menu-bar">
		<div class="menu-icons">
			<?php if ( 'yes' === BlockShop_Opt::get_option( 'header_search' ) ) : ?>
			<span class="show-search">
				<i class="icon-search-20x20"></i>
			</span>
			<?php endif; ?>
			<?php if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) : ?>
				<?php if ( 'yes' === BlockShop_Opt::get_option( 'header_user_account' ) ) : ?>
					<?php if ( ! is_user_logged_in() && ! is_account_page() ) : ?>
					<span class="show-account">
						<i class="icon-login-20x20"></i>
					</span>
					<?php else : ?>
						<a class="account-link" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>"><i class="icon-login-20x20"></i></a>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( 'yes' === BlockShop_Opt::get_option( 'header_cart' ) ) : ?>
				<span class="show-cart">
					<i class="icon-cart-20x20"></i>
					<span class="items-count shopping_bag_items_number"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
				</span>
				<?php endif; ?>
			<?php endif; ?>
			<?php
				if( 'top' === BlockShop_Opt::get_option( 'social_media_position' ) ) {
					do_action( 'blockshop_social_media' );
				}
			?>
		</div>
		<div class="menu-icons-bottom">
			<?php if ( 'yes' === BlockShop_Opt::get_option( 'footer_back_to_top' ) ) : ?>
			<a class="scroll-top">
				<i class="icon-scroll-to-top"></i>
			</a>
			<?php endif; ?>
			<?php
				if( 'bottom' === BlockShop_Opt::get_option( 'social_media_position' ) ) {
					do_action( 'blockshop_social_media' );
				}
			?>
		</div>
	</div>
</div>
