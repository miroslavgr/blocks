<?php
/**
 * The comments page template
 *
 * @package blockshop
 */

if ( post_password_required() ) {
	return;
}
?>

<div class="comments">
	<div class="heading">
		<span class="heading-2">
			<?php comments_number( __( 'No Replies on', 'block-shop' ), '1 ' . __( 'Reply on', 'block-shop' ), '% ' . __( 'Replies on', 'block-shop' ) ); ?> 
		</span>
		<?php the_title( '<h3 class="post-title">', '</h3>' ); ?>
	</div>

	<?php

	if ( have_comments() ) :
		?>

		<ul class="comments-list">
			<?php
				wp_list_comments(
					array(
						'avatar_size' => 100,
						'style'       => 'ol',
						'short_ping'  => true,
						'max_depth'   => 3,
					)
				);
			?>
		</ul>

		<script type="text/javascript">
			if (jQuery("li.pingback").length > 0){
				jQuery("li.pingback > .comment-body").each(function(){
					if ( this.childNodes[0].nodeType == 3 ) {
						var temp_pingback = this.childNodes[0].nodeValue;
						this.childNodes[0].nodeValue = '';
						var new_pingback = document.createElement("span");
						new_pingback.innerHTML = temp_pingback;
						new_pingback.className = 'new-pingback';
						this.before(new_pingback);
					}
				})
			}
		</script>

		<?php
		the_comments_pagination(
			array(
				'type'      => 'list',
				'prev_text' => '<span class="screen-reader-text">' . __( 'Previous', 'block-shop' ) . '</span>',
				'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'block-shop' ) . '</span>',
			)
		);

	endif; // Check for have_comments().

	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>

		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'block-shop' ); ?></p>

		<?php
	endif;
	?>
	<div class="comments-form">
		<?php comment_form(); ?>
	</div>
</div>
