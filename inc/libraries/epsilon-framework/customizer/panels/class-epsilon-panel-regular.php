<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Epsilon Panel Regular
 *
 * @since  1.3.4
 * @access public
 */
class Epsilon_Panel_Regular extends WP_Customize_Panel {
	/**
	 * @var
	 */
	public $panel;
	/**
	 * Panel can be hidden
	 *
	 * @since 1.4.0
	 * @var bool
	 */
	public $hidden = false;
	/**
	 * @var string
	 */
	public $type = 'epsilon-panel-regular';

	/**
	 * Epsilon_Panel_Regular constructor.
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string               $id
	 * @param array                $args
	 */
	public function __construct( WP_Customize_Manager $manager, $id, array $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$manager->register_panel_type( 'Epsilon_Panel_Regular' );
	}

	/**
	 * @return array
	 */
	public function json() {
		$array = wp_array_slice_assoc(
			(array) $this,
			array(
				'id',
				'description',
				'priority',
				'type',
				'panel',
			)
		);

		$array['title']          = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['content']        = $this->get_content();
		$array['active']         = $this->active();
		$array['instanceNumber'] = $this->instance_number;
		$array['hidden']         = $this->hidden;

		return $array;
	}
}
