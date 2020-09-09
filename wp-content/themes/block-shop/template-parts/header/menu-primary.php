<?php
/**
 * The template for diplaying the primary menu
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<div class="menu">
	<div class="menu-header">
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
		<span class="close-menu">
			<i class="icon-close-20x20"></i>
		</span>
	</div>
	<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-menu',
				'items_wrap'     => '<div class="scroll-container"><ul id="%1$s" class="%2$s">%3$s</ul></div>',
				'after'          => '<span class="plus-minus"></span>',
				'fallback_cb'    => false,
			)
		);
		?>

	<?php
		wp_nav_menu(
			array(
				'theme_location' => 'vertical',
				'container'      => false,
				'menu_class'     => 'mobile-secondary-menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'fallback_cb'    => false,
			)
		);
		?>
</div>
	<div class="mobile-menu-footer">
		<?php if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE && 'yes' === BlockShop_Opt::get_option( 'header_user_account' ) ) : ?>
		<div class="mobile-my-account">
			<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>"><?php esc_html_e( 'My account', 'block-shop' ); ?></a>
		</div>
		<?php endif; ?>
		<div class="mobile-social-icons">
			<?php
				do_action( 'blockshop_social_media' );
			?>
		</div>
	</div>
