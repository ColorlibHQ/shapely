<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * @since 1.1.0
 * Class Epsilon_Ajax_Controller
 */
class Epsilon_Ajax_Controller {
	/**
	 * Epsilon_Ajax_Controller constructor.
	 */
	public function __construct() {
		/**
		 * Action for easier AJAX handling
		 */
		add_action( 'wp_ajax_epsilon_framework_ajax_action', array(
			$this,
			'epsilon_framework_ajax_action',
		) );

	}

	/**
	 * Ajax handler
	 */
	public function epsilon_framework_ajax_action() {
		if ( !isset( $_POST['args'], $_POST['args']['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['args']['nonce'] ), 'epsilon_nonce' ) ) {
			wp_die(
				wp_json_encode(
					array(
						'status' => false,
						'error'  => esc_html__( 'Not allowed', 'epsilon-framework' ),
					)
				)
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
		    wp_die(
				json_encode(
					array(
						'status' => false,
						'error'  => 'Not allowed',
					)
				)
			);
		}

		$args_action = array_map( 'sanitize_text_field', wp_unslash( $_POST['args']['action'] ) );

		if ( count( $args_action ) !== 2 ) {
			wp_die(
				wp_json_encode(
					array(
						'status' => false,
						'error'  => esc_html__( 'Not allowed', 'epsilon-framework' ),
					)
				)
			);
		}

        $class = Epsilon_Ajax_Controller::sanitize_class_name( $args_action[0] );

		if (! $class || ! class_exists( $class ) ) {
			wp_die(
				wp_json_encode(
					array(
						'status' => false,
						'error'  => esc_html__( 'Class does not exist', 'epsilon-framework' ),
					)
				)
			);
		}

		$method = $args_action[1];

		if ( 'generate_partial_section' === $method ) {
			$args = array_map( 'Epsilon_Ajax_Controller::sanitize_arguments_for_output', wp_unslash( $_POST['args']['args'] ) );
		} else {
			$args = isset( $_POST['args']['args'] ) ? $_POST['args']['args'] : $_POST['args'];
			$args = array_map( 'Epsilon_Ajax_Controller::sanitize_arguments', wp_unslash( $args ) );
		}

		$response = $class::$method( $args );

		if ( is_array( $response ) ) {
			wp_die( wp_json_encode( $response ) );
		}

		if ( 'ok' === $response ) {
			wp_die(
				wp_json_encode(
					array(
						'status'  => true,
						'message' => 'ok',
					)
				)
			);
		}

		wp_die(
			wp_json_encode(
				array(
					'status'  => false,
					'message' => 'nok',
				)
			)
		);
	}

	/**
	 * Sanitize arguments
	 *
	 * @param $args
	 */
	public static function sanitize_arguments( $args ) {
		if ( is_array( $args ) ) {
			return array_map( 'sanitize_text_field', $args );
		} else {
			return sanitize_text_field( $args );
		}
	}

    /**
     * Sanitize class name
     *
     * @param $args
     */
    public static function sanitize_class_name( $class ) {
        $allowed_classes = array( 'Epsilon_Helper', 'Epsilon_Notify_System', 'Epsilon_Page_Generator', 'Epsilon_Typography', 'Epsilon_Color_Scheme', 'Epsilon_Notifications' );
        if ( in_array( $class, $allowed_classes ) ) {
            return $class;
        }else{
            return false;
        }
    }

	/**
	 * Sanitize arguments for output
	 *
	 * @param $args
	 */
	public static function sanitize_arguments_for_output( $args ) {
		if ( is_array( $args ) ) {
			return array_map( 'Epsilon_Ajax_Controller::sanitize_arguments_for_output', $args );
		} else {
			return wp_kses_post( $args );
		}
	}
}
