<?php
/**
 * The template for displaying the grid type blog layout
 *
 * @package BlockShop
 * @version 1.0
 */

?>
<div class="articles-grid-layout">
	<div class="row">
	<?php
	if ( ! ( 'yes' === BlockShop_Opt::get_option( 'blog_highlights' ) && ( $wp_query->found_posts <= 5 || get_option( 'posts_per_page' ) <= 5 ) )
			|| is_paged()
			|| is_archive()
			|| is_search()
		) :
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/posts/content', 'grid' );
				endwhile;
			else :
				get_template_part( 'template-parts/posts/content', 'none' );
			endif;
		endif;
	?>
	</div>
</div>
