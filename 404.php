<?php
/**
 * 404.
 *
 * @package CSGame
 */

get_header();
?>
<div class="container container--narrow error-404">
	<p class="error-404__code">404</p>
	<h1 class="page-head__title"><?php esc_html_e( 'Stránka nenalezena', 'csgame' ); ?></h1>
	<p class="muted"><?php esc_html_e( 'Tato stránka neexistuje nebo byla přesunuta.', 'csgame' ); ?></p>
	<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Zpět na hlavní stránku', 'csgame' ); ?></a>
</div>
<?php
get_footer();
