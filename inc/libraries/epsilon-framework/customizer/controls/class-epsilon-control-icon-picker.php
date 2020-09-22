<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Epsilon_Control_Icon_Picker
 */
class Epsilon_Control_Icon_Picker extends WP_Customize_Control {

	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'epsilon-icon-picker';

	/**
	 * @since 1.0.0
	 * @var array
	 */
	public $icons = array();

	/**
	 * Epsilon_Control_Icon_Picker constructor.
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_control_type( 'Epsilon_Control_Icon_Picker' );
	}

	/**
	 * @since 1.0.0
	 * @return array
	 */
	public function json() {
		$json          = parent::json();
		$json['id']    = $this->id;
		$json['link']  = $this->get_link();
		$json['value'] = $this->value();
		$json['icons'] = $this->get_icons();

		return $json;
	}

	/**
	 * @since 1.0.0
	 * @return mixed
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
	 * Display the control's content
	 */
	public function content_template() {
		//@formatter:off ?>
		<label class="epsilon-icon-picker-label">
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

			<div class="epsilon-icon-container">
				<div class="epsilon-icon-name"><i class="{{{ data.value }}}"></i> <div class="icon-label">{{{ data.icons[data.value] }}}</div></div>
				<span class="dashicons dashicons-arrow-down epsilon-open-icon-picker"></span>
			</div>
		</label>
		<input type="hidden" class="epsilon-icon-picker" {{{ data.link }}} value="{{{ data.value }}}">
		<div class="epsilon-icon-picker-container">
			<div class="search-container">
				<input type="text" class="widefat text" />
			</div>
			<div class="epsilon-icons-container">
				<div class="epsilon-icons">
					<# _.each(data.icons, function(k, v){ #>
						<i class="{{{ v }}} <# if( data.value === v ) { #> selected <# } #>" data-icon="{{{ v }}}" data-search="{{{ k }}}"></i>
					<# }) #>
				</div>
			</div>
		</div>
	<?php //@formatter:on
	}

	/**
	 * Empty, as it should be
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
	}
}
