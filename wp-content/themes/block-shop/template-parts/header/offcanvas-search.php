<?php
/**
 * The template for the offcanvas search
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<div class="search-box offcanvas">
	<span class="close-search">
		<i class="icon-close-20x20"></i>
	</span>
	<div class="canvas-wrapper">
		<div class="search-header">
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
		<?php if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) : ?>
			<div class="search-wrapper">
				<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">							
					<label>
						<input
							name="s"
							id="search"
							class="search-field" 
							type="search" 
							autocomplete="off" 
							value="<?php echo get_search_query(); ?>"
							data-min-chars="3"
							placeholder="<?php esc_attr_e( 'Search', 'block-shop' ); ?>"
							/>
							<input type="hidden" name="post_type" value="product" />
							<button class="submit-form" type="submit"><i class="icon-search-20x20"></i></button>
					</label>
				</form>
				<div class="search-results">
					<div class="search-results-wrapper">

					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="no-woocommerce-search search-wrapper">
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
