<?php
/**
 * Struktura hlavní stránky a kategorie (ТЗ bod 2).
 *
 * 2.1 název, úvod, nadpis před vitrínou
 * 2.2 datum publikace a aktualizace
 * 2.3 vitrína se zvýrazněným 1. místem
 * 2.4 disclaimer
 * 2.5 obsah (H2/H3)
 * 2.6 textový blok
 * 2.7 karta autora
 * 2.8 formulář odběru
 * 2.9 FAQ
 * 2.10 blok prolinkování
 *
 * Args: force_toplist (bool)
 *
 * @package CSGame
 */

$force = ! empty( $args['force_toplist'] );
$show  = $force ? csgame_flag( 'toplist_show', '1' ) : csgame_flag( 'toplist_show', '0' );
$ids   = csgame_meta( 'toplist_ids' ) ? array_filter( array_map( 'absint', explode( ',', csgame_meta( 'toplist_ids' ) ) ) ) : array();
$count = csgame_meta( 'toplist_count' ) ? (int) csgame_meta( 'toplist_count' ) : 10;
$tl_t  = csgame_meta( 'toplist_title' ) ? csgame_meta( 'toplist_title' ) : __( 'Nejlepší online kasina v Česku', 'csgame' );
?>
<div class="container">
	<?php get_template_part( 'template-parts/page-head', null, array( 'dates' => false ) ); ?>

	<?php if ( $show ) : ?>
		<div class="toplist-head">
			<h2 class="toplist-head__title"><?php echo esc_html( $tl_t ); ?></h2>
			<?php echo csgame_dates(); // phpcs:ignore ?>
		</div>
		<?php get_template_part( 'template-parts/toplist', null, array( 'query' => csgame_get_casinos( $count, $ids ) ) ); ?>
	<?php else : ?>
		<div class="page-head__dates"><?php echo csgame_dates(); // phpcs:ignore ?></div>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/disclaimer' ); ?>

	<div class="content-layout">
		<aside class="content-layout__aside"><?php csgame_toc(); ?></aside>
		<div class="content-layout__main">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'author', 'subscribe', 'faq', 'interlinks' ) ) ); ?>
		</div>
	</div>
</div>
