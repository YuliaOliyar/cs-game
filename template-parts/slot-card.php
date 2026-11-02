<?php
/**
 * Karta automatu: obrázek, poskytovatel, název, hodnocení.
 *
 * @package CSGame
 */

$id    = get_the_ID();
$terms = get_the_terms( $id, 'provider' );
$prov  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
$rtp   = csgame_meta( 'rtp', $id );
?>
<article class="slot-card">
	<a class="slot-card__img" href="<?php the_permalink(); ?>">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'csgame-slot', array( 'loading' => 'lazy' ) );
		} else {
			echo '<span class="slot-card__ph">' . csgame_icon( 'play' ) . '</span>'; // phpcs:ignore
		}
		?>
		<span class="slot-card__play"><?php echo csgame_icon( 'play' ); // phpcs:ignore ?> <?php esc_html_e( 'Demo', 'csgame' ); ?></span>
	</a>
	<div class="slot-card__body">
		<?php if ( $prov ) : ?>
			<a class="slot-card__prov" href="<?php echo esc_url( get_term_link( $prov ) ); ?>"><?php echo esc_html( $prov->name ); ?></a>
		<?php endif; ?>
		<h3 class="slot-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="slot-card__meta">
			<?php echo csgame_stars( csgame_meta( 'rating', $id ) ); // phpcs:ignore ?>
			<?php if ( $rtp ) : ?>
				<span class="label">RTP <?php echo esc_html( number_format_i18n( (float) $rtp, 2 ) ); ?> %</span>
			<?php endif; ?>
		</div>
	</div>
</article>
