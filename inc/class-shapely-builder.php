<?php

/**
*
*/
class Shapely_Builder {

	private static $instance = null;

	private $pages    = array();
	private $sidebars = array();

	function __construct() {

		$this->get_all_pages();

		// Hooks
		if ( ! empty( $this->pages ) ) {

			add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_builder_js' ) );
			add_filter( 'sidebars_widgets', array( $this, 'remove_specific_widget' ) );

		}

	}

	public function get_all_pages() {

		$args = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => '_wp_page_template',
					'value' => 'page-templates/template-widget.php',
				),
			),
		);

		$the_pages = new WP_Query( $args );

		if ( $the_pages->have_posts() ) {
			foreach ( $the_pages->posts as $post ) {
				$this->pages[ $post->post_name ] = array(
					'id'    => absint( $post->ID ),
					'title' => esc_html( $post->post_title ),
				);
				$this->sidebars[]                = 'shapely-' . $post->post_name;
			}
		}

	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_sidebars() {

		foreach ( $this->pages as $slug => $page ) {
			register_sidebar(
				array(
					'name'          => sprintf( esc_html__( 'Page: %s', 'shapely' ), $page['title'] ),
					'id'            => 'shapely-' . $slug,
					'description'   => sprintf( esc_html__( 'This widgets will appear in %s page', 'shapely' ), $page['title'] ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="widget-title">',
					'after_title'   => '</h2>',
				)
			);
		}

	}

	public function enqueue_builder_js() {
		$builder_settings = array(
			'siteURL' => esc_url( site_url() ),
			'pages'   => $this->pages,
		);
		wp_enqueue_script( 'shapely_builder_customizer', get_template_directory_uri() . '/assets/js/customizer-builder.js', array(), '20140317', true );

		wp_localize_script( 'shapely_builder_customizer', 'ShapelyBuilder', $builder_settings );
	}

	public function remove_specific_widget( $sidebars_widgets ) {

		foreach ( $sidebars_widgets as $widget_area => $widget_list ) {
			if ( ! in_array( $widget_area, $this->sidebars ) && 'sidebar-home' != $widget_area ) {
				foreach ( $widget_list as $pos => $widget_id ) {
					if ( strpos( $widget_id, 'shapely-page-content' ) !== false || strpos( $widget_id, 'shapely-page-title' ) !== false ) {
						unset( $sidebars_widgets[ $widget_area ][ $pos ] );
						;
					}
				}
			}
		}

		return $sidebars_widgets;

	}

}
