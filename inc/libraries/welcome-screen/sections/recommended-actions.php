<?php
/**
 * Template part for the recommended actions tab in welcome screen
 *
 * @package Epsilon Framework
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Actions required
 */

wp_enqueue_style( 'plugin-install' );
wp_enqueue_script( 'plugin-install' );
wp_enqueue_script( 'updates' );
$hooray = true;
?>

<div class="feature-section action-required demo-import-boxed" id="plugin-filter">
	<?php
	if ( ! empty( $this->actions ) ) {
		$actions_left = $this->get_actions_left();
		foreach ( $this->actions as $key => $value ) {
			$hidden = false;

			if ( isset( $actions_left[ $value['id'] ] ) && null !== $actions_left[ $value['id'] ] ) {
				$hidden = $actions_left[ $value['id'] ];
			}

			if ( $value['check'] ) {
				continue;
			}

			?>
			<div class="action-required-box">
				<span data-action="<?php echo $hidden ? 'visible' : 'hidden'; ?>" class="dashicons <?php echo $hidden ? 'dashicons-hidden' : 'dashicons-visibility'; ?> required-action-button" id="<?php echo esc_attr( $value['id'] ); ?>">
				</span>

				<?php if ( ! empty( $value['title'] ) ) { ?>
					<h3> <?php echo esc_html( $value['title'] ); ?></h3>
				<?php } ?>

				<?php if ( ! empty( $value['description'] ) ) { ?>
					<p>
						<?php echo wp_kses_post( $value['description'] ); ?>
						<br/>

					</p>
					<?php if ( ! empty( $value['help'] ) ) { ?>

						<?php

						if ( is_array( $value['help'] ) && is_a( $value['help'][0], 'Epsilon_Import_Data' ) ) {
							$class = $value['help'][0];
							$func  = $value['help'][1];

							echo $class->$func();
						} else {
							echo $value['help'];
						}
						?>

					<?php } ?>

				<?php } ?>

				<?php
				if ( ! empty( $value['plugin_slug'] ) ) {
					$plugin = $this->check_plugin( $value['plugin_slug'] );
					$link   = $this->create_plugin_link( $plugin['needs'], $value['plugin_slug'] );

					?>
					<p class="plugin-card-<?php echo esc_attr( $value['plugin_slug'] ); ?> action_button <?php echo ( 'install' !== $plugin['needs'] && $plugin['active'] ) ? 'active' : ''; ?>">
						<a data-slug="<?php echo esc_attr( $value['plugin_slug'] ); ?>" class="<?php echo esc_attr( $plugin['class'] ); ?>" href="<?php echo esc_url( $plugin['url'] ); ?>"> <?php echo esc_html( $plugin['label'] ); ?> </a>
					</p>
				<?php } ?>

				<?php $hooray = false; ?>
			</div>
		<?php } /* End foreach(). */ ?>

	<?php } /* End if(). */ ?>

	<?php if ( $hooray ) { ?>
		<span class="hooray"><?php echo esc_html__( 'Hooray! There are no required actions for you right now.', 'epsilon-framework' ); ?> </span>
	<?php } ?>

</div>
