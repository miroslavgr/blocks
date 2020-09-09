<?php
/**
 * The template for the offcanvas account forms
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<?php if ( ! is_user_logged_in() && BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE && ! ( is_account_page() ) ) : ?>
<div class="account-cont offcanvas">
	<div class="canvas-wrapper">
		<span class="close-account">
			<i class="icon-close-20x20"></i>
		</span>
		<div class="account-wrapper">
			<?php wc_get_template( 'myaccount/form-login.php' ); ?>
		</div>
	</div>
</div>
<?php endif; ?>
