<?php

/**
 * Homepage parralax section Widget
 * Flexible Theme
 */
class flexible_home_features extends WP_Widget
{
    function __construct(){

        $widget_ops = array('classname' => 'flexible_home_features','description' => esc_html__( "Widget to set Features in Home Section" ,'flexible') );
        parent::__construct('flexible_home_features', esc_html__('Flexible Features Widgets','flexible'), $widget_ops);
    }

    function widget($args , $instance) {
    	extract($args);
        $title[0] = isset($instance['title']) ? $instance['title'] : '';
        $body_content[0] = isset($instance['body_content']) ? $instance['body_content'] : '';
                
        $title[1] = isset($instance['title1']) ? $instance['title1'] : '';
        $title[2] = isset($instance['title2']) ? $instance['title2'] : '';
        $title[3] = isset($instance['title3']) ? $instance['title3'] : '';
        
        $icon[1] = isset($instance['icon1']) && !empty( $instance['icon1']) ? $instance['icon1'] : 'fa fa-cogs';
        $icon[2] = isset($instance['icon2']) && !empty( $instance['icon2']) ? $instance['icon2'] : 'fa fa-heartbeat';
        $icon[3] = isset($instance['icon3']) && !empty( $instance['icon3']) ? $instance['icon3'] : 'fa fa-paper-plane-o';
        
        $body_content[1] = isset($instance['body_content1']) ? $instance['body_content1'] : '';
        $body_content[2] = isset($instance['body_content2']) ? $instance['body_content2'] : '';
        $body_content[3] = isset($instance['body_content3']) ? $instance['body_content3'] : '';
        
        echo $before_widget;
        
        /**
		 * Widget Content
		 */
    ?>
        <section>
          <div class="container">
              <div class="row">
                  <div class="col-sm-12 text-center">
                      <h3 class="mb16"><?php echo $title[0]; ?></h3>
                      <p class="mb64"><?php echo $body_content[0]; ?></p>
                  </div>
              </div>
              <!--end of row-->
              <div class="row"><?php
                for( $i=1; $i<4; $i++ ){ 
                  if( $title[$i] != '' ) {?>
                    <div class="col-sm-4">
                        <div class="feature feature-1">
                            <div class="text-center">
                                <i class="<?php echo $icon[$i]; ?>"></i>
                                <h4><?php echo $title[$i]; ?></h4>
                            </div>
                            <p><?php echo $body_content[$i]; ?></p>
                        </div>
                        <!--end of feature-->
                    </div><?php
                  }
                }?>
              </div>
                <!--end of row-->
            </div>
            <!--end of container-->
          </section>


		<?php

		echo $after_widget;
    }


    function form($instance) {
        if(!isset($instance['title']) ) $instance['title']='';
        if(!isset($instance['body_content']) ) $instance['body_content']='';
        
        if(!isset($instance['title1']) ) $instance['title1']='';
        if(!isset($instance['title2']) ) $instance['title2']='';
        if(!isset($instance['title3']) ) $instance['title3']='';
        
        if(!isset($instance['icon1']) ) $instance['icon1']='';
        if(!isset($instance['icon2']) ) $instance['icon2']='';
        if(!isset($instance['icon3']) ) $instance['icon3']='';
       
        if(!isset($instance['body_content1'])) $instance['body_content1']='';        
        if(!isset($instance['body_content2'])) $instance['body_content2']='';        
        if(!isset($instance['body_content3'])) $instance['body_content3']='';        
    ?>

      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title ','flexible') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['title']); ?>"
                          name="<?php echo $this->get_field_name('title'); ?>"
                          id="<?php $this->get_field_id('title'); ?>"
                          class="widefat" />
      </p>      
      
      <p><label for="<?php echo $this->get_field_id('body_content'); ?>"><?php esc_html_e('Content ','flexible') ?></label>

      <textarea name="<?php echo $this->get_field_name('body_content'); ?>"
                          id="<?php $this->get_field_id('body_content'); ?>"
                          class="widefat"><?php echo esc_attr($instance['body_content']); ?></textarea>
      </p>
      
      
      <?php for ( $i=1; $i<4; $i++ ) { ?>
      <br>
      <b><?php echo sprintf( __( "Feature %s", 'flexible' ), $i ); ?></b>
      
      <p><label for="<?php echo $this->get_field_id('title'.$i); ?>"><?php esc_html_e('Title ','flexible') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['title'.$i]); ?>"
                          name="<?php echo $this->get_field_name('title'.$i); ?>"
                          id="<?php $this->get_field_id('title'.$i); ?>"
                          class="widefat" />
      </p>
      
      <p><label for="<?php echo $this->get_field_id('icon'.$i); ?>"><?php esc_html_e('Icon( Font Awsome ) ','flexible') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['icon'.$i]); ?>"
                          name="<?php echo $this->get_field_name('icon'.$i); ?>"
                          id="<?php $this->get_field_id('icon'.$i); ?>"
                          class="widefat" />
      </p>

      <p><label for="<?php echo $this->get_field_id('body_content'.$i); ?>"><?php esc_html_e('Content ','flexible') ?></label>

      <textarea name="<?php echo $this->get_field_name('body_content'.$i); ?>"
                          id="<?php $this->get_field_id('body_content'.$i); ?>"
                          class="widefat"><?php echo esc_attr($instance['body_content'.$i]); ?></textarea>
      </p><?php
      }
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
		$instance['body_content'] = ( ! empty( $new_instance['body_content'] ) ) ? esc_html( $new_instance['body_content'] ) : '';
		
        $instance['title1'] = ( ! empty( $new_instance['title1'] ) ) ? esc_html( $new_instance['title1'] ) : '';
        $instance['title2'] = ( ! empty( $new_instance['title2'] ) ) ? esc_html( $new_instance['title2'] ) : '';
        $instance['title3'] = ( ! empty( $new_instance['title3'] ) ) ? esc_html( $new_instance['title3'] ) : '';
		
        $instance['body_content1'] = ( ! empty( $new_instance['body_content1'] ) ) ? esc_html( $new_instance['body_content1'] ) : '';
        $instance['body_content2'] = ( ! empty( $new_instance['body_content2'] ) ) ? esc_html( $new_instance['body_content2'] ) : '';
        $instance['body_content3'] = ( ! empty( $new_instance['body_content3'] ) ) ? esc_html( $new_instance['body_content3'] ) : '';
        
        $instance['icon1'] = ( ! empty( $new_instance['icon1'] ) ) ? esc_html( $new_instance['icon1'] ) : '';
        $instance['icon2'] = ( ! empty( $new_instance['icon2'] ) ) ? esc_html( $new_instance['icon2'] ) : '';
        $instance['icon3'] = ( ! empty( $new_instance['icon3'] ) ) ? esc_html( $new_instance['icon3'] ) : '';
        
		return $instance;
	}
}

?>