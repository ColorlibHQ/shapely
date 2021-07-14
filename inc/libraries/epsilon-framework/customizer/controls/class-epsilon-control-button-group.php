<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Color_Scheme
 */
class Epsilon_Control_Button_Group extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.1.0
	 * @access public
	 * @var    string
	 */
	public $type = 'epsilon-button-group';

	/**
	 * @var string
	 */
	public $default = '';

	/**
	 * @var array
	 */
	public $choices = array();

	/**
	 * Epsilon_Control_Button_Group constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Button_Group' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.1.0
	 * @access public
	 */
	public function json() {
		$json              = parent::json();
		$json['id']        = $this->id;
		$json['link']      = $this->get_link();
		$json['value']     = $this->value();
		$json['default']   = $this->default;
		$json['choices']   = $this->choices;
		$json['groupType'] = $this->set_group_type();

		$this->json['inputAttrs'] = '';
		foreach ( $this->input_attrs as $attr => $value ) {
			$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
		}

		return $json;
	}

	/**
	 * Set group type
	 */
	public function set_group_type() {
		$arr = array(
			0 => 'none',
			1 => 'one',
			2 => 'two',
			3 => 'three',
			4 => 'four',
		);

		return $arr[ count( $this->choices ) ];
	}

	/**
	 * Display the control's content
	 */
	public function content_template() {
		//@formatter:off ?>
		<div class="epsilon-control-container">
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
			<div class="epsilon-control-set">
				<div class="epsilon-control-group epsilon-group-{{ data.groupType }}">
					<# for( var i in data.choices ) { #>
						<a href="#" data-value="{{ data.choices[i].value }}" <# if( data.value === data.choices[i].value ) { #> class="active" <# } #> >
							<# if( ! _.isUndefined( data.choices[i].icon ) ) { #>
								<i class="dashicons {{ data.choices[i].icon }}"/>
							<# } #>

							<# if( ! _.isUndefined( data.choices[i].png ) ) { #>
								<img src="{{ data.choices[i].png }}" />
							<# } #>
						</a>
					<# } #>
				</div>
			</div>
		</div>
	<?php //@formatter: on
	}
}
