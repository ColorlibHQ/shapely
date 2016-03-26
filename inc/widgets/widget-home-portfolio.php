<?php

/**
 * Homepage parralax section Widget
 * Shapely Theme
 */
class shapely_home_portfolio extends WP_Widget
{
    function __construct(){

        $widget_ops = array('classname' => 'shapely_home_portfolio','description' => esc_html__( "Shapely Porfolio for Home Widget Section" ,'shapely') );
        parent::__construct('shapely_home_portfolio', esc_html__('[Shapely] Porfolio for Home Widget Section','shapely'), $widget_ops);
    }

    function widget($args , $instance) {
      extract($args);
        $title = isset($instance['title']) ? $instance['title'] : '';
        $body_content = isset($instance['body_content']) ? $instance['body_content'] : '';

        if (post_type_exists( 'jetpack-portfolio' ) ) {

        echo $before_widget;

        /**
     * Widget Content
     */
    ?>
        <section class="projects bg-dark pb0">
              <div class="container">
                <div class="col-sm-12 text-center">
                    <h3 class="mb32"><?php echo $title; ?></h3>
                    <p class="mb40"><?php echo $body_content; ?></p>
                </div>
              </div><?php

              $portfolio_args = array(
                  'post_type' => 'jetpack-portfolio',
                  'posts_per_page' => 10,
                  'ignore_sticky_posts' => 1
              );

              $portfolio_query = new WP_Query($portfolio_args);

              if ( $portfolio_query->have_posts() ) : ?>

                <div class="row masonry-loader fixed-center fadeOut">
                    <div class="col-sm-12 text-center">
                        <div class="spinner"></div>
                    </div>
                </div>
                <div class="row masonry masonryFlyIn fadeIn"><?php

                  while ($portfolio_query->have_posts()) : $portfolio_query->the_post();

                  if( has_post_thumbnail() ){ ?>
                    <div class="col-md-3 col-sm-6 masonry-item project fadeIn">
                        <div class="image-tile inner-title hover-reveal text-center">
                          <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                            <?php the_post_thumbnail( 'full' ); ?>
                            <div class="title"><?php
                              the_title('<h5 class="mb0">','</h5>');

                              $project_types = wp_get_post_terms(get_the_ID(), 'jetpack-portfolio-type', array("fields" => "names"));
                              if( !empty( $project_types ) ){
                                echo '<span>'.implode(' / ',$project_types).'</span>';
                              }?>
                            </div>
                          </a>
                        </div>
                    </div><?php
                  }
                  endwhile; ?>
                </div><?php
              endif;
              wp_reset_postdata(); ?>
      </section>


    <?php

    echo $after_widget;

        }
    }


    function form($instance) {
        if(!isset($instance['title']) ) $instance['title']='';
        if(!isset($instance['body_content'])) $instance['body_content']='';
    ?>

      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title ','shapely') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['title']); ?>"
                          name="<?php echo $this->get_field_name('title'); ?>"
                          id="<?php $this->get_field_id('title'); ?>"
                          class="widefat" />
      </p>

      <p><label for="<?php echo $this->get_field_id('body_content'); ?>"><?php esc_html_e('Content ','shapely') ?></label>

      <textarea name="<?php echo $this->get_field_name('body_content'); ?>"
                          id="<?php $this->get_field_id('body_content'); ?>"
                          class="widefat"><?php echo esc_attr($instance['body_content']); ?></textarea>
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
    $instance['body_content'] = ( ! empty( $new_instance['body_content'] ) ) ? esc_html( $new_instance['body_content'] ) : '';

    return $instance;
  }

}

?>
