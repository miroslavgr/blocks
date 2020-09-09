<?php
/**
 * The main blog page tmeplate
 *
 * @package blockshop
 */

get_header(); ?>

	<?php
	/* Category List */
	if ( 'yes' === BlockShop_Opt::get_option( 'blog_categories' ) ) :
		?>
	<div class="archive-header">
		<ul class="archive-list">
			<?php
				$categories  = get_terms(
					'category',
					array(
						'hide_empty' => 0,
						'parent'     => 0,
					)
				);
				$current_cat = get_category( get_query_var( 'cat' ) );
				$current_cat = is_wp_error( $current_cat ) ? 0 : $current_cat->cat_ID;
			if ( is_tag() ) {
				$current_cat = -1;
			}
			?>
			<li class="cat-item <?php echo 0 === $current_cat ? 'current-cat' : ''; ?>">
				<?php echo 0 === $current_cat ? '<h1>' : ''; ?>
				<?php
				if ( 'page' === get_option( 'show_on_front' ) ) {
					$c_link = get_permalink( get_option( 'page_for_posts' ) );
				} else {
					$c_link = home_url();
				}
				?>
				<a href="<?php echo esc_url( $c_link ); ?>">
					<?php echo empty( get_the_title( get_option( 'page_for_posts' ) ) ) ? esc_html_e( 'All Articles', 'block-shop' ) : esc_html( get_the_title( get_option( 'page_for_posts' ) ) ); ?>
				</a>
				<span class="post-count">
					<?php
					$no_posts = wp_count_posts( 'post' );
					if ( isset( $no_posts->publish ) ) {
						echo esc_attr( $no_posts->publish );
					}
					?>
				</span>
				<?php echo 0 === $current_cat ? '</h1>' : ''; ?>
			</li>
			<?php foreach ( $categories as $c ) : ?>
				<li class="cat-item
				<?php
				if ( $c->term_id === $current_cat ) {
					echo 'current-cat';}
				?>
				">
					<?php
					if ( $c->term_id === $current_cat ) {
						echo '<h1>';}
					?>
					<a href="<?php echo esc_url( get_term_link( $c->slug, 'category' ) ); ?>"><?php echo esc_html( $c->name ); ?></a>
					<span class="post-count">
						<?php echo esc_html( $c->count ); ?>
					</span>
					<?php
					if ( $c->term_id === $current_cat ) {
						echo '</h1>';}
					?>
				</li>
				<?php
			endforeach;
			?>
		</ul>
	</div>
	<?php else : ?>
		<div class="archive-padding"></div>
	<?php endif; ?>
	<?php /* /Category list */ ?>

	<?php
	/* Highlighted posts */
	if ( 'yes' === BlockShop_Opt::get_option( 'blog_highlights' ) && ! is_paged() && ! is_archive() && ! is_search() ) :
		?>
	<section class="articles-section">
		<?php get_template_part( 'template-parts/posts/loop', 'masonry' ); ?>
	</section>
	<?php endif; ?>
	<?php /* /Highlighted posts */ ?>

	<?php
	/* Widget area*/
	if ( is_active_sidebar( 'blog-loop-widgets' ) && 'yes' === BlockShop_Opt::get_option( 'blog_highlights' ) && $wp_query->found_posts >= 1 && 'yes' === BlockShop_Opt::get_option( 'blog_widget_area' ) && ! ( is_paged() || is_archive() || is_search() ) ) :
		?>
	<section class="widgets-section">
		<?php get_template_part( 'template-parts/posts/widgets' ); ?>
	</section>
	<?php endif; ?>
	<?php /* /Widget area */ ?>

	<?php /* Normal posts */ ?>
	<section class="articles-section">
		<?php get_template_part( 'template-parts/posts/loop', 'grid' ); ?>
	</section>
	<?php /* /Normal Posts */ ?>

	<?php
	the_posts_navigation(
		array(
			'prev_text' => __( 'Older posts', 'block-shop' ),
			'next_text' => __( 'Newer posts', 'block-shop' ),
		)
	);
	?>
	<?php
	/* Widget area */
	if ( is_active_sidebar( 'blog-loop-widgets' ) && ( 'yes' !== BlockShop_Opt::get_option( 'blog_highlights' ) || $wp_query->found_posts < 1 || is_paged() || is_archive() || is_search() ) && 'yes' === BlockShop_Opt::get_option( 'blog_widget_area' ) ) :
		?>
	<section class="widgets-section">
		<?php get_template_part( 'template-parts/posts/widgets' ); ?>
	</section>
	<?php endif; ?>
	<?php /* /Widget area */ ?>
<?php
get_footer();
