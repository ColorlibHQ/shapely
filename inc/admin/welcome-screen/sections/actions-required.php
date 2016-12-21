<?php
/**
 * Actions required
 */

wp_enqueue_style( 'plugin-install' );
wp_enqueue_script( 'plugin-install' );
wp_enqueue_script( 'updates' );
?>

<div class="feature-section action-required demo-import-boxed" id="plugin-filter">

	<?php
	global $shapely_required_actions;
	if ( ! empty( $shapely_required_actions ) ):
		$shapely_show_required_actions = get_option( "shapely_show_required_actions" );
		$hooray = true;

		foreach ( $shapely_required_actions as $shapely_required_action_key => $shapely_required_action_value ):
			$hidden = false;
			if ( @$shapely_show_required_actions[ $shapely_required_action_value['id'] ] === false ) {
				$hidden = true;
			}
			if ( @$shapely_required_action_value['check'] ) {
				continue;
			}
			?>
			<div class="shapely-action-required-box">
				<?php if ( ! $hidden ): ?>
					<span data-action="dismiss" class="dashicons dashicons-visibility shapely-required-action-button"
					      id="<?php echo esc_attr( $shapely_required_action_value['id'] ); ?>"></span>
				<?php else: ?>
					<span data-action="add" class="dashicons dashicons-hidden shapely-required-action-button"
					      id="<?php echo esc_attr( $shapely_required_action_value['id'] ); ?>"></span>
				<?php endif; ?>
				<h3><?php if ( ! empty( $shapely_required_action_value['title'] ) ): echo esc_html( $shapely_required_action_value['title'] ); endif; ?></h3>
				<p>
					<?php if ( ! empty( $shapely_required_action_value['description'] ) ): echo esc_html( $shapely_required_action_value['description'] ); endif; ?>
					<?php if ( ! empty( $shapely_required_action_value['help'] ) ): echo '<br/>' . wp_kses_post( $shapely_required_action_value['help'] ); endif; ?>
				</p>
				<?php
				if ( ! empty( $shapely_required_action_value['external'] ) && file_exists( $shapely_required_action_value['external'] ) ) {
					require_once $shapely_required_action_value['external'];
				}
				?>
				<?php
				if ( ! empty( $shapely_required_action_value['plugin_slug'] ) ) {
					$active = $this->check_active( $shapely_required_action_value['plugin_slug'] );
					$url    = $this->create_action_link( $active['needs'], $shapely_required_action_value['plugin_slug'] );
					$label  = '';

					switch ( $active['needs'] ) {
						case 'install':
							$class = 'install-now button';
							$label = __( 'Install', 'shapely' );
							break;
						case 'activate':
							$class = 'activate-now button button-primary';
							$label = __( 'Activate', 'shapely' );
							break;
						case 'deactivate':
							$class = 'deactivate-now button';
							$label = __( 'Deactivate', 'shapely' );
							break;
					}

					?>
					<p class="plugin-card-<?php echo esc_attr( $shapely_required_action_value['plugin_slug'] ) ?> action_button <?php echo ( $active['needs'] !== 'install' && $active['status'] ) ? 'active' : '' ?>">
						<a data-slug="<?php echo esc_attr( $shapely_required_action_value['plugin_slug'] ) ?>"
						   class="<?php echo esc_attr( $class ); ?>"
						   href="<?php echo esc_url( $url ) ?>"> <?php echo esc_html( $label ) ?> </a>
					</p>
					<?php
				};
				?>
			</div>
			<?php
			$hooray = false;
		endforeach;
	endif;


	if ( $hooray ):
		echo '<span class="hooray">' . __( 'Hooray! There are no required actions for you right now.', 'shapely' ) . '</span>';
	endif;
	?>

</div>
