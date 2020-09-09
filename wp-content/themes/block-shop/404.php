<?php
/**
 * The 404 template
 *
 * @package blockshop
 */

get_header(); ?>
	<div class="error-404 not-found">
		<h1><?php esc_html_e( '404 - Not Found', 'block-shop' ); ?></h1>
	</div>
<?php
get_footer();
