<?php

/**
 * Homepage parralax section Widget
 * Flexible Theme
 */
class flexible_home_clients extends WP_Widget
{
    function __construct(){

        $widget_ops = array('classname' => 'flexible_home_clients','description' => esc_html__( "Flexible Our Client Section" ,'flexible') );
        parent::__construct('flexible_home_clients', esc_html__('Flexible Our Client section','flexible'), $widget_ops);
    }

    function widget($args , $instance) {
    	extract($args);
        $title = isset($instance['title']) && !empty($instance['title']) ? $instance['title'] : __('Our Main Clients','flexible');
        $logos = isset($instance['client_logo']) ? $instance['client_logo'] : [''];
        
        echo $before_widget;
        
        /**
		 * Widget Content
		 */
    ?>
    <?php if( count( $logos['img'] ) != 0 ){ ?>
      <section>
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    <h3 class="mb64 mb-xs-40"><?php echo $title; ?></h3>
                </div>
            </div>
            <!--end of row-->
            <div class="row">
                <div class="logo-carousel">
                    <ul class="slides"><?php
                    for( $i=0; $i<count($logos['img']); $i++ ) {
                      if( $logos['img'] != '' && $logos['link'] != '' ){ ?>
                        <li>
                            <a href="<?php echo $logos['link'][$i]; ?>">
                                <img alt="<?php _e('Logos', 'flexible'); ?>" src="<?php echo $logos['img'][$i]; ?>" />
                            </a>
                        </li><?php
                      }
                    }?>
                    </ul>
                </div>
                <!--end of logo slider-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <?php } ?>

		<?php

		echo $after_widget;
    }


    function form($instance) {
        if(!isset($instance['title']) ) $instance['title']='';
        if(!isset($instance['client_logo']['img'])) $instance['client_logo']['img']=[''];
        if(!isset($instance['client_logo']['link'])) $instance['client_logo']['link']=[''];
    ?>

      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title ','flexible') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['title']); ?>"
                          name="<?php echo $this->get_field_name('title'); ?>"
                          id="<?php $this->get_field_id('title'); ?>"
                          class="widefat" />
      </p>
      
      <ul class="client-sortable clone-wrapper"><?php
          $image_src = $instance['client_logo']['img'];
          $logo_link = $instance['client_logo']['link'];
          $slider_count = ( isset($image_src) && count( $image_src ) > 0 ) ? count( $image_src ) : 3 ;
                    
          for ( $i = 0; $i < $slider_count; $i++ ): ?>
              <li class="toclone">
                <br>
                  <label class="logo_heading"><b><?php _e( 'Logo #', 'flexible' ); ?><span class="count"><?php echo absint( $i )+1; ?></span></b></label>
                  <input name="<?php echo $this->get_field_name('client_logo').'[img]['.$i.']';?>" id="image_src<?php echo '-'.$i; ?>" class="widefat image_src" type="hidden" size="36"  value="<?php echo (isset( $image_src[$i] )) ? esc_url( $image_src[$i] ) : ''; ?>" /><br>
                  <button class="upload_image_button button button-primary" ><?php _e('Upload Logo','flexible'); ?></button>
                  <img class="image_demo" width="50px" height="50px" style="border:0; margin-left: 20px; vertical-align: top;" src="<?php echo ( isset($image_src[$i]) ) ? esc_url( $image_src[$i] ) : ''; ?>" />
                  <br/><br/>
                  <label><?php _e( 'Link:', 'flexible' ); ?></label>
                  <input name="<?php echo $this->get_field_name('client_logo').'[link]['.$i."]"; ?>" id="link<?php echo '-'.$i; ?>" class="widefat client-link" type="text" size="36"  value="<?php echo (isset( $logo_link[$i] )) ? esc_url( $logo_link[$i] ) : ''; ?>" /><br><br>
                  
                  <a href="#" class="clone button-primary"><?php _e('Add', 'flexible'); ?></a>
                  <a href="#" class="delete button"><?php _e('Delete', 'flexible'); ?></a>
              </li>
          <?php endfor; ?>
      </ul><?php
      
    }
    
    /**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? esc_html( $new_instance['title'] ) : '';
		if( isset( $instance['client_logo']['img'] ) && count( $instance['client_logo']['img'] ) != 0 ){
          for( $i=0; $i<count( $instance['client_logo']['img'] ); $i++ ){
            $instance['client_logo']['img'][$i] = ( ! empty( $instance['client_logo']['img'][$i] ) ) ? esc_url( $instance['client_logo']['img'][$i] ) : '';
          }
        }
		if( isset( $instance['client_logo']['link'] ) && count( $instance['client_logo']['link'] ) != 0 ){
          for( $i=0; $i<count( $instance['client_logo']['link'] ); $i++ ){
            $instance['client_logo']['link'][$i] = ( ! empty( $instance['client_logo']['link'][$i] ) ) ? esc_url( $instance['client_logo']['link'][$i] ) : '';
          }
        }
        
		return $instance;
	}

}

?>