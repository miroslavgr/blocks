<?php
/*
 * Plugin Name:       Products By Attributes & Variations for WooCommerce
 * Plugin URI:        https://woocommerce.com/products/show-products-by…butes-variations/
 * Description:       Show variations as product on shop page. (PLEASE TAKE BACKUP BEFORE UPDATING THE PLUGIN).
 * Version:           1.2.5
 * Author:            Addify
 * Developed By:      Addify
 * Author URI:        http://www.addifypro.com
 * Support:           http://www.addifypro.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       addify_wssvp
 *
 * Woo: 4923876:8af6db7286d5929de36a62745633365d
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {

	function addify_wssvp_admin_notice() {

		$addify_wssvp_allowed_tags = array(
			'a' => array(
				'class' => array(),
				'href' => array(),
				'rel' => array(),
				'title' => array(),
			),
			'b' => array(),

			'div' => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'p' => array(
				'class' => array(),
			),
			'strong' => array(),

		);

		// Deactivate the plugin
		deactivate_plugins(__FILE__);

		$addify_wssvp_woo_check = '<div id="message" class="error">
			<p><strong>Products By Attributes & Variations for WooCommerce Plugin is inactive.</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> must be active for this plugin to work. Please install &amp; activate WooCommerce »</p></div>';
		echo wp_kses(__($addify_wssvp_woo_check, 'addify_tax_exempt'), $addify_wssvp_allowed_tags);

	}

	add_action('admin_notices', 'addify_wssvp_admin_notice');
}

if ( !class_exists( 'Addify_Show_Single_Variations' ) ) {

	class Addify_Show_Single_Variations {

		public function __construct() {

			$this->afwssvp_global_constents_vars();
			$this->afwssv_register_taxonomy_for_object_type();
			add_action('wp_loaded', array( $this, 'afwssvp_init' ));

			if (is_admin() ) {
				include_once AFWSSV_PLUGIN_DIR . 'class_afwssvp_admin.php';
			} else {
				include_once AFWSSV_PLUGIN_DIR . 'class_afwssvp_front.php';
			}
		}

		public function afwssv_register_taxonomy_for_object_type() {

			register_taxonomy_for_object_type( 'product_cat', 'product_variation' );
			register_taxonomy_for_object_type( 'product_tag', 'product_variation' );

		}

		public function afwssvp_global_constents_vars() {

			if (!defined('AFWSSV_URL') ) {
				define('AFWSSV_URL', plugin_dir_url(__FILE__));
			}

			if (!defined('AFWSSV_BASENAME') ) {
				define('AFWSSV_BASENAME', plugin_basename(__FILE__));
			}

			if (! defined('AFWSSV_PLUGIN_DIR') ) {
				define('AFWSSV_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}

		public function afwssvp_init() {

			if (function_exists('load_plugin_textdomain') ) {
				load_plugin_textdomain('addify_wssvp', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			}
		}

		

		public function afwssv_add_taxonomies_to_variation_main( $variation_id ) {

			$parent_product_id = wp_get_post_parent_id( $variation_id );

			if ( $parent_product_id ) {

				// add categories and tags to variaition
				$taxonomies = array(
					'product_cat',
					'product_tag'
				);

				foreach ( $taxonomies as $taxonomy ) {

					$terms = (array) wp_get_post_terms( $parent_product_id, $taxonomy, array('fields' => 'ids') );
					wp_set_post_terms( $variation_id, $terms, $taxonomy );

				}

			}

		}

		public function afwssv_add_attributes_to_variation_main( $variation_id ) {

			$attributes = wc_get_product_variation_attributes( $variation_id );

			if ( $attributes && !empty( $attributes ) ) {

				foreach ( $attributes as $taxonomy => $value ) {

					$taxonomy = str_replace('attribute_', '', $taxonomy);
					$term = get_term_by('slug', $value, $taxonomy);
					wp_set_object_terms( $variation_id, $value, $taxonomy );

				}

			}

		}

		public function afwssb_attributes_to_variation_main( $post_id ) {

			$product = wc_get_product( $post_id );
			if ( !empty($product )  ) {

				$variations = $product->get_children();

				$attributes = $product->get_attributes();

				if ( !empty($attributes) ) {
					foreach ( $attributes as $taxonomy => $attribute_data ) {
						if ( 0 == $attribute_data['is_variation'] ) {

							$terms = wp_get_post_terms( $post_id, $taxonomy );

							if ( $variations && $terms && !is_wp_error( $terms ) ) {
								foreach ( $variations as $i => $variation_id ) {

									$term_ids = array();

									foreach ( $terms as $term ) {

										$term_ids[] = $term->term_id;

									}

									$set_terms = wp_set_object_terms( $variation_id, $term_ids, $taxonomy );

								}
							}

						}
					}
				}

			}

		}

	}

	new Addify_Show_Single_Variations();

}
