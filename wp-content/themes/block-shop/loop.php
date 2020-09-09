<?php
/**
 * The template for displaying posts loop
 *
 * @package blockshop
 */

?>
<div class="articles-masonry-layout">
	<div class="row">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/posts/content', get_post_format() );
		endwhile;
	?>
	</div>
</div>
