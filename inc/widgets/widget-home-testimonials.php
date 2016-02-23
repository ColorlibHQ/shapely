<?php

/**
 * Homepage parralax section Widget
 * Flexible Theme
 */
class flexible_home_testimonial extends WP_Widget
{
    function __construct(){

        $widget_ops = array('classname' => 'flexible_home_testimonial','description' => esc_html__( "Flexible Testimonial Widget Section" ,'flexible') );
        parent::__construct('flexible_home_testimonial', esc_html__('Flexible Testimonial Widget Section','flexible'), $widget_ops);
    }

    function widget($args , $instance) {
    	extract($args);
        $title = isset($instance['title']) ? $instance['title'] : esc_html__('People just like you are already loving Colorlib', 'flexible');
        $limit = isset($instance['limit']) ? $instance['limit'] : 5;
        $image_src = isset($instance['image_src']) ? $instance['image_src'] : '';

        echo $before_widget;

        /**
         * Widget Content
         */
        ?>

      <?php
      $testimonial_args = array(
          'post_type' => 'jetpack-testimonial',
          'posts_per_page' => $limit,
          'ignore_sticky_posts' => 1
      );

      $testimonial_query = new WP_Query($testimonial_args);

      if ($testimonial_query->have_posts()) : ?>
        <section class="parallax-section testimonial-section">
              <div class="parallax-window" data-parallax="scroll" data-image-src="<?php echo $image_src; ?>" style="height: 500px;">
                  <div class="container align-transform">
                      <div class="parallax-text image-bg testimonial">
                          <div class="row">
                              <div class="col-sm-12 text-center">
                                  <h3><?php echo $title; ?></h3>
                              </div>
                          </div>
                          <!--end of row-->
                          <div class="row">
                              <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                                  <div class="text-slider slider-arrow-controls text-center relative">
                                      <ul class="slides" style="overflow: hidden;"><?php
                                        while ($testimonial_query->have_posts()) : $testimonial_query->the_post(); ?>
                                          <?php if ( get_the_title() != '' ) : ?>
                                          <li>
                                            <p><?php the_content(); ?></p>
                                              <div class="testimonial-author-section"><?php
                                                  the_post_thumbnail( 'thumbnail', array( 'class' => 'testimonial-img')); ?>
                                                  
                                                  <div class="testimonial-author">
                                                      <strong><?php echo get_the_title(); ?></strong>
                                                  </div>
                                              </div>
                                          </li>
                                          <?php endif; ?>

                                        <?php endwhile; ?>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                          <!--end of row-->
                      </div>
                  </div>
              <!--end of container-->
              </div>
            </section><?php
          endif;
          wp_reset_postdata();
		echo $after_widget;
    }


    function form($instance) {
        if(!isset($instance['title']) ) $instance['title']='';
        if(!isset($instance['limit']) ) $instance['limit']='';
        if(!isset($instance['image_src'])) $instance['image_src']='';
        
    ?>

      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title ','flexible') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['title']); ?>"
                          name="<?php echo $this->get_field_name('title'); ?>"
                          id="<?php $this->get_field_id('title'); ?>"
                          class="widefat" />
      </p>
      
      <p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php esc_html_e('Limit ','flexible') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['limit']); ?>"
                          name="<?php echo $this->get_field_name('limit'); ?>"
                          id="<?php $this->get_field_id('limit'); ?>"
                          class="widefat" />
      </p>

      <p>
            <label for="<?php echo $this->get_field_name( 'image_src' ); ?>"><?php _e( 'Background Parallax Image:', 'flexible' ); ?></label>
            <input name="<?php echo $this->get_field_name( 'image_src' ); ?>" id="<?php echo $this->get_field_id( 'image_src' ); ?>" class="widefat image_src" type="text" size="36"  value="<?php echo esc_url( $instance['image_src'] ); ?>" /><br><br>
            <input class="upload_image_button button button-primary" type="button" value="Upload Image" />
            <img class="image_demo" width="100px" height="100px" style="margin-left: 20px; vertical-align: top;" src="<?php echo esc_url( $instance['image_src'] ); ?>" />
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
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) && is_numeric( $new_instance['limit'] )  ) ? esc_html( $new_instance['limit'] ) : '';
		$instance['image_src'] = ( ! empty( $new_instance['image_src'] ) ) ? esc_url( $new_instance['image_src'] ) : '';

		return $instance;
	}

}

?>
