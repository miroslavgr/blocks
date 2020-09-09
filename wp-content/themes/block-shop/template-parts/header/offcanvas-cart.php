<?php
/**
 * The template for the offcanvas cart
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<?php if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) : ?>
<div class="shopping-cart offcanvas">
	<div class="canvas-wrapper">
		<span class="close-cart">
			<i class="icon-close-20x20"></i>
		</span>
		<span class="vertical-title"><?php esc_html_e( 'Cart', 'woocommerce' ); ?></span>
		<?php
		if ( class_exists( 'WC_Widget_Cart' ) ) {
			the_widget( 'WC_Widget_Cart' ); }
		?>
	</div>
</div>
<?php endif; ?>
