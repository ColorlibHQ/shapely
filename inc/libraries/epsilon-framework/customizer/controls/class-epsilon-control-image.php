<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Control_Image
 */
class Epsilon_Control_Image extends WP_Customize_Control {
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'epsilon-image';

	/**
	 * @since 1.0.0
	 * @var array
	 */
	public $default;

	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $size = 'full';

	/**
	 * Epsilon_Control_Image constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Image' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function json() {
		$json = parent::json();

		$json['id']             = $this->id;
		$json['link']           = $this->get_link();
		$json['stringifiedVal'] = $this->value();
		$json['value']          = $this->sanitize_value();
		$json['default']        = $this->default;
		$json['size']           = $this->size;
		$json['sizeArray']      = Epsilon_Helper::get_image_sizes();

		return $json;
	}

	/**
	 * Sanitize the result so we can use it
	 *
	 * @since  1.2.0
	 * @access private
	 */
	private function sanitize_value() {
		$val = json_decode( $this->value() );

		return $val;
	}

	/**
	 * Empty, as it should be
	 *
	 * @since 1.0.0
	 */
	public function render_content() {

	}

	/**
	 * Controller template
	 *
	 * @since 1.0.0
	 */
	public function content_template() {
		//@formatter:off ?>
		<label>
			<span class="customize-control-title">
				{{{ data.label }}}
				<# if( data.description ){ #>
					<i class="dashicons dashicons-editor-help" style="vertical-align: text-bottom; position: relative;">
						<span class="mte-tooltip">
							{{{ data.description }}}
						</span>
					</i>
				<# } #>
			</span>
		</label>
		<div class="epsilon-controller-image-container image-upload">
			<input type="hidden" id="{{ data.id }}" {{{ data.link }}}  <# if ( '' !== data.stringifiedVal ) { #> value="{{ data.stringifiedVal }}" <# } #> />
			<# if ( ! _.isEmpty( data.value ) && data.value.url ) { #>
			<div class="epsilon-image">
				<img src="{{{ data.value.url }}}" />
			</div>
			<# } else { #>
			<div class="placeholder">
				<?php echo esc_html__( 'Upload image', 'epsilon-framework' ); ?>
				<# if ( ! _.isUndefined( data.sizeArray[data.size] ) ) { #>
					<span class="recommended-size"><?php echo esc_html__('Recommended resolution:', 'epsilon-framework'); ?> {{{ data.sizeArray[data.size].width }}} x {{{ data.sizeArray[data.size].height }}}</span>
				<# } #>
			</div>
			<# } #>
			<div class="actions">
				<button class="button image-upload-remove-button" <# if( '' === data.stringifiedVal ) { #> style="display:none;" <# } #>>
					<?php esc_attr_e( 'Remove', 'epsilon-framework' ); ?>
				</button>

				<button type="button" class="button-secondary image-default-button" <# if ( _.isEmpty( data.default ) ) { #> style="display:none;" <# } #>>
					<?php echo esc_html__( 'Default', 'epsilon-framework' ); ?>
				</button>

				<button type="button" class="button-primary image-upload-button">
					<?php echo esc_html__( 'Select File', 'epsilon-framework' ); ?>
				</button>
			</div>
		</div>
		<?php
		//@formatter:on
	}
}
