<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( !class_exists( 'Addify_Show_Single_Variations_Front' ) ) {

	class Addify_Show_Single_Variations_Front extends Addify_Show_Single_Variations {

		public function __construct() {

			add_action( 'woocommerce_product_query', array( $this, 'afwssv_show_variations' ), 10, 2 );
			
			add_filter( 'the_title', array( $this, 'afwssv_change_variation_title' ), 99, 2 );
			add_filter( 'woocommerce_get_filtered_term_product_counts_query', array( $this, 'afwssv_filtered_term_product_counts_where_clause' ), 10, 1);
			add_filter( 'post_class', array( $this, 'afwssv_post_classes_in_loop' ) );
			add_filter( 'post_type_link', array( $this, 'afwssv_variation_link' ), 10, 2 );
			add_filter( 'woocommerce_product_get_gallery_image_ids', array( $this, 'afwssv_product_gallery' ), 10, 2 );
			add_filter( 'get_terms', array( $this, 'afwssv_term_counts' ), 10, 2 );
			add_filter( 'woocommerce_price_filter_post_type', array( $this, 'afwssv_product_variation_to_price_filter' ), 10, 1 );
			add_action( 'delete_transient_wc_term_counts', array( $this, 'afwssv_delete_term_counts_transient' ), 10, 1 );

			add_filter( 'woocommerce_get_catalog_ordering_args', array($this, 'afwssv_custom_query_sort_args' ));

			//Variation drop down
			add_action( 'wp_loaded', array( $this, 'afwssvfront_scripts' ) );
			if (!empty(get_option('afwssv_enable_dropdown')) && get_option('afwssv_enable_dropdown') == 'yes') {

				add_filter( 'init', array($this, 'afwssv_change_loop_add_to_cart'), 10 );
				add_action( 'wp_head', array($this, 'afwssv_add_redirect_hook') );
			}

			add_filter('woocommerce_product_query_meta_query', array($this, 'afwssv_exclude_product'), 20);
		}

		public function afwssv_exclude_product( $meta_query ) {
			
			if ( is_shop() || is_archive() ) {
				// $meta_query[] = array(
				// 	'key'           => '_afwssv_exclude_show_as_single',
				// 	'value'         => 'yes',
				// 	'compare'       => 'NOT EXISTS'
				// );


				$meta_query[] = array(
					array(
					'relation' => 'OR',
					array(
						'key'     => '_afwssv_exclude_show_as_single',
						'compare' => 'NOT EXISTS'
					),
					array(
						'key'     => '_afwssv_exclude_show_as_single',
						'value'   => 'no',
						'compare' => 'IN'
					)
				)
				);
			}
			return $meta_query;
		}

		public function afwssvfront_scripts() {

			wp_enqueue_style( 'afwssv_frontc', plugins_url( '/assets/css/afwssv_front_css.css', __FILE__ ), false, '1.0' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'afwssv_front', plugins_url( '/assets/js/afwssv_front.js', __FILE__ ), false, '1.0' );
			$afwssv_data = array(
				'afwssv_enable_toggle'  => esc_attr(get_option('afwssv_enable_toggle')),
				'afwssv_toggle_text'  => esc_attr(get_option('afwssv_toggle_text')),

			);
			wp_localize_script( 'afwssv_front', 'afwssv_phpvars', $afwssv_data );
		}

		public function afwssv_add_redirect_hook() {

			if ( is_shop() || is_product_tag() ) {
				$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
				echo '<script>jQuery(document).ready(function(){ jQuery("form.variations_form").attr("action" , "' . esc_url($shop_page_url) . '");});</script>';
			} else if (is_product_category()) {
				global $wp;
				$current_url = home_url(add_query_arg(array(), $wp->request));
				echo '<script>jQuery(document).ready(function(){ jQuery("form.variations_form").attr("action" , "' . esc_url($current_url) . '");});</script>';
			}
		}

		public function afwssv_change_loop_add_to_cart() {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			add_action( 'woocommerce_after_shop_loop_item', array($this, 'afwssv_template_loop_add_to_cart'), 10 );
		}

		/**
		 * Replace add to cart button in the loop.
		 */
		public function afwssv_template_loop_add_to_cart() {

			global $product;
			$product_type = $product->get_type();

			if ( ! $product->is_type( 'variable' ) ) {
				woocommerce_template_loop_add_to_cart();
				return;
			}

			$afwssv_enable = esc_attr(get_option('afwssv_enable_dropdown'));

			if ('yes' == $afwssv_enable) {

				if (!empty(get_option('afwssv_choose_display_products'))) {
					$pec_select_products_afwssv = maybe_unserialize(get_option('afwssv_choose_display_products'));
				} else {
					$pec_select_products_afwssv = array();
				}

				if (!empty(get_option('afwssv_choose_display_categories'))) {
					$pec_select_categories_afwssv = maybe_unserialize(get_option('afwssv_choose_display_categories'));
				} else {
					$pec_select_categories_afwssv = array();
				}


				//Category Products
				$rargs         = array(
					'numberposts' => -1,
					'post_type' => 'product',
					'post_stats' => 'publish',
					'tax_query'     => array(
						array(
							'taxonomy'  => 'product_cat',
							'field'     => 'id',
							'terms'     => $pec_select_categories_afwssv
						)
					)
				);
				$rproducts_ids = get_posts($rargs);

				if (!empty($rproducts_ids)) {
					foreach ($rproducts_ids as $rproid) {
						$pec_select_products_afwssv[] .= $rproid->ID;
					}
				}

				if (in_array($product->get_id(), $pec_select_products_afwssv)) {
					$pec_enable_afwssv_qty = esc_attr(get_option('afwssv_enable_qty_box'));
					if ( 'yes' != $pec_enable_afwssv_qty) {
						remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
						add_action('woocommerce_single_variation', array($this, 'afwssv_loop_variation_add_to_cart_button'), 20);
					}

					woocommerce_template_single_add_to_cart();
				} else { 

					if (1 == $product->is_purchasable()) {

						$cls = 'add_to_cart_button';
					} else {
						$cls = '';
					}

					echo '<a href="' . esc_url($product->add_to_cart_url()) . '" rel="nofollow" data-product_id="' . esc_attr($product->get_id()) . '" data-product_sku="' . esc_attr($product->get_sku()) . '" class="button ' . esc_attr($cls) . ' product_type_' . esc_attr($product_type) . '">' . esc_html( $product->add_to_cart_text() ) . '</a>';

					/*echo esc_url(apply_filters( 'woocommerce_loop_add_to_cart_link',
						sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
							esc_url( $product->add_to_cart_url() ),
							esc_attr( $product->get_id() ),
							esc_attr( $product->get_sku() ),
							$product->is_purchasable() ? 'add_to_cart_button' : '',
							esc_attr( $product_type ),
							esc_html( $product->add_to_cart_text() )
						),
						$product ));*/

				}
			} else {

				switch ( $product_type ) {
					case 'external':
						echo esc_url(apply_filters( 'woocommerce_loop_add_to_cart_link',
							sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
								esc_url( $product->add_to_cart_url() ),
								esc_attr( $product->get_id() ),
								esc_attr( $product->get_sku() ),
								$product->is_purchasable() ? 'add_to_cart_button' : '',
								esc_attr( $product_type ),
								esc_html( $product->add_to_cart_text() )
							),
							$product ));
						break;
					case 'grouped':
						echo esc_url(apply_filters( 'woocommerce_loop_add_to_cart_link',
							sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
								esc_url( $product->add_to_cart_url() ),
								esc_attr( $product->get_id() ),
								esc_attr( $product->get_sku() ),
								$product->is_purchasable() ? 'add_to_cart_button' : '',
								esc_attr( $product_type ),
								esc_html( $product->add_to_cart_text() )
							),
							$product ));
						break;
					case 'simple':
						echo esc_url(apply_filters( 'woocommerce_loop_add_to_cart_link',
							sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
								esc_url( $product->add_to_cart_url() ),
								esc_attr( $product->get_id() ),
								esc_attr( $product->get_sku() ),
								$product->is_purchasable() ? 'add_to_cart_button' : '',
								esc_attr( $product_type ),
								esc_html( $product->add_to_cart_text() )
							),
							$product ));
						break;
					case 'variable':
						echo esc_url(apply_filters( 'woocommerce_loop_add_to_cart_link',
							sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
								esc_url( $product->add_to_cart_url() ),
								esc_attr( $product->get_id() ),
								esc_attr( $product->get_sku() ),
								$product->is_purchasable() ? 'add_to_cart_button' : '',
								esc_attr( $product_type ),
								esc_html( $product->add_to_cart_text() )
							),
							$product ));
						break;
					default:
						echo esc_url(apply_filters( 'woocommerce_loop_add_to_cart_link',
							sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
								esc_url( $product->add_to_cart_url() ),
								esc_attr( $product->get_id() ),
								esc_attr( $product->get_sku() ),
								$product->is_purchasable() ? 'add_to_cart_button' : '',
								esc_attr( $product_type ),
								esc_html( $product->add_to_cart_text() )
							),
							$product ));
				}

			}
		}


		public function afwssv_loop_variation_add_to_cart_button() {
			global $product;

			?>
			<div class="woocommerce-variation-add-to-cart variations_button">
				<button type="submit"
						class="single_add_to_cart_button button"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
				<input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>"/>
				<input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>"/>
				<input type="hidden" name="variation_id" class="variation_id" value="0"/>
			</div>
			<?php
		}


        public static function miro_get_ids_from_filter_2()
        {
            $arr_new_ids = array();
            $tire_season = 0;
            $vehicle_type = 0;
            $tire_width = 0;
            $tire_profile = 0;
            $rim_size = 0;
            $tire_speed_index = 0;
            $tire_load_index = 0;
            $tire_options = 0;
            $filters = array();
            $tire_options = array();
        
       
            
			if(isset($_GET["tire_season"]) && !empty($_GET["tire_season"]))
			{
                $filters["tire_season"] = $_GET["tire_season"];
                if($filters["tire_season"]=="All_season")
                    $filters["tire_season"]="All Season";
			}
            
            if(isset($_GET["vehicle_type"]) && !empty($_GET["vehicle_type"]))
               $filters["vehicle_type"] = $_GET["vehicle_type"];
            
            if(isset($_GET["tire_width"]) && !empty($_GET["tire_width"]))
                $filters["tire_width"] = $_GET["tire_width"];
                
            if(isset($_GET["tire_profile"]) && !empty($_GET["tire_profile"]))
                $filters["tire_profile"] = $_GET["tire_profile"];
                
            if(isset($_GET["rim_size"]) && !empty($_GET["rim_size"]))
                $filters["rim_size"] = $_GET["rim_size"];
                
            if(isset($_GET["tire_speed_index"]) && !empty($_GET["tire_speed_index"]))
               $filters["tire_speed_index"] = $_GET["tire_speed_index"];
                
            if(isset($_GET["tire_load_index"]) && !empty($_GET["tire_load_index"]))
                $filters["tire_load_index"] = $_GET["tire_load_index"];    
                
            if(isset($_GET["tire_options"]) && !empty($_GET["tire_options"]))
                $tire_options = ($_GET["tire_options"]); 
            
             global $wpdb;
             
            if(count($filters) > 0 || count($tire_options) > 0)
            {
                
               $sql = "SELECT  p.ID FROM $wpdb->posts as p";
               
               $count = 1;
               
               foreach($filters as $key => $value) {
                    $sql.= " left join $wpdb->postmeta as m$count ON p.ID = m$count.post_id";
                    
                    $count += 1;
                }
                
                foreach($tire_options as $key => $value) {
                    $sql.= " left join $wpdb->postmeta as m$count ON p.ID = m$count.post_id";
                    
                    $count += 1;
                }
                
                $sql.= " WHERE p.post_type = 'product_variation' ";
                
                $count = 1;
                   
               foreach($filters as $key => $value) {
                    $sql.= " AND m$count.meta_key LIKE '$key' AND m$count.meta_value LIKE '$value'";
                    
                    $count += 1;
                }
                
                foreach($tire_options as $key => $value) {
                  $sql.= " AND m$count.meta_key LIKE 'tire_options' AND m$count.meta_value LIKE '%$value%'";
                    
                    $count += 1;
                }
               // var_dump($sql);
               // exit;
                
               
                
                $result = $wpdb->get_results ($sql);

                $final_arr = array();
                foreach ($result as $obj)
                {
                    $final_arr[] = (int)$obj->ID;
                }
                
               // echo count($final_arr);
              
              
                return $final_arr;


            }
                
        }
        
         public static function miro_get_ids_from_filter_1()
        {
            $arr_new_ids = array();
            $tire_season = 0;
            $tire_width = 0;
            $tire_profile = 0;
            $rim_size = 0;
            $tire_options = 0;
            $filters = array();
            $tire_options = array();
            
			if(isset($_GET["tire_season"]) && !empty($_GET["tire_season"]))
			{
                $filters["tire_season"] = $_GET["tire_season"];
                    if($filters["tire_season"]=="All_season")
                    $filters["tire_season"]="All Season";   
			}
            
            if(isset($_GET["tire_width"]) && !empty($_GET["tire_width"]))
                $filters["tire_width"] = $_GET["tire_width"];
                
            if(isset($_GET["tire_profile"]) && !empty($_GET["tire_profile"]))
                $filters["tire_profile"] = $_GET["tire_profile"];
                
            if(isset($_GET["rim_size"]) && !empty($_GET["rim_size"]))
                $filters["rim_size"] = $_GET["rim_size"];
                
            if(isset($_GET["tire_options"]) && !empty($_GET["tire_options"]))
                $tire_options = ($_GET["tire_options"]); 
            
             global $wpdb;
             
            if(count($filters) > 0 || count($tire_options) > 0)
            {
               $sql = "SELECT  p.ID FROM $wpdb->posts as p";
               
               $count = 1;
               
               foreach($filters as $key => $value) {
                    $sql.= " left join $wpdb->postmeta as m$count ON p.ID = m$count.post_id";
                    
                    $count += 1;
                }
                
                foreach($tire_options as $key => $value) {
                    $sql.= " left join $wpdb->postmeta as m$count ON p.ID = m$count.post_id";
                    
                    $count += 1;
                }
                
                $sql.= " WHERE p.post_type = 'product_variation' ";
                
                $count = 1;
                   
               foreach($filters as $key => $value) {
                    $sql.= " AND m$count.meta_key LIKE '$key' AND m$count.meta_value LIKE '$value'";
                    
                    $count += 1;
                }
                
                foreach($tire_options as $key => $value) {
                  $sql.= " AND m$count.meta_key LIKE 'tire_options' AND m$count.meta_value LIKE '%$value%'";
                    
                    $count += 1;
                }
               // var_dump($sql);
               // exit;
                
               
                
                $result = $wpdb->get_results ($sql);

                $final_arr = array();
                foreach ($result as $obj)
                {
                    $final_arr[] = (int)$obj->ID;
                }
                return $final_arr;


            }
                
        }
        
		public function afwssv_show_variations( $q, $wc_query) {

			if ( !is_admin() && is_woocommerce() && $q->is_main_query() && isset( $q->query_vars['wc_query'] ) ) {

				global $_chosen_attributes;

				if (!empty(unserialize(get_option('afwssv_applied_on_products')))) {
					$afwssv_applied_on_products = unserialize(get_option('afwssv_applied_on_products'));
				} else {
					$afwssv_applied_on_products = array();
				}

				if (!empty(unserialize(get_option('afwssv_applied_on_categories')))) {
					$afwssv_applied_on_categories = unserialize(get_option('afwssv_applied_on_categories'));
				} else {
					$afwssv_applied_on_categories = array();
				}

				if (!empty(unserialize(get_option('afwssv_applied_on_products')))) {
					$afwssv_applied_on_products1 = unserialize(get_option('afwssv_applied_on_products'));
				} else {
					$afwssv_applied_on_products1 = array();
				}

				if (!empty(unserialize(get_option('afwssv_applied_on_categories')))) {
					$afwssv_applied_on_categories1 = unserialize(get_option('afwssv_applied_on_categories'));
				} else {
					$afwssv_applied_on_categories1 = array();
				}


				if ( 'yes' == get_option('afwssv_enable_single_variation')) {

					$post_type   = (array) $q->get('post_type');
					$post_type[] = 'product_variation';
					if (!in_array('product', $post_type)) {
						$post_type[] = 'product';
					}
					$q->set('post_type', array_filter($post_type));

					$unpublished_variable_products = $this->get_unpublished_variable_products();
					if ($unpublished_variable_products) {
						$post_parent__not_in = (array) $q->get('post_parent__not_in');
						$q->set('post_parent__not_in', array_merge($post_parent__not_in, $unpublished_variable_products));
					}

					$variation_ids_with_no_parent = $this->get_variation_ids_with_no_parent();
					if ($variation_ids_with_no_parent) {
						$post__not_in = (array) $q->get('post__not_in');
						$q->set('post__not_in', array_merge($post__not_in, $variation_ids_with_no_parent));
					}

					$main_pro     = array();
					$all_products = array();

					if ('yes' == get_option('afwssv_hide_main_product')) {

						//Category Products
						$rrargs         = array(
							'numberposts' => -1,
							'post_type' => 'product',
							'post_status' => 'publish',
							'tax_query'     => array(
								array(
									'taxonomy'  => 'product_cat',
									'field'     => 'id',
									'terms'     => $afwssv_applied_on_categories1
								)
							)
						);
						$rrproducts_ids = get_posts($rrargs);

						if (!empty($rrproducts_ids)) {
							foreach ($rrproducts_ids as $rrproid) {
								$_product = wc_get_product( $rrproid->ID );

								if ('variable' == $_product->get_type()) {
									$afwssv_applied_on_products1[] .= $rrproid->ID;
								}
								
							}
						}

						//All Products
						

						$all_args = array(
							'post_type' => 'product',
							'post_status' => 'publish',
							'numberposts' => -1,
							
						);
						$all_pros = get_posts($all_args);

						if (!empty($all_pros)) {
							foreach ($all_pros as $all_pro_id) {

								

								if (!in_array($all_pro_id->ID, $afwssv_applied_on_products1)) {
									$all_products[] .= $all_pro_id->ID;
								}
								
							}
						}
						

					} else {

						//All Products
						

						$all_args = array(
							'post_type' => 'product',
							'post_status' => 'publish',
							'numberposts' => -1,
							
						);
						$all_pros = get_posts($all_args);

						if (!empty($all_pros)) {
							foreach ($all_pros as $all_pro_id) {
								$all_products[] .= $all_pro_id->ID;
							}
						}


					}

					

					//Category Products
					$rargs         = array(
						'numberposts' => -1,
						'post_type' => 'product',
						'post_status' => 'publish',
						'tax_query'     => array(
							array(
								'taxonomy'  => 'product_cat',
								'field'     => 'id',
								'terms'     => $afwssv_applied_on_categories
							)
						)
					);
					$rproducts_ids = get_posts($rargs);

					if (!empty($rproducts_ids)) {
						foreach ($rproducts_ids as $rproid) {
							$afwssv_applied_on_products[] .= $rproid->ID;
						}
					}

					$children_ids = array();
					$newarr       = array();

					foreach ($afwssv_applied_on_products as $expros) {

						$product        = wc_get_product($expros);
						$children_ids[] = $product->get_children();
					}

					foreach ($children_ids as $child) {

						foreach ($child as $key => $value) {

							$newarr[] = $value;
						}
					}
                
					$q->set('post__in', array_merge($newarr, $all_products));
	           // Second filter
	           
	            $arr_new_ids = 0;
	           
				$arr_new_ids = Addify_Show_Single_Variations_Front::miro_get_ids_from_filter_2();
				
				if($arr_new_ids != 0)
				{
            	    $q->set('post__in', $arr_new_ids);
				}
            

                //first filter
                $filter1 = 0;
                
                $filter1 = Addify_Show_Single_Variations_Front::miro_get_ids_from_filter_1();
                
                if($filter1 != 0)
                {
            	    $q->set('post__in', $filter1);
                }
            	    
            	    
				$meta_query = (array) $q->get('meta_query');
				$meta_query = $this->update_meta_query($meta_query);


				$q->set('meta_query', $meta_query);
				}
			

			}

		}


		public function get_unpublished_variable_products() {


			$statuses = array('trash','future','auto-draft','pending','draft');

			if ( !current_user_can('edit_posts') ) {
				$statuses[] = 'draft';
			}

			$args = array(
				'post_type' => 'product',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'variable',
					),
				),
				'posts_per_page' => -1,
				'post_status' => $statuses
			);

			$products = new WP_Query( $args );

			wp_reset_postdata();

			if ( !$products->have_posts() ) {
				return false;
			}

			return wp_list_pluck( $products->posts, 'ID' );

		}

		public function get_variation_ids_with_no_parent() {

			global $wpdb;

			$variation_ids = $wpdb->get_results(
				"
			SELECT  p1.ID
			FROM $wpdb->posts p1
			WHERE p1.post_type = 'product_variation'
			AND p1.post_parent NOT IN (
				SELECT DISTINCT p2.ID
				FROM $wpdb->posts p2
				WHERE p2.post_type = 'product'
			)
			", ARRAY_A
			);

			if ( !$variation_ids ) {
				return false;
			}

			return wp_list_pluck( $variation_ids, 'ID' );

		}

		public function update_meta_query( $meta_query ) {

			$index = 0;

			if ( !empty($meta_query) ) {
				foreach ( $meta_query as $index => $meta_query_item ) {
					if ( isset( $meta_query_item['key'] ) && '_afwssv_show_as_single' == $meta_query_item['key'] ) {

						$meta_query[$index]             = array();
						$meta_query[$index]['relation'] = 'AND';

						$meta_query[$index]['_afwssv_show_as_single'] = array(
							'key' => '_afwssv_show_as_single',
							'value' => 'yes',
							'compare' => '='
						);
					}
				}
			}

			return $meta_query;

		}

		public function afwssv_change_variation_title( $title, $id = false) {

			if ( $id && $this->afwssv_is_product_variation( $id ) ) {
				$title = $this->afwssv_get_variation_title( $id, $title );
			}

			return $title;

		}

		public function afwssv_is_product_variation( $id ) {

			$post_type = get_post_type( $id );

			return 'product_variation' == $post_type ? true : false;

		}

		public function afwssv_get_variation_title( $variation_id, $title ) {

			if ( !$variation_id || '' == $variation_id ) {
				return '';
			}

			$variation              = wc_get_product( absint( $variation_id ) );
			$variation_title        = $title;
			$variation_custom_title = get_post_meta($variation_id, '_afwssv_variation_title', true);

			return ( $variation_custom_title ) ? $variation_custom_title : $variation_title;

		}

		public function afwssv_filtered_term_product_counts_where_clause( $query ) {

			global $wpdb, $wp_the_query;

			$query['where'] = str_replace("'product'", "'product', 'product_variation'", $query['where']);

			if ( empty( $wp_the_query->query_vars['post_parent__not_in'] ) ) {
				return $query;
			}

			$query['where'] = sprintf("%s AND %s.post_parent NOT IN ('%s')", $query['where'], $wpdb->posts, implode("','", $wp_the_query->query_vars['post_parent__not_in']));

			return $query;

		}


		public function afwssv_post_classes_in_loop( $loop_class) {

			global $post, $product;

			if ( $product && 'product_variation' === $post->post_type ) {

				$loop_class = array_diff($loop_class, array('hentry', 'post'));

				$loop_class[] = 'product';

			}

			return $loop_class;
		}

		

		public function afwssv_variation_link( $url, $product) {

			if ( 'product_variation' == $product->post_type ) {

				$variation = wc_get_product( absint( $product->ID ) );

				return $this->afwssv_get_variation_url( $variation );

			}

			return $url;

		}

		public function afwssv_get_variation_url( $vari) {

			$url = '';

			if ( $vari->get_id() ) {

				$variation_data     = array_filter( wc_get_product_variation_attributes( $vari->get_id() ) );
				$parent_product_id  = $vari->get_parent_id();
				$parent_product_url = get_the_permalink( $parent_product_id );

				$url = add_query_arg( $variation_data, $parent_product_url );

			}

			return $url;

		}

		public function afwssv_product_gallery( $data_ids, $product) {

			if ( 'variation' == $product->get_type() ) {

				$data_ids       = array();
				$additional_ids = get_post_meta( $product->get_id(), 'variation_image_gallery', true );

				if ( $additional_ids ) {

					$data_ids = explode(',', $additional_ids);

				}

			}

			return $data_ids;

		}

		public function afwssv_term_counts( $terms, $taxonomies) {

			if ( is_admin() || is_ajax() ) {
				return $terms;
			}

			if ( ! isset( $taxonomies[0] ) || ! in_array( $taxonomies[0], apply_filters( 'woocommerce_change_term_counts', array( 'product_cat', 'product_tag' ) ) ) ) {
				return $terms;
			}

			$variation_term_counts = get_transient( 'afwssv_term_counts' );

			if ( false === $variation_term_counts ) {

				$variation_term_counts = array();

				foreach ( $terms as &$term ) {

					if ( !is_object( $term ) ) {
						continue;
					}

					$variation_term_counts[ $term->term_id ] = absint( $this->get_variations_count_in_term( $term ) );

				}

				set_transient( 'afwssv_term_counts', $variation_term_counts );

			}

			$term_counts = get_transient( 'wc_term_counts' );

			foreach ( $terms as &$term ) {

				if ( !is_object( $term ) ) {
					continue;
				}

				if ( !isset( $term_counts[ $term->term_id ] ) ) {
					continue;
				}

				$child_term_count = isset( $variation_term_counts[ $term->term_id ] ) ? $variation_term_counts[ $term->term_id ] : 0;

				if (!empty($term_counts) || 0 != $term_counts) {

					if ('' == $child_term_count || 0 == $child_term_count) {
						$term_counts[ $term->term_id ] = 0;

					} else {
						$term_counts[ $term->term_id ] = $term_counts[ $term->term_id ] + $child_term_count;
					}
				} else {
					$term_counts[ $term->term_id ] = 0;
				}

				

				if ( empty( $term_counts[ $term->term_id ] ) ) {
					continue;
				}

				$term->count = absint( $term_counts[ $term->term_id ] );

			}

			return $terms;
		}

		public function get_variations_count_in_term( $term ) {

			global $wpdb;

			

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `wp_posts` wp
	            INNER JOIN `wp_postmeta` wm ON (wm.`post_id` = wp.`ID` AND wm.`meta_key`='_visibility')
	            INNER JOIN `wp_term_relationships` wtr ON (wp.`ID` = wtr.`object_id`)
	            INNER JOIN `wp_term_taxonomy` wtt ON (wtr.`term_taxonomy_id` = wtt.`term_taxonomy_id`)
	            INNER JOIN `wp_terms` wt ON (wt.`term_id` = wtt.`term_id`)
	            AND wtt.taxonomy = %s AND wt.`slug` = %s
	            AND wp.post_status = 'publish' AND ( wm.meta_value LIKE %s OR wm.meta_value LIKE %s )
	            AND wp.post_type = 'product_variation'
	            ORDER BY wp.post_date DESC", $term->taxonomy, $term->slug, '%visible%', '%catalog%' ) );

			return apply_filters( 'afwssv_variations_count_in_term', $count, $term );
		}

		
		public function afwssv_get_woo_version_number() {

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once  ABSPATH . 'wp-admin/includes/plugin.php' ;
			}

			$plugin_folder = get_plugins( '/woocommerce' );
			$plugin_file   = 'woocommerce.php';

			if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
				return $plugin_folder[$plugin_file]['Version'];

			} else {
				return null;
			}

		}

		public function afwssv_product_variation_to_price_filter( $post_types) {

			$post_types[] = 'product_variation';

			return $post_types;

		}

		public function afwssv_delete_term_counts_transient() {

			delete_transient( 'afwssv_term_counts' );

		}

		public function afwssv_custom_query_sort_args() {

			// Sort by and order
			$current_order = ( isset( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

			switch ( $current_order ) {
				case 'date':
					$orderby  = 'date';
					$order    = 'desc';
					$meta_key = '';
					break;
				case 'price':
					$orderby  = 'meta_value_num';
					$order    = 'asc';
					$meta_key = '_price';
					break;
				case 'price-desc':
					$orderby  = 'meta_value_num';
					$order    = 'desc';
					$meta_key = '_price';
					break;
				case 'rating':
					$orderby  = 'meta_value_num';
					$order    = 'desc';
					$meta_key = '_wc_average_rating';
					break;
				case 'popularity':
					$orderby  = 'meta_value_num';
					$order    = 'desc';
					$meta_key = 'total_sales';
					break;
				case 'title':
					$orderby  = 'meta_value';
					$order    = 'asc';
					$meta_key = '_woocommerce_product_short_title';
					break;
				default:
					$orderby  = 'menu_order title';
					$order    = 'asc';
					$meta_key = '';         
					break;
			}

			$args = array();

			$args['orderby'] = $orderby;
			$args['order']   = $order;

			if ($meta_key) :
				$args['meta_key'] = $meta_key;
			endif;

			return $args;
		}

	}

	new Addify_Show_Single_Variations_Front();

}
