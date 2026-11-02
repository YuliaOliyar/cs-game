<?php
/**
 * Bonusy podle typu / hashtagu.
 *
 * @package CSGame
 */

get_header();
global $wp_query;
?>
<div class="container">
	<header class="page-head">
		<h1 class="page-head__title"><?php single_term_title(); ?></h1>
		<?php the_archive_description( '<div class="page-head__intro">', '</div>' ); ?>
	</header>
	<?php get_template_part( 'template-parts/interlinks', null, array( 'location' => 'bonus_links', 'style' => 'horizontal', 'current' => single_term_title( '', false ) ) ); ?>
	<?php get_template_part( 'template-parts/bonus-list', null, array( 'query' => $wp_query ) ); ?>
	<?php get_template_part( 'template-parts/disclaimer' ); ?>
</div>
<?php
get_footer();
