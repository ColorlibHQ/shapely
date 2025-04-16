<?php
/**
 * Template part for the recommended plugins tab in welcome screen
 *
 * @package Epsilon Framework
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
wp_enqueue_style( 'plugin-install' );
wp_enqueue_script( 'plugin-install' );
wp_enqueue_script( 'updates' );
add_thickbox();
?>

<div class="feature-section recommended-plugins three-col demo-import-boxed" id="plugin-filter">
	<?php
	foreach ( $this->plugins as $plugin => $prop ) {
		$info = $this->get_plugin_information( $plugin );
		?>
		<div class="col plugin_box">

			<?php if ( isset( $info['info']->recommended ) && $info['info']->recommended ) { ?>
				<span class="recommended"><?php echo esc_html__( 'Recommended', 'epsilon-framework' ); ?></span>
			<?php } ?>

			<?php if ( ! empty( $info['icon'] ) ) { ?>
				<img src="<?php echo esc_attr( $info['icon'] ); ?>" alt="plugin box image">
			<?php } ?>
			
			<?php if ( ! is_wp_error( $info['info'] ) ) { ?>
				<span class="version"><?php echo esc_html__( 'Version:', 'epsilon-framework' ); ?><?php echo esc_html( $info['info']->version ); ?></span>
				<span class="separator">|</span> <?php echo wp_kses_post( $info['info']->author ); ?>
			<?php } ?>
			
			<div class="action_bar <?php echo ( 'install' !== $info['needs'] && $info['active'] ) ? 'active' : ''; ?>">
				<span class="plugin_name"><?php echo ( 'install' !== $info['needs'] && $info['active'] ) ? 'Active: ' : ''; ?><?php echo is_wp_error( $info['info'] ) ? esc_html( $plugin ) : esc_html( $info['info']->name ); ?></span>
			</div>
			<span class="plugin-card-<?php echo esc_attr( $plugin ); ?> action_button <?php echo ( 'install' !== $info['needs'] && $info['active'] ) ? 'active' : ''; ?>">
				<a data-slug="<?php echo esc_attr( $plugin ); ?>" class="<?php echo esc_attr( $info['class'] ); ?>" href="<?php echo esc_url( $info['url'] ); ?>"> <?php echo esc_attr( $info['label'] ); ?> </a>
			</span>
		</div>
	<?php } ?>
</div>
