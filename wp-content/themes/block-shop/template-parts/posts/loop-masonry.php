<?php
/**
 * The template for displaying the masonry type blog layout
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<div class="articles-masonry-layout">
	<div class="row">
	<?php
		$i = 0;
	while ( have_posts() ) :
		the_post();
		$i++;
		get_template_part( 'template-parts/posts/content', 'masonry' );
		if ( 5 === $i ) {
			break;
		}
		endwhile;
	?>
	</div>
</div>
