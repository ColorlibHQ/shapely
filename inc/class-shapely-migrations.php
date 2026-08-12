<?php
/**
 * Upgrade routines.
 *
 * 1.3.0 reaches existing sites through auto-update, so the upgrade has to be
 * invisible: a site owner who never opens the customizer should not be able to
 * tell it happened.
 *
 * Worth stating plainly what is *not* here. No theme mod was renamed in 1.3.0
 * -- all 52 customizer settings keep the names they had, so saved values are
 * read by exactly the code that wrote them and need no mapping. The migration
 * surface is only the handful of options the retired Epsilon framework owned.
 *
 * @package Shapely
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Shapely_Migrations' ) ) :
	/**
	 * Runs one-time upgrade steps.
	 */
	class Shapely_Migrations {

		/**
		 * Stores the version the migrations last ran for.
		 */
		const VERSION_OPTION = 'shapely_migrated_version';

		/**
		 * Where the welcome screen keeps dismissed action ids.
		 *
		 * Duplicated from Shapely_Welcome::DISMISSED_OPTION rather than
		 * referenced, so migrations do not drag the admin-only class into every
		 * front-end request. Keep the two in step.
		 */
		const DISMISSED_OPTION = 'shapely_dismissed_actions';

		/**
		 * Hook the check.
		 */
		public static function init() {
			add_action( 'after_setup_theme', array( __CLASS__, 'maybe_migrate' ), 20 );
		}

		/**
		 * Run any migration the stored version has not seen yet.
		 */
		public static function maybe_migrate() {
			$from = get_option( self::VERSION_OPTION, '' );

			if ( SHAPELY_VERSION === $from ) {
				return;
			}

			/*
			 * '' means either a brand new site or an upgrade from a release
			 * before this option existed (anything up to 1.2.21). Those are told
			 * apart by looking for evidence of prior use, below.
			 */
			if ( version_compare( (string) $from, '1.3.0', '<' ) ) {
				self::migrate_to_130();
			}

			update_option( self::VERSION_OPTION, SHAPELY_VERSION, false );
		}

		/**
		 * Has this site been used before, or is it a fresh activation?
		 *
		 * An upgraded site should not suddenly be shown the onboarding notice it
		 * either dismissed years ago or never needed.
		 *
		 * @return bool
		 */
		private static function looks_established() {
			// Any Epsilon-era state at all means the theme has run here before.
			foreach ( array( 'shapely_actions_left', 'shapely_plugins_left', 'shapely_show_required_actions', 'shapely_show_required_plugins' ) as $legacy ) {
				if ( false !== get_option( $legacy, false ) ) {
					return true;
				}
			}

			// Or any saved customizer value.
			$mods = get_theme_mods();
			if ( is_array( $mods ) ) {
				unset( $mods['0'] );
				if ( ! empty( $mods ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * 1.3.0: carry the retired framework's state into its replacement.
		 */
		private static function migrate_to_130() {
			/*
			 * Epsilon stored dismissals as an id => false map in
			 * shapely_actions_left; the welcome screen keeps a flat list of
			 * dismissed ids. Without this, every action a site owner had already
			 * dealt with would reappear after the update.
			 *
			 * The legacy options are read but not deleted. They are inert, and
			 * leaving them means a downgrade to 1.2.x still finds its own state.
			 */
			$legacy = get_option( 'shapely_actions_left', array() );

			if ( is_array( $legacy ) && ! empty( $legacy ) ) {
				$dismissed = (array) get_option( self::DISMISSED_OPTION, array() );

				foreach ( $legacy as $id => $still_outstanding ) {
					// false = the user dismissed it.
					if ( ! $still_outstanding && ! in_array( $id, $dismissed, true ) ) {
						$dismissed[] = sanitize_key( $id );
					}
				}

				update_option( self::DISMISSED_OPTION, array_values( array_unique( $dismissed ) ), false );
			}

			/*
			 * Only greet genuinely new installs. On an established site the
			 * notice would be the one visible sign of an update that is meant to
			 * change nothing.
			 */
			if ( self::looks_established() ) {
				update_option( 'shapely_welcome_notice_dismissed', 1, false );
			}
		}
	}
endif;

Shapely_Migrations::init();
