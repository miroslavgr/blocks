<?php
/**
 * This adds the "showfeatured image metabox to posts"
 *
 * @package blockshop
 */

add_action( 'add_meta_boxes', 'blockshop_post_options_meta_box_add' );

/**
 * This adds the metabox
 */
function blockshop_post_options_meta_box_add() {
	add_meta_box( 'post_options_meta_box', 'Post Options', 'blockshop_post_options_meta_box_content', 'post', 'side', 'high' );
}

/**
 * [The content for the metabox]
 */
function blockshop_post_options_meta_box_content() {
	// $post is already set, and contains an object: the WordPress post.
	global $post;
	$values = get_post_custom( $post->ID );
	$check  = isset( $values['post_featured_image_meta_box_check'] ) ? esc_attr( $values['post_featured_image_meta_box_check'][0] ) : 'on';
	?>
	<div class="components-panel__row">
        <div class="components-base-control">
            <div class="components-base-control__field">
                <span class="components-checkbox-control__input-container">
					<input type="checkbox" id="post_featured_image_meta_box_check" class="components-checkbox-control__input" name="post_featured_image_meta_box_check" <?php checked( $check, 'on' ); ?> />
                </span>
				<label for="post_featured_image_meta_box_check"><?php esc_html_e( 'Show Featured Image', 'block-shop' ); ?></label>
            </div>
        </div>
    </div>
	<?php

	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'post_options_meta_box', 'post_options_meta_box_nonce' );
}

add_action( 'save_post', 'blockshop_post_options_meta_box_save' );

/**
 * This saves the metabox content
 *
 * @param  [int] $post_id [ID of the post].
 */
function blockshop_post_options_meta_box_save( $post_id ) {
	// Bail if we're doing an auto save.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$post = '';
	if ( isset( $_POST['post_options_meta_box_nonce'] ) ) {
		$post = sanitize_text_field( wp_unslash( $_POST['post_options_meta_box_nonce'] ) );
	}
	// if our nonce isn't there, or we can't verify it, bail.
	if ( ! wp_verify_nonce( $post, 'post_options_meta_box' ) ) {
		return;
	}

	// if our current user can't edit this post, bail.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$chk = isset( $_POST['post_featured_image_meta_box_check'] ) ? 'on' : 'off';
	update_post_meta( $post_id, 'post_featured_image_meta_box_check', $chk );
}
