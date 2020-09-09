<?php
/**
 * Configuration for customizer
 *
 * @package blockshop
 */

add_action( 'admin_enqueue_scripts', 'blockshop_enqueue_dropdown_control' );
/**
 * Enqueue Go To Page Assets
 *
 * @return void
 */
function blockshop_enqueue_dropdown_control() {
	wp_register_script( 'blockshop-gotopage-scripts', get_template_directory_uri() . '/core/customizer/assets/js/go-to-page.js', array( 'jquery' ), blockshop_version(), false );
	$gotopage_nonce = array( 'gotopage_nonce' => wp_create_nonce( 'gotopage' ) );
	wp_localize_script( 'blockshop-gotopage-scripts', 'gotopage', $gotopage_nonce );
	wp_enqueue_script( 'blockshop-gotopage-scripts' );
}

add_action( 'wp_ajax_get_section_url', 'blockshop_get_section_url' );
/**
 * Retrieve page url for customizer sections preview
 *
 * @return void
 */
function blockshop_get_section_url() {
	check_ajax_referer( 'gotopage', 'security' );
	if ( isset( $_POST['page'] ) ) {
		$pg = sanitize_text_field( wp_unslash( $_POST['page'] ) );
		switch ( $pg ) {
			case 'shop':
				echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) );
				break;
			case 'blog':
				echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) );
				break;
			case 'product':
				$args    = array(
					'orderby' => 'rand',
					'limit'   => 1,
				);
				$product = wc_get_products( $args );
				echo esc_url( get_permalink( $product[0]->get_id() ) );
				break;
			default:
				echo esc_url( get_home_url() );
				break;
		}
	}
	exit();
}

/**
 * Converts bool to string
 *
 * @param bool $bool [the booloean].
 *
 * @return string
 */
function blockshop_bool_to_string( $bool ) {
	$bool = is_bool( $bool ) ? $bool : ( 'yes' === $bool || 1 === $bool || 'true' === $bool || '1' === $bool );

	return true === $bool ? 'yes' : 'no';
}

/**
 * Converts string to bool
 *
 * @param string $string [the string].
 *
 * @return bool
 */
function blockshop_string_to_bool( $string ) {
	return is_bool( $string ) ? $string : ( 'yes' === $string || 1 === $string || 'true' === $string || '1' === $string );
}

/**
 * Sanitizes select controls
 *
 * @param string $input [the input].
 * @param string $setting [the settings].
 *
 * @return string
 */
function blockshop_sanitize_select( $input, $setting ) {
	$input   = sanitize_key( $input );
	$choices = $setting->manager->get_control( $setting->id )->choices;

	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Sanitizes repeater controls
 *
 * @param string $input [the input].
 * @param string $setting [the settings].
 *
 * @return string
 */
function blockshop_sanitize_repeater( $input ) {
	$input_decoded = json_decode($input,true);

	if(!empty($input_decoded)) {
		foreach ($input_decoded as $boxk => $box ){
			foreach ($box as $key => $value){
				$input_decoded[$boxk][$key] = wp_kses_post( force_balance_tags( $value ) );
			}
		}

		return json_encode($input_decoded);
	}

	return $input;
}
