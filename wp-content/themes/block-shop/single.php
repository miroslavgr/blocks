<?php
/**
 * The template for displaying the single post
 *
 * @package blockshop
 */

get_header(); ?>
	<div class="single-wrapper">
		<?php
		/* Start the Loop */
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/posts/content', 'single' );
			?>
				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					?>
					<section class="comments-section">
					<?php
						comments_template();
					?>
					</section>
					<?php
				endif;
				?>
			<?php
		endwhile; // End of the loop.
		?>
	</div>
<?php
get_footer();
