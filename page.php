<?php
/**
 * Stránka / kategorie (stejná struktura jako hlavní stránka,
 * toplist se zapíná v boxu „CS Game – toplist kasin“).
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/landing' );
}
get_footer();
