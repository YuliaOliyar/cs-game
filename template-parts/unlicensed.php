<?php
/**
 * Varování u nelegálního kasina + doporučené legální alternativy.
 *
 * Args: casino_id (int), count (int)
 *
 * @package CSGame
 */

$cid   = isset( $args['casino_id'] ) ? (int) $args['casino_id'] : get_the_ID();
$count = isset( $args['count'] ) ? (int) $args['count'] : 3;
$name  = get_the_title( $cid );
$note  = get_post_meta( $cid, '_cs_unlicensed_note', true );
$alt   = get_post_meta( $cid, '_cs_alternatives', true );
$ids   = $alt ? array_filter( array_map( 'absint', explode( ',', $alt ) ) ) : array();
$ids   = array_diff( $ids, array( $cid ) );
$q     = csgame_get_casinos( $count, $ids );
?>
<section class="unlic" id="nelegalni">
	<div class="unlic__warn">
		<span class="unlic__icon" aria-hidden="true"><?php echo csgame_icon( 'alert' ); // phpcs:ignore ?></span>
		<div>
			<p class="unlic__title"><?php esc_html_e( 'Kasino bez licence MF ČR', 'csgame' ); ?></p>
			<div class="unlic__text">
				<?php if ( $note ) : ?>
					<?php echo wpautop( esc_html( $note ) ); // phpcs:ignore ?>
				<?php else : ?>
					<p>
						<?php
						/* translators: %s: casino name */
						printf( esc_html__( '%s nemá licenci Ministerstva financí ČR, a proto ho nedoporučujeme. Hraní u nelicencovaných kasin je v Česku nelegální, výhry nejsou chráněny zákonem a v případě sporu se nemáte kam obrátit.', 'csgame' ), '<strong>' . esc_html( $name ) . '</strong>' ); // phpcs:ignore
						?>
					</p>
				<?php endif; ?>
			</div>
			<ul class="unlic__risks">
				<li><?php echo csgame_icon( 'x' ); // phpcs:ignore ?><?php esc_html_e( 'Bez dohledu českých úřadů', 'csgame' ); ?></li>
				<li><?php echo csgame_icon( 'x' ); // phpcs:ignore ?><?php esc_html_e( 'Riziko nevyplacení výher', 'csgame' ); ?></li>
				<li><?php echo csgame_icon( 'x' ); // phpcs:ignore ?><?php esc_html_e( 'Web může být zablokován', 'csgame' ); ?></li>
			</ul>
		</div>
	</div>

	<?php if ( $q->have_posts() ) : ?>
		<div class="unlic__alt">
			<p class="unlic__alt-title"><?php echo csgame_icon( 'shield' ); // phpcs:ignore ?> <?php esc_html_e( 'Doporučujeme místo něj – legální kasina s licencí', 'csgame' ); ?></p>
			<ul class="unlic__list">
				<?php
				while ( $q->have_posts() ) :
					$q->the_post();
					$aid = get_the_ID();
					?>
					<li class="unlic__item">
						<a class="unlic__logo" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo csgame_logo( $aid ); // phpcs:ignore ?></a>
						<div class="unlic__info">
							<a class="unlic__name" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<span class="unlic__meta">
								<span class="unlic__rating">★ <?php echo esc_html( number_format_i18n( (float) csgame_meta( 'rating', $aid ), 1 ) ); ?></span>
								<?php if ( csgame_meta( 'bonus_text', $aid ) ) : ?>
									<span><?php echo esc_html( csgame_meta( 'bonus_text', $aid ) ); ?></span>
								<?php endif; ?>
							</span>
						</div>
						<a class="btn btn--primary unlic__btn" href="<?php echo esc_url( csgame_aff_url( $aid ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Hrát legálně', 'csgame' ); ?></a>
					</li>
				<?php endwhile; ?>
			</ul>
			<p class="unlic__note muted">18+ | <?php esc_html_e( 'Všechna doporučená kasina mají licenci Ministerstva financí ČR.', 'csgame' ); ?></p>
		</div>
		<?php
		wp_reset_postdata();
	endif;
	?>
</section>
