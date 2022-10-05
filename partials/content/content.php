<?php
/**
 * Template part: Article content.
 *
 * @package Theme
 */

use function Theme\Markup\{ open_markup, close_markup };

open_markup(
	'post',
	'article',
	array(
		'id'    => 'post-' . get_the_ID(),
		'class' => implode( ' ', get_post_class( 'entry' ) ),
	)
); // <article ...> .

	open_markup( 'post_title', 'h1', array( 'class' => 'entry__title' ) ); // <h1 ...> .

		the_title();

	close_markup( 'post_title' ); // </h1> .

	open_markup( 'post_summary', 'div', array( 'class' => 'entry__summary' ) ); // <p ...>

		the_excerpt();

	close_markup( 'post_summary' ); // </p> .

close_markup( 'post' ); // </article> .
