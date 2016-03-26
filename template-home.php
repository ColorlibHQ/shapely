<?php
/**
 * Template Name: Home Page
 *
 * Displays the Home page with Parallax effects.
 *
 */
?>

<?php get_header(); ?>

<div class="col-md-12"><?php
    if ( !function_exists('dynamic_sidebar')  || !dynamic_sidebar( 'sidebar-home' ) ):  ?>  
        <div class="container p24 wp-caption-text"><h5><?php _e('This is the "Home Sidebar Section", add some widgets to it to change it.', 'shapely'); ?></h5>  </<div>
    <?php endif; ?>
    
</div>

<?php get_footer(); ?>