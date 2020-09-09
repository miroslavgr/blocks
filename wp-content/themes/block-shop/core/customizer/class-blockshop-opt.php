<?php
/**
 * This class reads and returns theme mods with a set of defaults
 *
 * @package blockshop
 */

?>
<?php
/**
 * Reads theme options and returns the option or a default
 */
class BlockShop_Opt {

	/**
	 * Cache each request to prevent duplicate queries
	 *
	 * @var array
	 */
	protected static $cached = [];

	/**
	 *  We don't need a constructor
	 */
	private function __construct() {}

	/**
	 * Default values for theme options
	 *
	 * @return array
	 */
	private static function theme_defaults() {
		return [

			// Header.
			'header_search'                   => 'yes',
			'header_user_account'             => 'yes',
			'header_cart'                     => 'yes',
			'header_background_color'         => '#fff',
			'header_text_color'               => '#000',

			// Footer.
			'footer_text'                     => esc_html__( 'Powered by ', 'block-shop' ) . '<a href="https://blockshop.getbowtied.com" title="eCommerce WordPress Theme for WooCommerce">'.esc_html__( 'Block Shop', 'block-shop' ).'</a>.',
			'footer_back_to_top'              => 'yes',

			// Fonts.
			'font_size'                       => '20',
			'main_background_color'           => '#fff',
			'main_font_color'                 => '#000',
			'main_font_family'                => 'Archivo',

			// Shop.
			'category_menu'                   => 'yes',
			'shop_pagination'                 => 'infinite_scroll',
			'product_image_border'            => 'yes',
			'2nd_image'                       => 'yes',

			// Product Page.
			'number_related_products'         => 6,
			'category_navigation'             => 'yes',

			// Blog.
			'blog_categories'                 => 'yes',
			'blog_highlights'                 => 'yes',
			'blog_widget_area'                => 'yes',
			'blog_pagination'                 => 'infinite_scroll',
			'single_featured_img_size'        => 'full',

			// Social Media.
			'social_media_repeater'           => json_encode( array() ),
			'social_media_position'			  => 'bottom',
		];
	}

	/**
	 * Switch case for options that need post processing
	 *
	 * @param  [string] $key   [name of option].
	 * @param  [string] $value [value].
	 *
	 * @return [string]        [processed value]
	 */
	private static function process_option( $key, $value ) {
			$opacity_dark        = .75;
			$opacity_medium      = .5;
			$opacity_light       = .3;
			$opacity_ultra_light = .07;
		switch ( $key ) {
			case 'font_size':
				if ( self::get_option( 'font_size' ) > 24 ) {
					return 24;
				}
				if ( self::get_option( 'font_size' ) < 12 ) {
					return 12;
				}
				return self::get_option( 'font_size' );
			case 'text_dark':
				return 'rgba(' . blockshop_hex2rgb( self::get_option( 'main_font_color' ) ) . ',' . $opacity_dark . ')';
			case 'text_medium':
				return 'rgba(' . blockshop_hex2rgb( self::get_option( 'main_font_color' ) ) . ',' . $opacity_medium . ')';
			case 'text_light':
				return 'rgba(' . blockshop_hex2rgb( self::get_option( 'main_font_color' ) ) . ',' . $opacity_light . ')';
			case 'text_ultra_light':
				return 'rgba(' . blockshop_hex2rgb( self::get_option( 'main_font_color' ) ) . ',' . $opacity_ultra_light . ')';
			case 'body_dark':
				return 'rgba(' . blockshop_hex2rgb( self::get_option( 'main_background_color' ) ) . ',' . $opacity_dark . ')';
			default:
				return $value;
		}

			return $value;
	}

	/**
	 * Return the theme option from cache; if it isn't cached fetch it and cache it
	 *
	 * @param  string $option_name [name of the option].
	 * @param  string $default     [default value of option].
	 *
	 * @return string
	 */
	public static function get_option( $option_name, $default = '' ) {
		/* Return cached if possible */
		if ( array_key_exists( $option_name, self::$cached ) && empty( $default ) ) {
			return self::$cached[ $option_name ];
		}
		/* If no default is given, fetch from theme defaults variable */
		if ( empty( $default ) ) {
			$default = array_key_exists( $option_name, self::theme_defaults() ) ? self::theme_defaults()[ $option_name ] : '';
		}

		$opt = get_theme_mod( $option_name, $default );

		/* Cache the result */
		self::$cached[ $option_name ] = $opt;

		/* Process the variable */
		if ( self::process_option( $option_name, $opt ) !== $opt ) {
			self::$cached[ $option_name ] = self::process_option( $option_name, $opt );
		}

		return self::$cached[ $option_name ];
	}
}
