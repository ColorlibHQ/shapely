<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * @since 1.0.0
 *
 * Class Epsilon_Content_Backup
 */
class Epsilon_Content_Backup {
	/**
	 * @since 1.0.0
	 * @var array
	 */
	public $fields = array();
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $slug = '';
	/**
	 * @since 1.0.0
	 * @var int
	 */
	public $setting_page;
	/**
	 * @since 1.2.0
	 * @var
	 */
	public $pages = array();
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $hash;
	/**
	 * @since 1.0.0
	 * @var string
	 */
	public $mode = 'theme_mods';
	/**
	 * @since 1.2.0
	 * @var WordPress Manager Object
	 */
	public $manager = null;
	/**
	 * The single instance of the backup class
	 *
	 * @var     object
	 * @access   private
	 * @since    1.2.0
	 */
	private static $_instance = null;

	/**
	 * @since 1.0.0
	 * Epsilon_Content_Backup constructor.
	 */
	public function __construct() {
		$theme              = wp_get_theme();
		$this->slug         = get_stylesheet();
		$this->setting_page = get_option( $this->slug . '_backup_settings', false );
		$this->hash         = $this->calculate_hash();

		if ( ! $this->setting_page || null === get_post( $this->setting_page ) ) {
			$args = array(
				'post_title'  => $theme->get( 'Name' ) . ' Backup Settings',
				'post_status' => 'draft',
				'post_type'   => 'page',
				'post_author' => 0,
			);

			$this->setting_page = wp_insert_post( $args );
			if ( ! is_wp_error( $this->setting_page ) ) {
				update_option( $this->slug . '_backup_settings', $this->setting_page );
			}
		}

		/**
		 * Add a notice, inform user that this page is only for backup purposes
		 */
		add_action( 'add_meta_boxes', array( $this, 'add_notice_to_page' ), 10, 2 );
		/**
		 * We need to keep this page as draft, forever.
		 */
		add_action( 'admin_init', array( $this, 'check_page_status' ) );
		/**
		 * We need to use this hook so we have a reference of the fields that are required as back up
		 */
		add_action( 'customize_save_after', array( $this, 'backup_settings' ) );
		/**
		 * Save page builder
		 */
		add_action( 'customize_save_after', array( $this, 'save_page_builder' ) );
		/**
		 * Disable the form editor if we're in production
		 */
		add_action( 'edit_form_after_title', array( $this, 'disable_front_page_editor' ) );
	}

	/**
	 * @since 1.0.0
	 * @return Epsilon_Content_Backup
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Registers a field in the "backup" collection
	 *
	 * @since 1.0.0
	 */
	public function add_field( $id, $args ) {
		$this->fields[ $id ] = $args;
	}

	/**
	 * Pages who should be backed up
	 *
	 * @param $page_id
	 * @param $id
	 * @param $args
	 */
	public function add_pages( $page_id, $id, $args ) {
		$this->pages[ $page_id ] = array(
			'id'       => $page_id,
			'field_id' => $id,
			'fields'   => $args,
		);
	}

	/**
	 * Disables the frontend editor
	 *
	 * @since 1.3.4
	 */
	public function disable_front_page_editor( $post ) {
		if ( true === WP_DEBUG ) {
			return false;
		}

		if ( $this->setting_page == $post->ID ) {
			remove_post_type_support( $post->post_type, 'editor' );
		}
	}

	/**
	 * Calculates the hash of the settings
	 */
	private function calculate_hash() {
		$hash = array(
			'theme_mods' => md5( json_encode( get_option( 'theme_mods_' . $this->slug ) ) ),
			'post_meta'  => md5( json_encode( get_post_meta( $this->setting_page ) ) ),
		);

		return $hash;
	}

	/**
	 * Check the status of the settings page, it should always be draft
	 *
	 * @since 1.0.0
	 */
	public function check_page_status() {
		$post = get_post( $this->setting_page );
		if ( 'draft' !== $post->post_status ) {
			$settings = array(
				'ID'          => $this->setting_page,
				'post_status' => 'draft',
			);

			wp_update_post( $settings );
		}
	}

	/**
	 * Adds a notice to the page
	 *
	 * @since 1.0.0
	 */
	public function add_notice_to_page( $post_type, $post ) {
		$continue = false;

		if ( 'page' !== $post_type ) {
			return;
		}

		/**
		 * Need to make sure we are in a page that has content saved in the customizer
		 *
		 * Verify the last element of the explode meta,
		 * if it's the same as the post ID it means we're doing it right
		 */
		$meta = get_post_meta( $post->ID );
		foreach ( $meta as $key => $value ) {
			$key = explode( '_', $key );
			if ( end( $key ) === $post->ID ) {
				$continue = true;
			}
		}

		if ( $this->setting_page === $post->ID ) {
			$continue = true;
		}

		if ( ! $continue ) {
			return;
		}

		$notifications = Epsilon_Notifications::get_instance();
		$notifications->add_notice(
			array(
				'id'      => $this->slug . '_content_backup',
				'type'    => 'notice notice-info',
				'message' => '<p>' . esc_html__( 'This page contains the content created by the customizer.', 'epsilon-framework' ) . '</p>',
			)
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $manager WordPress Customizer Manager
	 */
	public function backup_settings( $manager ) {
		$check         = $this->check_hash();
		$this->manager = $manager;

		if ( $check['status'] ) {
			return;
		};

		$settings = array(
			'ID'           => $this->setting_page,
			'post_content' => $this->parse_content(),
		);

		wp_update_post( $settings );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $manager WordPress Customizer Manager
	 */
	public function save_page_builder( $manager ) {
		$this->manager = $manager;
		$check         = $this->check_advanced_hash();

		$this->mode = 'post_meta';
		foreach ( $this->pages as $page ) {
			if ( $check[ $page['id'] ]['status'] ) {
				continue;
			};

			$meta = get_post_meta( $page['id'], $page['field_id'], true );
			if ( empty( $meta[ $page['field_id'] ] ) ) {
				continue;
			}

			$settings = array(
				'ID'           => $page['id'],
				'post_content' => $this->parse_content_advanced( $page ),
			);

			wp_update_post( $settings );
		};
	}

	/**
	 * @since 1.0.0
	 * @return array
	 */
	private function check_hash() {
		$arr = array(
			'status' => true,
		);

		$temp = reset( $this->fields );

		/**
		 * In case we save options as post_meta, we need to use that particular hash
		 */
		if ( is_array( $temp ) && isset( $temp['save_as_meta'] ) && $this->setting_page === $temp['save_as_meta'] ) {
			$this->mode = 'post_meta';
		}

		$last_known_hash = get_transient( $this->slug . '_hash_update' );
		if ( false === $last_known_hash ) {
			set_transient( $this->slug . '_hash_update', $this->hash[ $this->mode ], 5 * MINUTE_IN_SECONDS );
		}

		if ( $last_known_hash !== $this->hash[ $this->mode ] ) {
			$arr['status'] = false;
		}

		return $arr;
	}

	/**
	 * @since 1.0.0
	 * @return array
	 */
	private function check_advanced_hash() {
		$arr = array();

		foreach ( $this->pages as $page ) {
			$arr[ $page['id'] ] = array(
				'status' => true,
			);

			$last_known_hash = get_transient( $this->slug . '_' . $page['id'] . '_hash_update' );
			$hash            = md5( json_encode( get_post_meta( $page['id'] ) ) );
			if ( false === $last_known_hash ) {
				set_transient( $this->slug . '_' . $page['id'] . '_hash_update', $hash, 5 * MINUTE_IN_SECONDS );
			}

			if ( $last_known_hash !== $hash ) {
				$arr[ $page['id'] ] = array(
					'status' => false,
				);
			}
		}

		return $arr;
	}

	/**
	 * @since 1.2.0
	 * @return string
	 */
	private function parse_content_advanced( $page ) {
		$content    = '';
		$collection = array();

		$options = get_post_meta( $page['id'] );
		foreach ( $page['fields'] as $field ) {
			$collection[ $page['field_id'] ] = array(
				'id'      => $page['field_id'],
				'content' => get_post_meta( $page['id'], $page['field_id'], true ),
				'type'    => 'epsilon-section-repeater',
			);
		}

		foreach ( $collection as $field => $props ) {
			$content .= $this->_parse_content( $props );
		}

		return $content;
	}

	/**
	 * @since 1.0.0
	 * @return string
	 */
	private function parse_content() {
		$content    = '';
		$collection = array();

		switch ( $this->mode ) {
			case 'post_meta':
				$options = get_post_meta( $this->setting_page );
				foreach ( $this->fields as $id => $field ) {
					if ( array_key_exists( $id, $options ) ) {
						$collection[ $id ] = array(
							'id'      => $id,
							'content' => get_post_meta( $this->setting_page, $id, true ),
							'type'    => $field['type'],
						);
					}
				}
				break;
			default:
				$options = get_option( 'theme_mods_' . $this->slug );
				foreach ( $this->fields as $id => $field ) {
					if ( array_key_exists( $id, $options ) ) {
						$collection[ $id ] = array(
							'id'      => $id,
							'content' => get_theme_mod( $id ),
							'type'    => $field['type'],
						);
					}
				}
				break;
		}

		foreach ( $collection as $field => $props ) {
			$content .= $this->_parse_content( $props );
		}

		return $content;
	}

	/**
	 * @since 1.0.0
	 * @return string;
	 */
	private function _parse_content( $field ) {
		if ( empty( $field['content'] ) ) {
			return '';
		}

		$control = $this->manager->get_control( $field['id'] );
		$content = '';
		if ( 'post_meta' === $this->mode ) {
			$field['content'] = $field['content'][ $field['id'] ];
		}

		switch ( $field['type'] ) {
			case 'epsilon-section-repeater':
				foreach ( $field['content'] as $single_section ) {
					$content .= '<!-- epsilon/' . $control->repeatable_sections[ $single_section['type'] ]['id'] . ' -->' . "\n";
					foreach ( $single_section as $id => $val ) {
						$args = array(
							'val'    => $val,
							'id'     => $id,
							'fields' => $control->repeatable_sections[ $single_section['type'] ]['fields'],
						);

						$condition = $this->check_backup_condition( $args );

						if ( ! $condition ) {
							continue;
						}

						$content .= $this->create_content_value( $args['val'], $args['fields'][ $id ]['type'] );
					}
					$content .= '<!-- /epsilon/' . $control->repeatable_sections[ $single_section['type'] ]['id'] . ' -->' . "\n \n";
				}
				$content .= "\n \n";
				break;
			case 'epsilon-repeater':
				$content .= $this->_format_default( $control, $field['content'], $control->id );
				break;
			default:
				$content .= "\n";
				$content .= $field['content'];
				$content .= "\n \n";
				break;
		}// End switch().

		return $content;
	}

	/**
	 * Checks if we need to generate backup for this item
	 *
	 * @param $args Array Array of arguments.
	 *
	 * @return bool
	 */
	private function check_backup_condition( $args ) {
		/**
		 * Empty values don't need to be saved
		 */
		if ( empty( $args['val'] ) ) {
			return false;
		}

		/**
		 * Id of the field doesn't need saving
		 */
		if ( 'type' === $args['id'] ) {
			return false;
		}

		/**
		 * Design related items should not be saved
		 */
		$skip = array(
			'epsilon-customizer-navigation',
			'epsilon-icon-picker',
			'epsilon-toggle',
			'epsilon-slider',
			'epsilon-color-picker',
			'select',
			'selectize',
			'epsilon-button-group',
		);

		/**
		 * Customization fields, should bot be backedup
		 */
		if ( ! array_key_exists( $args['id'], $args['fields'] ) ) {
			return false;
		}

		if ( in_array( $args['fields'][ $args['id'] ]['type'], $skip ) ) {
			return false;
		}

		/**
		 * If conditions are false, we return true
		 */
		return true;
	}

	/**
	 * Parse the value and create "readable" content.
	 *
	 *
	 * @param $value array|string Can be both.
	 * @param $type  string Type of field we are saving content.
	 *
	 * @return string
	 */
	private function create_content_value( $value, $type ) {
		switch ( $type ) {
			case 'epsilon-image':
				return '<img src="' . $value . '" />' . "\n";
				break;
			case 'hidden':
				$control = $this->manager->get_control( $value );
				if ( is_a( $control, 'Epsilon_Control_Repeater' ) ) {
					switch ( $this->mode ) {
						case 'post_meta':
							$val = get_post_meta( $this->setting_page, $value, true );
							if ( empty( $val ) ) {
								return $val;
							}
							if ( ! isset( $val[ $value ] ) ) {
								return $val;
							}
							$val = $val[ $value ];
							break;
						default:
							$val = get_theme_mod( $value, array() );
							break;
					}

					$content = '';
					$content .= $this->format_block( $control, $val, $control->id );

					return $content;
				};// End if().

				return '';
				break;
			default:
				return $value . "\n";
				break;
		}// End switch().
	}

	/**
	 * Formats the repeater field HTML as per outside (if given) instructions
	 *
	 * @since 1.3.4
	 *
	 * @param $control
	 * @param $val
	 * @param $id
	 */
	private function format_block( $control, $value, $id ) {
		$parser = $this->slug . '_post_parser';

		if ( ! class_exists( $parser ) ) {
			return $this->_format_default( $control, $value, $id );
		}

		$parser = $parser::get_instance();
		$method = 'parse_' . $id;
		if ( ! method_exists( $parser, $method ) ) {
			return $this->_format_default( $control, $value, $id );
		}

		return $parser->$method( $control, $value, $id );
	}

	/**
	 * Provides a fallback for the content block formatting
	 *
	 * @since 1.3.4
	 *
	 * @param $control
	 * @param $value
	 * @param $id
	 *
	 * @return string
	 */
	private function _format_default( $control, $value, $id ) {
		$content = '';
		foreach ( $value as $fields ) {
			$content .= '<!-- epsilon/' . $control->label . ' -->' . "\n";

			foreach ( $fields as $id => $f_val ) {
				if ( empty( $f_val ) ) {
					continue;
				}

				if ( ! isset( $control->fields[ $id ] ) ) {
					continue;
				}

				if ( ! isset( $control->fields[ $id ]['type'] ) ) {
					continue;
				}

				if ( 'epsilon-color-picker' === $control->fields[ $id ]['type'] || 'epsilon-icon-picker' === $control->fields[ $id ]['type'] ) {
					continue;
				};

				if ( 'epsilon-image' === $control->fields[ $id ]['type'] ) {
					$content .= '<img src="' . $f_val . '" />' . "\n";
				} else {
					$content .= $f_val . "\n";
				}
			}
			$content .= '<!-- /epsilon/' . $control->label . '-->' . "\n";
		}

		return $content;
	}
}
