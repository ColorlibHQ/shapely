<?php

if ( ! class_exists( 'Shapely_Builder' ) ) :
/**
 * Shapely Builder Class
 */
class Shapely_Builder {

        private static $instance = null;

        private $pages    = array();
        private $sidebars = array();

        function __construct() {
                $this->get_all_pages();
                $this->check_template_files();

                // Hooks
                if ( ! empty( $this->pages ) ) {
                        add_action( 'widgets_init', array( $this, 'register_sidebars' ), 20 );
                        add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_builder_js' ) );
                        add_filter( 'sidebars_widgets', array( $this, 'remove_specific_widget' ) );
                        add_action( 'wp_ajax_shapely_builder_data', array( $this, 'handle_builder_data' ) );
                }
        }

        /**
         * Handle AJAX requests for builder data
         */
        public function handle_builder_data() {
                check_ajax_referer( 'shapely_builder_nonce', 'nonce' );
                
                if ( ! current_user_can( 'edit_theme_options' ) ) {
                        wp_send_json_error( 'Permission denied' );
                }
                
                $response = array(
                        'success' => true,
                        'pages' => $this->pages,
                        'sidebars' => $this->sidebars
                );
                
                wp_send_json_success( $response );
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
                                $this->sidebars[] = 'shapely-' . $post->post_name;
                        }
                }

                // Store sidebars in transient for later use
                delete_transient( 'shapely_builder_sidebars' );
                set_transient( 'shapely_builder_sidebars', $this->sidebars, DAY_IN_SECONDS );
        }

        public static function get_instance() {
                if ( is_null( self::$instance ) ) {
                        self::$instance = new self();
                }
                return self::$instance;
        }

        public function register_sidebars() {
                if ( empty( $this->pages ) ) {
                        return;
                }

                foreach ( $this->pages as $slug => $page ) {
                        $sidebar_id = 'shapely-' . $slug;
                        register_sidebar(
                                array(
                                        'name'          => sprintf( esc_html__( 'Page: %s', 'shapely' ), $page['title'] ),
                                        'id'            => $sidebar_id,
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
                        'ajaxurl' => admin_url( 'admin-ajax.php' ),
                        'nonce'   => wp_create_nonce( 'shapely_builder_nonce' ),
                );
                wp_enqueue_script( 'shapely_builder_customizer', get_template_directory_uri() . '/assets/js/customizer-builder.js', array( 'jquery', 'customize-controls' ), '20240430', true );

                wp_localize_script( 'shapely_builder_customizer', 'ShapelyBuilder', $builder_settings );
        }

        public function remove_specific_widget( $sidebars_widgets ) {
                // Get the stored sidebars
                $stored_sidebars = get_transient( 'shapely_builder_sidebars' );
                $sidebar_list = $stored_sidebars ? $stored_sidebars : $this->sidebars;
                
                // Add home sidebar to the list of allowed sidebars
                $sidebar_list[] = 'sidebar-home';
                
                // Only process if we have sidebars to check against
                if ( empty( $sidebar_list ) || ! is_array( $sidebars_widgets ) ) {
                        return $sidebars_widgets;
                }
                
                foreach ( $sidebars_widgets as $widget_area => $widget_list ) {
                        // Skip if not an array (like 'array_version')
                        if ( ! is_array( $widget_list ) ) {
                                continue;
                        }
                        
                        // Only process areas that are NOT our builder sidebars
                        if ( ! in_array( $widget_area, $sidebar_list ) ) {
                                foreach ( $widget_list as $pos => $widget_id ) {
                                        // Remove page-specific widgets from non-builder areas
                                        if ( is_string( $widget_id ) && 
                                             ( strpos( $widget_id, 'shapely-page-content' ) !== false || 
                                               strpos( $widget_id, 'shapely-page-title' ) !== false ) ) {
                                                unset( $sidebars_widgets[ $widget_area ][ $pos ] );
                                        }
                                }
                        }
                }

                return $sidebars_widgets;
        }

        /**
         * Check if template files exist
         */
        private function check_template_files() {
                $template_path = get_template_directory() . '/page-templates/template-widget.php';
                
                if ( ! file_exists( $template_path ) ) {
                        return;
                }
        }
}
endif; 