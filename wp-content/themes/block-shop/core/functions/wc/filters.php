<?php
/**
 * All WooCommerce Filters Applied
 *
 * @package blockshop
 */

?>
<?php
if ( ! function_exists( 'blockshop_woocommerce_widget_cart_everywhere' ) ) :
	/**
	 * Show the WooCommerce Cart Widget everywhere
	 *
	 * @return [bool]
	 */
	function blockshop_woocommerce_widget_cart_everywhere() {
		return false;
	}
	add_filter( 'woocommerce_widget_cart_is_hidden', 'blockshop_woocommerce_widget_cart_everywhere', 10, 1 );
	endif;

if ( ! function_exists( 'blockshop_output_related_products' ) ) :
	/**
	 * Outputs related products on single product page
	 *
	 * @param  [array] $args [query args for related products]
	 */
	function blockshop_output_related_products( $args ) {
		if ( 0 === BlockShop_Opt::get_option( 'number_related_products' ) ) {
			$args['posts_per_page'] = 0;
			return( $args );
		}
		$args['posts_per_page'] = BlockShop_Opt::get_option( 'number_related_products' );
		$args['columns']        = BlockShop_Opt::get_option( 'number_related_products' );
		$args['orderby']        = 'rand';

		return( $args );
	}
	endif;
	add_filter( 'woocommerce_output_related_products_args', 'blockshop_output_related_products', 20 );

if ( ! function_exists( 'blockshop_wc_categories_postcount_filter' ) ) :
	/**
	 * Replaces the category count wrappings with a span.
	 *
	 * @param  [int] $variable [the count].
	 *
	 * @return [string]           [the html].
	 */
	function blockshop_wc_categories_postcount_filter( $variable ) {
		$variable = str_replace( '<span class="count">(', '<span class="count">', $variable );
		$variable = str_replace( ')</span>', '</span>', $variable );
		return $variable;
	}
	add_filter( 'wp_list_categories', 'blockshop_wc_categories_postcount_filter' );
	endif;

if ( ! function_exists( 'blockshop_layered_nav_filter' ) ) :
	/**
	 * Replace the layered nav count wrappings with a span.
	 *
	 * @param  [int] $variable [the count].
	 *
	 * @return [string]           [thr html].
	 */
	function blockshop_layered_nav_filter( $variable ) {
		$variable = str_replace( '(', '', $variable );
		$variable = str_replace( ')', '', $variable );
		return $variable;
	}
	add_filter( 'woocommerce_layered_nav_count', 'blockshop_layered_nav_filter' );
	endif;


if ( ! function_exists( 'blockshop_rating_filter_count' ) ) :
	/**
	 * Replace the ratings count wrappings with a span.
	 *
	 * @param  [int] $variable [the count].
	 *
	 * @return [string]           [thr html].
	 */
	function blockshop_rating_filter_count( $variable ) {
		$variable = str_replace( '(', '', $variable );
		$variable = str_replace( ')', '', $variable );
		return $variable;
	}
	add_filter( 'woocommerce_rating_filter_count', 'blockshop_rating_filter_count' );
	endif;


	add_filter( 'embed_oembed_html', 'blockshop_wrap_embed_with_div', 99, 4 );

	/**
	 * Wrap the oembeds with a div
	 *
	 * @param  [string] $cache cache.
	 * @param  [string] $url url of embed.
	 * @param  [string] $attr attribute of embed.
	 * @param  [int]    $post_ID the post ID.
	 *
	 * @return [string]          [the html].
	 */
function blockshop_wrap_embed_with_div( $cache, $url, $attr, $post_ID ) {
	$classes = array( 'responsive-embed' );

	// Check for different providers and add appropriate classes.

	if ( false !== strpos( $url, 'vimeo.com' ) ) {
		$classes[] = 'vimeo responsive-embed';
	}

	if ( false !== strpos( $url, 'youtube.com' ) ) {
		$classes[] = 'youtube responsive-embed';
	}

	return '<div class="' . esc_attr( implode( $classes, ' ' ) ) . '">' . $cache . '</div>';
}


if ( ! function_exists( 'blockshop_filter_woocommerce_my_account_my_orders_query' ) ) :
	add_filter( 'woocommerce_my_account_my_orders_query', 'blockshop_filter_woocommerce_my_account_my_orders_query', 10, 1 );

	/**
	 * Changes the number of orders shown perpage.
	 *
	 * @param  [array] $array [the query].
	 *
	 * @return [array]        [the query].
	 */
	function blockshop_filter_woocommerce_my_account_my_orders_query( $array ) {
		$array['numberposts'] = 8;
		return $array;
	};
endif;

	/**
	 * Remove the title from the product tabs
	 */
function blockshop_echo_empty() {
	echo '';
}
	add_filter( 'woocommerce_product_additional_information_heading', 'blockshop_echo_empty' );
	add_filter( 'woocommerce_product_description_heading', 'blockshop_echo_empty' );


	// Thumb size for product gallery.
	add_filter(
		'woocommerce_gallery_thumbnail_size',
		function( $size ) {
			return 'thumbnail';
		}
	);

	add_filter( 'woocommerce_upsell_display_args', 'custom_woocommerce_upsell_display_args' );

	/**
	 * Change number of upsell products.
	 *
	 * @param  [array] $args [the query].
	 *
	 * @return [array]       [the query].
	 */
	function custom_woocommerce_upsell_display_args( $args ) {
		$args['posts_per_page'] = 6; // Change this number.
		$args['columns']        = 6; // This is the number shown per row.
		return $args;
	}

	add_filter( 'woocommerce_demo_store', 'blockshop_notice_filter', 10, 1 );
	/**
	 * Change the store notice classes
	 *
	 * @param  [string] $text [The store notice text].
	 *
	 * @return [string]       [The store notice text].
	 */
	function blockshop_notice_filter( $text ) {
		$text = str_replace( 'class="woocommerce-store-notice__dismiss-link"', 'class="blockshop-store-notice__dismiss-link"', $text );
		return $text;
	}

	if ( ! function_exists( 'blockshop_cross_sells_columns' ) ) :
		/**
		 * The number of cross sell products columns
		 *
		 * @param  [int] $columns [Number of columns].
		 *
		 * @return [int]          [Number of columns].
		 */
		function blockshop_cross_sells_columns( $columns ) {
			return 6;
		}
		add_filter( 'woocommerce_cross_sells_columns', 'blockshop_cross_sells_columns' );
	endif;

	// Wrap the Shop Archive.
	add_action(
		'woocommerce_before_main_content',
		function() {
			echo '<div class="shop-content">';
		}
	);

	// Close Shop Archive wrap.
	add_action(
		'woocommerce_after_main_content',
		function() {
			echo '</div>';
		}
	);

	if ( ! function_exists( 'blockshop_shop_archive_header' ) ) :
		/**
		 * Header area for Shop Archive
		 */
		function blockshop_shop_archive_header() {
			if ( is_shop() || is_product_category() || is_product_tag() ) :
				$categories = get_terms(
					'product_cat',
					array(
						'hide_empty' => 0,
						'parent'     => 0,
					)
				);
				if ( woocommerce_get_loop_display_mode() === 'subcategories' || woocommerce_get_loop_display_mode() === 'both' ) {
					$subcategories = $categories;
				}

				$current_cat = 0;
				if ( is_product_category() ) {
					global $wp_query;
					$current_cat    = $wp_query->get_queried_object();
					$current_cat_id = isset( $current_cat->term_id ) ? $current_cat->term_id : 0;
					if ( 0 !== $current_cat_id ) {
						$subcategories = get_terms(
							'product_cat',
							array(
								'hide_empty' => 0,
								'parent'     => $current_cat_id,
							)
						);
					}
				}
				?>
			<div class="shop-header-wrapper">
				<div class="shop-header-block">
					<div class="filter">
						<?php if ( is_active_sidebar( 'shop-filters-widgets' ) ) : ?>
							<span class="toggle-filter">
								<i class="icon-filter"></i><span><?php esc_html_e( 'Filter', 'block-shop' ); ?></span>
							</span>
						<?php endif; ?>
					</div>
					<?php if ( 'yes' === BlockShop_Opt::get_option( 'category_menu' ) ) : ?>
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
								<?php
								if ( is_shop() ) {
									echo '<h1>';}
								?>
								<a href="<?php echo esc_url( $shop_page_url ); ?>"><?php echo esc_html( get_the_title( wc_get_page_id( 'shop' ) ) ); ?></a>
								<span class="count"><?php echo esc_html( wc_get_loop_prop( 'total' ) ); ?></span>
								<?php
								if ( is_shop() ) {
									echo '</h1>';}
								?>
							</li>
							<?php
						endif;
						if ( ! empty( $categories ) ) :
							foreach ( $categories as $cat ) :
								if ( 0 === $cat->count ) {
									continue;
								}
								?>
								<li class="cat-item
								<?php
								if ( isset( $current_cat_id ) && $current_cat_id === $cat->term_id ) {
									echo 'current-cat';}
								?>
								">
									<?php
									if ( isset( $current_cat_id ) && $current_cat_id === $cat->term_id ) {
										echo '<h1>';}
									?>
									<a href="<?php echo esc_url( get_term_link( $cat->slug, 'product_cat' ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
									<span class="count"><?php echo esc_attr( $cat->count ); ?></span>
									<?php
									if ( isset( $current_cat_id ) && $current_cat_id === $cat->term_id ) {
										echo '</h1>';}
									?>
								</li>
								<?php
							endforeach;
						endif;
						?>
						</ul>
						<?php the_widget( 'WC_Widget_Layered_Nav_Filters' ); ?>
					</div>
					<?php endif; ?>
					<div class="sort-products">
							<?php do_action( 'blockshop_woocommerce_catalog_ordering' ); ?>
					</div>
				</div>
					<?php if ( is_active_sidebar( 'shop-filters-widgets' ) ) : ?>
				<div class="expanded-filter">
					<div class="woocommerce-widgets-wrapper">
						<?php dynamic_sidebar( 'shop-filters-widgets' ); ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
				<?php
			endif;
		}
	endif;
	add_action( 'woocommerce_before_main_content', 'blockshop_shop_archive_header', 10 );

	if ( ! function_exists( 'blockshop_shop_categories' ) ) :
		/**
		 * Custom categories area for Shop Archive
		 */
		function blockshop_shop_categories() {
			$categories = get_terms(
				'product_cat',
				array(
					'hide_empty' => 0,
					'parent'     => 0,
				)
			);
			if ( woocommerce_get_loop_display_mode() === 'subcategories' || woocommerce_get_loop_display_mode() === 'both' ) {
				$subcategories = $categories;
			}

			$current_cat = 0;
			if ( is_product_category() ) {
				global $wp_query;
				$current_cat    = $wp_query->get_queried_object();
				$current_cat_id = isset( $current_cat->term_id ) ? $current_cat->term_id : 0;
				if ( 0 !== $current_cat_id ) {
					$subcategories = get_terms(
						'product_cat',
						array(
							'hide_empty' => 0,
							'parent'     => $current_cat_id,
						)
					);
				}
			}
			if ( ! empty( $subcategories ) && 'products' !== woocommerce_get_loop_display_mode() ) :
				?>
		<section class="section-categories">
			<div class="cont-row">
				<?php
				foreach ( $subcategories as $c ) :
					if ( 0 === $c->count ) {
						continue;
					}
					?>
					<div class="category-grid-item">
						<a class="category-img" href="<?php echo esc_url( get_term_link( $c->slug, 'product_cat' ) ); ?>">
							<?php
								$thumbnail_id = get_term_meta( $c->term_id, 'thumbnail_id', true );
								$image        = wp_get_attachment_image_src( $thumbnail_id, 'large' );
								$image        = isset( $image[0] ) ? $image[0] : wc_placeholder_img_src();
							if ( isset( $image ) ) {
								?>
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $c->name ); ?>" />
								<?php
							}
							?>
						</a>
						<h4 class="category-title">
							<?php echo esc_html( $c->name ); ?><span class="count"><?php echo esc_attr( $c->count ); ?></span>
						</h4>
					</div>
			<?php endforeach; ?>
			</div>
		</section>
				<?php
				remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
				if ( 'subcategories' === woocommerce_get_loop_display_mode() ) {
					wc_set_loop_prop( 'total', 0 );
				}
			endif;
		}
	endif;
	add_action( 'woocommerce_before_shop_loop', 'blockshop_shop_categories', 12 );

	/**
	 * Hide the page title for shop archive
	 *
	 * @return [bool] [false].
	 */
	function blockshop_hide_shop_title() {
		if ( is_shop() || is_product_category() || is_product_tag() ) {
			return false;
		}
	}
	add_filter( 'woocommerce_show_page_title', 'blockshop_hide_shop_title' );

	// woocommerce_before_shop_loop_item.
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	// woocommerce_before_shop_loop_item_title.
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	// woocommerce_after_shop_loop_item_title.
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
	// woocommerce_after_shop_loop_item.
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	// remove thumbnail from product title.
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	// woocommerce_shop_loop_wishlist.
	add_action( 'woocommerce_shop_loop_wishlist', 'add_wishlist_icon_in_product_card', 10 );
	// woocommerce_shop_loop_add_to_cart.
	add_action( 'woocommerce_shop_loop_add_to_cart', 'woocommerce_template_loop_add_to_cart', 10 );
	// Wrap Product Card.
	add_action(
		'woocommerce_before_shop_loop_item',
		function() {
			echo '<div class="shop-product-box">';
		},
		1
	);
	add_action(
		'woocommerce_after_shop_loop_item',
		function() {
			echo '</div>';
		},
		99
	);
	// Wrap Product Info.
	add_action(
		'woocommerce_before_shop_loop_item_title',
		function() {
			echo '<div class="product-info">';
		},
		1
	);
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 98 );
	add_action(
		'woocommerce_after_shop_loop_item_title',
		function() {
			echo '</div>';
		},
		99
	);

	/**
	 * Outputs the product badges in product card
	 */
	function blockshop_loop_badges() {
		global $product;

		if ( $product->is_on_sale() || ! ( $product->is_in_stock() ) ) {
			echo '<div class="product_badges">';
			if ( ! $product->is_in_stock() ) {
				echo '<span class="stock out-of-stock">' . esc_html__( 'Out of stock', 'woocommerce' ) . '</span>';
			}
			if ( $product->is_on_sale() ) {
				echo '<span class="onsale">' . esc_html__( 'Sale', 'woocommerce' ) . '</span>';
			}
			echo '</div>';
		}
	}
	add_action( 'woocommerce_before_shop_loop_item', 'blockshop_loop_badges' );

	/**
	 * Outputs the product image in the product loop
	 */
	function blockshop_loop_image() {
		global $product;

		if ( 'yes' === BlockShop_Opt::get_option( '2nd_image' ) ) {
			$attachment_ids = $product->get_gallery_image_ids();
			if ( $attachment_ids ) {
				$loop = 0;
				foreach ( $attachment_ids as $attachment_id ) {
					$image_link = wp_get_attachment_url( $attachment_id );
					if ( ! $image_link ) {
						continue;
					}
					$loop++;
					$product_thumbnail_second = wp_get_attachment_image_src( $attachment_id, 'shop_catalog' );
					if ( 1 === $loop ) {
						break;
					}
				}
			}

			if ( isset( $product_thumbnail_second[0] ) ) {
				$style = 'background-image:url(' . $product_thumbnail_second[0] . ')';
				$class = 'with_second_image';
			}
		}
		?>
		<div class="ft_image <?php echo isset( $class ) ? esc_attr( $class ) : ''; ?>">
			<a href="<?php echo esc_url( get_the_permalink() ); ?>">
				<?php echo woocommerce_get_product_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php
				if ( isset( $style ) ) {
					?>
					<span class="product_thumbnail_background" style="<?php echo isset( $style ) ? esc_attr( $style ) : ''; ?>"></span> <?php } ?>
			</a>
		</div>
		<?php
	}
	add_action( 'woocommerce_before_shop_loop_item', 'blockshop_loop_image' );
