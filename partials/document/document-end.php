<?php
/**
 * Close HTML document.
 *
 * @package [Theme]
 * @since 1.0.0
 */

use function Theme\Markup\{ close_markup };

wp_footer();

close_markup( 'document_body' ); // </body> .
close_markup( 'document_html' ); // </html> .
