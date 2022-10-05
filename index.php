<?php
/**
 * The main template file
 *
 * @package Theme
 * @since 1.0.0
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post(); // Query Current Post.

		get_template_part( 'partials/content/content', get_post_type() );
	endwhile;
endif;

get_footer();
