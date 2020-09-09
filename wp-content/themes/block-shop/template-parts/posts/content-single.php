<?php
/**
 * The template for displaying the single type post
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="single-header entry-header">
		<div class="post-info">
			<span class="post-author">
				<?php the_author(); ?> 
			</span>
			<span class="gray-text"><?php esc_html_e( 'in', 'block-shop' ); ?></span>
			<span class="post-category">
				<?php the_category( ' ' ); ?>
			</span>
		</div>	

		<div class="single-title">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</div>

		<?php if ( has_post_thumbnail() && BlockShop_Opt::get_option( 'single_featured_img_size' ) !== 'none' && get_post_meta( $post->ID, 'post_featured_image_meta_box_check', true ) !== 'off' ) : ?>
			<div class="single-feature-img">
				<?php the_post_thumbnail( BlockShop_Opt::get_option( 'single_featured_img_size' ) ); ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="row">
		<div class="<?php blockshop_content_class(); ?>">
			<ul class="entry-post-meta">
				<li class="post-date">
					<?php the_date( 'F j, Y', '<span>' . __( 'on', 'block-shop' ) . '</span> ' ); ?>
				</li>
				<li class="comments-count">
					<span class="comment-sum">
						<i class="icon-comment"></i>
						<?php comments_number( '</span>' . __( 'No Comments', 'block-shop' ), '1 </span>' . __( 'Comment', 'block-shop' ), '% </span>' . __( 'Comments', 'block-shop' ) ); ?> 
				</li>
			</ul>
			<div class="single-content entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	<?php
	wp_link_pages(
		array(
			'before'      => '<div class="page-links"><span class="pages">' . __( 'Pages:', 'block-shop' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		)
	);
	?>
	<?php the_tags( '<div class="meta-tags">', ' ', '</div>' ); ?>
</article><!-- #post-## -->
