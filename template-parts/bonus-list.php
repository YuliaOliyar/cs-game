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

	<?php
	if ( $filter ) :
		// Counts from the listed bonuses.
		$groups = array(
			'type'   => array( 'label' => __( 'Typ bonusu', 'csgame' ), 'items' => array() ),
			'casino' => array( 'label' => __( 'Casino', 'csgame' ), 'items' => array() ),
			'other'  => array( 'label' => __( 'Ostatní', 'csgame' ), 'items' => array() ),
		);
		$other_labels = array(
			'exclusive' => '💎 ' . __( 'Exkluzivní bonusy', 'csgame' ),
			'promo'     => '🏷️ ' . __( 'S promo kódem', 'csgame' ),
		);
		foreach ( $q->posts as $bp ) {
			$tt = get_the_terms( $bp->ID, 'bonus_type' );
			if ( $tt && ! is_wp_error( $tt ) ) {
				foreach ( $tt as $t ) {
					if ( ! isset( $groups['type']['items'][ $t->slug ] ) ) {
						$groups['type']['items'][ $t->slug ] = array( $t->name, 0 );
					}
					$groups['type']['items'][ $t->slug ][1]++;
				}
			}
			$cid = (int) get_post_meta( $bp->ID, '_cs_casino_id', true );
			if ( $cid ) {
				if ( ! isset( $groups['casino']['items'][ $cid ] ) ) {
					$groups['casino']['items'][ $cid ] = array( get_the_title( $cid ), 0 );
				}
				$groups['casino']['items'][ $cid ][1]++;
			}
			if ( '1' === get_post_meta( $bp->ID, '_cs_exclusive', true ) ) {
				$groups['other']['items']['exclusive'] = array( $other_labels['exclusive'], ( isset( $groups['other']['items']['exclusive'] ) ? $groups['other']['items']['exclusive'][1] : 0 ) + 1 );
			}
			if ( get_post_meta( $bp->ID, '_cs_promo_code', true ) ) {
				$groups['other']['items']['promo'] = array( $other_labels['promo'], ( isset( $groups['other']['items']['promo'] ) ? $groups['other']['items']['promo'][1] : 0 ) + 1 );
			}
		}
		uasort(
			$groups['type']['items'],
			function ( $x, $y ) {
				return $y[1] - $x[1];
			}
		);
		$groups = array_filter(
			$groups,
			function ( $g ) {
				return ! empty( $g['items'] );
			}
		);
		if ( $groups ) :
			?>
			<div class="bonus-layout">
			<aside class="bonus-layout__aside">
			<div class="bonus-filter is-collapsed" data-collapsible>
				<button class="bonus-filter__toggle" type="button" aria-expanded="false" aria-controls="bonus-filter-panel">
					<?php echo csgame_icon( 'filter' ); // phpcs:ignore ?>
					<span><?php esc_html_e( 'Filtry', 'csgame' ); ?></span>
					<span class="bonus-filter__count" data-filter-count hidden></span>
					<?php echo csgame_icon( 'chevron', 'ico ico--chev' ); // phpcs:ignore ?>
				</button>
				<div class="bonus-filter__panel" id="bonus-filter-panel">
					<?php
					$first = true;
					foreach ( $groups as $gkey => $g ) :
						?>
						<details class="fgroup"<?php echo $first ? ' open' : ''; ?>>
							<summary class="fgroup__head">
								<span><?php echo esc_html( $g['label'] ); ?></span>
								<span class="fgroup__badge" data-group-badge hidden></span>
								<?php echo csgame_icon( 'chevron' ); // phpcs:ignore ?>
							</summary>
							<ul class="fgroup__list" role="list">
								<?php foreach ( $g['items'] as $val => $it ) : ?>
									<li>
										<button type="button" class="fopt" data-group="<?php echo esc_attr( $gkey ); ?>" data-value="<?php echo esc_attr( $val ); ?>" aria-pressed="false">
											<span class="fopt__check"><?php echo csgame_icon( 'check' ); // phpcs:ignore ?></span>
											<span class="fopt__label"><?php echo esc_html( $it[0] ); ?></span>
											<sup class="fopt__count"><?php echo (int) $it[1]; ?></sup>
										</button>
									</li>
								<?php endforeach; ?>
							</ul>
						</details>
						<?php
						$first = false;
					endforeach;
					?>
					<div class="bonus-filter__foot">
						<button type="button" class="btn btn--ghost" data-filter-reset><?php esc_html_e( 'Zrušit filtry', 'csgame' ); ?></button>
						<button type="button" class="btn btn--primary" data-filter-close><?php esc_html_e( 'Zobrazit', 'csgame' ); ?> <span data-filter-shown><?php echo (int) count( $q->posts ); ?></span></button>
					</div>
				</div>
			</div>
			</aside>
			<div class="bonus-layout__main">
				<div class="bonus-head">
					<p class="bonus-head__title"><strong><?php esc_html_e( 'Aktuální bonusy', 'csgame' ); ?></strong> <span class="muted">(<?php echo esc_html( sprintf( __( 'aktualizováno: %s', 'csgame' ), get_the_modified_date() ) ); ?>)</span></p>
					<label class="bonus-sort">
						<span class="screen-reader-text"><?php esc_html_e( 'Řazení', 'csgame' ); ?></span>
						<select data-bonus-sort>
							<option value="pos"><?php esc_html_e( 'Doporučené', 'csgame' ); ?></option>
							<option value="rating"><?php esc_html_e( 'Dle hodnocení', 'csgame' ); ?></option>
							<option value="date"><?php esc_html_e( 'Nejnovější', 'csgame' ); ?></option>
						</select>
						<svg class="ico" aria-hidden="true" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M7 4v16M3 8l4-4 4 4M17 20V4M13 16l4 4 4-4"/></svg>
					</label>
				</div>
		<?php
			$layout_open = true;
		endif;
		?>
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
	<?php if ( ! empty( $layout_open ) ) : ?>
			</div>
		</div>
	<?php endif; ?>
</section>
<?php
wp_reset_postdata();
