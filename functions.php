<?php
/**
 * Functions and definitions
 *
 * @package Theme
 * @since 1.0.0
 */

// phpcs:disable
use function Theme\Markup\{
  change_markup,
  change_markup_filter,
  remove_markup,
  restore_markup,
  add_attributes,
  add_attributes_filter,
  remove_attributes,
  restore_attributes,
  add_before_action,
  add_prepend_action,
  add_append_action,
  add_after_action,
  is_markup_open,
};

define( 'THEME_NAME', 'Theme' );
define( 'THEME_SLUG', 'theme_' );
/**
 * If true, all markup ID's will be added
 * in the frontend with an attribute
 * called `data-markup-id`.
 */
define( 'THEME_DEBUG_MARKUP', false );

// Theme files.
require_once get_theme_file_path( 'includes/class-custom-nav-menu.php' );
require_once get_theme_file_path( 'includes/setup.php' );
require_once get_theme_file_path( 'includes/markup.php' );

/**
 * To use all the benefits of this Markup System,
 * the only thing needed is the `Markup ID`.
 *
 * Two ways to find it:
 * 1. The first argument of all called functions is the Markup ID.
 * 2. By enabling `THEME_DEBUG_MARKUP`, the ID's will be visible with Browser Inspector.
 *
 * comment/uncomment to see changes
 */


/**
 * 1. Ability to change the tag name of a markup.
 *
 * The target markup ID will be `main`
 * and this markup is inside `header.php` file.
 *
 * - Simple way with direct value passed to helper
 * - Advanced way with conditional + callback.
 */

//change_markup( 'main', 'section' );

//change_markup_filter( 'main', function ( $tag ) {
//	$condition = true;
//	return $condition ? 'div' : $tag;
//} );


/**
 * 2. Remove a markup from displaying.
 */

//remove_markup( 'main' );


/**
 * 3. Restore the original tag name.
 *
 * This will remove any change done (like above)
 * on a specific markup ID.
 *
 * If it's removed, it will bring back again.
 */

//restore_markup( 'main' );


/**
 * 4. Add Attributes
 *
 * - Simple way with direct value passed to helper
 * - Advanced way with conditional + callback.
 */
//add_attributes(
//	'main',
//	array(
//		'data-from'  => 'functions.php',
//		'data-using' => 'markup-hooks',
//	)
//);

//add_attributes_filter(
//	'main',
//	function ( $attributes ) {
//		if ( strpos( $attributes['class'], 'site-main' ) !== false ) {
//			$attributes['class'] .= ' site-main--wider';
//		}
//		return $attributes;
//	}
//);


/**
 * 5. Remove attributes
 */

//remove_attributes( 'main', array( 'data-from', 'data-using' ) );


/**
 * 6. Restore attributes
 */

//restore_attributes( 'main' );


/**
 * 7. Extend any markup relative to their position in the DOM.
 *
 * `before`, `prepend`, `append` or `after`.
 */

//add_before_action( 'main', function () {
//	echo '*** Text Is Added From `functions.php` before `.site-main` ***';
//} );

//add_prepend_action( 'main', function () {
//	echo '*** Text Is Added From `functions.php` after opening `.site-main` ***';
//} );

//add_append_action( 'main', function () {
//	echo '*** Text Is Added From `functions.php` before closing `.site-main` ***';
//} );

//add_after_action( 'main', function () {
//	echo '*** Text Is Added From `functions.php` after closing `.site-main` ***';
//} );


/**
 * Complex Use Cases.
 *
 * Adding an SVG arrow to menu links that:
 * - has markup ID `nav_menu_link`, used in `includes/class-custom-nav-menu.php`
 * - has a dropdown sub-menu.
 * - is only the depth 1.
 * - is on Header, not footer (both use same walker).
 */

//add_append_action( 'nav_menu_link', 'theme_add_dropdown_link_arrow', 10, 3 );
//function theme_add_dropdown_link_arrow( $item, $has_children, $depth ) {
//	if ( ! is_markup_open( 'header' ) || ! $has_children || (int) $depth >= 1 ) {
//		return;
//	}
//
//	$svg_icon  = '<svg xmlns="http://www.w3.org/2000/svg" width="20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" //stroke="currentColor">';
//	$svg_icon .= '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />';
//	$svg_icon .= '</svg>';
//
//	echo $svg_icon; // phpcs:ignore
//}

/**
 * Target any menu link in the footer
 * and add a class of text-red.
 */

//add_attributes_filter( 'nav_menu_link', function ( $attributes ) {
//	if ( ! is_markup_open( 'footer' ) ) {
//		return $attributes;
//	}
//	if ( isset( $attributes['class'] ) ) {
//		$attributes['class'] .= ' text-red';
//	} else {
//		$attributes['class'] = 'text-red';
//	}
//	return $attributes;
//} );
