<?php
/**
 * Template-part: Header
 *
 * @package Theme
 * @since 1.0.0
 */

use Theme\Classes\Custom_Nav_Menu;
use function Theme\Markup\{ open_markup, close_markup };

// Opens HTML Document + WP hooks.
get_template_part( 'partials/document/document', 'head' );

open_markup(
	'header',
	'header',
	array(
		'class'     => 'site-header',
		'itemscope' => true,
		'itemtype'  => 'http://schema.org/WPHeader',
	)
); // <header ...> .

if ( has_nav_menu( 'primary_menu' ) ) {
	wp_nav_menu(
		array(
			'container'      => false,
			'theme_location' => 'primary_menu',
			'walker'         => new Custom_Nav_Menu(),
			'fallback_cb'    => false,
		)
	);
}

close_markup( 'header' ); // </header> .

echo '<!-- TARGET MARKUP WILL BE "MAIN" BELOW !-->';

open_markup( 'main', 'main', array( 'class' => 'site-main' ) ); // <main ...> .
