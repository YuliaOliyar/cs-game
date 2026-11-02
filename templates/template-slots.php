<?php
/**
 * Template Name: Automaty (přehled automatů)
 * Template Post Type: page
 *
 * ТЗ bod 6: název, rozbalovací text, prolinkování na poskytovatele,
 * filtr, blok automatů, stránkování + autor a data, obsah, text,
 * vitrína kasin, další text, blok bonusů, FAQ, odběr.
 *
 * Doporučená adresa: /casino/automaty/ (podstránka „automaty“ stránky „casino“).
 * Druhý textový blok (6.7) oddělte v editoru blokem „Více“ (<!--more-->).
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	$slots = csgame_slots_query( 24 );
	$parts = get_extended( get_post_field( 'post_content', get_the_ID() ) );
	?>
	<div class="container">
		<header class="page-head">
			<h1 class="page-head__title"><?php the_title(); ?></h1>
			<?php if ( csgame_meta( 'intro' ) ) : ?>
				<div class="collapsible" data-collapsible-text>
					<div class="collapsible__body"><?php echo wpautop( wp_kses_post( csgame_meta( 'intro' ) ) ); // phpcs:ignore ?></div>
					<button class="collapsible__more" type="button"><?php esc_html_e( 'Zobrazit více', 'csgame' ); ?></button>
				</div>
			<?php endif; ?>
		</header>

		<?php get_template_part( 'template-parts/interlinks', null, array( 'location' => 'providers', 'style' => 'horizontal', 'current' => __( 'Všichni poskytovatelé', 'csgame' ) ) ); ?>

		<?php csgame_slot_filter_form(); ?>

		<?php if ( $slots->have_posts() ) : ?>
			<div class="slot-grid">
				<?php
				while ( $slots->have_posts() ) {
					$slots->the_post();
					get_template_part( 'template-parts/slot-card' );
				}
				wp_reset_postdata();
				?>
			</div>
			<?php
			$links = paginate_links(
				array(
					'total'     => $slots->max_num_pages,
					'current'   => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
					'mid_size'  => 1,
					'prev_text' => '‹',
					'next_text' => '›',
					'type'      => 'list',
				)
			);
			if ( $links ) {
				echo '<nav class="pagination" aria-label="' . esc_attr__( 'Stránkování', 'csgame' ) . '">' . $links . '</nav>'; // phpcs:ignore
			}
			?>
		<?php else : ?>
			<p class="muted"><?php esc_html_e( 'Žádné automaty neodpovídají filtru.', 'csgame' ); ?></p>
		<?php endif; ?>

		<div class="byline-wrap"><?php echo csgame_byline( get_the_ID(), false ); // phpcs:ignore ?></div>

		<div class="content-layout">
			<aside class="content-layout__aside"><?php csgame_toc(); ?></aside>
			<div class="content-layout__main">
				<div class="entry-content"><?php echo apply_filters( 'the_content', $parts['main'] ); // phpcs:ignore ?></div>
			</div>
		</div>

		<?php get_template_part( 'template-parts/toplist', null, array( 'query' => csgame_get_casinos( 5 ), 'title' => __( 'Kde hrát automaty online', 'csgame' ) ) ); ?>

		<?php if ( trim( $parts['extended'] ) ) : ?>
			<div class="container--narrow entry-content"><?php echo apply_filters( 'the_content', $parts['extended'] ); // phpcs:ignore ?></div>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/bonus-list', null, array( 'query' => csgame_get_bonuses( array( 'posts_per_page' => 5 ) ), 'title' => __( 'Bonusy na automaty', 'csgame' ) ) ); ?>

		<div class="container--narrow">
			<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'faq', 'subscribe' ) ) ); ?>
		</div>
	</div>
	<?php
endwhile;
get_footer();
