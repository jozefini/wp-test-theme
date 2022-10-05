<?php
/**
 * Template-part: Footer
 *
 * @package Theme
 * @since 1.0.0
 */

use Theme\Classes\Custom_Nav_Menu;
use function Theme\Markup\{ open_markup, close_markup };

close_markup( 'main' );

echo '<!-- TARGET MARKUP WILL BE "MAIN" ABOVE !-->';

open_markup(
	'footer',
	'footer',
	array(
		'class'     => 'site-footer',
		'itemscope' => true,
		'itemtype'  => 'http://schema.org/WPFooter',
	)
);

if ( has_nav_menu( 'primary_menu' ) ) {
	wp_nav_menu(
		array(
			'container'      => false,
			'theme_location' => 'primary_menu',
			'walker'         => new Custom_Nav_Menu(),
			'depth'          => 1,
			'fallback_cb'    => false,
		)
	);
}

close_markup( 'footer' );

// Closes HTML Document + WP Hooks.
get_template_part( 'partials/document/document', 'end' );
