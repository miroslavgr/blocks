<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( !class_exists( 'Addify_Show_Single_Variations_Admin' ) ) {

	class Addify_Show_Single_Variations_Admin extends Addify_Show_Single_Variations {

		public function __construct() {
			
			add_action( 'admin_menu', array( $this, 'afwssv_custom_menu_admin' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'afwssv_admin_assets' ) );
			if (isset($_POST['afwssv_save_settings']) && '' != $_POST['afwssv_save_settings']) {
				include_once(ABSPATH . 'wp-includes/pluggable.php');
				if (!empty($_REQUEST['afwssv_nonce_field'])) {

						$retrieved_nonce = sanitize_text_field($_REQUEST['afwssv_nonce_field']);
				} else {
						$retrieved_nonce = 0;
				}

				if (!wp_verify_nonce($retrieved_nonce, 'afwssv_nonce_action')) {

					die('Failed security check');
				}

				$this->afwssv_save_data();
				add_action('admin_notices', array($this, 'afwssv_author_admin_notice'));
			}

			add_action( 'woocommerce_product_after_variable_attributes', array($this, 'afwssv_add_variation_data'), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array($this, 'afwssv_save_custom_field_variations'), 10, 2 );
			add_action( 'set_object_terms', array( $this, 'afwssb_set_variation_terms' ), 10, 6 );
			add_action( 'updated_post_meta', array( $this, 'afwssb_updated_product_attributes' ), 10, 4 );
			add_action( 'save_post', array( $this, 'afwssv_product_save' ), 10, 1 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'afwssv_variation_save' ), 10, 2 );

			add_action('wp_ajax_afwssvsearchProducts', array($this, 'afwssvsearchProducts'));
		}

		public function afwssv_custom_menu_admin() {

			add_submenu_page( 'woocommerce', esc_html__('Products By Attributes', 'addify_wssvp'), esc_html__('Products By Attributes', 'addify_wssvp'), 'manage_options', 'addify-variation-product', array($this, 'afwssv_exempt_settings') );
		}

		public function afwssv_admin_assets() {

			wp_enqueue_style( 'afwssv_adminc', plugins_url( '/assets/css/afwssv_admin_css.css', __FILE__ ), false, '1.0' );
			wp_enqueue_script( 'afwssv_adminj', plugins_url( '/assets/js/afwssv_admin.js', __FILE__ ), false, '1.0' );
			$afwssv_data = array(
				'admin_url'  => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('afwssv-ajax-nonce'),

			);
			wp_localize_script( 'afwssv_adminj', 'afwssv_php_vars', $afwssv_data );

			wp_enqueue_style( 'select2', plugins_url( '/assets/css/select2.css', __FILE__ ), false , '1.0' );
			wp_enqueue_script( 'select2', plugins_url( '/assets/js/select2.js', __FILE__ ), false, '1.0'  );
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script( 'jquery-ui', plugins_url( '/assets/js/jquery-ui.js', __FILE__ ), array('jquery'), '1.0'  );

		}

		public function afwssv_exempt_settings() {

			?>
			<div id="addify_settings_tabs">

				<div class="addify_setting_tab_ulli">
					<div class="addify-logo">
						<img src="<?php echo esc_url(AFWSSV_URL . '/assets/img/addify-logo.png'); ?>" width="200">
						
					</div>

					<ul>
						<li><a href="#tabs-1"><span class="dashicons dashicons-admin-tools"></span><?php echo esc_html__('Products by Attributes Settings', 'addify_wssvp'); ?></a></li>
						<li><a href="#tabs-2"><span class="dashicons dashicons-admin-tools"></span><?php echo esc_html__('Display Variations Dropdown Settings', 'addify_wssvp'); ?></a></li>
					</ul>
				</div>

				<div class="addify-tabs-content">
					<form id="addify_setting_form" action="" method="post">
						<?php wp_nonce_field('afwssv_nonce_action', 'afwssv_nonce_field'); ?>
						<div class="addify-top-content">
							<h1><?php echo esc_html__('Settings for WooCommerce Products by Attributes & Variations', 'addify_wssvp'); ?></h1>

						</div>

						<div class="addify-singletab" id="tabs-1">
							<h2><?php echo esc_html__('Products by Attributes Settings', 'addify_wssvp'); ?></h2>
							<table class="addify-table-optoin">
								<tbody>

									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Enable', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<div id="like_dislike">
												<input <?php echo checked('no', esc_attr(get_option('afwssv_enable_single_variation'))); ?> value="no" class="allow_guest likespermission" id="extld0" type="radio" name="afwssv_enable_single_variation">
												<label class="extndc" for="extld0"><?php echo esc_html__('No', 'addify_wssvp'); ?></label>
												<input <?php echo checked('yes', esc_attr(get_option('afwssv_enable_single_variation'))); ?> value="yes" class="allow_guest likespermission" id="extld1" type="radio" name="afwssv_enable_single_variation">
												<label class="extndc" for="extld1"><?php echo esc_html__('Yes', 'addify_wssvp'); ?></label>
												<div id="like_dislikeb"></div>
											</div>
											<p><?php echo esc_html__('When you enable this, the variations will be displayed as simple products. It will only be applied for the products specified below.', 'addify_wssvp'); ?></p>
										</td>
									</tr>

									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Exclude Main Variable Product', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<div id="like_dislike_cat">
												<input <?php echo checked('no', esc_attr(get_option('afwssv_hide_main_product'))); ?> value="no" class="allow_guest likespermission" id="extld2" type="radio" name="afwssv_hide_main_product">
												<label class="extndc" for="extld2"><?php echo esc_html__('No', 'addify-sitemap'); ?></label>
												<input <?php echo checked('yes', esc_attr(get_option('afwssv_hide_main_product'))); ?> value="yes" class="allow_guest likespermission" id="extld3" type="radio" name="afwssv_hide_main_product">
												<label class="extndc" for="extld3"><?php echo esc_html__('Yes', 'addify-sitemap'); ?></label>
												<div id="like_dislikeb_cat"></div>
											</div>
											<p><?php echo esc_html__('When enabled, the main variable product will not be displayed on shop/category pages. Only the variations will be displayed as simple products.', 'addify_wssvp'); ?></p>
										</td>
									</tr>

									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Applied On Products', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<select class="afwssv_applied_on_products" name="afwssv_applied_on_products[]" id="afwssv_applied_on_products"  multiple='multiple'>

												<?php

												$afwssv_applied_on_products = unserialize(get_option('afwssv_applied_on_products'));

												if (!empty($afwssv_applied_on_products)) {

													foreach ( $afwssv_applied_on_products as $pro) {

														$prod_post = get_post($pro);

														?>

															<option value="<?php echo intval($pro); ?>" selected="selected"><?php echo esc_attr($prod_post->post_title); ?></option>

														<?php 
													}
												}
												?>

											</select>
											<p><?php echo esc_html__('The variations of these specified products will be displayed as simple products on shop pages.', 'addify_wssvp'); ?></p>
										</td>
									</tr>


									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Categories', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<div class="all_cats">
												<ul>
													<?php

													$afwssv_applied_on_categories = unserialize(get_option('afwssv_applied_on_categories'));

													if (!empty($afwssv_applied_on_categories)) {
														$pre_vals = $afwssv_applied_on_categories;
													} else {
														$pre_vals = array();
													}

													$args = array(
														'taxonomy' => 'product_cat',
														'hide_empty' => false,
														'parent'   => 0
													);

													$product_cat = get_terms( $args );
													foreach ($product_cat as $parent_product_cat) {
														?>
														<li class="par_cat">
															<input type="checkbox" class="parent" name="afwssv_applied_on_categories[]" id="afwssv_applied_on_categories" value="<?php echo intval($parent_product_cat->term_id); ?>"
															<?php
															if (!empty($pre_vals) && in_array($parent_product_cat->term_id, $pre_vals)) {
																echo 'checked';
															} 
															?>
															/>
															<?php echo esc_attr($parent_product_cat->name); ?>

															<?php
															$child_args = array(
																'taxonomy' => 'product_cat',
																'hide_empty' => false,
																'parent'   => $parent_product_cat->term_id
															);
															$child_product_cats = get_terms( $child_args );
															if (!empty($child_product_cats)) {
																?>
																<ul>
																	<?php
																	foreach ($child_product_cats as $child_product_cat) {
																		?>
																		<li class="child_cat">
																			<input type="checkbox" class="child parent" name="afwssv_applied_on_categories[]" id="afwssv_applied_on_categories" value="<?php echo intval($child_product_cat->term_id); ?>"
																			<?php
																			if (!empty($pre_vals) &&in_array($child_product_cat->term_id, $pre_vals)) {
																				echo 'checked';
																			}
																			?>
																			/>
																			<?php echo esc_attr($child_product_cat->name); ?>

																			<?php
																			//2nd level
																			$child_args2= array(
																				'taxonomy' => 'product_cat',
																				'hide_empty' => false,
																				'parent'   => $child_product_cat->term_id
																			);

																			$child_product_cats2 = get_terms( $child_args2 );
																			if (!empty($child_product_cats2)) {
																				?>

																				<ul>
																					<?php
																					foreach ($child_product_cats2 as $child_product_cat2) {
																						?>

																						<li class="child_cat">
																							<input type="checkbox" class="child parent" name="afwssv_applied_on_categories[]" id="afwssv_applied_on_categories" value="<?php echo intval($child_product_cat2->term_id); ?>"
																							<?php
																							if (!empty($pre_vals) &&in_array($child_product_cat2->term_id, $pre_vals)) {
																								echo 'checked';
																							}
																							?>
																							/>
																							<?php echo esc_attr($child_product_cat2->name); ?>


																							<?php
																							//3rd level
																							$child_args3= array(
																								'taxonomy' => 'product_cat',
																								'hide_empty' => false,
																								'parent'   => $child_product_cat2->term_id
																							);

																							$child_product_cats3 = get_terms( $child_args3 );
																							if (!empty($child_product_cats3)) {
																								?>

																								<ul>
																									<?php
																									foreach ($child_product_cats3 as $child_product_cat3) {
																										?>

																										<li class="child_cat">
																											<input type="checkbox" class="child parent" name="afwssv_applied_on_categories[]" id="afwssv_applied_on_categories" value="<?php echo intval($child_product_cat3->term_id); ?>"
																											<?php
																											if (!empty($pre_vals) &&in_array($child_product_cat3->term_id, $pre_vals)) {
																												echo 'checked';
																											}
																											?>
																											/>
																											<?php echo esc_attr($child_product_cat3->name); ?>


																											<?php
																											//4th level
																											$child_args4= array(
																												'taxonomy' => 'product_cat',
																												'hide_empty' => false,
																												'parent'   => $child_product_cat3->term_id
																											);

																											$child_product_cats4 = get_terms( $child_args4 );
																											if (!empty($child_product_cats4)) {
																												?>

																												<ul>
																													<?php
																													foreach ($child_product_cats4 as $child_product_cat4) {
																														?>

																														<li class="child_cat">
																															<input type="checkbox" class="child parent" name="afwssv_applied_on_categories[]" id="afwssv_applied_on_categories" value="<?php echo intval($child_product_cat4->term_id); ?>"
																															<?php
																															if (!empty($pre_vals) &&in_array($child_product_cat4->term_id, $pre_vals)) {
																																echo 'checked';
																															}
																															?>
																															/>
																															<?php echo esc_attr($child_product_cat4->name); ?>


																															<?php
																															//5th level
																															$child_args5= array(
																																'taxonomy' => 'product_cat',
																																'hide_empty' => false,
																																'parent'   => $child_product_cat4->term_id
																															);

																															$child_product_cats5 = get_terms( $child_args5 );
																															if (!empty($child_product_cats5)) {
																																?>

																																<ul>
																																	<?php
																																	foreach ($child_product_cats5 as $child_product_cat5) {
																																		?>

																																		<li class="child_cat">
																																			<input type="checkbox" class="child parent" name="afwssv_applied_on_categories[]" id="afwssv_applied_on_categories" value="<?php echo intval($child_product_cat5->term_id); ?>"
																																			<?php
																																			if (!empty($pre_vals) &&in_array($child_product_cat5->term_id, $pre_vals)) {
																																				echo 'checked';
																																			}
																																			?>
																																			/>
																																			<?php echo esc_attr($child_product_cat5->name); ?>


																																			<?php
																																			//6th level
																																			$child_args6= array(
																																				'taxonomy' => 'product_cat',
																																				'hide_empty' => false,
																																				'parent'   => $child_product_cat5->term_id
																																			);

																																			$child_product_cats6 = get_terms( $child_args6 );
																																			if (!empty($child_product_cats6)) {
																																				?>

																																				<ul>
																																					<?php
																																					foreach ($child_product_cats6 as $child_product_cat6) {
																																						?>

																																						<li class="child_cat">
																																							<input type="checkbox" class="child" name="afwssv_applied_on_categories[]" id="afwssv_applied_on_categories" value="<?php echo intval($child_product_cat6->term_id); ?>"
																																							<?php
																																							if (!empty($pre_vals) &&in_array($child_product_cat6->term_id, $pre_vals)) {
																																								echo 'checked';
																																							}
																																							?>
																																							/>
																																							<?php echo esc_attr($child_product_cat6->name); ?>
																																						</li>

																																					<?php } ?>
																																				</ul>

																																			<?php } ?>

																																		</li>

																																	<?php } ?>
																																</ul>

																															<?php } ?>


																														</li>

																													<?php } ?>
																												</ul>

																											<?php } ?>


																										</li>

																									<?php } ?>
																								</ul>

																							<?php } ?>

																						</li>

																					<?php } ?>
																				</ul>

																			<?php } ?>

																		</li>
																	<?php } ?>
																</ul>
															<?php } ?>

														</li>
														<?php
													}
													?>
												</ul>
											</div>
											<p><?php echo esc_html__('Specify categories on which you want to apply. This will be applied to all variable products of the specified categories.', 'addify_wssvp'); ?></p>
										</td>
									</tr>

								</tbody>
							</table>
						</div>

						<!-- Display Variations Settings -->
						<div class="addify-singletab" id="tabs-2">
							<h2><?php echo esc_html__('Display Variations Dropdown Settings', 'addify_wssvp'); ?></h2>
							<table class="addify-table-optoin">
								<tbody>

								<tr class="addify-option-field">
									<th>
										<div class="option-head">
											<h3><?php echo esc_html__('Enable Variations Dropdown', 'addify_wssvp'); ?></h3>
										</div>
									</th>
									<td>
										<div id="like_dislike_drop">
											<input <?php echo checked('no', esc_attr(get_option('afwssv_enable_dropdown'))); ?> value="no" class="allow_guest likespermission" id="extld2222" type="radio" name="afwssv_enable_dropdown">
											<label class="extndc" for="extld2222"><?php echo esc_html__('No', 'addify-sitemap'); ?></label>
											<input <?php echo checked('yes', esc_attr(get_option('afwssv_enable_dropdown'))); ?> value="yes" class="allow_guest likespermission" id="extld3333" type="radio" name="afwssv_enable_dropdown">
											<label class="extndc" for="extld3333"><?php echo esc_html__('Yes', 'addify-sitemap'); ?></label>
											<div id="like_dislikeb_drop"></div>
										</div>
										<p><?php echo esc_html__('Enable/Disable variations dropdown. When this option is enabled then variations will be displayed as dropdown on shop/categories pages with the variable products.', 'addify_wssvp'); ?></p>
									</td>
								</tr>

									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Select Products', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<select class="afwssv_applied_on_products" name="afwssv_choose_display_products[]" id="afwssv_choose_display_products"  multiple='multiple'>

												<?php

												$afwssv_choose_display_products = unserialize(get_option('afwssv_choose_display_products'));


												if (!empty($afwssv_choose_display_products)) {

													foreach ( $afwssv_choose_display_products as $pro) {

														$prod_post = get_post($pro);

														?>

															<option value="<?php echo intval($pro); ?>" selected="selected"><?php echo esc_attr($prod_post->post_title); ?></option>

														<?php 
													}
												}
												?>

											</select>
											<p><?php echo esc_html__('Select variable products on which you want to show variations dropdown on shop/category pages.', 'addify_wssvp'); ?></p>
										</td>
									</tr>


									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Select Categories', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<div class="all_cats">
												<ul>
													<?php

													$afwssv_choose_display_categories = unserialize(get_option('afwssv_choose_display_categories'));

													if (!empty($afwssv_choose_display_categories)) {
														$pre_vals = $afwssv_choose_display_categories;
													} else {
														$pre_vals = array();
													}

													$args = array(
														'taxonomy' => 'product_cat',
														'hide_empty' => false,
														'parent'   => 0
													);

													$product_cat = get_terms( $args );
													foreach ($product_cat as $parent_product_cat) {
														?>
														<li class="par_cat">
															<input type="checkbox" class="parent" name="afwssv_choose_display_categories[]" id="afwssv_choose_display_categories" value="<?php echo intval($parent_product_cat->term_id); ?>"
															<?php
															if (!empty($pre_vals) && in_array($parent_product_cat->term_id, $pre_vals)) {
																echo 'checked';
															}
															?>
															/>
															<?php echo esc_attr($parent_product_cat->name); ?>

															<?php
															$child_args = array(
																'taxonomy' => 'product_cat',
																'hide_empty' => false,
																'parent'   => $parent_product_cat->term_id
															);
															$child_product_cats = get_terms( $child_args );
															if (!empty($child_product_cats)) {
																?>
																<ul>
																	<?php
																	foreach ($child_product_cats as $child_product_cat) {
																		?>
																		<li class="child_cat">
																			<input type="checkbox" class="child parent" name="afwssv_choose_display_categories[]" id="afwssv_choose_display_categories" value="<?php echo intval($child_product_cat->term_id); ?>"
																			<?php
																			if (!empty($pre_vals) &&in_array($child_product_cat->term_id, $pre_vals)) {
																				echo 'checked';
																			}
																			?>
																			/>
																			<?php echo esc_attr($child_product_cat->name); ?>

																			<?php
																			//2nd level
																			$child_args2= array(
																				'taxonomy' => 'product_cat',
																				'hide_empty' => false,
																				'parent'   => $child_product_cat->term_id
																			);

																			$child_product_cats2 = get_terms( $child_args2 );
																			if (!empty($child_product_cats2)) {
																				?>

																				<ul>
																					<?php
																					foreach ($child_product_cats2 as $child_product_cat2) {
																						?>

																						<li class="child_cat">
																							<input type="checkbox" class="child parent" name="afwssv_choose_display_categories[]" id="afwssv_choose_display_categories" value="<?php echo intval($child_product_cat2->term_id); ?>"
																							<?php
																							if (!empty($pre_vals) &&in_array($child_product_cat2->term_id, $pre_vals)) {
																								echo 'checked';
																							}
																							?>
																							/>
																							<?php echo esc_attr($child_product_cat2->name); ?>


																							<?php
																							//3rd level
																							$child_args3= array(
																								'taxonomy' => 'product_cat',
																								'hide_empty' => false,
																								'parent'   => $child_product_cat2->term_id
																							);

																							$child_product_cats3 = get_terms( $child_args3 );
																							if (!empty($child_product_cats3)) {
																								?>

																								<ul>
																									<?php
																									foreach ($child_product_cats3 as $child_product_cat3) {
																										?>

																										<li class="child_cat">
																											<input type="checkbox" class="child parent" name="afwssv_choose_display_categories[]" id="afwssv_choose_display_categories" value="<?php echo intval($child_product_cat3->term_id); ?>"
																											<?php
																											if (!empty($pre_vals) &&in_array($child_product_cat3->term_id, $pre_vals)) {
																												echo 'checked';
																											}
																											?>
																											/>
																											<?php echo esc_attr($child_product_cat3->name); ?>


																											<?php
																											//4th level
																											$child_args4= array(
																												'taxonomy' => 'product_cat',
																												'hide_empty' => false,
																												'parent'   => $child_product_cat3->term_id
																											);

																											$child_product_cats4 = get_terms( $child_args4 );
																											if (!empty($child_product_cats4)) {
																												?>

																												<ul>
																													<?php
																													foreach ($child_product_cats4 as $child_product_cat4) {
																														?>

																														<li class="child_cat">
																															<input type="checkbox" class="child parent" name="afwssv_choose_display_categories[]" id="afwssv_choose_display_categories" value="<?php echo intval($child_product_cat4->term_id); ?>"
																															<?php
																															if (!empty($pre_vals) &&in_array($child_product_cat4->term_id, $pre_vals)) {
																																echo 'checked';
																															}
																															?>
																															/>
																															<?php echo esc_attr($child_product_cat4->name); ?>


																															<?php
																															//5th level
																															$child_args5= array(
																																'taxonomy' => 'product_cat',
																																'hide_empty' => false,
																																'parent'   => $child_product_cat4->term_id
																															);

																															$child_product_cats5 = get_terms( $child_args5 );
																															if (!empty($child_product_cats5)) {
																																?>

																																<ul>
																																	<?php
																																	foreach ($child_product_cats5 as $child_product_cat5) {
																																		?>

																																		<li class="child_cat">
																																			<input type="checkbox" class="child parent" name="afwssv_choose_display_categories[]" id="afwssv_choose_display_categories" value="<?php echo intval($child_product_cat5->term_id); ?>"
																																			<?php
																																			if (!empty($pre_vals) &&in_array($child_product_cat5->term_id, $pre_vals)) {
																																				echo 'checked';
																																			}
																																			?>
																																			/>
																																			<?php echo esc_attr($child_product_cat5->name); ?>


																																			<?php
																																			//6th level
																																			$child_args6= array(
																																				'taxonomy' => 'product_cat',
																																				'hide_empty' => false,
																																				'parent'   => $child_product_cat5->term_id
																																			);

																																			$child_product_cats6 = get_terms( $child_args6 );
																																			if (!empty($child_product_cats6)) {
																																				?>

																																				<ul>
																																					<?php
																																					foreach ($child_product_cats6 as $child_product_cat6) {
																																						?>

																																						<li class="child_cat">
																																							<input type="checkbox" class="child" name="afwssv_choose_display_categories[]" id="afwssv_choose_display_categories" value="<?php echo intval($child_product_cat6->term_id); ?>"
																																							<?php
																																							if (!empty($pre_vals) &&in_array($child_product_cat6->term_id, $pre_vals)) {
																																								echo 'checked';
																																							}
																																							?>
																																							/>
																																							<?php echo esc_attr($child_product_cat6->name); ?>
																																						</li>

																																					<?php } ?>
																																				</ul>

																																			<?php } ?>

																																		</li>

																																	<?php } ?>
																																</ul>

																															<?php } ?>


																														</li>

																													<?php } ?>
																												</ul>

																											<?php } ?>


																										</li>

																									<?php } ?>
																								</ul>

																							<?php } ?>

																						</li>

																					<?php } ?>
																				</ul>

																			<?php } ?>

																		</li>
																	<?php } ?>
																</ul>
															<?php } ?>

														</li>
														<?php
													}
													?>
												</ul>
											</div>
											<p><?php echo esc_html__('Specify categories on which you want to apply. This will be applied to all variable products of the specified categories.', 'addify_wssvp'); ?></p>
										</td>
									</tr>

									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Enable Quantity Box', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<div id="like_dislike_qty">
												<input <?php echo checked('no', esc_attr(get_option('afwssv_enable_qty_box'))); ?> value="no" class="allow_guest likespermission" id="extld22" type="radio" name="afwssv_enable_qty_box">
												<label class="extndc" for="extld22"><?php echo esc_html__('No', 'addify-sitemap'); ?></label>
												<input <?php echo checked('yes', esc_attr(get_option('afwssv_enable_qty_box'))); ?> value="yes" class="allow_guest likespermission" id="extld33" type="radio" name="afwssv_enable_qty_box">
												<label class="extndc" for="extld33"><?php echo esc_html__('Yes', 'addify-sitemap'); ?></label>
												<div id="like_dislikeb_qty"></div>
											</div>
											<p><?php echo esc_html__('Enable/Disable quantity box with variations on shop/category pages.', 'addify_wssvp'); ?></p>
										</td>
									</tr>

									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Enable Show/Hide Variations Toggle', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<div id="like_dislike_tog">
												<input <?php echo checked('no', esc_attr(get_option('afwssv_enable_toggle'))); ?> value="no" class="allow_guest likespermission" id="extld222" type="radio" name="afwssv_enable_toggle">
												<label class="extndc" for="extld222"><?php echo esc_html__('No', 'addify-sitemap'); ?></label>
												<input <?php echo checked('yes', esc_attr(get_option('afwssv_enable_toggle'))); ?> value="yes" class="allow_guest likespermission" id="extld333" type="radio" name="afwssv_enable_toggle">
												<label class="extndc" for="extld333"><?php echo esc_html__('Yes', 'addify-sitemap'); ?></label>
												<div id="like_dislikeb_tog"></div>
											</div>
											<p><?php echo esc_html__('If this option is enable then show/hide variations toggle button will be displayed and if this option is disabled than variations will be always displayed.', 'addify_wssvp'); ?></p>
										</td>
									</tr>

									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h3><?php echo esc_html__('Show/Hide Variations Dropdown Toggle Text', 'addify_wssvp'); ?></h3>
											</div>
										</th>
										<td>
											<input type="text" class="afrfq_input_class" name="afwssv_toggle_text" id="afwssv_toggle_text" value="<?php echo esc_attr(get_option('afwssv_toggle_text')); ?>" />
											<p><?php echo esc_html__('Customized toggle text for Show/Hide variations dropdown.', 'addify_wssvp'); ?></p>
										</td>
									</tr>

								</tbody>
							</table>
						</div>

						<?php submit_button(esc_html__('Save Settings', 'addify_wssvp' ), 'primary', 'afwssv_save_settings'); ?>

					</form>
				</div>

			</div>
			<?php


		}

		public function afwssv_save_data() {

			global $wp;

			if (!empty($_REQUEST['afwssv_nonce_field'])) {

					$retrieved_nonce = sanitize_text_field($_REQUEST['afwssv_nonce_field']);
			} else {
					$retrieved_nonce = 0;
			}

			if (!wp_verify_nonce($retrieved_nonce, 'afwssv_nonce_action')) {

				die('Failed security check');
			}

			if ( isset( $_POST['afwssv_enable_single_variation'] ) ) {
				update_option('afwssv_enable_single_variation', sanitize_text_field( $_POST['afwssv_enable_single_variation'] ) );
			}

			if ( isset( $_POST['afwssv_hide_main_product'] ) ) {
				update_option('afwssv_hide_main_product', sanitize_text_field( $_POST['afwssv_hide_main_product'] ) );
			}

			if ( isset( $_POST['afwssv_applied_on_products'])) {
				update_option('afwssv_applied_on_products', serialize(sanitize_meta('afwssv_applied_on_products', $_POST['afwssv_applied_on_products'], '') ));
			} else {
				update_option('afwssv_applied_on_products', '');
			}

			if ( isset( $_POST['afwssv_applied_on_categories']) ) {
				update_option('afwssv_applied_on_categories', serialize( sanitize_meta('afwssv_applied_on_categories', $_POST['afwssv_applied_on_categories'], '') ));
			} else {
				update_option('afwssv_applied_on_categories', '');
			}

			if ( isset( $_POST['afwssv_enable_dropdown'] ) ) {
				update_option('afwssv_enable_dropdown', sanitize_text_field( $_POST['afwssv_enable_dropdown'] ) );
			}

			if ( isset( $_POST['afwssv_choose_display_products']) ) {
				update_option('afwssv_choose_display_products', serialize( sanitize_meta('afwssv_choose_display_products', $_POST['afwssv_choose_display_products'], '') ));
			} else {
				update_option('afwssv_choose_display_products', '');
			}

			if ( isset( $_POST['afwssv_choose_display_categories']) ) {
				update_option('afwssv_choose_display_categories', serialize( sanitize_meta('afwssv_choose_display_categories', $_POST['afwssv_choose_display_categories'], '') ));
			} else {
				update_option('afwssv_choose_display_categories', '');
			}

			if ( isset( $_POST['afwssv_enable_qty_box'] ) ) {
				update_option('afwssv_enable_qty_box', sanitize_text_field( $_POST['afwssv_enable_qty_box'] ) );
			}

			if ( isset( $_POST['afwssv_enable_toggle'] ) ) {
				update_option('afwssv_enable_toggle', sanitize_text_field( $_POST['afwssv_enable_toggle'] ) );
			}

			if ( isset( $_POST['afwssv_toggle_text'])) {
				update_option('afwssv_toggle_text', sanitize_text_field( $_POST['afwssv_toggle_text'] ) );
			}





		}

		public function afwssv_author_admin_notice() {
			?>
			<div class="updated notice notice-success is-dismissible">
				<p><?php echo esc_html__('Settings saved successfully.', 'addify_wssvp'); ?></p>
			</div>
			<?php
		}

		public function afwssv_add_variation_data( $loop, $variation_data, $variation) {

			global $post;
			$afwssv_exclude_show_as_single = get_post_meta($variation->ID, '_afwssv_exclude_show_as_single', true);
			$afwssv_variation_title = get_post_meta($variation->ID, '_afwssv_variation_title', true);
			?>
			<?php wp_nonce_field('afwssv_nonce_action', 'afwssv_nonce_field'); ?>
			<div id='addify_wssv_panel' class=''>

				<div class="afwssv_div">

					<h2><?php echo esc_html__('Show as Single Product', 'addify_wssvp'); ?></h2>
					<div class="inner_div">
						<label><?php echo esc_html__('Exclude this Variation?', 'addify_wssvp'); ?></label>
						<input <?php echo checked('yes', esc_attr($afwssv_exclude_show_as_single)); ?> type="checkbox" name="afwssv_exclude_show_as_single[<?php echo esc_attr($loop); ?>]" id="afwssv_show_as_single<?php echo esc_attr($loop); ?>" value="yes">
					</div>

					<div class="inner_div">
						<label><?php echo esc_html__('Variation Title', 'addify_wssvp'); ?></label>
						<input type="text" name="afwssv_variation_title[<?php echo esc_attr($loop); ?>]" id="afwssv_variation_title<?php echo esc_attr($loop); ?>" value="
						<?php
						if (!empty($afwssv_variation_title)) {
							echo esc_attr($afwssv_variation_title);
						}
						?>
						" class="inputf">
						<br />
						<?php echo esc_html__('Enter title if you want to overwrite default variation title.(This is for display purpose, this will only overwrite title on shop and category pages. In Other places variation actual title is used)', 'addify_wssvp'); ?>
					</div>

					<div class="inner_div">
						<label><?php echo esc_html__('Menu Order', 'addify_wssvp'); ?></label>
						<input type="text" name="afwssv_variation_menu_order[<?php echo esc_attr($loop); ?>]" id="afwssv_variation_menu_order<?php echo esc_attr($loop); ?>" value="<?php echo esc_attr($variation->menu_order); ?>" class="inputf">
						<br />
						<?php echo esc_html__('Sort order of the variation on shop page.', 'addify_wssvp'); ?>
					</div>

				</div>

			</div>

			<?php


		}

		public function afwssb_set_variation_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {

			$post_type = get_post_type( $object_id );

			if ( 'product' === $post_type ) {

				if ( 'product_cat' === $taxonomy || 'product_tag' === $taxonomy ) {

					$variations = get_children(array(
						'post_parent' => $object_id,
						'post_type' => 'product_variation'
					), ARRAY_A);

					if ( $variations && !empty( $variations ) ) {

						$variation_ids = array_keys( $variations );

						foreach ( $variation_ids as $variation_id ) {
							wp_set_object_terms( $variation_id, $terms, $taxonomy, $append );
						}

					}

				}

			}

		}

		public function afwssv_product_save( $post_id ) {

			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}

			$post_type = get_post_type( $post_id );

			if ( 'product' != $post_type ) {
				return;
			}

			$this->afwssb_attributes_to_variation( $post_id );

		}

		public function afwssv_variation_save( $variation_id, $i ) {

			$this->afwssvadd_taxonomies_to_variation( $variation_id, $i );
			$this->afwssvadd_attributes_to_variation( $variation_id, $i );

		}

		public function afwssvadd_attributes_to_variation( $variation_id, $i = false, $force = false ) {

			$attributes = wc_get_product_variation_attributes( $variation_id );

			if ( $attributes && !empty( $attributes ) ) {

				foreach ( $attributes as $taxonomy => $value ) {

					$taxonomy = str_replace('attribute_', '', $taxonomy);
					$term = get_term_by('slug', $value, $taxonomy);
					wp_set_object_terms( $variation_id, $value, $taxonomy );

					if ( $term ) {

						$this->delete_count_transient( $taxonomy, $term->term_taxonomy_id );

					}

				}

			}

		}

		public function afwssvadd_taxonomies_to_variation( $variation_id, $i = false ) {

			$parent_product_id = wp_get_post_parent_id( $variation_id );

			if ( $parent_product_id ) {

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

		public function delete_count_transient( $taxonomy, $taxonomy_id ) {

			$transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $taxonomy_id ) );
			delete_transient($transient_name);

		}

		public function afwssb_updated_product_attributes( $meta_id, $object_id, $meta_key, $_meta_value) {

			if ( '_product_attributes' == $meta_key ) {

				$this->afwssb_attributes_to_variation( $object_id );

			}

		}

		public function afwssb_attributes_to_variation( $post_id ) {
			$product = wc_get_product( $post_id );
			if ( !empty($product) ) {

				$variations = $product->get_children();

				$attributes = $product->get_attributes();

				if ( !empty( $attributes) ) {
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

		public function afwssv_save_custom_field_variations( $variation_id, $i) {
			if (!empty($_REQUEST['afwssv_nonce_field'])) {

					$retrieved_nonce = sanitize_text_field($_REQUEST['afwssv_nonce_field']);
			} else {
					$retrieved_nonce = 0;
			}

			if (!wp_verify_nonce($retrieved_nonce, 'afwssv_nonce_action')) {

				die('Failed security check');
			}

			remove_action( 'save_post', array($this, 'afwssv_save_custom_field_variations'));

			
			if ( isset($_POST['afwssv_variation_menu_order'][$i]) ) {
				wp_update_post( array( 'ID' => intval($variation_id), 'menu_order' => sanitize_text_field($_POST['afwssv_variation_menu_order'][$i]) ) );
			}

			if ( isset( $_POST['afwssv_exclude_show_as_single'][$i]) ) {
				update_post_meta( $variation_id, '_afwssv_exclude_show_as_single', sanitize_text_field($_POST['afwssv_exclude_show_as_single'][$i]));
			} else {
				delete_post_meta( $variation_id, '_afwssv_exclude_show_as_single');
			}
			
			if ( isset( $_POST['afwssv_variation_title'][$i])) {
				update_post_meta( $variation_id, '_afwssv_variation_title', sanitize_text_field($_POST['afwssv_variation_title'][$i]));
			}

		}



		public function afwssvsearchProducts() {

			

			if (isset($_POST['nonce']) && '' != $_POST['nonce']) {

				$nonce = sanitize_text_field( $_POST['nonce'] );
			} else {
				$nonce = 0;
			}

			if (isset($_POST['q']) && '' != $_POST['q']) {

				if ( ! wp_verify_nonce( $nonce, 'afwssv-ajax-nonce' ) ) {

					die ( 'Failed ajax security check!');
				}
				

				$pro = sanitize_text_field( $_POST['q'] );

			} else {

				$pro = '';

			}


			$data_array = array();
			$args       = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'numberposts' => -1,
				's'	=>  $pro,
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'variable',
					),
				)
			);
			$pros       = get_posts($args);

			if ( !empty($pros)) {

				foreach ($pros as $proo) {

					$title        = ( mb_strlen( $proo->post_title ) > 50 ) ? mb_substr( $proo->post_title, 0, 49 ) . '...' : $proo->post_title;
					$data_array[] = array( $proo->ID, $title ); // array( Post ID, Post Title )
				}
			}
			
			echo json_encode( $data_array );

			die();
		}

	}

	new Addify_Show_Single_Variations_Admin();

}
