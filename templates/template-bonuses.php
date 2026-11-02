<?php
/**
 * Template Name: Bonusy (přehled bonusů)
 * Template Post Type: page
 *
 * ТЗ bod 3: název, text, data, blok s ikonami, vodorovné prolinkování,
 * filtr, vitrína bonusů, obsah, text, odběr, FAQ, autor.
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<div class="container">
		<?php get_template_part( 'template-parts/page-head', null, array( 'byline' => false, 'dates' => true ) ); ?>
		<?php get_template_part( 'template-parts/features' ); ?>

		<?php get_template_part( 'template-parts/interlinks', null, array( 'location' => 'bonus_links', 'style' => 'horizontal', 'current' => get_the_title() ) ); ?>

		<?php
		get_template_part(
			'template-parts/bonus-list',
			null,
			array(
				'query'  => csgame_get_bonuses( array( 'posts_per_page' => 60 ) ),
				'filter' => true,
			)
		);
		?>

		<?php get_template_part( 'template-parts/disclaimer' ); ?>

		<div class="content-layout">
			<aside class="content-layout__aside"><?php csgame_toc(); ?></aside>
			<div class="content-layout__main">
				<div class="entry-content"><?php the_content(); ?></div>
				<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'subscribe', 'faq', 'author' ) ) ); ?>
			</div>
		</div>
	</div>
	<?php
endwhile;
get_footer();
