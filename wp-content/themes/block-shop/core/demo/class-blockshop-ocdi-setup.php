<?php
/**
 * Configure One Click Demo Import with theme specific options
 *
 * @package blockshop
 */

if ( ! class_exists( 'BlockShop_OCDI_Setup' ) ) :
	/**
	 * Configures One Click Demo Import with theme specific demo and options
	 */
	class BlockShop_OCDI_Setup {

		/**
		 * Adds filters to One Click Demo Import
		 */
		public function __construct() {
			add_action( 'pt-ocdi/before_content_import', array( $this, 'ocdi_before_content_import_setup' ) );
			add_filter( 'pt-ocdi/import_files', array( $this, 'set_import_files' ) );
			add_action( 'pt-ocdi/after_import', array( $this, 'ocdi_after_import_setup' ) );
			add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
		}

		/**
		 * Loads the import files
		 */
		public function set_import_files() {
			return array(
				array(
					'import_file_name'             => 'Default',
					'local_import_file'            => trailingslashit( get_template_directory() ) . 'core/demo/blockshop.wordpress.xml',
					'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'core/demo/blockshop.widgets.wie',
					'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'core/demo/blockshop.export.dat',
				),
			);
		}

		/**
		 * Sets default pages
		 *
		 * @param @array $settings Array of settings.
		 */
		public function set_reading_options( $settings ) {
			$reading_settings = $settings['reading_settings'];
			if ( ! empty( $reading_settings ) ) {
				$homepage = get_page_by_title( html_entity_decode( $reading_settings['homepage'] ) );
				$blog     = get_page_by_title( html_entity_decode( $reading_settings['blog'] ) );
				if ( ( isset( $homepage ) && $homepage->ID ) && ( isset( $blog ) && $blog->ID ) ) {
					update_option( 'show_on_front', 'page' );
					update_option( 'page_on_front', $homepage->ID );
					update_option( 'page_for_posts', $blog->ID );
					return true;
				}
			}
			return false;
		}

		/**
		 * Sets WooCommerce pages
		 *
		 * @param @array $settings Array of settings.
		 */
		public function set_woocommerce_pages( $settings ) {
			if ( class_exists( 'Woocommerce' ) && ! empty( $settings['woocommerce_pages'] ) ) {
				foreach ( $settings['woocommerce_pages'] as $woo_name => $woo_title ) {
					$woopage = get_page_by_title( $woo_title );
					if ( isset( $woopage ) && property_exists( $woopage, 'ID' ) ) {
						update_option( $woo_name, $woopage->ID );
					}
				}

				return true;
			}
			return false;
		}

		/**
		 * Adds menu links to shop page in primary menu and vertical menu
		 */
		public function add_shop_page_to_menu() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}
			$vertical_menu = wp_get_nav_menu_items( 'Vertical Menu' );
			$vertical_flag = false;
			if ( is_array( $vertical_menu ) ) {
				foreach ( $vertical_menu as $m ) {
					if ( property_exists( $m, 'post_title' ) && 'The Shop' === $m->post_title ) {
						$vertical_flag = true;
					}
				}
			}

			$primary_menu = wp_get_nav_menu_items( 'Primary Menu' );
			$primary_flag = false;
			if ( is_array( $primary_menu ) ) {
				foreach ( $primary_menu as $m ) {
					if ( property_exists( $m, 'post_title' ) && 'The Shop' === $m->post_title ) {
						$primary_flag = true;
					}
				}
			}

			$vertical_menu = wp_get_nav_menu_object( 'Vertical Menu' );
			if ( false === $vertical_flag && property_exists( $vertical_menu, 'term_id' ) ) {
				wp_update_nav_menu_item(
					$vertical_menu->term_id,
					0,
					array(
						'menu-item-title'     => 'The Shop',
						'menu-item-object-id' => wc_get_page_id( 'shop' ),
						'menu-item-object'    => 'page',
						'menu-item-status'    => 'publish',
						'menu-item-type'      => 'post_type',
					)
				);
			}

			$primary_menu = wp_get_nav_menu_object( 'Primary Menu' );
			if ( false === $primary_flag && property_exists( $primary_menu, 'term_id' ) ) {
				wp_update_nav_menu_item(
					$primary_menu->term_id,
					0,
					array(
						'menu-item-title'     => 'The Shop',
						'menu-item-object-id' => wc_get_page_id( 'shop' ),
						'menu-item-object'    => 'page',
						'menu-item-status'    => 'publish',
						'menu-item-type'      => 'post_type',
						'menu-item-position'  => 2,
					)
				);
			}
		}

		/**
		 * Sets navigation menus
		 *
		 * @param @array $settings Array of settings.
		 */
		public function set_nav_menus( $settings ) {
			if ( is_array( $settings['navigation'] ) ) {
				$locations = get_theme_mod( 'nav_menu_locations' );
				$menus     = wp_get_nav_menus();
				foreach ( (array) $menus as $theme_menu ) {
					foreach ( (array) $settings['navigation'] as $import_menu ) {
						if ( $theme_menu->name === $import_menu['name'] ) {
							$locations[ $import_menu['location'] ] = $theme_menu->term_id;
						}
					}
				}
				set_theme_mod( 'nav_menu_locations', $locations );
				return true;
			}
			return false;
		}

		/**
		 * Filter to be executed before content import
		 */
		public function ocdi_before_content_import_setup() {
			if ( class_exists( 'WooCommerce' ) ) {
				WC_Install::create_pages();
			}
		}

		/**
		 * Filter to be executed after import
		 */
		public function ocdi_after_import_setup() {
			$settings = array(
				'reading_settings'  => array(
					'homepage' => 'Home V1',
					'blog'     => 'The Blog',
				),
				'woocommerce_pages' => array(
					'woocommerce_shop_page_id' => 'The Shop',
				),
				'navigation'        => array(
					0 => array(
						'name'     => 'Primary Menu',
						'location' => 'primary',
					),
					1 => array(
						'name'     => 'Vertical Menu',
						'location' => 'vertical',
					),
					2 => array(
						'name'     => 'Footer Menu',
						'location' => 'footer',
					),
				),
			);

			$this->set_reading_options( $settings );
			$this->set_nav_menus( $settings );
			set_theme_mod( 'imported_demo', 1 );
			flush_rewrite_rules();
			$this->add_shop_page_to_menu();
		}
	}

	new BlockShop_OCDI_Setup();
endif;
