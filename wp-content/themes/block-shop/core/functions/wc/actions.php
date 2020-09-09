<?php
/**
 * All WooCommerce actions modified
 *
 * @package blockshop
 */

if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) {

	if ( ! function_exists( 'blockshop_remove_woocommerce_styles' ) ) :
		/**
		 * Removes default WooCommerce styles
		 */
		function blockshop_remove_woocommerce_styles() {
			add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		}
		add_action( 'after_setup_theme', 'blockshop_remove_woocommerce_styles' );
	endif;

	// Remove breadcrumbs.
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

	// Remove result count and catelog ordering.
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20, 0 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30, 0 );
	add_action( 'blockshop_woocommerce_catalog_ordering', 'woocommerce_catalog_ordering', 30, 0 );


	add_action( 'woocommerce_after_cart_totals', 'blockshop_add_continue_shopping_button_to_cart' );
	if ( ! function_exists( 'blockshop_add_continue_shopping_button_to_cart' ) ) :
		/**
		 * Wrap the continue shopping button on cart page.
		 */
		function blockshop_add_continue_shopping_button_to_cart() {
			$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
			if ( ! empty( $shop_page_url ) ) :
				echo '<div class="continue-shopping">';
				echo ' <a href="' . esc_url( $shop_page_url ) . '" class="button">' . esc_html__( 'Continue shopping', 'woocommerce' ) . '</a>';
				echo '</div>';
		endif;
		}
	endif;

	if ( ! function_exists( 'blockshop_shopping_bag_items_number' ) ) :
		add_filter( 'woocommerce_add_to_cart_fragments', 'blockshop_shopping_bag_items_number' );
		/**
		 * Update the cart count icon on adding to cart
		 *
		 * @param  [array] $fragments [the shopping cart fragments].
		 *
		 * @return [array]            [the shopping cart fragments].
		 */
		function blockshop_shopping_bag_items_number( $fragments ) {
			ob_start();
			?>
			<span class="items-count shopping_bag_items_number"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>

			<?php
			$fragments['.shopping_bag_items_number'] = ob_get_clean();
			return $fragments;
		}
	endif;

	add_action( 'woocommerce_product_badges', 'blockshop_template_loop_stock', 10 );
	/**
	 * Outputs the product badges
	 */
	function blockshop_template_loop_stock() {
		global $product;
		if ( ! $product->is_in_stock() ) {
			echo '<span class="stock out-of-stock">' . esc_html__( 'Out of stock', 'woocommerce' ) . '</span>';
		}
		if ( $product->is_on_sale() ) {
			echo '<span class="onsale">' . esc_html__( 'Sale', 'woocommerce' ) . '</span>';
		}
	}

	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	add_action( 'woocommerce_before_single_product_summary', 'blockshop_single_sale_badge', 10 );
	if ( ! function_exists( 'blockshop_single_sale_badge' ) ) :
		/**
		 * Wrap the single product salebadge
		 */
		function blockshop_single_sale_badge() {
			echo '<div class="sale-badge-box">
  				<div class="rotate-this">';
					do_action( 'woocommerce_product_badges' );
			echo '</div></div>';
		}
	endif;

	add_action( 'woocommerce_after_customer_login_form', 'blockshop_account_toggle_buttons' );
	/**
	 * Add the Login/Register toggle buttons
	 */
	function blockshop_account_toggle_buttons() {
		if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) :
			?>
		<div class="toggle-forms">
			<button class="border-btn toggle-register"><?php esc_html_e( 'Register', 'block-shop' ); ?></button>
			<button class="border-btn toggle-login"><?php esc_html_e( 'Login', 'block-shop' ); ?></button>
		</div>
			<?php
		endif;
	}

	add_action( 'woocommerce_before_single_product', 'blockshop_single_categories' );
	if ( ! function_exists( 'blockshop_single_categories' ) ) :
		/**
		 * Add category navigation to single product page
		 */
		function blockshop_single_categories() {
			if ( 'no' === BlockShop_Opt::get_option( 'category_navigation' ) ) {
				return;
			}

			global $product;
			$categories         = get_terms(
				'product_cat',
				array(
					'hide_empty' => 0,
					'parent'     => 0,
				)
			);
			$product_categories = $product->get_category_ids();
			?>
			<div class="shop-categories">
				<ul class="shop-list">
					<?php
						$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
					if ( ! empty( $shop_page_url ) ) :
						?>
							<li class="cat-item
							<?php
							if ( is_shop() ) {
								echo 'current-cat';}
							?>
							">
								<a href="<?php echo esc_url( $shop_page_url ); ?>"><?php echo esc_html( get_the_title( wc_get_page_id( 'shop' ) ) ); ?></a>
								<span class="count"><?php echo esc_html( wc_get_loop_prop( 'total' ) ); ?></span>
							</li>
						<?php
						endif;
					?>
					<?php
					if ( ! empty( $categories ) ) :
						foreach ( $categories as $c ) :
							if ( 0 === $c->count ) {
								continue;
							}
							?>
								<li class="cat-item
								<?php
								if ( isset( $product_categories ) && in_array( $c->term_id, $product_categories, true ) ) {
									echo 'current-cat';}
								?>
								">
									<a href="<?php echo esc_url( get_term_link( $c->slug, 'product_cat' ) ); ?>"><?php echo esc_html( $c->name ); ?></a>
									<span class="count"><?php echo esc_html( $c->count ); ?></span>
								</li>
							<?php
					endforeach;
						endif;
					?>
				</ul>
			</div>
			<?php
		}
	endif;

	/**
	 * WooCommerce Cart is empty remove notice class
	 */
	remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
	add_action( 'woocommerce_cart_is_empty', 'blockshop_empty_cart_message', 10 );
	function blockshop_empty_cart_message() {
		echo '<p class="cart-empty">' . wp_kses_post( apply_filters( 'wc_empty_cart_message', __( 'Your cart is currently empty.', 'woocommerce' ) ) ) . '</p>';
	}
}
