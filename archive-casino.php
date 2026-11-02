<?php
/**
 * Přehled recenzí kasin (/recenze/).
 *
 * @package CSGame
 */

get_header();
?>
<div class="container">
	<header class="page-head">
		<h1 class="page-head__title"><?php echo esc_html( csgame_mod( 'csgame_casinos_title' ) ); ?></h1>
	</header>
	<?php
	global $wp_query;
	get_template_part( 'template-parts/toplist', null, array( 'query' => $wp_query ) );
	get_template_part( 'template-parts/disclaimer' );
	?>
</div>
<?php
get_footer();
