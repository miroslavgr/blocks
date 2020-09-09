<?php
/**
 * The blog footer
 *
 * @package blockshop
 */

?>
	</div><!-- .container-fluid -->
		<footer class="page-footer">
			<div class="sub-footer">
				<div class="row">
					<div class="col-xl-6">
							<span class="copyright">
							<?php
							$text = BlockShop_Opt::get_option( 'footer_text' );
							printf(
								wp_kses(
									$text,
									array(
										'a'      => array(
											'href'  => array(),
											'title' => array(),
										),
										'br'     => array(),
										'em'     => array(),
										'strong' => array(),
									)
								)
							);
							?>
							</span>
					</div>
					<div class="col-xl-6">
						<div class="footer-links">
							<?php
								wp_nav_menu(
									array(
										'theme_location' => 'footer',
										'container'      => false,
										'menu_class'     => 'footer-menu',
										'depth'          => 1,
										'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
										'fallback_cb'    => false,
									)
								);
								?>
						</div>
					</div>
				</div>
			</div>
		</footer>
	<?php wp_footer(); ?>
	<div class="getbowtied_qv_content"></div>
</body>
</html>

