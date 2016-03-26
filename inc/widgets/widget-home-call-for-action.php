<?php

/**
 * Homepage Call for Action section Widget
 * Shapely Theme
 */
class shapely_home_CFA extends WP_Widget
{
    function __construct(){

        $widget_ops = array('classname' => 'shapely_home_CFA','description' => esc_html__( "[Shapely] Call for Action Section" ,'shapely') );
        parent::__construct('shapely_home_CFA', esc_html__('[Shapely] Call for Action Section For FrontPage','shapely'), $widget_ops);
    }

    function widget($args , $instance) {
    	extract($args);
        $title = isset($instance['title']) ? $instance['title'] : '';
        $button = isset($instance['button']) ? $instance['button'] : '';
        $button_link = isset($instance['button_link']) ? $instance['button_link'] : '';

        echo $before_widget;

        /**
		 * Widget Content
		 */
    ?>
    <?php if( $title != '' ): ?>
        <section class="cfa-section bg-secondary">
              <div class="container">
                <div class="row">
                  <div class="col-sm-12 text-center p0">
                    <div class="overflow-hidden">
                      <div class="col-sm-9">
                          <h3 class="cfa-text"><?php echo $title; ?></h3>
                      </div>
                      <div class="col-sm-3">
                          <a href="<?php echo $button_link; ?>" alt="<?php echo $title; ?>" class="mb0 btn btn-lg btn-filled cfa-button"><?php echo $button; ?></a>
                      </div>
                    </div>
                  </div>
                </div>
                <!--end of row-->
              </div>
              <!--end of container-->
        </section><?php
      endif;

      echo $after_widget;
    }


    function form($instance) {
        if(!isset($instance['title']) ) $instance['title']='';
        if(!isset($instance['button'])) $instance['button']='';
        if(!isset($instance['button_link'])) $instance['button_link']='';
    ?>

      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Callout Text ','shapely') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['title']); ?>"
                          name="<?php echo $this->get_field_name('title'); ?>"
                          id="<?php $this->get_field_id('title'); ?>"
                          class="widefat" />
      </p>

      <p><label for="<?php echo $this->get_field_id('button'); ?>"><?php esc_html_e('Button Text ','shapely') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['button']); ?>"
                          name="<?php echo $this->get_field_name('button'); ?>"
                          id="<?php $this->get_field_id('button'); ?>"
                          class="widefat" />
      </p>

      <p><label for="<?php echo $this->get_field_id('button_link'); ?>"><?php esc_html_e('Button Link ','shapely') ?></label>

      <input type="text" value="<?php echo esc_url($instance['button_link']); ?>"
                          name="<?php echo $this->get_field_name('button_link'); ?>"
                          id="<?php $this->get_field_id('button_link'); ?>"
                          class="widefat" />
      </p><?php
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
		$instance['button'] = ( ! empty( $new_instance['button'] ) ) ? esc_html( $new_instance['button'] ) : '';
		$instance['button_link'] = ( ! empty( $new_instance['button_link'] ) ) ? esc_url( $new_instance['button_link'] ) : '';

		return $instance;
	}

}

?>
