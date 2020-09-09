<?php
/**
 * This adds metaboxes to the page template: page title, transparent header, transparent footer
 *
 * @package blockshop
 */

add_action( 'add_meta_boxes', 'blockshop_page_options_meta_box_add' );

/**
 * This adds the metaboxes
 */
function blockshop_page_options_meta_box_add() {
	global $post;
	$curr_page    = $post->ID;
	$woo_page_ids = blockshop_woo_page_ids();

	if ( ! empty( $curr_page ) && ! empty( $woo_page_ids ) && in_array( $curr_page, $woo_page_ids, true ) ) {
		return;
	}
	add_meta_box( 'page_options_meta_box', 'Page Options', 'blockshop_page_options_meta_box_content', 'page', 'side', 'high' );
}

/**
 * The metaboxes content
 */
function blockshop_page_options_meta_box_content() {
	// $post is already set, and contains an object: the WordPress post.
	global $post;
	$values           = get_post_custom( $post->ID );
	$page_title_check = isset( $values['page_title_meta_box_check'] ) ? esc_attr( $values['page_title_meta_box_check'][0] ) : 'on';
	$footer_check     = isset( $values['transparent_footer_meta_box_check'] ) ? esc_attr( $values['transparent_footer_meta_box_check'][0] ) : 'off';
	$header_check     = isset( $values['transparent_header_meta_box_check'] ) ? esc_attr( $values['transparent_header_meta_box_check'][0] ) : 'off';
	?>
	<div class="components-panel__row">
        <div class="components-base-control">
            <div class="components-base-control__field">
                <span class="components-checkbox-control__input-container">
					<input type="checkbox" id="transparent_header_meta_box_check" class="components-checkbox-control__input" name="transparent_header_meta_box_check" <?php checked( $header_check, 'on' ); ?> />
                </span>
				<label for="transparent_header_meta_box_check"><?php esc_html_e( 'Transparent Header', 'block-shop' ); ?></label>
            </div>
        </div>
    </div>

	<div class="components-panel__row">
        <div class="components-base-control">
            <div class="components-base-control__field">
                <span class="components-checkbox-control__input-container">
					<input type="checkbox" id="page_title_meta_box_check" class="components-checkbox-control__input" name="page_title_meta_box_check" <?php checked( $page_title_check, 'on' ); ?> />
                </span>
				<label for="page_title_meta_box_check"><?php esc_html_e( 'Show Page Title', 'block-shop' ); ?></label>
            </div>
        </div>
    </div>

	<div class="components-panel__row">
        <div class="components-base-control">
            <div class="components-base-control__field">
                <span class="components-checkbox-control__input-container">
					<input type="checkbox" id="transparent_footer_meta_box_check" class="components-checkbox-control__input" name="transparent_footer_meta_box_check" <?php checked( $footer_check, 'on' ); ?> />
                </span>
				<label for="transparent_footer_meta_box_check"><?php esc_html_e( 'Transparent Footer', 'block-shop' ); ?></label>
            </div>
        </div>
    </div>
	<?php

	// We'll use this nonce field later on when saving.
	wp_nonce_field( 'page_options_meta_box', 'page_options_meta_box_nonce' );
}


add_action( 'save_post', 'blockshop_page_options_meta_box_save' );

/**
 * This saves the metaboxes content
 *
 * @param  [int] $post_id [ID of the post].
 */
function blockshop_page_options_meta_box_save( $post_id ) {
	// Bail if we're doing an auto save.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$post = '';
	if ( isset( $_POST['page_options_meta_box_nonce'] ) ) {
		$post = sanitize_text_field( wp_unslash( $_POST['page_options_meta_box_nonce'] ) );
	}
	// if our nonce isn't there, or we can't verify it, bail.
	if ( ! wp_verify_nonce( $post, 'page_options_meta_box' ) ) {
		return;
	}

	// if our current user can't edit this post, bail.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$page_title_chk = isset( $_POST['page_title_meta_box_check'] ) ? 'on' : 'off';
	update_post_meta( $post_id, 'page_title_meta_box_check', $page_title_chk );

	$footer_chk = isset( $_POST['transparent_footer_meta_box_check'] ) ? 'on' : 'off';
	update_post_meta( $post_id, 'transparent_footer_meta_box_check', $footer_chk );

	$header_chk = isset( $_POST['transparent_header_meta_box_check'] ) ? 'on' : 'off';
	update_post_meta( $post_id, 'transparent_header_meta_box_check', $header_chk );
}
