<?php
/**
 * Flexible Theme Customizer.
 *
 * @package Flexible
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function flexible_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'flexible_customize_register' );


/**
 * Options for WordPress Theme Customizer.
 */
function flexible_customizer( $wp_customize ) {

	// logo
	$wp_customize->add_setting( 'header_logo', array(
		'default' => '',
		'transport'   => 'refresh',
        'sanitize_callback' => 'flexible_sanitize_number'
	) );
        $wp_customize->add_control(new WP_Customize_Media_Control( $wp_customize, 'header_logo', array(
    		'label' => __( 'Logo', 'flexible' ),
    		'section' => 'title_tagline',
    		'mime_type' => 'image',
    		'priority'  => 10,
    	) ) );
    	
        
      /* Main option Settings Panel */
    $wp_customize->add_panel('flexible_main_options', array(
        'capability' => 'edit_theme_options',
        'theme_supports' => '',
        'title' => __('Flexible Options', 'flexible'),
        'description' => __('Panel to update flexible theme options', 'flexible'), // Include html tags such as <p>.
        'priority' => 10 // Mixed with top-level-section hierarchy.
    ));

	// add "Sidebar" section
        $wp_customize->add_section('flexible_layout_section', array(
            'title' => __('Layout options', 'flexible'),
            'description' => '',
            'priority' => 31,
            'panel' => 'flexible_main_options'
        ));
            // Layout options
            global $site_layout;
            $wp_customize->add_setting('flexible_sidebar_position', array(
                 'default' => 'side-right',
                 'sanitize_callback' => 'flexible_sanitize_layout'
            ));
            $wp_customize->add_control('flexible_sidebar_position', array(
                 'label' => __('Website Layout Options', 'flexible'),
                 'section' => 'flexible_layout_section',
                 'type'    => 'select',
                 'description' => __('Choose between different layout options to be used as default', 'flexible'),
                 'choices'    => $site_layout
            ));	
	
            $wp_customize->add_setting('link_color', array(
                    'default' => '',
                    'sanitize_callback' => 'flexible_sanitize_hexcolor'
                ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'link_color', array(
                'label' => __('Link Color', 'flexible'),
                'description'   => __('Default used if no color is selected','flexible'),
                'section' => 'flexible_layout_section',
            )));
            $wp_customize->add_setting('link_hover_color', array(
                    'default' => '',
                    'sanitize_callback' => 'flexible_sanitize_hexcolor'
                ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'link_hover_color', array(
                'label' => __('Link Hover Color', 'flexible'),
                'description'   => __('Default used if no color is selected','flexible'),
                'section' => 'flexible_layout_section',
            )));
            $wp_customize->add_setting('button_color', array(
                    'default' => '',
                    'sanitize_callback' => 'flexible_sanitize_hexcolor'
                ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'button_color', array(
                'label' => __('Button Color', 'flexible'),
                'description'   => __('Default used if no color is selected','flexible'),
                'section' => 'flexible_layout_section',
            )));
            $wp_customize->add_setting('button_hover_color', array(
                    'default' => '',
                    'sanitize_callback' => 'flexible_sanitize_hexcolor'
                ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'button_hover_color', array(
                'label' => __('Button Hover Color', 'flexible'),
                'description'   => __('Default used if no color is selected','flexible'),
                'section' => 'flexible_layout_section',
            )));
            
            $wp_customize->add_setting('social_color', array(
                    'default' => '',
                    'sanitize_callback' => 'flexible_sanitize_hexcolor'
                ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'social_color', array(
                'label' => __('Social Icon Color', 'flexible'),
                'description'   => __('Default used if no color is selected','flexible'),
                'section' => 'flexible_layout_section',
            )));
            
        // add "Sidebar" section
    $wp_customize->add_section('flexible_main_section', array(
        'title' => __('Main options', 'flexible'),
        'description' => sprintf(__('', 'flexible')),
        'priority' => 11,
        'panel' => 'flexible_main_options'
    ));
      
      $wp_customize->add_setting( 'top_callout', array(
		'default' => 1,
		'sanitize_callback' => 'flexible_sanitize_checkbox',
      ) );

      $wp_customize->add_control( 'top_callout', array(
          'label'		=> esc_html__( 'check to show title in top call out box', 'flexible' ),
          'section'	=> 'flexible_main_section',
          'priority'	=> 20,
          'type'      => 'checkbox',
      ) );
      
      $wp_customize->add_setting('blog_name', array(
          'default' => '',
          'sanitize_callback' => 'flexible_sanitize_strip_slashes'
      ));
      $wp_customize->add_control('blog_name', array(
          'label' => __('Blog Name in top callout', 'flexible'),
          'description'   => __('Heading for the Blog page','flexible'),
          'section' => 'flexible_main_section',
      ) );    
    
      if( post_type_exists('jetpack-portfolio') ){
        $wp_customize->add_setting('portfolio_name', array(
          'default' => '',
          'sanitize_callback' => 'flexible_sanitize_strip_slashes'
        ));
        $wp_customize->add_control('portfolio_name', array(
            'label' => __('Portfolio Archive Title', 'flexible'),
            'section' => 'flexible_main_section',
        ) ); 
        
        $wp_customize->add_setting('portfolio_description', array(
          'default' => '',
          'sanitize_callback' => 'flexible_sanitize_strip_slashes'
        ));
        $wp_customize->add_control( 'portfolio_description', array(
            'type' => 'textarea',
            'label' => __('Portfolio Archive Description', 'flexible'),
            'section' => 'flexible_main_section',
        ) );
      }
      
      $wp_customize->add_setting( 'footer_callout_text', array(
		'default' => "",
		'sanitize_callback' => 'flexible_sanitize_strip_slashes',
      ) );
      $wp_customize->add_control('footer_callout_text', array(
          'label' => __('Text for footer callout', 'flexible'),
          'description'   => __('Footer Callout','flexible'),
          'section' => 'flexible_main_section',
      ) );
      
      $wp_customize->add_setting( 'footer_callout_btntext', array(
		'default' => "",
		'sanitize_callback' => 'flexible_sanitize_strip_slashes',
      ) );
      $wp_customize->add_control('footer_callout_btntext', array(
          'label' => __('Text for footer callout button', 'flexible'),
          'section' => 'flexible_main_section',
      ) ); 
      $wp_customize->add_setting('footer_callout_link', array(
          'default' => '',
          'sanitize_callback' => 'esc_url_raw'
      ));
      $wp_customize->add_control('footer_callout_link', array(
          'label' => __('CFA button link', 'flexible'),
          'section' => 'flexible_main_section',
          'description' => __('Enter the link for Call For Action button in footer', 'flexible'),
          'type' => 'text'
      ));
      
    
	// add "Footer" section
	$wp_customize->add_section( 'flexible_footer_section' , array(
		'title'      => esc_html__( 'Footer', 'flexible' ),
		'priority'   => 90,
	) );	
	
	$wp_customize->add_setting( 'flexible_footer_copyright', array(
		'default' => '',
		'transport'   => 'refresh',
        'sanitize_callback' => 'flexible_sanitize_strip_slashes'
	) );

	$wp_customize->add_control( 'flexible_footer_copyright', array(
		'type' => 'textarea',
		'label' => 'Copyright Text',
		'section' => 'flexible_footer_section',
	) );
        
        /* Flexible Other Options */
        $wp_customize->add_section('flexible_other_options', array(
            'title' => __('Other', 'flexible'),
            'priority' => 70,
            'panel' => 'flexible_main_options'
        ));
            $wp_customize->add_setting('custom_css', array(
                'default' => '',
                'sanitize_callback' => 'flexible_sanitize_strip_slashes'
            ));
            $wp_customize->add_control('custom_css', array(
                'label' => __('Custom CSS', 'flexible'),
                'description' => sprintf(__('Additional CSS', 'flexible')),
                'section' => 'flexible_other_options',
                'type' => 'textarea'
            ));
            
        /* Support & Documentation */
        $wp_customize->add_section('flexible_important_links', array(
        'priority' => 5,
        'title' => __('Support and Documentation', 'flexible')
        ));
            $wp_customize->add_setting('flexible[imp_links]', array(
              'sanitize_callback' => 'esc_url_raw'
            ));
            $wp_customize->add_control(
            new Flexible_Important_Links(
            $wp_customize,
                'flexible[imp_links]', array(
                'section' => 'flexible_important_links',
                'type' => 'flexible-important-links'
            )));

}
add_action( 'customize_register', 'flexible_customizer' );

/**
 * Adds sanitization callback function: Strip Slashes
 * @package Flexible
 */
function flexible_sanitize_strip_slashes($input) {
    return wp_kses_stripslashes($input);
}

/**
 * Sanitzie checkbox for WordPress customizer
 */
function flexible_sanitize_checkbox( $input ) {
    if ( $input == 1 ) {
        return 1;
    } else {
        return '';
    }
}
/**
 * Adds sanitization callback function: Sidebar Layout
 * @package Flexible
 */
function flexible_sanitize_layout( $input ) {
    global $site_layout;
    if ( array_key_exists( $input, $site_layout ) ) {
        return $input;
    } else {
        return '';
    }
}

/**
 * Adds sanitization callback function: colors
 * @package Flexible
 */
function flexible_sanitize_hexcolor($color) {
    if ($unhashed = sanitize_hex_color_no_hash($color))
        return '#' . $unhashed;
    return $color;
}

/**
 * Adds sanitization callback function: Slider Category
 * @package Flexible
 */
function flexible_sanitize_slidecat( $input ) {
    
    if ( array_key_exists( $input, flexible_cats()) ) {
        return $input;
    } else {
        return '';
    }
}

/**
 * Adds sanitization callback function: Radio Header
 * @package Flexible
 */
function flexible_sanitize_radio_header( $input ) {
   global $header_show;
    if ( array_key_exists( $input, $header_show ) ) {
        return $input;
    } else {
        return '';
    }
}

/**
 * Adds sanitization callback function: Number
 * @package Flexible
 */
function flexible_sanitize_number($input) {
    if ( isset( $input ) && is_numeric( $input ) ) {
        return $input;
    }
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function flexible_customize_preview_js() {
	wp_enqueue_script( 'flexible_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'flexible_customize_preview_js' );

/**
 * Add CSS for custom controls
 */
function flexible_customizer_custom_control_css() {
	?>
    <style>
        #customize-control-flexible-main_body_typography-size select, #customize-control-flexible-main_body_typography-face select,#customize-control-flexible-main_body_typography-style select { width: 60%; }
    </style><?php
}
add_action( 'customize_controls_print_styles', 'flexible_customizer_custom_control_css' );

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;
/**
 * Class to create a Flexible important links
 */
class Flexible_Important_Links extends WP_Customize_Control {

   public $type = "flexible-important-links";

   public function render_content() {?>
        <!-- Twitter -->
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

        <!-- Facebook -->
        <div id="fb-root"></div>
        <div id="fb-root"></div>
        <script>
            (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=328285627269392";
            fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>

        <div class="inside">
            <div id="social-share">
              <div class="fb-like" data-href="https://www.facebook.com/colorlib" data-send="false" data-layout="button_count" data-width="90" data-show-faces="true"></div>
              <div class="tw-follow" ><a href="https://twitter.com/colorlib" class="twitter-follow-button" data-show-count="false">Follow @colorlib</a></div>
            </div>
            <p><b><a href="http://colorlib.com/wp/support/flexible"><?php _e('Flexible Documentation','flexible'); ?></a></b></p>
            <p><?php _e('The best way to contact us with <b>support questions</b> and <b>bug reports</b> is via','flexible') ?> <a href="http://colorlib.com/wp/forums"><?php _e('Colorlib support forum','flexible') ?></a>.</p>
            <p><?php _e('If you like this theme, I\'d appreciate any of the following:','flexible') ?></p>
            <ul>
                <li><a class="button" href="http://wordpress.org/support/view/theme-reviews/flexible?filter=5" title="<?php esc_attr_e('Rate this Theme', 'flexible'); ?>" target="_blank"><?php printf(__('Rate this Theme','flexible')); ?></a></li>
                <li><a class="button" href="http://www.facebook.com/colorlib" title="Like Colorlib on Facebook" target="_blank"><?php printf(__('Like on Facebook','flexible')); ?></a></li>
                <li><a class="button" href="http://twitter.com/colorlib/" title="Follow Colrolib on Twitter" target="_blank"><?php printf(__('Follow on Twitter','flexible')); ?></a></li>
            </ul>
        </div><?php
   }

}

/*
 * Custom Scripts
 */
add_action( 'customize_controls_print_footer_scripts', 'customizer_custom_scripts' );

function customizer_custom_scripts() { ?>
<style>
    li#accordion-section-flexible_important_links h3.accordion-section-title, li#accordion-section-flexible_important_links h3.accordion-section-title:focus { background-color: #00cc00 !important; color: #fff !important; }
    li#accordion-section-flexible_important_links h3.accordion-section-title:hover { background-color: #00b200 !important; color: #fff !important; }
    li#accordion-section-flexible_important_links h3.accordion-section-title:after { color: #fff !important; }
    #TB_window{ z-index: 999999!important; }
</style>
<?php
}