<?php
/**
 * Theme Setup.
 *
 * @package Theme
 * @since 1.0.0
 */

namespace Theme\Setup;

add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_features' );
/**
 * Register theme features.
 *
 * @since 1.0.0
 * @ignore
 */
function register_features() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'comment-list',
			'search-form',
			'gallery',
			'caption',
		)
	);

	register_nav_menus(
		array(
			'primary_menu' => __( 'Primary Menu', 'theme' ),
		)
	);
}

add_action( 'widgets_init', __NAMESPACE__ . '\\register_widget_areas', 5 );
/**
 * Register widget areas.
 *
 * @since 1.0.0
 * @ignore
 */
function register_widget_areas() {
	register_sidebar(
		array(
			'id'   => 'primary',
			'name' => __( 'Primary Sidebar', 'theme' ),
		)
	);
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets', 5 );
/**
 * Register scripts and styles.
 *
 * @since 1.0.0
 * @ignore
 */
function enqueue_assets() {
	// Default style file.
	wp_enqueue_style( 'theme', get_stylesheet_uri() ); // phpcs:ignore

	// Comment-reply.
	if ( (bool) get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
