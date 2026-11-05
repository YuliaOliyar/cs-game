<?php
/**
 * Boční panel na stránce bonusu (desktop): nejlépe hodnocené bonusy.
 *
 * Args: exclude (int), count (int)
 *
 * @package CSGame
 */

$exclude = isset( $args['exclude'] ) ? (int) $args['exclude'] : 0;
$count   = isset( $args['count'] ) ? (int) $args['count'] : 6;
$q       = csgame_get_bonuses( array( 'posts_per_page' => 40, 'post__not_in' => array( $exclude ) ) );
if ( ! $q->have_posts() ) {
	return;
}
$list = $q->posts;
usort(
	$list,
	function ( $a, $b ) {
		$ra = (float) get_post_meta( (int) get_post_meta( $a->ID, '_cs_casino_id', true ), '_cs_rating', true );
		$rb = (float) get_post_meta( (int) get_post_meta( $b->ID, '_cs_casino_id', true ), '_cs_rating', true );
		$ea = '1' === get_post_meta( $a->ID, '_cs_exclusive', true );
		$eb = '1' === get_post_meta( $b->ID, '_cs_exclusive', true );
		if ( $ea !== $eb ) {
			return $ea ? -1 : 1;
		}
		return $rb <=> $ra ?: $a->menu_order <=> $b->menu_order;
	}
);
$seen = array();
$out  = array();
foreach ( $list as $p ) {
	$cid = (int) get_post_meta( $p->ID, '_cs_casino_id', true );
	if ( isset( $seen[ $cid ] ) ) {
		continue;
	}
	$seen[ $cid ] = true;
	$out[]        = $p;
	if ( count( $out ) >= $count ) {
		break;
	}
}
?>
<aside class="bside" aria-label="<?php esc_attr_e( 'Nejlépe hodnocené bonusy', 'csgame' ); ?>">
	<p class="bside__title"><?php esc_html_e( 'Nejlépe hodnocené bonusy', 'csgame' ); ?></p>
	<?php foreach ( $out as $i => $p ) : ?>
		<?php
		$cid    = (int) get_post_meta( $p->ID, '_cs_casino_id', true );
		$amount = get_post_meta( $p->ID, '_cs_bonus_amount', true );
		$excl   = '1' === get_post_meta( $p->ID, '_cs_exclusive', true );
		$rating = (float) get_post_meta( $cid, '_cs_rating', true );
		?>
		<article class="bside__item<?php echo 0 === $i && $excl ? ' bside__item--hl' : ''; ?>">
			<?php if ( 0 === $i && $excl ) : ?>
				<p class="bside__flag"><?php esc_html_e( '💎 Exkluzivní bonus týdne', 'csgame' ); ?></p>
			<?php endif; ?>
			<div class="bside__row">
				<a class="bside__logo" href="<?php echo esc_url( get_permalink( $p ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo csgame_logo( $cid ? $cid : $p->ID ); // phpcs:ignore ?></a>
				<div class="bside__info">
					<h3 class="bside__name"><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( $amount ? $amount : get_the_title( $p ) ); ?></a></h3>
					<p class="bside__meta">
						<span><?php echo esc_html( $cid ? get_the_title( $cid ) : '' ); ?></span>
						<?php if ( $rating ) : ?>
							<span class="bside__rating">★ <?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
						<?php endif; ?>
					</p>
				</div>
			</div>
			<div class="bside__actions">
				<a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php esc_html_e( 'Detail', 'csgame' ); ?></a>
				<a class="btn btn--primary" href="<?php echo esc_url( csgame_aff_url( $p->ID ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Získat bonus', 'csgame' ); ?></a>
			</div>
		</article>
	<?php endforeach; ?>
</aside>
