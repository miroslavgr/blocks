<?php
/**
 * Load required plugins to TGM
 *
 * @package blockshop
 */

/**
 * Registers required plugins to TGM
 */
function blockshop_register_required_plugins() {
	$plugins = array(
		'woocommerce'           => array(
			'name'     => 'WooCommerce',
			'slug'     => 'woocommerce',
			'required' => true,
		),
		'one-click-demo-import' => array(
			'name'     => 'One Click Demo Import',
			'slug'     => 'one-click-demo-import',
			'required' => false,
		),
	);
	$config  = array(
		'id'           => 'block-shop',
		'default_path' => '',
		'parent_slug'  => 'themes.php',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => true,
		'is_automatic' => true,
	);
	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'blockshop_register_required_plugins' );
