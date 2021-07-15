<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Control_Repeater
 *
 * @since 1.0.0
 */
class Epsilon_Control_Repeater extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.2.0
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-repeater';
	/**
	 * @since 1.0.0
	 * @var array
	 */
	public $choices = array();
	/**
	 * @since 1.0.0
	 * @var array|mixed
	 */
	public $fields = array();
	/**
	 * @since 1.0.0
	 * @var array
	 */
	public $row_label = array();
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $button_label = null;
	/**
	 * Will store a filtered version of value for advanced fields.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var array
	 */
	protected $filtered_value = array();
	/**
	 * Save as meta
	 *
	 * @var string
	 */
	public $save_as_meta = '';
	/**
	 * Icons array
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $icons = array();

	/**
	 * Epsilon_Control_Repeater constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Repeater' );
	}

	/**
	 * Load the necessary styles and scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function json() {
		$json = parent::json();

		$json['id']           = $this->id;
		$json['link']         = $this->get_link();
		$json['value']        = $this->value();
		$json['choices']      = $this->choices;
		$json['fields']       = $this->get_fields();
		$json['rowLabel']     = $this->get_row_label();
		$json['save_as_meta'] = $this->save_as_meta;
		$json['buttonLabel']  = ( isset( $this->button_label ) ) ? $this->button_label : __( 'Add', 'epsilon-framework' );
		$json['default']      = ( isset( $this->default ) ) ? $this->default : $this->setting->default;

		return $json;
	}

	/**
	 * Get custom repeater icons
	 */
	public function get_icons() {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$path = $this->icons;
		/**
		 * In case we don`t have path to icons, we load our own library
		 */
		if ( empty( $this->icons ) || ! file_exists( $path ) ) {
			$path = EPSILON_PATH . '/assets/data/icons.json';
		}

		$icons = $wp_filesystem->get_contents( $path );
		$icons = json_decode( $icons );

		/**
		 * In case the json could not be decoded, we return a new stdClass
		 */
		if ( null === $icons ) {
			return new stdClass();
		}

		return $icons;
	}

	/**
	 * Set defaults, label and add an ID for the fields
	 *
	 * @since 1.0.0
	 * @return array|mixed
	 */
	public function get_fields() {
		if ( empty( $this->fields ) || ! is_array( $this->fields ) ) {
			$this->fields = array();
		}
		$sizes = Epsilon_Helper::get_image_sizes();
		foreach ( $this->fields as $key => $value ) {
			$this->fields[ $key ]['metaId'] = ! empty( $this->save_as_meta ) ? $this->save_as_meta : '';

			if ( ! isset( $value['default'] ) ) {
				$this->fields[ $key ]['default'] = '';
			}

			if ( ! isset( $value['label'] ) ) {
				$this->fields[ $key ]['label'] = '';
			}

			if ( 'epsilon-icon-picker' === $value['type'] ) {
				$this->fields[ $key ]['icons'] = $this->get_icons();
			}

			/**
			 * Range Slider defaults
			 */
			if ( 'epsilon-slider' === $value['type'] ) {
				if ( ! isset( $this->fields[ $key ]['choices'] ) ) {
					$this->fields[ $key ]['choices'] = array();
				}

				if ( '' == $this->fields[ $key ]['default'] ) {
					$this->fields[ $key ]['default'] = 0;
				}

				$default = array(
					'min'  => 0,
					'max'  => 10,
					'step' => 1,
				);

				$this->fields[ $key ]['choices'] = wp_parse_args( $this->fields[ $key ]['choices'], $default );
			}

			if ( 'epsilon-button-group' === $value['type'] ) {
				if ( ! isset( $this->fields[ $key ]['choices'] ) ) {
					$this->fields[ $key ]['choices'] = array();
				}

				$this->fields[ $key ]['groupType'] = $this->set_group_type( $this->fields[ $key ]['choices'] );
			}

			/**
			 * Epsilon Image
			 */
			if ( 'epsilon-image' === $value['type'] ) {
				if ( ! isset( $this->fields[ $key ]['default'] ) ) {
					$this->fields[ $key ]['default'] = array();
				}
				$this->fields[ $key ]['sizeArray'] = $sizes;
				$this->fields[ $key ]['size']      = ! empty( $this->fields[ $key ]['size'] ) ? $this->fields[ $key ]['size'] : 'full';
				$this->fields[ $key ]['mode']      = ! empty( $this->fields[ $key ]['mode'] ) ? $this->fields[ $key ]['mode'] : 'url';
			}

			/**
			 * Color picker defaults
			 */
			if ( 'epsilon-color-picker' === $value['type'] ) {
				$this->fields[ $key ]['mode'] = ! empty( $this->fields[ $key ]['mode'] ) ? $this->fields[ $key ]['mode'] : 'hex';
			}

			$this->fields[ $key ]['id'] = $key;
		} // End foreach().

		return $this->fields;
	}

	/**
	 * Set group type
	 */
	public function set_group_type( $choices = array() ) {
		$arr = array(
			0 => 'none',
			1 => 'one',
			2 => 'two',
			3 => 'three',
			4 => 'four',
		);

		return $arr[ count( $choices ) ];
	}

	/**
	 * Setup the row's label
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_row_label() {
		$default = array(
			'type'  => 'text',
			'value' => esc_html__( 'Row', 'epsilon-framework' ),
			'field' => false,
		);

		$label = wp_parse_args( $this->row_label, $default );

		/**
		 * Default to text
		 */
		if ( 'field' === $label['type'] && ( ! $label['field'] || empty( $this->fields[ $label['field'] ] ) ) ) {
			$label['type'] = 'text';
		}

		return $label;
	}

	/**
	 * As it should be
	 *
	 * @since 1.0.0
	 */
	public function render_content() {

	}

	/**
	 * Displays the control content.
	 *
	 * @since 1.0.0
	 */
	public function content_template() {
		//@formatter:off  ?>
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

		<ul class="repeater-fields"></ul>
		<# if(!_.isUndefined(data.choices.limit)){ #>
		<?php /* Translators: Section limit */ ?>
		<p class="limit"><?php echo esc_html__( 'Limit: ','epsilon-framework' ); ?> {{{ data.choices.limit }}} <?php echo esc_html__( 'sections', 'epsilon-framework' ); ?></p>
		<# } #>
		<div class="button-holder">
			<input type="hidden" value="" {{{ data.link }}} />
			<button class="button-primary epsilon-repeater-add">{{ data.buttonLabel }}</button>
		</div>
		<?php //@formatter:on
	}
}
