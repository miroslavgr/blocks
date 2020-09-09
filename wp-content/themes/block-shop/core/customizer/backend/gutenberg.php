<?php
/**
 * Enqueue styles and assets to the Gutenberg editor
 *
 * @package blockshop
 */

/**
 * Enqueue stylesheet for block editor
 */
function blockshop_block_assets() {
	if ( is_admin() ) {
		wp_enqueue_style( 'blockshop-gutenberg-blocks', get_template_directory_uri() . '/src/css/editor.css', '', blockshop_version() );

		$google_font_url = BlockShop_Fonts::get_google_font_url();
		if ( $google_font_url ) {
			wp_enqueue_style( 'blockshop-editor-google-font', $google_font_url, false, blockshop_version(), 'all' );
		}
	}
};

add_action( 'enqueue_block_assets', 'blockshop_block_assets' );

/**
 * Outputs blocks styles based on theme options
 *
 * @package blockshop
 */

if ( ! function_exists( 'blockshop_custom_gutenberg_styles' ) ) {
	/**
	 * Apply custom styles to gutenberg editor
	 */
	function blockshop_custom_gutenberg_styles() {

		if ( is_admin() ) {
			$text_color       = BlockShop_Opt::get_option( 'main_font_color' );
			$body_color       = BlockShop_Opt::get_option( 'main_background_color' );
			$body_dark        = BlockShop_Opt::get_option( 'body_dark' );
			$text_dark        = BlockShop_Opt::get_option( 'text_dark' );
			$text_medium      = BlockShop_Opt::get_option( 'text_medium' );
			$text_light       = BlockShop_Opt::get_option( 'text_light' );
			$text_ultra_light = BlockShop_Opt::get_option( 'text_ultra_light' );

			$body_rgb = blockshop_hex2rgb( $body_color );
			$text_rgb = blockshop_hex2rgb( $text_color );

			$styles  = '';
			$styles .= '
			.edit-post-visual-editor.editor-styles-wrapper,
			.edit-post-visual-editor form,
			.edit-post-visual-editor input,
			.edit-post-visual-editor textarea,
			.edit-post-visual-editor button,
			.edit-post-visual-editor select,
			.editor-post-title__block .editor-post-title__input,
			.editor-default-block-appender textarea.editor-default-block-appender__content,
			.wc-block-product-search .wc-block-product-search__field,
			.wp-block-search__button-rich-text
			{
				font-family: ' . BlockShop_Fonts::get_font( BlockShop_Opt::get_option( 'main_font_family' ) ) . ';
			}

			.block-editor .editor-styles-wrapper
			{
				background-color: ' . esc_html( $body_color ) . ';
				font-size: ' . esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				color: ' . esc_html( $text_color ) . ';
			}

			.block-editor .editor-styles-wrapper .block-editor-block-list__block,
			.block-editor .editor-styles-wrapper .block-editor-block-list__block p,
			.block-editor .editor-styles-wrapper .wp-block-preformatted pre,
			.block-editor .editor-styles-wrapper select,
			.block-editor .editor-styles-wrapper .wp-block-pullquote,
			.block-editor .editor-styles-wrapper .editor-default-block-appender textarea.editor-default-block-appender__content {
				line-height: 1.6;
				font-size: ' . esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				color: ' . esc_html( $text_color ) . ';
			}

			.block-editor .editor-styles-wrapper.edit-post-visual-editor h1.block-editor-block-list__block,
		    .block-editor .editor-styles-wrapper.edit-post-visual-editor .block-editor-block-list__block h1
			{
				font-size: ' . 2 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				margin-top: ' . 2.5 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper textarea.editor-post-title__input
			{
				font-size: ' . 2.4 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper.edit-post-visual-editor h2.block-editor-block-list__block,
		    .block-editor .editor-styles-wrapper.edit-post-visual-editor .block-editor-block-list__block h2
			{
				font-size: ' . 1.65 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				margin-top: ' . 2.5 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper.edit-post-visual-editor h3.block-editor-block-list__block,
		    .block-editor .editor-styles-wrapper.edit-post-visual-editor .block-editor-block-list__block h3,
			.block-editor .editor-styles-wrapper .block-editor-block-list__block .wp-block-cover-text
			{
				font-size: ' . 1.375 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				margin-top: ' . 2.5 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper.edit-post-visual-editor h4.block-editor-block-list__block,
		    .block-editor .editor-styles-wrapper.edit-post-visual-editor .block-editor-block-list__block h4
			{
				font-size: ' . 1.15 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				margin-top: ' . 2.5 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper.edit-post-visual-editor h5.block-editor-block-list__block,
		    .block-editor .editor-styles-wrapper.edit-post-visual-editor .block-editor-block-list__block h5
			{
				font-size: ' . 0.95 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				margin-top: ' . 2.5 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper.edit-post-visual-editor h6.block-editor-block-list__block,
		    .block-editor .editor-styles-wrapper.edit-post-visual-editor .block-editor-block-list__block h6
			{
				font-size: ' . 0.8 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
				margin-top: ' . 2.5 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper .wp-block-preformatted pre {
				font-size: ' . 0.9 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper .wp-block-quote p,
			.block-editor .editor-styles-wrapper .wp-block-pullquote blockquote > .editor-rich-text p {
				font-size: ' . 1.15 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper .wp-block-quote.is-style-large p {
				font-size: ' . 1.375 * esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper .wp-block-quote .wp-block-quote__citation,
			.block-editor .editor-styles-wrapper .wp-block-pullquote .wp-block-pullquote__citation,
			.block-editor .editor-styles-wrapper .wc-block-reviews-by-product .wc-block-review-list-item__author {
				font-size: ' . esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper .block-editor-block-list__block a,
			.block-editor .editor-styles-wrapper .block-editor-block-list__block .wc-block-grid__product-add-to-cart button {
				color: ' . esc_html( $text_color ) . ';
				border-bottom: solid 1px ' . esc_html( $text_color ) . ';
			}

			.block-editor .editor-styles-wrapper .wp-block-latest-posts__post-date,
			.block-editor .editor-styles-wrapper .wp-block-quote .wp-block-quote__citation {
				color: ' . esc_html( $text_medium ) . ';
				font-size: ' . esc_html( BlockShop_Opt::get_option( 'font_size' ) ) . 'px;
			}

			.block-editor .editor-styles-wrapper .wc-block-products-grid .wc-product-preview .wc-product-preview__rating.star-rating::before,
			.block-editor .editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-rating .star-rating::before,
			.block-editor .editor-styles-wrapper .editor-inserter-with-shortcuts .components-icon-button,
			.block-editor .editor-styles-wrapper .editor-block-list__empty-block-inserter .components-icon-button,
			.block-editor .editor-styles-wrapper .editor-post-title__block .editor-post-title__input::placeholder,
			.block-editor .editor-styles-wrapper .editor-block-mover__control,
			.block-editor .editor-styles-wrapper .editor-default-block-appender textarea.editor-default-block-appender__content {
				color: ' . esc_html( $text_medium ) . ';
			}

			.editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-price del {
				color: ' . esc_html( $text_dark ) . ';
			}

			.block-editor .editor-styles-wrapper select,
			.block-editor-block-list__block .wc-block-product-search .wc-block-product-search__fields .wc-block-product-search__field,
			.editor-styles-wrapper .wc-block-all-reviews .wc-block-review-order-select .wc-block-order-select__select,
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/price-filter"] .wc-block-price-filter__controls .wc-block-price-filter__amount,
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/active-filters"] ul li.wc-block-active-filters-list-item {
				border-color: ' . esc_html( $text_light ) . ';
			}

			.block-editor .editor-styles-wrapper .wp-block-pullquote,
			.block-editor .editor-styles-wrapper .wp-block-video figcaption,
			.block-editor .editor-styles-wrapper .wp-block-image figcaption,
			.block-editor .editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-price del {
				border-color: ' . esc_html( $text_medium ) . ';
			}

			.block-editor .editor-styles-wrapper .wp-block-quote:not(.is-style-large),
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/active-filters"] .wc-block-active-filters__clear-all,
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/attribute-filter"] ul li input[type=checkbox] {
				border-color: ' . esc_html( $text_color ) . ';
			}

			.block-editor .editor-styles-wrapper .wp-block-button.is-style-squared .wp-block-button__link,
			.block-editor .editor-styles-wrapper .wp-block-button.is-style-default .wp-block-button__link,
			.block-editor .editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-onsale,
			.editor-styles-wrapper .wc-block-grid .wc-block-pagination .wc-block-pagination-page.wc-block-pagination-page--active:before {
				background-color: ' . esc_html( $text_color ) . ';
			}

			.editor-styles-wrapper .wc-block-grid .wc-block-pagination .wc-block-pagination-page:after {
				background-color: ' . esc_html( $text_light ) . ';
			}

			.block-editor .editor-styles-wrapper .wp-block-button.is-style-squared .wp-block-button__link,
			.block-editor .editor-styles-wrapper .wp-block-button.is-style-default .wp-block-button__link,
			.block-editor .editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-onsale {
				color: ' . esc_html( $body_color ) . ';
			}
			.block-editor .editor-styles-wrapper .wc-block-grid .wc-block-grid__products, .wc-block-grid .wc-block-grid__products .wc-block-grid__product-title,
			.block-editor .editor-styles-wrapper .wp-block-button.is-style-outline .wp-block-button__link,
			.block-editor .editor-styles-wrapper textarea.editor-post-title__input,
			.block-editor .editor-styles-wrapper .wc-block-products-grid .wc-product-preview .wp-block-button .wc-product-preview__add-to-cart,
			.block-editor .editor-styles-wrapper .wc-block-products-grid .wc-product-preview .wc-product-preview__rating.star-rating > span::before,
			.block-editor .editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-rating .star-rating > span::before,
			.editor-block-mover__control-drag-handle, .editor-block-mover__control-drag-handle:not(:disabled):not([aria-disabled="true"]):not(.is-default):hover,
			.editor-block-mover__control-drag-handle:not(:disabled):not([aria-disabled="true"]):not(.is-default):active,
			.editor-block-mover__control-drag-handle:not(:disabled):not([aria-disabled="true"]):not(.is-default):focus,
			.editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-add-to-cart .wp-block-button__link,
			.block-editor .editor-styles-wrapper .wc-block-review-list-item__product a,
			.block-editor .editor-styles-wrapper .wc-block-review-list-item__rating__stars span:before,
			.wp-block-search .wp-block-search__input,
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/price-filter"] .wc-block-price-filter__controls .wc-block-price-filter__amount,
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/active-filters"] ul li.wc-block-active-filters-list-item,
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/active-filters"] ul li.wc-block-active-filters-list-item button:before,
			.editor-styles-wrapper .block-editor-block-list__block[data-type="woocommerce/active-filters"] .wc-block-active-filters__clear-all,
			.editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-price .wc-block-grid__product-price__value,
			.editor-styles-wrapper .wc-block-grid .wc-block-pagination .wc-block-pagination-page,
			.editor-styles-wrapper .wc-block-grid .wc-block-grid__products .wc-block-grid__product .wc-block-grid__product-rating__stars>span:before {
				color: ' . esc_html( $text_color ) . ';
			}

			select:not([multiple]) {
				background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D\'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg\'%20viewBox%3D\'0%200%2050%2050\'%3E%3Cpath%20d%3D\'M 24.90625 7.96875 C 24.863281 7.976563 24.820313 7.988281 24.78125 8 C 24.316406 8.105469 23.988281 8.523438 24 9 L 24 38.53125 L 14.8125 29.34375 C 14.625 29.144531 14.367188 29.035156 14.09375 29.03125 C 13.6875 29.035156 13.324219 29.28125 13.171875 29.660156 C 13.023438 30.035156 13.113281 30.46875 13.40625 30.75 L 24.28125 41.65625 L 25 42.34375 L 25.71875 41.65625 L 36.59375 30.75 C 36.890625 30.507813 37.027344 30.121094 36.941406 29.746094 C 36.855469 29.375 36.5625 29.082031 36.191406 28.996094 C 35.816406 28.910156 35.429688 29.046875 35.1875 29.34375 L 26 38.53125 L 26 9 C 26.011719 8.710938 25.894531 8.433594 25.6875 8.238281 C 25.476563 8.039063 25.191406 7.941406 24.90625 7.96875 Z \'%20fill%3D\'%23' . str_replace( '#', '', esc_attr( $text_color ) ) . '\'%2F%3E%3C%2Fsvg%3E") !important;
			}

			.block-editor-block-list__block[data-type="woocommerce/price-filter"] .wc-block-price-filter__range-input-wrapper .wc-block-price-filter__range-input-progress {
				--range-color: ' . esc_attr( $text_color ) . ';
			}

			.block-editor-block-list__block[data-type="woocommerce/price-filter"] .wc-block-price-filter__range-input::-webkit-slider-thumb
			{
				background-color: ' . esc_attr( $body_color ) . ';
				border-color: ' . esc_attr( $text_color ) . ' !important;
			}

			.block-editor-block-list__block[data-type="woocommerce/price-filter"] .wc-block-price-filter__range-input::-moz-range-thumb
			{
				background-color: ' . esc_attr( $body_color ) . ';
				border-color: ' . esc_attr( $text_color ) . ' !important;
			}

			.block-editor-block-list__block[data-type="woocommerce/price-filter"] .wc-block-price-filter__range-input::-ms-thumb
			{
				background-color: ' . esc_attr( $body_color ) . ';
				border-color: ' . esc_attr( $text_color ) . ' !important;
			}';

			wp_add_inline_style( 'blockshop-gutenberg-blocks', $styles );
		}
	}
}
add_action( 'enqueue_block_assets', 'blockshop_custom_gutenberg_styles' );
