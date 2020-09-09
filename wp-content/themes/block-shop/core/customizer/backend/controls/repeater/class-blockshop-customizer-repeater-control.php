<?php
/**
 * Options Repeater control class
 *
 * @package BlockShop_Customizer_Repeater_Control
 */

if ( ! class_exists( 'WP_Customize_Control' ) ) {
	include ABSPATH . WPINC . '/class-wp-customize-control.php';
}

/**
 * Class BlockShop_Customizer_Repeater_Control
 */
class BlockShop_Customizer_Repeater_Control extends WP_Customize_Control {

	/**
	 * Social Field ID.
	 *
     * @var string
	 */
	public $id;

	/**
	 * Social Media Profiles.
	 *
	 * Used for icons dropdown list.
	 *
     * @var array
	 */
	public $profiles = array();

	/**
	 * Constructor.
	 *
	 * Supplied `$args` override class property defaults.
	 *
	 * If `$args['settings']` is not defined, use the $id as the setting ID.
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      Control ID.
	 * @param array                $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {

		parent::__construct( $manager, $id, $args );

		if ( !empty( $args['profiles'] ) ) {
			$this->profiles = $args['profiles'];
		}

		if ( !empty( $id ) ) {
			$this->id = $id;
		}
	}

	/**
	 * Render the control's content.
	 */
	public function render_content() {

		// Get default options.
		$this_default = json_decode( $this->setting->default );

		// Get values.
		$values = $this->value();
		$json = json_decode( $values );

		if ( ! is_array( $json ) ) {
			$json = array( $values );
		}
		?>

		<div class="customizer-repeater-general-control-repeater customizer-repeater-general-control-droppable">
			<?php
			if ( ( count( $json ) == 1 && '' === $json[0] ) || empty( $json ) ) {
				if ( ! empty( $this_default ) ) {
					$this->iterate_array( $this_default ); ?>
					<input type="hidden"
					id="customizer-repeater-<?php echo esc_attr( $this->id ); ?>-colector" <?php esc_attr( $this->link() ); ?>
					class="customizer-repeater-colector"
					value="<?php echo esc_textarea( json_encode( $this_default ) ); ?>"/>
					<?php
				} else {
					$this->iterate_array(); ?>
					<input type="hidden"
					id="customizer-repeater-<?php echo esc_attr( $this->id ); ?>-colector" <?php esc_attr( $this->link() ); ?>
					class="customizer-repeater-colector"/>
					<?php
				}
			} else {
				$this->iterate_array( $json ); ?>
				<input type="hidden" id="customizer-repeater-<?php echo esc_attr( $this->id ); ?>-colector" <?php esc_attr( $this->link() ); ?>
				class="customizer-repeater-colector" value="<?php echo esc_textarea( $this->value() ); ?>"/>
				<?php
			} ?>
		</div>
		<button type="button" class="button add_field customizer-repeater-new-field">
			<?php esc_html_e( 'Add a Profile', 'block-shop' ); ?>
		</button>

		<?php
	}

	/**
	 * Render the social media's content.
	 *
	 * @param array $array Social media profiles list.
	 */
	private function iterate_array( $array = array() ) {

		if( !empty( $array ) ) {
			foreach( $array as $icon ) {
				?>
				<div class="customizer-repeater-general-control-repeater-container customizer-repeater-draggable">
					<div class="customizer-repeater-customize-control-title">
						<?php echo !empty( $icon->title ) ? esc_html( $icon->title ) : esc_html( 'Social Media Profile', 'block-shop' ); ?>
					</div>
					<div class="customizer-repeater-box-content-hidden">
						<?php

						$id 		= !empty( $icon->id ) ? $icon->id : '';
						$link 		= !empty( $icon->link ) ? $icon->link : '';
						$title 		= !empty( $icon->title ) ? $icon->title : '';
						$icon_slug 	= !empty( $icon->icon_slug ) ? $icon->icon_slug : '';
						$image_url 	= !empty( $icon->image_url ) ? $icon->image_url : '';

						$show_title = ( 'custom' === $icon_slug ) ? true : false;

						$this->icons_control( $icon_slug );
						$this->image_control( $image_url, $icon_slug );

						$this->input_control( array(
							'label' => apply_filters('repeater_input_labels_filter', esc_html__( 'Title:','block-shop' ), $this->id, 'customizer_repeater_title_control' ),
							'class' => 'customizer-repeater-title-control',
							'type'  => apply_filters('customizer_repeater_input_types_filter', '', $this->id, 'customizer_repeater_title_control' ),
							'sanitize_callback' => 'esc_html',
						), $title, $show_title );

						$this->input_control( array(
							'label' => apply_filters('repeater_input_labels_filter', esc_html__( 'Link:','block-shop' ), $this->id, 'customizer_repeater_link_control' ),
							'class' => 'customizer-repeater-link-control',
							'type'  => apply_filters('customizer_repeater_input_types_filter', '', $this->id, 'customizer_repeater_link_control' ),
							'sanitize_callback' => 'esc_url_raw',
						), $link );

						?>

						<input type="hidden" class="social-repeater-box-id" value="<?php echo esc_attr( $id ); ?>">
						<button type="button" class="social-repeater-general-control-remove-field">
							<?php esc_html_e( 'Remove', 'block-shop' ); ?>
						</button>
						<span>|</span>
						<button type="button" class="social-repeater-general-control-done-field">
							<?php esc_html_e( 'Done', 'block-shop' ); ?>
						</button>
					</div>
				</div>
				<?php
			}
		} else { ?>
			<div class="customizer-repeater-general-control-repeater-container">
				<div class="customizer-repeater-customize-control-title">
					<?php esc_html_e( 'Social Media Profile', 'block-shop' ); ?>
				</div>
				<div class="customizer-repeater-box-content-hidden">
					<?php

					$this->icons_control();
					$this->image_control();

					$this->input_control( array(
						'label' => apply_filters('repeater_input_labels_filter', esc_html__( 'Title:','block-shop' ), $this->id, 'customizer_repeater_title_control' ),
						'class' => 'customizer-repeater-title-control',
						'type'  => apply_filters('customizer_repeater_input_types_filter', '', $this->id, 'customizer_repeater_title_control' ),
						'sanitize_callback' => 'esc_html',
					), '', false );

					$this->input_control( array(
						'label' => apply_filters('repeater_input_labels_filter', esc_html__( 'Link:','block-shop' ), $this->id, 'customizer_repeater_link_control' ),
						'class' => 'customizer-repeater-link-control',
						'type'  => apply_filters('customizer_repeater_input_types_filter', '', $this->id, 'customizer_repeater_link_control' ),
						'sanitize_callback' => 'esc_url_raw',
					) );

					?>
					<input type="hidden" class="social-repeater-box-id">
					<button type="button" class="social-repeater-general-control-remove-field">
						<?php esc_html_e( 'Remove', 'block-shop' ); ?>
					</button>
					<span>|</span>
					<button type="button" class="social-repeater-general-control-done-field">
						<?php esc_html_e( 'Done', 'block-shop' ); ?>
					</button>
				</div>
			</div>
			<?php
		}

		return;
	}

	/**
	 * Render the input field.
	 *
	 * @param array  $options Input settings.
	 * @param string $value Input value.
	 */
	 private function input_control( $options, $value = '', $show = true ) {
		?>
		<div class="<?php esc_attr_e( $options['class'] ); ?>-wrapper" <?php echo !$show ? 'style="display:none;"' : ''; ?>>
			<span class="customize-control-title"><?php esc_html_e( $options['label'] ); ?></span>
			<input type="text" value="<?php esc_attr_e( $value ); ?>" class="<?php esc_attr_e( $options['class'] ); ?>"/>
		</div>
		<?php

		return;
	}

	/**
	 * Render the theme default icon dropdown.
	 *
	 * @param string $value Select value.
	 */
	private function icons_control( $value = 'facebook' ) {
		?>
		<div class="customizer-repeater-icon-control">
			<span class="customize-control-title">
				<?php esc_html_e('Icon:','block-shop'); ?>
			</span>

			<form class="customizer-repeater-icons">
				<?php foreach( $this->profiles as $profile ) { ?>
					<div>
						<input type="radio" name="customizer_repeater_icon" class="<?php esc_attr_e( 'icon-social-' . esc_attr( $profile['icon'] ) ); ?> customizer-repeater-icon" value="<?php esc_attr_e( $profile['icon'] ); ?>" <?php checked( $value, $profile['icon'] ); ?> />

						<span class="tooltip"><?php esc_attr_e( $profile['name'] ); ?></span>
					</div>
				<?php } ?>
				<div>
					<input type="radio" name="customizer_repeater_icon" class="icon-plus-icon-dark customizer-repeater-icon" value="custom" <?php checked( $value, 'custom' ); ?> />
					<span class="tooltip"><?php esc_html_e( 'Custom Icon', 'block-shop' ); ?></span>
				</div>
			</form>
		</div>
		<?php

		return;
	}

	/**
	 * Render the image control.
	 *
	 * @param string $value Select value.
	 * @param string $show Determines if the control should be displayed based on icon choice.
	 */
	private function image_control( $value = '', $icon_slug = '' ) {
		?>
		<div class="customizer-repeater-image-control" <?php echo ( $icon_slug !== 'custom' ) ? 'style="display:none;"' : ''; ?>>
			<span class="customize-control-title">
				<?php esc_html_e('Image:','block-shop')?>
			</span>

			<input type="hidden" class="widefat custom-media-url" value="<?php echo esc_url( $value ); ?>">
			<img class="custom-media-url-preview" src="<?php echo esc_url( $value ); ?>" />
			<input type="button" class="button button-secondary customizer-repeater-custom-media-button" value="<?php echo !empty($value) ? esc_attr( 'Change Image','block-shop' ) : esc_attr( 'Add Image','block-shop' ); ?>" />
		</div>
		<?php

		return;
	}

	/**
	 * Enqueue control related scripts/styles.
	 */
	 public function enqueue() {

		 wp_enqueue_style(
 			'blockshop-customizer-font-styles',
 			get_template_directory_uri() . '/src/css/fonts/icomoon/style.css',
 			array(),
 			blockshop_version(),
 			'all'
 		);

		wp_enqueue_style(
			'blockshop-customizer-repeater-styles',
			get_template_directory_uri() . '/core/customizer/backend/controls/repeater/assets/css/customizer_repeater.css',
			array(),
			blockshop_version(),
			'all'
		);

		wp_enqueue_script(
			'blockshop-customizer-repeater-script',
			get_template_directory_uri() . '/core/customizer/backend/controls/repeater/assets/js/customizer_repeater.js',
			array( 'jquery', 'jquery-ui-draggable' ),
			blockshop_version(),
			true
		);

		return;
	}
}
