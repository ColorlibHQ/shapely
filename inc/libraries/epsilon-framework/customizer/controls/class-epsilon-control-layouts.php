<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Control_Layouts
 *
 * @since 1.0.0
 */
class Epsilon_Control_Layouts extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.2.0
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-layouts';

	/**
	 * Layouts array
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $layouts = array();

	/**
	 * Defaults array
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $default = array();

	/**
	 * Fixed layout
	 *
	 * @since 1.3.4
	 * @var bool
	 */
	public $fixed = false;
	/**
	 * Minimum span ( no column will go lower than this )
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $min_span;

	/**
	 * Epsilon_Control_Layout constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Layouts' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.2.0
	 * @access public
	 */
	public function json() {
		$json              = parent::json();
		$json['layouts']   = $this->get_layouts();
		$json['id']        = $this->id;
		$json['link']      = $this->get_link();
		$json['value']     = $this->value();
		$json['default']   = $this->default;
		$json['columns']   = $this->get_columns();
		$json['minSpan']   = null === $this->min_span ? 2 : (int) $this->min_span;
		$json['intString'] = $this->match_int_to_string( count( $json['layouts'] ) );
		$json['fixed']     = $this->fixed;

		$this->json['inputAttrs'] = '';
		foreach ( $this->input_attrs as $attr => $value ) {
			$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
		}

		return $json;
	}

	/**
	 * Create a custom array so it's easier to setup columns
	 *
	 * @since  1.2.0
	 * @access private
	 */
	private function get_columns() {
		$arr = array();
		$val = $this->value();

		if ( '' === $val ) {
			return $this->default;
		}

		if ( is_string( $val ) ) {
			$val = json_decode( $val );
		}

		return $val;
	}

	/**
	 * Create a custom array to hold options
	 *
	 * @since 1.0.0
	 * @acces private
	 */
	private function get_layouts() {
		$arr = array();
		foreach ( $this->layouts as $k => $v ) {
			$arr[] = array(
				'value' => $k,
				'label' => $v,
			);
		}

		return $arr;
	}

	/**
	 * Matches an int to a string
	 *
	 * @since 1.3.4
	 */
	public function match_int_to_string( $int = 1 ) {
		$arr = array(
			1 => 'one',
			2 => 'two',
			3 => 'three',
			4 => 'four',
			5 => 'five',
			6 => 'six',
		);

		return $arr[ $int ];
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
		//@formatter:off ?>
		<div class="epsilon-layouts-container" data-min-span="{{ data.minSpan }}">
			<div class="customize-control-content">
				<input {{{ data.link }}} {{{ data.inputAttrs }}} type="hidden" <# if( data.value ) { #> value='{{{ data.value }}}' <# } #> />
			</div>
			<div class="epsilon-layouts-container-buttons">
				<label>
					<span class="customize-control-title epsilon-button-label">
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

				<div class="epsilon-control-set-<# if( ! data.fixed ) { #>advanced<# } #>">
					<div class="epsilon-control-group epsilon-group-{{ data.intString }}">
						<# if( data.layouts.length > 0 ){ #>
							<# for (layout in data.layouts) { #>
								<a href="#" data-button-value="{{ data.layouts[layout].value }}" <# if( data.columns.columnsCount === data.layouts[layout].value) { #> class="active" <# } #>>
									<img src="{{ data.layouts[layout].label }}" />
								</a>
							<# } #>
						<# } #>
					</div>
				<# if ( ! data.fixed ) { #>
					<div class="epsilon-control-advanced" data-unique-id="{{{ data.id }}}">
						<i class="dashicons dashicons-admin-generic"/>
					</div>
				<# } #>
				</div>
			</div>

			<div class="epsilon-layouts-container-advanced" id="{{{ data.id }}}">
				<span class="epsilon-layouts-container-label"><?php echo esc_html__( 'Edit column size', 'epsilon-framework' ) ?></span>
				<div class="epsilon-layouts-setup">
					<# for (column in data.columns.columns) { #>
					<div class="epsilon-column col{{{ data.columns.columns[column].span }}}" data-columns="{{{ data.columns.columns[column].span }}}">
						<a href="#" data-action="left"><span class="dashicons dashicons-arrow-left"></span></a>
						<a href="#" data-action="right"><span class="dashicons dashicons-arrow-right"></span></a>
					</div>
					<# } #>
				</div>
			</div>
		</div>
		<?php //@formatter:on
	}
}
