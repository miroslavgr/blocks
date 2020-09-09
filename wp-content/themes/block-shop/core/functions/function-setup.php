<?php
/**
 * Theme setup
 *
 * @package blockshop
 */

if ( ! isset( $content_width ) ) {
	$content_width = 1920; /* pixels */
}
add_action( 'after_setup_theme', 'blockshop_setup' );
/**
 * Main setup function
 */
function blockshop_setup() {

	/* Load child theme languages */
	load_theme_textdomain( 'block-shop', get_stylesheet_directory() . '/languages' );
	/* load theme languages */
	load_theme_textdomain( 'block-shop', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	/* WooCommerce Support */
	add_theme_support(
		'woocommerce',
		array(
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'max_rows'        => 10,

				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
			'thumbnail_image_width' => 450,
			'single_image_width'    => 600,
		)
	);

	/* Add support for HTML5 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'widgets',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 32,
			'width'       => 266,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	/* Add support for WooCommerce gallery*/
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Gutenberg.
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );

	/*  Registrer menus. */
	register_nav_menus(
		array(
			'vertical' => __( 'Vertical Menu', 'block-shop' ),
			'primary'  => __( 'Primary Menu', 'block-shop' ),
			'footer'   => __( 'Footer Menu', 'block-shop' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'blockshop_enqueue' );
/**
 * Enqueue scripts and styles
 */
function blockshop_enqueue() {

	if ( BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE ) {
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'flexslider' );
		wp_enqueue_script( 'wc-single-product' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}

	$google_font_url = BlockShop_Fonts::get_google_font_url();
	if ( $google_font_url ) {
		wp_enqueue_style( 'blockshop-google-font', $google_font_url, false, blockshop_version(), 'all' );
	}

	wp_register_style( 'blockshop-main', get_template_directory_uri() . '/src/css/screen.css', array(), blockshop_version(), 'all' );

	if( is_rtl() ) {
		wp_deregister_style( 'blockshop-main' );
		wp_register_style( 'blockshop-main', get_template_directory_uri() . '/src/css/rtl.css', array(), blockshop_version(), 'all' );
	}

	wp_enqueue_style( 'blockshop-main' );

	wp_enqueue_script(
		'blockshop-isotope',
		get_template_directory_uri() . '/src/js/_vendor/isotope.pkgd.min.js',
		array( 'jquery' ),
		blockshop_version(),
		true
	);
	wp_enqueue_script(
		'blockshop-hover',
		get_template_directory_uri() . '/src/js/_vendor/jquery.hoverIntent.min.js',
		array( 'jquery' ),
		blockshop_version(),
		true
	);
	wp_enqueue_script(
		'blockshop-js',
		get_template_directory_uri() . '/src/js/options.js',
		array( 'jquery', 'blockshop-isotope', 'blockshop-hover' ),
		blockshop_version(),
		true
	);
	wp_enqueue_script(
		'blockshop-scripts',
		get_template_directory_uri() . '/src/js/scripts-dist.js',
		array( 'jquery', 'blockshop-isotope', 'blockshop-hover' ),
		blockshop_version(),
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	$blockshop_js_vars = array(
		'blog_pagination_type' => BlockShop_Opt::get_option( 'blog_pagination' ),
		'shop_pagination_type' => BlockShop_Opt::get_option( 'shop_pagination' ),
		'load_more_locale'     => __( 'Load More', 'block-shop' ),
		'is_customize_preview' => is_customize_preview(),
		'ajax_url'             => admin_url( 'admin-ajax.php' ),
		'select_placeholder'   => __( 'Choose an Option', 'block-shop' ),
	);

	wp_localize_script( 'blockshop-scripts', 'blockshop_js_var', $blockshop_js_vars );
}

add_action( 'widgets_init', 'blockshop_widgets' );
/**
 * Register Theme Widgets
 */
function blockshop_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Widgets', 'block-shop' ),
			'id'            => 'blog-loop-widgets',
			'before_widget' => '<div class="column"><aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside></div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Shop Filters', 'block-shop' ),
			'id'            => 'shop-filters-widgets',
			'before_widget' => '<div class="column"><aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside></div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
}

if ( ! function_exists( 'blockshop_favicon' ) ) {
	/**
	 * Add default favicon to header
	 */
	function blockshop_favicon() {

		if ( false === has_site_icon() ) {
			echo '<link rel="icon" href="' . esc_url( get_stylesheet_directory_uri() ) . '/favicon.png" />';
		}

	}
	add_action( 'wp_head', 'blockshop_favicon' );
}

add_action( 'admin_notices', 'blockshop_import_demo_notification' );
if ( ! function_exists( 'blockshop_import_demo_notification' ) ) {
	/**
	 * Add a notification about importing the theme's demo content
	 *
	 */
	function blockshop_import_demo_notification() { ?>
		<?php if ( class_exists( 'OCDI_Plugin' ) && BLOCKSHOP_WOOCOMMERCE_IS_ACTIVE && ( 1 !== get_theme_mod( 'imported_demo' ) ) && ( '1' !== get_option( 'dismiss-blockshop-demo-notice' ) ) ) : ?>
		<div class="notice-info notice is-dismissible blockshop_demo_import_notice">
			<p><strong><?php esc_html_e( 'You\'re all set to import Block Shop\'s demo content.', 'block-shop' ); ?></strong></p>
			<p>
				<a class="button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=pt-one-click-demo-import' ) ); ?>">
					<?php esc_html_e( 'Begin Importing', 'block-shop' ); ?>
				</a>
				<?php $dismiss_demo_link = wp_nonce_url( add_query_arg( 'blockshop-hide-notice', 'blockshop-demo' ), 'blockshop_hide_notices_nonce', '_bs_notice_nonce' ); ?>
				<a class="button-secondary skip submit" href="<?php echo esc_url( $dismiss_demo_link ); ?>">
					<?php esc_html_e( 'Skip Demo Content', 'block-shop' ); ?>
				</a>
			</p>
		</div>
			<?php
		endif;
	}
}

add_action( 'wp_loaded', 'blockshop_hide_notices' );
if ( ! function_exists( 'blockshop_hide_notices' ) ) {
	/**
	 * Hide the demo notice if skip button pressed
	 *
	 */
	function blockshop_hide_notices() {
		if ( isset( $_GET['blockshop-hide-notice'] ) && isset( $_GET['_bs_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_bs_notice_nonce'] ) ), 'blockshop_hide_notices_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'block-shop' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['blockshop-hide-notice'] ) );
			update_option( 'dismiss-' . $hide_notice . '-notice', '1' );
		}
	}
}

/**
 * Custom css for admin area
 *
 */
function blockshop_custom_wp_admin_style() {
		wp_register_style( 'blockshop_admin_css', get_template_directory_uri() . '/src/css/admin.css', array(), blockshop_version(), 'all' );
		wp_enqueue_style( 'blockshop_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'blockshop_custom_wp_admin_style' );
