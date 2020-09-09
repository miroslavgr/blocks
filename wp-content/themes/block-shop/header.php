<?php
/**
 * The blog header
 *
 * @package blockshop
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<header id="masthead" class="header">
		<nav class="nav">
			<?php get_template_part( 'template-parts/header/menu-mobile' ); ?>
			<?php get_template_part( 'template-parts/header/menu-primary' ); ?>
			<?php get_template_part( 'template-parts/header/menu-vertical' ); ?>
		</nav>

		<?php
		if ( function_exists( 'is_account_page' ) && ! is_account_page() ) :
				wc_print_notices();
			endif;
		?>

		<?php
		if ( 'yes' === BlockShop_Opt::get_option( 'header_cart' ) ) {
			get_template_part( 'template-parts/header/offcanvas-cart' );}
		?>
		<?php
		if ( 'yes' === BlockShop_Opt::get_option( 'header_user_account' ) ) {
			get_template_part( 'template-parts/header/offcanvas-account' );}
		?>
		<?php
		if ( 'yes' === BlockShop_Opt::get_option( 'header_search' ) ) {
			get_template_part( 'template-parts/header/offcanvas-search' );}
		?>
	</header><!-- #masthead -->

	<div class="container-fluid">
