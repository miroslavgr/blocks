<?php
/**
 * The Social Media section options
 *
 * @package blockshop
 */

global $social_media_profiles;

$social_media_profiles = array(
	array(
		'slug'        => 'facebook',
		'name'        => 'Facebook',
		'icon'        => 'facebook-circled',
	),
	array(
		'slug'        => 'facebook_messenger',
		'name'        => 'Facebook Messenger',
		'icon'        => 'facebook-messenger',
	),
	array(
		'slug'        => 'instagram',
		'name'        => 'Instagram',
		'icon'        => 'instagram',
	),
	array(
		'slug'        => 'twitter',
		'name'        => 'Twitter',
		'icon'        => 'twitter',
	),
	array(
		'slug'        => 'pinterest',
		'name'        => 'Pinterest',
		'icon'        => 'pinterest',
	),
	array(
		'slug'        => 'linkedin',
		'name'        => 'LinkedIn',
		'icon'        => 'linkedin',
	),
	array(
		'slug'        => 'youtube',
		'name'        => 'YouTube Play',
		'icon'        => 'youtube-play-button-logo',
	),
	array(
		'slug'        => 'whatsapp',
		'name'        => 'WhatsApp',
		'icon'        => 'whatsapp',
	),
	array(
		'slug'        => 'google_maps',
		'name'        => 'Google Maps',
		'icon'        => 'google-maps',
	),
	array(
		'slug'        => 'apple_app_store',
		'name'        => 'Apple App Store',
		'icon'        => 'apple-app-store',
	),
	array(
		'slug'        => 'google_play',
		'name'        => 'Google Play',
		'icon'        => 'google-play',
	),
	array(
		'slug'        => 'behance',
		'name'        => 'Behance',
		'icon'        => 'behance',
	),
	array(
		'slug'        => 'foursquare',
		'name'        => 'Foursquare',
		'icon'        => 'foursquare',
	),
	array(
		'slug'        => 'github',
		'name'        => 'Github',
		'icon'        => 'github',
	),
	array(
		'slug'        => 'help_center',
		'name'        => 'Help Center',
		'icon'        => 'help-center',
	),
	array(
		'slug'        => 'line',
		'name'        => 'Line',
		'icon'        => 'line',
	),
	array(
		'slug'        => 'reddit',
		'name'        => 'Reddit',
		'icon'        => 'reddit',
	),
	array(
		'slug'        => 'tumblr',
		'name'        => 'Tumblr',
		'icon'        => 'tumblr',
	),
	array(
		'slug'        => 'viber',
		'name'        => 'Viber',
		'icon'        => 'viber',
	),
	array(
		'slug'        => 'vkcom',
		'name'        => 'Vkcom',
		'icon'        => 'vkcom',
	),
	array(
		'slug'        => 'weibo',
		'name'        => 'Weibo',
		'icon'        => 'weibo',
	),
	array(
		'slug'        => 'wordpress',
		'name'        => 'WordPress',
		'icon'        => 'wordpress',
	),
	array(
		'slug'        => 'yelp',
		'name'        => 'Yelp',
		'icon'        => 'yelp',
	),
	array(
		'slug'        => 'medium',
		'name'        => 'Medium',
		'icon'        => 'medium',
	),
);

/*
 * Import old options
 */
if( !get_theme_mod( 'blockshop_social_media_options_import', false ) ) {
	$repeater_default = array();
	foreach( $social_media_profiles as $social) {
		if( !empty( get_theme_mod( 'social_media_' . $social['slug'] ) ) ) {
			$repeater_default[] = array(
				'icon_slug' => $social['icon'],
				'link' => get_theme_mod( 'social_media_' . $social['slug'] ),
				'title' => $social['name'],
			);
		}
	}
	set_theme_mod( 'social_media_repeater', json_encode( $repeater_default ) );
	set_theme_mod( 'blockshop_social_media_options_import', true );
}

add_action( 'customize_register', 'blockshop_customizer_social_media_controls' );

/**
 * Adds controls for social media section
 *
 * @param  [object] $wp_customize [customizer object].
 */
function blockshop_customizer_social_media_controls( $wp_customize ) {

	global $social_media_profiles;

	// Social Media Icons Position.
	$wp_customize->add_setting(
		'social_media_position',
		array(
			'type'              => 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'blockshop_sanitize_select',
			'default'           => 'bottom',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'social_media_position',
			array(
				'type'     => 'radio',
				'label'    => esc_attr__( 'Social Media Icons Position', 'block-shop' ),
				'section'  => 'social_media',
				'description' => __( 'On screens smaller than 1024px, the icons will show up as part of the Menu.', 'block-shop' ),
				'priority' => 10,
				'choices'  => array(
					'top'    => esc_attr__( 'Top Right Corner', 'block-shop' ),
					'bottom' => esc_attr__( 'Bottom Right Corner', 'block-shop' ),
				),
			)
		)
	);

	// Fields
	$wp_customize->add_setting( 'social_media_repeater', array(
		'type'		 		=> 'theme_mod',
		'sanitize_callback' => 'blockshop_sanitize_repeater',
		'capability' 		=> 'edit_theme_options',
		'default' 			=> json_encode( array() ),
	) );

	$wp_customize->add_control(
		new BlockShop_Customizer_Repeater_Control(
			$wp_customize,
			'social_media_repeater',
			array(
				'section' => 'social_media',
				'profiles' => $social_media_profiles,
				'priority' => 10,
			)
		)
	);
}
