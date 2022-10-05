<?php
/**
 * Open HTML document.
 *
 * @package [Theme]
 * @since 1.0.0
 */

use function Theme\Markup\{ open_markup, close_markup };

echo '<!DOCTYPE html>';

open_markup(
	'document_html',
	'html',
	array(
		'dir'  => is_rtl() ? 'rtl' : 'ltr',
		'lang' => get_bloginfo( 'language' ),
	)
); // <html ...> .

open_markup( 'document_head', 'head' );  // <head> .

wp_head();

close_markup( 'document_head' ); // </head> .

open_markup(
	'document_body',
	'body',
	array(
		'class'     => implode( ' ', get_body_class() ),
		'itemscope' => true,
		'itemtype'  => 'http://schema.org/WebPage',
	)
); // <body ...> .

wp_body_open();
