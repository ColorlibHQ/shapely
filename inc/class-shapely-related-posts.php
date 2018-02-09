<?php

/**
 * Class Shapely_Related_Posts
 *
 * This file does the social sharing handling for the Muscle Core Lite Framework
 *
 * @author           Colorlib
 * @copyright    (c) Copyright by Colrolib
 * @link             https://www.colorlib.com
 * @package          Shapely
 */

if ( ! function_exists( 'shapely_call_related_posts_class' ) ) {
	/**
	 *
	 * Gets called only if the "display related posts" option is checked
	 * in the back-end
	 *
	 * @since   1.0.0
	 *
	 */
	function shapely_call_related_posts_class() {
		$display_related_blog_posts = get_theme_mod( 'related_posts_area', true );

		if ( $display_related_blog_posts ) {

			// instantiate the class & load everything else
			Shapely_Related_Posts::get_instance();
		}
	}

	add_action( 'wp_loaded', 'shapely_call_related_posts_class' );
}


if ( ! class_exists( 'Shapely_Related_Posts' ) ) {

	/**
	 * Class Shapely_Related_Posts
	 */
	class Shapely_Related_Posts {

		/**
		 * @var Singleton The reference to *Singleton* instance of this class
		 */
		private static $instance;

		/**
		 * Constructor
		 */
		protected function __construct() {

			$related_posts = get_theme_mod( 'related_posts_area', true );
			if ( $related_posts ) {
				add_action( 'shapely_single_after_article', array( $this, 'output_related_posts' ), 2 );
			}

		}

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @return Singleton The *Singleton* instance.
		 */
		public static function get_instance() {
			if ( null === static::$instance ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * Private clone method to prevent cloning of the instance of the
		 * *Singleton* instance.
		 *
		 * @return void
		 */
		private function __clone() {
		}

		/**
		 * Private unserialize method to prevent unserializing of the *Singleton*
		 * instance.
		 *
		 * @return void
		 */
		private function __wakeup() {
		}


		/**
		 * Get related posts by category
		 *
		 * @param  integer $post_id      current post id
		 * @param  integer $number_posts number of posts to fetch
		 *
		 * @return object                  object with posts info
		 */
		public function get_related_posts( $post_id, $number_posts = - 1 ) {

			$related_postquery = new WP_Query();
			$args              = '';

			if ( 0 == $number_posts ) {
				return $related_postquery;
			}

			$args = wp_parse_args(
				$args, array(
					'category__in'        => wp_get_post_categories( $post_id ),
					'ignore_sticky_posts' => 0,
					'posts_per_page'      => $number_posts,
					'post__not_in'        => array( $post_id ),
				)
			);

			if ( is_singular( 'jetpack-portfolio' ) ) {
				unset( $args['category__in'] );
				$args['post_type'] = 'jetpack-portfolio';

				$terms_args = array(
					'fields' => 'ids',
				);
				$types      = wp_get_object_terms( get_the_ID(), 'jetpack-portfolio-type', $terms_args );
				$tags       = wp_get_object_terms( get_the_ID(), 'jetpack-portfolio-tag', $terms_args );

				$tax_query = array();

				if ( ! empty( $types ) ) {
					array_push(
						$tax_query, array(
							'taxonomy' => 'jetpack-portfolio-type',
							'field'    => 'term_id',
							'terms'    => $types,
						)
					);
				}

				if ( ! empty( $tags ) ) {
					array_push(
						$tax_query, array(
							'taxonomy' => 'jetpack-portfolio-tag',
							'field'    => 'term_id',
							'terms'    => $tags,
						)
					);
				}

				if ( ! empty( $tax_query ) ) {
					$args['tax_query'] = $tax_query;
				}
			}

			$related_postquery = new WP_Query( $args );

			// reset post query
			wp_reset_postdata();

			return $related_postquery;
		}

		/**
		 * Render related posts carousel
		 *
		 * @return string                    HTML markup to display related posts
		 **/
		function output_related_posts() {

			if ( is_singular( 'jetpack-portfolio' ) ) {
				if ( ! get_theme_mod( 'related_projects_area', true ) ) {
					return;
				}
			}

			// Check if related posts should be shown
			$related_posts = $this->get_related_posts( get_the_ID(), get_option( 'posts_per_page' ) );

			if ( 0 == $related_posts->post_count ) {
				return false;
			}

			echo '<div class="shapely-related-posts">';

			// Number of posts to show / view
			$limit      = get_theme_mod( 'shapely_howmany_blog_posts', 3 );
			$show_title = get_theme_mod( 'shapely_enable_related_title_blog_posts', true );
			$show_date  = get_theme_mod( 'shapely_enable_related_date_blog_posts', false );
			$auto_play  = get_theme_mod( 'shapely_autoplay_blog_posts', true );

			echo '<div class="row">';

			/*
			 * Heading
			 */
			echo '<div class="col-lg-11 col-sm-10 col-xs-12 shapely-related-posts-title">';
			if ( is_singular( 'jetpack-portfolio' ) ) {
				echo '<h3><span>' . esc_html__( 'Related projects', 'shapely' ) . '</span></h3>';
			} else {
				echo '<h3><span>' . esc_html__( 'Related articles ', 'shapely' ) . '</span></h3>';
			}
			echo '</div>';

			echo '</div><!--/.row-->';

			/*
			 * Arrows
			 */
			echo '<div class="shapely-carousel-navigation hidden-xs">';
			echo '<ul class="shapely-carousel-arrows clearfix">';
			echo '<li><a href="#" class="shapely-owl-prev fa fa-angle-left"></a></li>';
			echo '<li><a href="#" class="shapely-owl-next fa fa-angle-right"></a></li>';
			echo '</ul>';
			echo '</div>';

			echo sprintf(
				'<div class="owlCarousel owl-carousel owl-theme" data-slider-id="%s" id="owlCarousel-%s" 
			data-slider-items="%s" 
			data-slider-speed="400" data-slider-auto-play="%s" data-slider-navigation="false">', get_the_ID(), get_the_ID(), absint( $limit ), esc_html( $auto_play )
			);

			// Loop through related posts
			while ( $related_posts->have_posts() ) {
				$related_posts->the_post();

				echo '<div class="item">';
				if ( has_post_thumbnail( $related_posts->post->ID ) ) {
					echo '<a href="' . esc_url( get_the_permalink() ) . '" class="related-item-thumbnail" style="background-image: url( ' . get_the_post_thumbnail_url( $related_posts->post->ID, 'shapely-grid' ) . ' )">' . get_the_post_thumbnail( $related_posts->post->ID, 'shapely-grid' ) . '</a>';
				} else {
					echo '<a href="' . esc_url( get_the_permalink() ) . '" class="related-item-thumbnail" style="background-image: url( ' . get_template_directory_uri() . '/assets/images/placeholder.jpg )"><img class="wp-post-image" alt="" src="' . get_template_directory_uri() . '/assets/images/placeholder.jpg" /></a>';
				}

				if ( $show_title ) {
					echo '<div class="shapely-related-post-title">';

					# Post Title
					echo '<a href="' . esc_url( get_the_permalink() ) . '">' . wp_trim_words( get_the_title(), 5 ) . '</a>';
					echo '</div>';

				}

				if ( $show_date ) {

					echo '<div class="shapely-related-posts-date">';

					#Post Date
					echo esc_html( get_the_date() );

					echo '</div>';
				}

				echo '</div><!--/.item-->';
			}

			echo '</div><!--/.owlCarousel-->';
			echo '</div><!--/.mt-related-posts-->';

			wp_reset_postdata();
		}
	}
}// End if().
