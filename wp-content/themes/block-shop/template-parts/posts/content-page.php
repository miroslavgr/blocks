<?php
/**
 * The template for displaying the page type
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<header class="single-header entry-header">
	<?php if ( get_post_meta( blockshop_page_id(), 'page_title_meta_box_check', true ) !== 'off' ) : ?>
	<div class="single-title">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</div>
	<?php endif; ?>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="single-feature-img">
			<?php the_post_thumbnail( 'full' ); ?>
		</div>
	<?php endif; ?>
</header><!-- .entry-header -->
<div class="row">
	<div class="<?php blockshop_content_class(); ?>">
		<div class="single-content entry-content">
			<?php the_content(); ?>
		</div>
	</div>
</div>
