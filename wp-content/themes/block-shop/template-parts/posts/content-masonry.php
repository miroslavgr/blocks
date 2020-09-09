<?php
/**
 * The template for displaying the masonry type post layout
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<div class="article-wrapper col-xl-3 col-sm-6 ms-item">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail feature-img">
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<?php the_post_thumbnail( 'large' ); ?>
			</a>
		</div>
		<?php endif; ?>
		<div class="entry-content-wrap post-details">
			<div class="entry-meta post-meta">        
				<?php the_category( ' ' ); ?>
			</div>
			<?php the_title( '<h2 class="entry-title post-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
			<div class="entry-content post-content">
				<div><?php the_excerpt(); ?></div>
			</div>
			<a class="read-more" href="<?php echo( esc_url( get_permalink() ) ); ?>"><?php echo esc_html_e( 'Read More', 'block-shop' ); ?></a>
		</div>
	</article>
</div>
