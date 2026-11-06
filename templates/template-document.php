<?php
/**
 * Template Name: Dokument (zásady, podmínky)
 * Template Post Type: page
 *
 * Jednoduchá textová stránka: H1, datum aktualizace, obsah (TOC) a text. Bez reklam a toplistů.
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<div class="container">
		<header class="page-head doc-head">
			<h1 class="page-head__title"><?php the_title(); ?></h1>
			<p class="doc-head__date muted">
				<?php echo csgame_icon( 'calendar' ); // phpcs:ignore ?>
				<?php
				/* translators: %s: date */
				printf( esc_html__( 'Platné od: %s', 'csgame' ), esc_html( get_the_modified_date() ) );
				?>
			</p>
			<?php $intro = csgame_meta( 'intro' ); ?>
			<?php if ( $intro ) : ?>
				<div class="page-head__intro"><?php echo wpautop( wp_kses_post( $intro ) ); // phpcs:ignore ?></div>
			<?php endif; ?>
		</header>
		<div class="content-layout">
			<aside class="content-layout__aside"><?php csgame_toc(); ?></aside>
			<div class="content-layout__main">
				<div class="entry-content entry-content--doc"><?php the_content(); ?></div>
			</div>
		</div>
	</div>
	<?php
endwhile;
get_footer();
