<?php

/**
 * Shapely Top Posts Widget
 * Shapely Theme
 */
class shapely_recent_posts extends WP_Widget {

  function __construct() {

    $widget_ops = array('classname' => 'shapely-recent-posts col-sm-12 text-center', 'description' => esc_html__("Widget to show recent posts with thumbnails", 'shapely'));
    parent::__construct('shapely_recent_posts', esc_html__('[Shapely] Recent Posts', 'shapely'), $widget_ops);
  }

  function widget($args, $instance) {
    extract($args);
    $title = isset($instance['title']) ? $instance['title'] : esc_html__('recent Posts', 'shapely');
    $limit = isset($instance['limit']) ? $instance['limit'] : 5;

    echo $before_widget;
    ?>
    <section>
    <?php
    echo $before_title;
    echo $title;
    echo $after_title;

    /**
     * Widget Content
     */
    ?>

    <!-- recent posts -->
    <div class="recent-posts-wrapper nolist">

      <?php
      $featured_args = array(
          'posts_per_page' => $limit,
          'post_type' => 'post',
          'ignore_sticky_posts' => 1
      );

      $featured_query = new WP_Query($featured_args);
      $bootstrapColWidth = floor(12/$featured_query->post_count);
      if ($featured_query->have_posts()) : ?>

        <ul class="link-list recent-posts"><?php

          while ($featured_query->have_posts()) : $featured_query->the_post(); ?>

            <?php if (get_the_content() != '') : ?>

              <!-- content -->
              <li class="post-content col-sm-<?php echo $bootstrapColWidth; ?>">
                <a href="<?php echo get_permalink(); ?>">
                  <?php if ( has_post_thumbnail() ) {
                  	the_post_thumbnail();
                  } ?>
                  <?php echo get_the_title(); ?>
                </a>
                <span class="date"><?php echo get_the_date('d M , Y'); ?></span>
              </li>
              <!-- end content -->

            <?php endif; ?>

          <?php endwhile; ?>
        </ul><?php

      endif;
      wp_reset_query();
      ?>

    </div> <!-- end posts wrapper -->
  </section>
    <?php
    echo $after_widget;
  }

  function form($instance) {

    if (!isset($instance['title']))
      $instance['title'] = esc_html__('recent Posts', 'shapely');
    if (!isset($instance['limit']))
      $instance['limit'] = 5;
    ?>

    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title', 'shapely') ?></label>

      <input  type="text" value="<?php echo esc_attr($instance['title']); ?>"
              name="<?php echo $this->get_field_name('title'); ?>"
              id="<?php $this->get_field_id('title'); ?>"
              class="widefat" />
    </p>

    <p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php esc_html_e('Limit Posts Number', 'shapely') ?></label>

      <input  type="text" value="<?php echo esc_attr($instance['limit']); ?>"
              name="<?php echo $this->get_field_name('limit'); ?>"
              id="<?php $this->get_field_id('limit'); ?>"
              class="widefat" />
    <p>

    <?php
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

		return $instance;
	}

}
?>
