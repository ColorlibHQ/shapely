<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Epsilon_Control_Typography extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-typography';

	/**
	 * @since  1.0.0
	 * @access public
	 * @var string
	 */
	public $selectors;

	/**
	 * @since  1.0.3
	 * @access public
	 * @var array
	 */
	public $font_defaults;

	/**
	 * @since  1.2.0
	 * @access public
	 * @var
	 */
	public $default;

	/**
	 * @since  1.2.0
	 * @access public
	 * @var array
	 */
	public $choices = array();

	/**
	 * @since 1.3.4
	 * @var int
	 */
	public $styleHelper = 'full';

	/**
	 * @since  1.2.0
	 * @access public
	 * @var string
	 */
	public $stylesheet = 'epsilon-typography-css';

	/**
	 * Epsilon_Control_Typography constructor.
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Typography' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function json() {
		$json                 = parent::json();
		$json['id']           = $this->id;
		$json['link']         = $this->get_link();
		$json['value']        = $this->value();
		$json['choices']      = $this->choices;
		$json['default']      = $this->default;
		$json['fontDefaults'] = $this->set_font_defaults();
		$json['inputs']       = $this->get_values( $this->id );
		$json['fonts']        = $this->google_fonts();
		$json['selectors']    = $this->set_selectors();
		$json['stylesheet']   = $this->stylesheet;

		$i = 0;

		if ( in_array( 'font-family', $json['choices'] ) ) {
			$i ++;
		}

		if ( in_array( 'font-weight', $json['choices'] ) ) {
			$i ++;
		}

		if ( in_array( 'font-style', $json['choices'] ) ) {
			$i ++;
		}

		if ( in_array( 'letter-spacing', $json['choices'] ) || in_array( 'line-height', $json['choices'] ) || in_array( 'font-size', $json['choices'] ) ) {
			$i ++;
		}

		$arr = array(
			1 => 'one',
			2 => 'two',
			3 => 'three',
			4 => 'full',
		);

		$json['styleHelper'] = $arr[ $i ];

		return $json;
	}

	/**
	 * Sets the typography defaults
	 */
	public function set_font_defaults() {
		$arr = array();
		if ( empty( $this->font_defaults ) ) {
			$arr[ $this->id ] = array();
		}

		if ( ! empty( $this->font_defaults ) ) {
			$arr[ $this->id ] = $this->font_defaults;
		}

		return $arr;
	}

	/**
	 * Enqueues selectize js
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_style( 'selectize', EPSILON_URI . '/assets/vendors/selectize/selectize.css' );
		wp_enqueue_script( 'selectize', EPSILON_URI . '/assets/vendors/selectize/selectize.min.js', array( 'jquery' ), '1.0.0', true );
	}

	/**
	 * Grabs the value from the json and creates a k/v array
	 *
	 * @since 1.0.0
	 *
	 * @param $values
	 *
	 * @return array
	 */
	public function get_values( $values ) {
		$defaults = $this->set_font_defaults();
		$defaults = $defaults[ $this->id ];

		if ( empty( $defaults ) ) {
			$defaults = array(
				'font-family'    => 'Select font',
				'font-weight'    => '',
				'font-style'     => '',
				'letter-spacing' => '0',
				'font-size'      => '16',
				'line-height'    => '18',
			);
		}

		$arr = array();
		foreach ( $this->choices as $choice ) {
			if ( array_key_exists( $choice, $defaults ) ) {
				$arr[ $choice ] = $defaults[ $choice ];
			}
		}

		if ( empty( $values ) ) {
			return $arr;
		}

		$json = get_theme_mod( $values, '' );

		if ( '' === $json ) {
			return $arr;
		}

		$json    = str_replace( '&quot;', '"', $json );
		$json    = (array) json_decode( $json );
		$options = (array) $json['json'];

		/**
		 * Changed these options (font-style and weight) in toggles
		 */
		if ( ! empty( $options['font-style'] ) ) {
			$options['font-style'] = 'on';
		}
		if ( ! empty( $options['font-weight'] ) ) {
			$options['font-weight'] = 'on';
		}

		$return = array_merge( $arr, $options );

		foreach ( $return as $k => $v ) {
			$return[ $k ] = esc_attr( $v );
		}

		return $return;
	}

	/**
	 * Access the GFonts Json and parse its content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array|mixed|object
	 */
	public function google_fonts() {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		$path   = EPSILON_PATH . '/assets/data/gfonts.json';
		$gfonts = $wp_filesystem->get_contents( $path );
		$gfonts = json_decode( $gfonts );

		if ( null === $gfonts ) {
			return new stdClass();
		}

		return $gfonts;
	}

	/**
	 * @return string
	 */
	public function set_selectors() {
		return implode( ',', $this->selectors );
	}

	/**
	 * Display the control's content
	 *
	 * @since  1.2.0
	 * @access public
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
		<div class="customize-control-content">
			<input disabled type="hidden" class="epsilon-typography-input" id="hidden_input_{{{ data.id }}}" <# if ( data.value ) { value="{{{ data.value }}}"  } #> {{{ data.link }}}/>
		</div>

		<div class="epsilon-typography-container group-of-{{ data.styleHelper }}" data-unique-id="{{{ data.id }}}">
			<# if( _.contains( data.choices, 'font-family' ) ) { #>
				<div class="epsilon-typography-font-family">
					<select id="{{{ data.id }}}-font-family" class="epsilon-typography-input">
						<option value="default_font"><?php echo esc_html__( 'Theme default', 'epsilon-framework' ); ?></option>
						<# for ( font in data.fonts ) { #>
							<option value="{{ font }}" <# if( font === data.inputs['font-family'] ) { #> selected="selected" <# } #> > {{ font }} </option>
						<# } #>
					</select>
				</div>
			<# } #>

			<# if( _.contains( data.choices, 'font-weight' ) ) { #>
			<div class="epsilon-typography-font-weight">
				<div class="epsilon-font-weight-switch">
					<input type="checkbox" id="{{{ data.id }}}-font-weight" class="epsilon-typography-input epsilon-font-weight-switch-checkbox" value="on" <# if( 'on' === data.inputs['font-weight'] ) { #> checked="checked" <# } #>>
					<label class="epsilon-font-weight-switch-label" for="{{{ data.id }}}-font-weight"></label>
				</div>
			</div>
			<# } #>

			<# if( _.contains( data.choices, 'font-style' ) ) { #>
			<div class="epsilon-typography-font-style">
				<div class="epsilon-font-style-switch">
					<input type="checkbox" id="{{{ data.id }}}-font-style" class="epsilon-typography-input epsilon-font-style-switch-checkbox" value="on" <# if( 'on' === data.inputs['font-style'] ) { #> checked="checked" <# } #>>
					<label class="epsilon-font-style-switch-label" for="{{{ data.id }}}-font-style"></label>
				</div>
			</div>
			<# } #>

			<# if( _.contains( data.choices, 'font-size' ) || _.contains( data.choices, 'line-height' ) || _.contains( data.choices, 'letter-spacing' ) ) { #>
				<div class="epsilon-typography-advanced">
					<a href="#" data-toggle="{{{ data.id }}}-toggle" class="epsilon-typography-advanced-options-toggler"><span class="dashicons dashicons-admin-generic"></span></a>
				</div>
				<div class="epsilon-typography-advanced-options" id="{{{ data.id }}}-toggle">
					<# if( _.contains( data.choices, 'font-size' ) ) { #>
						<label for="{{{ data.id }}}-font-size">
							<?php echo esc_html__( 'Font Size', 'epsilon-framework' ); ?>
						</label>
						<div class="slider-container" data-slider-type="font-size">
							<input data-default-font-size="{{{ data.fontDefaults[data.id]['font-size'] }}}" type="text" class="epsilon-typography-input rl-slider" id="{{{ data.id }}}-font-size" value="{{{ data.inputs['font-size'] }}}"/>
							<div id="slider_{{{ data.id }}}-font-size" data-attr-min="0" data-attr-max="40" data-attr-step="1" class="ss-slider"></div>
						</div>
					<# } #>
					<# if( _.contains( data.choices, 'line-height' ) ) { #>
						<label for="{{{ data.id }}}-line-height">
							<?php echo esc_html__( 'Line Height', 'epsilon-framework' ); ?>
						</label>
						<div class="slider-container" data-slider-type="line-height">
							<input data-default-line-height="{{{ data.fontDefaults[data.id]['line-height'] }}}" type="text" class="epsilon-typography-input rl-slider" id="{{{ data.id }}}-line-height" value="{{{ data.inputs['line-height'] }}}"/>
							<div id="slider_{{{ data.id }}}-line-height" data-attr-min="0" data-attr-max="40" data-attr-step="1" class="ss-slider"></div>
						</div>
					<# } #>
					<# if( _.contains( data.choices, 'letter-spacing' ) ) { #>
						<label for="{{{ data.id }}}-letter-spacing">
							<?php echo esc_html__( 'Letter Spacing', 'epsilon-framework' ); ?>
						</label>
						<div class="slider-container" data-slider-type="letter-spacing">
							<input data-default-letter-spacing="{{{ data.fontDefaults[data.id]['letter-spacing'] }}}" type="text" class="epsilon-typography-input rl-slider" id="{{{ data.id }}}-letter-spacing" value="{{{ data.inputs['letter-spacing'] }}}"/>
							<div id="slider_{{{ data.id }}}-letter-spacing" data-attr-min="0" data-attr-max="5" data-attr-step="0.1" class="ss-slider"></div>
						</div>
					<# } #>
				</div>
			<# } #>
		</div>
		<?php //@formatter:on
	}

	/**
	 * Displays the control content. ( should be empty )
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render_content() {
	}
}
