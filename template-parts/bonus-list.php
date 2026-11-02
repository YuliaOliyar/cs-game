<?php
/**
 * Seznam bonusů s volitelným filtrem podle typu.
 *
 * Args: query (WP_Query), filter (bool), title
 *
 * @package CSGame
 */

$q      = isset( $args['query'] ) ? $args['query'] : csgame_get_bonuses();
$filter = ! empty( $args['filter'] );
$title  = ! empty( $args['title'] ) ? $args['title'] : '';

if ( ! $q->have_posts() ) {
	echo '<p class="muted">' . esc_html__( 'Zatím zde nejsou žádné bonusy.', 'csgame' ) . '</p>';
	return;
}
?>
<section class="section bonus-section" data-bonus-list>
	<?php if ( $title ) : ?>
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>

	<?php if ( $filter ) : ?>
		<?php
		$types = get_terms( array( 'taxonomy' => 'bonus_type', 'hide_empty' => true ) );
		if ( $types && ! is_wp_error( $types ) ) :
			?>
			<div class="bonus-filter" data-collapsible>
				<button class="bonus-filter__toggle" type="button" aria-expanded="true">
					<?php echo csgame_icon( 'filter' ); // phpcs:ignore ?>
					<span><?php esc_html_e( 'Filtr bonusů', 'csgame' ); ?></span>
					<?php echo csgame_icon( 'chevron', 'ico ico--chev' ); // phpcs:ignore ?>
				</button>
				<div class="bonus-filter__menu" role="group">
					<button type="button" class="chip is-active" data-filter="*"><?php esc_html_e( 'Všechny', 'csgame' ); ?></button>
					<?php foreach ( $types as $t ) : ?>
						<button type="button" class="chip" data-filter="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<ol class="bonus-list">
		<?php
		$pos = 0;
		while ( $q->have_posts() ) {
			$q->the_post();
			$pos++;
			get_template_part( 'template-parts/bonus-card', null, array( 'pos' => $pos ) );
		}
		?>
	</ol>
	<p class="bonus-list__empty muted" hidden><?php esc_html_e( 'Pro zvolený filtr nejsou žádné bonusy.', 'csgame' ); ?></p>
</section>
<?php
wp_reset_postdata();
