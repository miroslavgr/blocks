<?php
/**
 * Currency Converter Main Class
 *
 * @package woocommerce-currency-converter-widget
 */

/**
 * Currency Converter Main Class
 */
class WC_Currency_Converter {

	/**
	 * Base Currency
	 *
	 * @var string
	 */
	public $base;

	/**
	 * Current Currency
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * Rates
	 *
	 * @var array
	 */
	public $rates;

	/**
	 * Plugin Settings
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->settings = array(
			array(
				'name' => __( 'Open Exchange Rate API', 'woocommerce-currency-converter-widget' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'woocommerce-currency-converter-widget',
			),
			array(
				'name' => __( 'App Key', 'woocommerce-currency-converter-widget' ),
				/* translators: %s: Open Exchange signup link */
				'desc' => sprintf( __( '(optional) If you have an <a href="%s">Open Exchange Rate API app ID</a>, enter it here.', 'woocommerce-currency-converter-widget' ), 'https://openexchangerates.org/signup' ),
				'id'   => 'wc_currency_converter_app_id',
				'type' => 'text',
				'std'  => '',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'woocommerce-currency-converter-widget',
			),
		);

		$rates = get_transient( 'woocommerce_currency_converter_rates' );
		if ( false === $rates ) {

			$app_id      = get_option( 'wc_currency_converter_app_id' );
			$app_id      = $app_id ? $app_id : 'e65018798d4a4585a8e2c41359cc7f3c';
			$rates       = wp_remote_retrieve_body( wp_safe_remote_get( 'http://openexchangerates.org/api/latest.json?app_id=' . $app_id ) );
			$check_rates = json_decode( $rates );

			// Check for error.
			if ( is_wp_error( $rates ) || ! empty( $check_rates->error ) || empty( $rates ) ) {

				if ( 401 === $check_rates->status ) {
					add_action( 'admin_notices', array( $this, 'admin_notice_wrong_key' ) );
				}
			} else {
				set_transient( 'woocommerce_currency_converter_rates', $rates, HOUR_IN_SECONDS * 12 );
			}
		}

		$rates = json_decode( $rates );

		if ( $rates && ! empty( $rates->base ) && ! empty( $rates->rates ) ) {
			$this->base  = $rates->base;
			$this->rates = $rates->rates;

			// Actions.
			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );
			add_action( 'widgets_init', array( $this, 'widgets' ) );

			// Shortcode.
			add_shortcode( 'woocommerce_currency_converter', array( $this, 'shortcode' ) );
			add_action( 'woocommerce_currency_converter', array( $this, 'get_converter_form' ), 10, 2 );
		}

		// Settings.
		add_action( 'woocommerce_settings_general_options_after', array( $this, 'admin_settings' ) );
		add_action( 'woocommerce_update_options_general', array( $this, 'save_admin_settings' ) );
		add_action( 'init', array( $this, 'includes' ) );
	}

	/**
	 * Files to be included.
	 */
	public function includes() {
		include_once __DIR__ . '/woocommerce-currency-converter-privacy.php';
	}

	/**
	 * Display admin notices
	 */
	public function admin_notice_wrong_key() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'WooCommerce Currency Converter: Incorrect key entered!', 'woocommerce-currency-converter-widget' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-currency-converter-widget', false, dirname( plugin_basename( __DIR__ ) ) . '/languages/' );
	}

	/**
	 * Show Admin Settings
	 */
	public function admin_settings() {
		woocommerce_admin_fields( $this->settings );
	}

	/**
	 * Save Admin Settings
	 */
	public function save_admin_settings() {
		woocommerce_update_options( $this->settings );
		delete_transient( 'woocommerce_currency_converter_rates' );
	}

	/**
	 * Looks at how a currency should be formatted and returns the currency's correct position for the symbol
	 *
	 * @param string $currency Currency characters.
	 */
	public function get_symbol_position( $currency ) {
		if ( '* ' === substr( $currency, 0, 2 ) ) {
			return 'left_space';
		} elseif ( '*' === substr( $currency, 0, 1 ) ) {
			return 'left';
		} elseif ( ' *' === substr( $currency, -2 ) ) {
			return 'right_space';
		} elseif ( '*' === substr( $currency, -1 ) ) {
			return 'right';
		} else {
			return get_option( 'woocommerce_currency_pos' );
		}
	}

	/**
	 * Display the currency converter form.
	 *
	 * @since  1.4.0
	 *
	 * @param array $instance Arguments.
	 * @param bool  $echo     Whether to display or return the output.
	 *
	 * @return string
	 */
	public function get_converter_form( $instance, $echo = true ) {
		wp_enqueue_script( 'wc_currency_converter' );
		wp_enqueue_script( 'wc_currency_converter_inline' );

		$html  = '<form method="post" id="currency_converter" action="">' . "\n";
		$html .= '<div>' . "\n";

		if ( ! empty( $instance['message'] ) ) {
			$html .= wpautop( wp_kses_post( $instance['message'] ) );
		}

		$currencies = array();
		if ( ! empty( $instance['currency_codes'] ) ) {
			// Split on a comma if there is one. Else, use a new line split.
			if ( stristr( $instance['currency_codes'], ',' ) ) {
				$currencies = array_map( 'trim', array_filter( explode( ',', $instance['currency_codes'] ) ) );
			} else {
				$currencies = array_map( 'trim', array_filter( explode( "\n", $instance['currency_codes'] ) ) );
			}
		}

		// Figure out where the currency symbols should be displayed.
		$symbol_positions = array();
		foreach ( $currencies as $key => $currency ) {
			$display_currency                      = trim( str_replace( '*', '', $currency ) );
			$symbol_positions[ $display_currency ] = $this->get_symbol_position( $currency );
			if ( strpos( $currency, '*' ) !== false ) {
				$currencies[ $key ] = $display_currency;
			}
		}

		// Defaults if unset.
		if ( empty( $currencies ) ) {
			$currencies = array_unique( array( get_woocommerce_currency(), 'USD', 'EUR' ) );
		}

		if ( $currencies ) {
			// Prepend store currency.
			$currencies = array_unique( array_merge( array( get_woocommerce_currency() ), $currencies ) );

			if ( ! empty( $instance['currency_display'] ) && 'select' === $instance['currency_display'] ) {
				$html .= '<label for="currency_switcher" class="currency_switcher_label">Choose a Currency</label>';
				$html .= '<select id="currency_switcher" class="currency_switcher select" data-default="' . get_woocommerce_currency() . '">';

				foreach ( $currencies as $currency ) {
					$label = empty( $instance['show_symbols'] ) ? $currency : get_woocommerce_currency_symbol( $currency );
					$html .= '<option value="' . esc_attr( $currency ) . '">' . esc_html( $label ) . '</option>';
				}

				$html .= '</select>';

				if ( ! empty( $instance['show_reset'] ) ) {
					$html .= ' <a href="#" class="wc-currency-converter-reset reset">' . __( 'Reset', 'woocommerce-currency-converter-widget' ) . '</a>';
				}

			} else {
				$html .= '<ul class="currency_switcher">';

				foreach ( $currencies as $currency ) {
					$class = get_woocommerce_currency() === $currency ? 'default currency-' . $currency : 'currency-' . $currency;
					$label = empty( $instance['show_symbols'] ) ? $currency : get_woocommerce_currency_symbol( $currency );

					$html .= '<li><a href="#" class="' . esc_attr( $class ) . '" data-currencycode="' . esc_attr( $currency ) . '">' . esc_html( $label ) . '</a></li>';
				}

				if ( ! empty( $instance['show_reset'] ) ) {
					$html .= '<li><a href="#" class="wc-currency-converter-reset reset">' . __( 'Reset', 'woocommerce-currency-converter-widget' ) . '</a></li>';
				}

				$html .= '</ul>';
			}
		}

		$html .= '</div>' . "\n";
		$html .= '</form>' . "\n";

		// The current currency to use.
		$current_currency = null;

		// Is location based currency enabled or disabled?
		$disable_location_based_currency = isset( $instance['disable_location'] ) ? $instance['disable_location'] : false;
		$disable_location_based_currency = apply_filters( 'woocommerce_disable_location_based_currency', $disable_location_based_currency );

		// Assume default currency from WooCommerce.
		$current_currency = get_woocommerce_currency();

		if ( ! $disable_location_based_currency ) {
			if ( ! empty( $_COOKIE['woocommerce_current_currency'] ) ) {
				// If a cookie is set then use that
				$current_currency = $_COOKIE['woocommerce_current_currency'];
			} else {
				// Get the users local currency based on their location
				$users_default_currency = WC_Currency_Converter::get_users_default_currency();

				// If its an allowed currency, then use it
				if ( isset( $users_default_currency ) && is_array( $currencies ) && in_array( $users_default_currency, $currencies ) ) {
					$current_currency = $users_default_currency;
				}
			}
		}

		$wc_currency_converter_inline_params = array(
			'current_currency' => esc_js( $current_currency ),
			'symbol_positions' => $symbol_positions,
		);

		wp_localize_script(
			'wc_currency_converter_inline',
			'wc_currency_converter_inline_params',
			apply_filters( 'wc_currency_converter_inline_params', $wc_currency_converter_inline_params )
		);

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Shortcode wrapper.
	 *
	 * @since  1.4.0
	 *
	 * @param array  $atts    Arguments.
	 * @param string $content The contents, if this is a wrapping shortcode.
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = null ) {
		$defaults = array( 'currency_codes' => '', 'message' => '', 'show_symbols' => '0', 'show_reset' => '0', 'currency_display' => '', 'disable_location' => '0' );
		$settings = shortcode_atts( $defaults, $atts );

		return $this->get_converter_form( $settings, false );
	}

	/**
	 * Init Widgets
	 */
	public function widgets() {
		include_once __DIR__ . '/currency-converter-widget.php';
	}

	/**
	 * Enqueue Styles and scripts
	 */
	public function enqueue_assets() {
		if ( is_admin() ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Styles.
		wp_enqueue_style( 'currency_converter_styles', plugins_url( '/assets/css/converter.css', __DIR__ ), array(), WC_CURRENCY_CONVERTER_VERSION );

		// Scripts.
		wp_register_script( 'moneyjs', plugins_url( '/assets/js/money' . $suffix . '.js', __DIR__ ), array( 'jquery' ), '0.1.3', true );
		wp_register_script( 'accountingjs', plugins_url( '/assets/js/accounting' . $suffix . '.js', __DIR__ ), array( 'jquery' ), '0.3.2', true );
		wp_enqueue_script( 'jquery-cookie' );
		wp_register_script( 'wc_currency_converter_inline', plugins_url( '/assets/js/conversion_inline' . $suffix . '.js', __DIR__ ), array( 'jquery' ), WC_CURRENCY_CONVERTER_VERSION, false );

		wp_register_script(
			'wc_currency_converter',
			plugins_url( '/assets/js/conversion' . $suffix . '.js', __DIR__ ),
			array(
				'jquery',
				'moneyjs',
				'accountingjs',
				'jquery-cookie',
				'wc_currency_converter_inline',
			),
			WC_CURRENCY_CONVERTER_VERSION,
			true
		);

		$symbols = array();
		$codes   = get_woocommerce_currencies();

		foreach ( $codes as $code => $name ) {
			$symbols[ $code ] = get_woocommerce_currency_symbol( $code );
		}

		$zero_replace = get_option( 'woocommerce_price_decimal_sep', '.' );
		$decimals     = absint( get_option( 'woocommerce_price_num_decimals' ) );

		for ( $i = 0; $i < $decimals; $i ++ ) {
			$zero_replace .= '0';
		}

		$should_we_trim_zeros = intval( get_option( 'woocommerce_price_num_decimals' ) ) === 0;

		$wc_currency_converter_params = array(
			'current_currency'       => isset( $_COOKIE['woocommerce_current_currency'] ) ? $_COOKIE['woocommerce_current_currency'] : '',
			'currencies'             => wp_json_encode( $symbols ),
			'rates'                  => $this->rates,
			'base'                   => $this->base,
			'currency_format_symbol' => get_woocommerce_currency_symbol(),
			'currency'               => get_woocommerce_currency(),
			'currency_pos'           => get_option( 'woocommerce_currency_pos' ),
			'num_decimals'           => wc_get_price_decimals(),
			'trim_zeros'             => $should_we_trim_zeros,
			'thousand_sep'           => esc_attr( wc_get_price_thousand_separator() ),
			'decimal_sep'            => esc_attr( wc_get_price_decimal_separator() ),
			'i18n_oprice'            => __( 'Original price:', 'woocommerce-currency-converter-widget' ),
			'zero_replace'           => $zero_replace,
			'currency_rate_default'  => apply_filters( 'wc_currency_converter_default_rate', 1 ),
		);

		wp_localize_script( 'wc_currency_converter', 'wc_currency_converter_params', apply_filters( 'wc_currency_converter_params', $wc_currency_converter_params ) );
	}

	/**
	 * Update order meta with currency information
	 *
	 * @param int $order_id Order ID.
	 */
	public function update_order_meta( $order_id ) {
		if ( ! empty( $_COOKIE['woocommerce_current_currency'] ) ) {
			update_post_meta( $order_id, 'Viewed Currency', wc_clean( $_COOKIE['woocommerce_current_currency'] ) );

			$order_total     = number_format( WC()->cart->total, 2, '.', '' );
			$store_currency  = get_woocommerce_currency();
			$target_currency = wc_clean( $_COOKIE['woocommerce_current_currency'] );

			if ( $store_currency && $target_currency && $this->rates->$target_currency && $this->rates->$store_currency ) {
				$new_order_total = ( $order_total / $this->rates->$store_currency ) * $this->rates->$target_currency;
				$new_order_total = round( $new_order_total, 2 ) . ' ' . $target_currency;
				update_post_meta( $order_id, 'Converted Order Total', $new_order_total );
			}
		}
	}

	/**
	 * Function to return a the users default currency code
	 *
	 * @since  1.4.1
	 *
	 * @return string
	 */
	public static function get_users_default_currency() {
		if ( class_exists( 'WC_Geolocation' ) ) {
			$location = WC_Geolocation::geolocate_ip();
			if ( isset( $location['country'] ) ) {
				return self::get_currency_from_country_code( $location['country'] );
			}
		}
		return false;
	}

	/**
	 * Function to return a currency code based on a country code
	 *
	 * @since  1.4.1
	 *
	 * @param string $country_code Country code.
	 *
	 * @return string
	 */
	public static function get_currency_from_country_code( $country_code ) {
		$codes = array( 'NZ' => 'NZD', 'CK' => 'NZD', 'NU' => 'NZD', 'PN' => 'NZD', 'TK' => 'NZD', 'AU' => 'AUD', 'CX' => 'AUD', 'CC' => 'AUD', 'HM' => 'AUD', 'KI' => 'AUD', 'NR' => 'AUD', 'NF' => 'AUD', 'TV' => 'AUD', 'AS' => 'EUR', 'AD' => 'EUR', 'AT' => 'EUR', 'BE' => 'EUR', 'FI' => 'EUR', 'FR' => 'EUR', 'GF' => 'EUR', 'TF' => 'EUR', 'DE' => 'EUR', 'GR' => 'EUR', 'GP' => 'EUR', 'IE' => 'EUR', 'IT' => 'EUR', 'LU' => 'EUR', 'MQ' => 'EUR', 'YT' => 'EUR', 'MC' => 'EUR', 'NL' => 'EUR', 'PT' => 'EUR', 'RE' => 'EUR', 'WS' => 'EUR', 'SM' => 'EUR', 'SI' => 'EUR', 'ES' => 'EUR', 'VA' => 'EUR', 'GS' => 'GBP', 'GB' => 'GBP', 'JE' => 'GBP', 'IO' => 'USD', 'GU' => 'USD', 'MH' => 'USD', 'FM' => 'USD', 'MP' => 'USD', 'PW' => 'USD', 'PR' => 'USD', 'TC' => 'USD', 'US' => 'USD', 'UM' => 'USD', 'VG' => 'USD', 'VI' => 'USD', 'HK' => 'HKD', 'CA' => 'CAD', 'JP' => 'JPY', 'AF' => 'AFN', 'AL' => 'ALL', 'DZ' => 'DZD', 'AI' => 'XCD', 'AG' => 'XCD', 'DM' => 'XCD', 'GD' => 'XCD', 'MS' => 'XCD', 'KN' => 'XCD', 'LC' => 'XCD', 'VC' => 'XCD', 'AR' => 'ARS', 'AM' => 'AMD', 'AW' => 'ANG', 'AN' => 'ANG', 'AZ' => 'AZN', 'BS' => 'BSD', 'BH' => 'BHD', 'BD' => 'BDT', 'BB' => 'BBD', 'BY' => 'BYR', 'BZ' => 'BZD', 'BJ' => 'XOF', 'BF' => 'XOF', 'GW' => 'XOF', 'CI' => 'XOF', 'ML' => 'XOF', 'NE' => 'XOF', 'SN' => 'XOF', 'TG' => 'XOF', 'BM' => 'BMD', 'BT' => 'INR', 'IN' => 'INR', 'BO' => 'BOB', 'BW' => 'BWP', 'BV' => 'NOK', 'NO' => 'NOK', 'SJ' => 'NOK', 'BR' => 'BRL', 'BN' => 'BND', 'BG' => 'BGN', 'BI' => 'BIF', 'KH' => 'KHR', 'CM' => 'XAF', 'CF' => 'XAF', 'TD' => 'XAF', 'CG' => 'XAF', 'GQ' => 'XAF', 'GA' => 'XAF', 'CV' => 'CVE', 'KY' => 'KYD', 'CL' => 'CLP', 'CN' => 'CNY', 'CO' => 'COP', 'KM' => 'KMF', 'CD' => 'CDF', 'CR' => 'CRC', 'HR' => 'HRK', 'CU' => 'CUP', 'CY' => 'CYP', 'CZ' => 'CZK', 'DK' => 'DKK', 'FO' => 'DKK', 'GL' => 'DKK', 'DJ' => 'DJF', 'DO' => 'DOP', 'TP' => 'IDR', 'ID' => 'IDR', 'EC' => 'ECS', 'EG' => 'EGP', 'SV' => 'SVC', 'ER' => 'ETB', 'ET' => 'ETB', 'EE' => 'EEK', 'FK' => 'FKP', 'FJ' => 'FJD', 'PF' => 'XPF', 'NC' => 'XPF', 'WF' => 'XPF', 'GM' => 'GMD', 'GE' => 'GEL', 'GI' => 'GIP', 'GT' => 'GTQ', 'GN' => 'GNF', 'GY' => 'GYD', 'HT' => 'HTG', 'HN' => 'HNL', 'HU' => 'HUF', 'IS' => 'ISK', 'IR' => 'IRR', 'IQ' => 'IQD', 'IL' => 'ILS', 'JM' => 'JMD', 'JO' => 'JOD', 'KZ' => 'KZT', 'KE' => 'KES', 'KP' => 'KPW', 'KR' => 'KRW', 'KW' => 'KWD', 'KG' => 'KGS', 'LA' => 'LAK', 'LV' => 'LVL', 'LB' => 'LBP', 'LS' => 'LSL', 'LR' => 'LRD', 'LY' => 'LYD', 'LI' => 'CHF', 'CH' => 'CHF', 'LT' => 'LTL', 'MO' => 'MOP', 'MK' => 'MKD', 'MG' => 'MGA', 'MW' => 'MWK', 'MY' => 'MYR', 'MV' => 'MVR', 'MT' => 'MTL', 'MR' => 'MRO', 'MU' => 'MUR', 'MX' => 'MXN', 'MD' => 'MDL', 'MN' => 'MNT', 'MA' => 'MAD', 'EH' => 'MAD', 'MZ' => 'MZN', 'MM' => 'MMK', 'NA' => 'NAD', 'NP' => 'NPR', 'NI' => 'NIO', 'NG' => 'NGN', 'OM' => 'OMR', 'PK' => 'PKR', 'PA' => 'PAB', 'PG' => 'PGK', 'PY' => 'PYG', 'PE' => 'PEN', 'PH' => 'PHP', 'PL' => 'PLN', 'QA' => 'QAR', 'RO' => 'RON', 'RU' => 'RUB', 'RW' => 'RWF', 'ST' => 'STD', 'SA' => 'SAR', 'SC' => 'SCR', 'SL' => 'SLL', 'SG' => 'SGD', 'SK' => 'SKK', 'SB' => 'SBD', 'SO' => 'SOS', 'ZA' => 'ZAR', 'LK' => 'LKR', 'SD' => 'SDG', 'SR' => 'SRD', 'SZ' => 'SZL', 'SE' => 'SEK', 'SY' => 'SYP', 'TW' => 'TWD', 'TJ' => 'TJS', 'TZ' => 'TZS', 'TH' => 'THB', 'TO' => 'TOP', 'TT' => 'TTD', 'TN' => 'TND', 'TR' => 'TRY', 'TM' => 'TMT', 'UG' => 'UGX', 'UA' => 'UAH', 'AE' => 'AED', 'UY' => 'UYU', 'UZ' => 'UZS', 'VU' => 'VUV', 'VE' => 'VEF', 'VN' => 'VND', 'YE' => 'YER', 'ZM' => 'ZMK', 'ZW' => 'ZWD', 'AX' => 'EUR', 'AO' => 'AOA', 'AQ' => 'AQD', 'BA' => 'BAM', 'CD' => 'CDF', 'GH' => 'GHS', 'GG' => 'GGP', 'IM' => 'GBP', 'LA' => 'LAK', 'MO' => 'MOP', 'ME' => 'EUR', 'PS' => 'JOD', 'BL' => 'EUR', 'SH' => 'GBP', 'MF' => 'ANG', 'PM' => 'EUR', 'RS' => 'RSD', 'USAF' => 'USD' );

		if ( isset( $codes[ $country_code ] ) ) {
			return $codes[ $country_code ];
		} else {
			return false;
		}
	}

}
