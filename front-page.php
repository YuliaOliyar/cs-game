<?php
/**
 * Hlavní stránka.
 *
 * @package CSGame
 */

if ( 'page' !== get_option( 'show_on_front' ) ) {
	get_template_part( 'home' );
	return;
}

get_header();
while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/landing', null, array( 'force_toplist' => true ) );
}
get_footer();
