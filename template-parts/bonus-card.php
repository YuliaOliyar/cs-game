<?php
/**
 * Karta bonusu.
 *
 * Pořadí, logo, typ bonusu, bonus, promo kód, tlačítko do kasina,
 * tlačítko na recenzi bonusu, rozbalovací detaily, hodnocení kasina,
 * exkluzivita, hashtagy.
 *
 * Args: pos (int), compact (bool)
 *
 * @package CSGame
 */

$pos     = isset( $args['pos'] ) ? (int) $args['pos'] : 0;
$compact = ! empty( $args['compact'] );
$id      = get_the_ID();
$casino  = (int) csgame_meta( 'casino_id', $id );
$types   = get_the_terms( $id, 'bonus_type' );
$tags    = get_the_terms( $id, 'bonus_tag' );
$code    = csgame_meta( 'promo_code', $id );
$details = csgame_meta( 'details', $id );
$excl    = '1' === csgame_meta( 'exclusive', $id );
$slugs   = array();
$tnames  = array();
if ( $types && ! is_wp_error( $types ) ) {
	foreach ( $types as $t ) {
		$slugs[]  = $t->slug;
		$tnames[] = $t->name;
	}
}
?>
<li class="bonus-card<?php echo 1 === $pos && ! $compact ? ' bonus-card--top' : ''; ?><?php echo $excl ? ' bonus-card--excl' : ''; ?>" data-types="<?php echo esc_attr( implode( ' ', $slugs ) ); ?>">
	<?php if ( $pos && ! $compact ) : ?>
		<span class="bonus-card__pos"><?php echo (int) $pos; ?></span>
	<?php endif; ?>

	<div class="bonus-card__main">
		<div class="bonus-card__logo">
			<?php echo csgame_logo( $casino ? $casino : $id ); // phpcs:ignore ?>
			<?php if ( $casino ) : ?>
				<?php echo csgame_stars( csgame_meta( 'rating', $casino ) ); // phpcs:ignore ?>
			<?php endif; ?>
		</div>

		<div class="bonus-card__info">
			<div class="bonus-card__labels">
				<?php if ( $tnames ) : ?>
					<span class="label"><?php echo csgame_icon( 'gift' ); // phpcs:ignore ?><?php echo esc_html( implode( ', ', $tnames ) ); ?></span>
				<?php endif; ?>
				<?php if ( $excl ) : ?>
					<span class="label label--gold"><?php esc_html_e( 'Exkluzivní', 'csgame' ); ?></span>
				<?php endif; ?>
			</div>
			<h3 class="bonus-card__title">
				<a href="<?php the_permalink(); ?>"><?php echo esc_html( csgame_meta( 'bonus_amount', $id ) ? csgame_meta( 'bonus_amount', $id ) : get_the_title() ); ?></a>
			</h3>
			<?php if ( $casino ) : ?>
				<p class="bonus-card__casino muted"><?php echo esc_html( get_the_title( $casino ) ); ?></p>
			<?php endif; ?>
			<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
				<p class="hashtags">
					<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>
		</div>

		<div class="bonus-card__actions">
			<?php if ( $code ) : ?>
				<button class="promo" type="button" data-copy="<?php echo esc_attr( $code ); ?>">
					<span class="promo__code"><?php echo esc_html( $code ); ?></span>
					<?php echo csgame_icon( 'copy' ); // phpcs:ignore ?>
				</button>
			<?php endif; ?>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( csgame_aff_url( $id ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Získat bonus', 'csgame' ); ?></a>
			<a class="btn btn--ghost btn--block" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Detail bonusu', 'csgame' ); ?></a>
		</div>
	</div>

	<?php if ( $details && ! $compact ) : ?>
		<details class="bonus-card__details">
			<summary><?php esc_html_e( 'Jak bonus získat', 'csgame' ); ?> <?php echo csgame_icon( 'chevron' ); // phpcs:ignore ?></summary>
			<div class="bonus-card__details-body"><?php echo wpautop( esc_html( $details ) ); // phpcs:ignore ?></div>
		</details>
	<?php endif; ?>
</li>
