<?php
/**
 * Apply some styles to WordPress Customizer
 *
 * @package blockshop
 */

/**
 * Applies styles to WordPress Customzier
 */
function blockshop_customizer_styles() { ?>
		<style>
			#customize-theme-controls #sub-accordion-panel-panel_mega_dropdown {
				display:flex;
				flex-direction: column;
				flex-grow:1;
			}

			#customize-theme-controls #sub-accordion-panel-panel_mega_dropdown > li {
				width: 100%;
			}

			#customize-theme-controls #sub-accordion-panel-panel_mega_dropdown #accordion-panel-panel_mega_dropdown_megamenu {
				order:2;
				border: none;
			}

			#customize-theme-controls #sub-accordion-panel-panel_mega_dropdown #accordion-panel-panel_mega_dropdown_settings {
				border-top: 1px solid #ddd;
			} 

			#customize-controls .description {
				font-size: 12px;
				color: #777;
				font-style: normal;
				margin-bottom: 10px;
			}

			#customize-controls .big-separator {
				background: #555d66;
				display: block;
				font-size: 14px;
				line-height: 44px;
				font-weight: 600;
				margin-bottom: 10px;
				padding-left: 23px;
				color: #fff;
			}

			#customize-controls .big-separator.margin-top {
				margin-top: 40px;
			}
			#customize-controls .accordion-section.open .accordion-section h3.accordion-section-title {
				color: #555d66;
				background-color: #fff;
				border-bottom: 1px solid #ddd;
				border-left: 4px solid #fff;
			}
			#customize-controls .accordion-section.open .accordion-section h3.accordion-section-title:after{
				color: #a0a5aa;
			}

			#customize-theme-controls .customize-pane-child.accordion-section-content {
				display: flex;
				-webkit-flex-direction: column;
					-ms-flex-direction: column;
						flex-direction: column;
				-webkit-flex-grow: 1;
						flex-grow: 1;
			}

			#customize-theme-controls .customize-pane-child.accordion-section-content > li:first-child {
				-webkit-order: 0;
					-ms-order: 0;
						order: 0;
			}
			#customize-theme-controls .customize-pane-child.accordion-section-content > li.customize-control {
				-webkit-order: 1;
					-ms-order: 1;
						order: 1;
			}

			#customize-theme-controls .customize-pane-child.accordion-section-content > li.accordion-section {
				-webkit-order: 2;
					-ms-order: 2;
						order: 2;
			}

			#customize-control-header_template {
				margin-bottom: 30px;
			}

			.wp-customizer .select2-container.select2-container--open,
			.wp-customizer .repeater-row-label,
			.wp-customizer .select2-selection__rendered {
				text-transform: capitalize;
			}

			.customize-control .attachment-media-view .thumbnail-image img{
				max-height: 150px;
			}

		</style>
		<?php

}
	add_action( 'customize_controls_print_styles', 'blockshop_customizer_styles', 999 );
