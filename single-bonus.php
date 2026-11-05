<?php
/**
 * Recenze bonusu (ТЗ bod 4).
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	$id     = get_the_ID();
	$casino = (int) csgame_meta( 'casino_id' );
	$steps  = csgame_lines( csgame_meta( 'how_to_get' ) );
	$conds  = csgame_pairs( csgame_meta( 'conditions' ) );
	$tags   = get_the_terms( $id, 'bonus_tag' );
	$types  = get_the_terms( $id, 'bonus_type' );
	$code   = csgame_meta( 'promo_code' );
	$icons  = array_filter(
		array(
			array( 'repeat', __( 'Protočení', 'csgame' ), csgame_meta( 'wagering' ) ),
			array( 'coins', __( 'Min. vklad', 'csgame' ), csgame_meta( 'min_deposit' ) ),
			array( 'trophy', __( 'Max. výhra', 'csgame' ), csgame_meta( 'max_win' ) ),
			array( 'clock', __( 'Platnost', 'csgame' ), csgame_meta( 'validity' ) ),
		),
		function ( $i ) {
			return '' !== $i[2];
		}
	);
	?>
	<div class="container">
		<div class="bonus-page">
		<div class="bonus-page__main">
		<div class="bonus-hero">
			<div class="bonus-hero__main">
				<h1 class="page-head__title"><?php the_title(); ?></h1>
				<?php if ( csgame_meta( 'intro' ) ) : ?>
					<div class="page-head__intro"><?php echo wpautop( wp_kses_post( csgame_meta( 'intro' ) ) ); // phpcs:ignore ?></div>
				<?php endif; ?>
				<?php if ( $steps ) : ?>
					<h2 class="bonus-hero__sub"><?php esc_html_e( 'Jak bonus získat', 'csgame' ); ?></h2>
					<ol class="steps">
						<?php foreach ( $steps as $step ) : ?>
							<li><?php echo esc_html( $step ); ?></li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
				<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
					<p class="hashtags">
						<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>
			</div>

			<aside class="casino-box card">
				<?php echo csgame_logo( $casino ? $casino : $id, 'logo-box logo-box--lg' ); // phpcs:ignore ?>
				<?php if ( $casino ) : ?>
					<a class="casino-box__name" href="<?php echo esc_url( get_permalink( $casino ) ); ?>"><?php echo esc_html( get_the_title( $casino ) ); ?></a>
					<?php echo csgame_stars( csgame_meta( 'rating', $casino ) ); // phpcs:ignore ?>
				<?php endif; ?>
				<?php if ( $types && ! is_wp_error( $types ) ) : ?>
					<span class="label"><?php echo esc_html( $types[0]->name ); ?></span>
				<?php endif; ?>
				<p class="casino-box__bonus"><?php echo esc_html( csgame_meta( 'bonus_amount' ) ? csgame_meta( 'bonus_amount' ) : get_the_title() ); ?></p>
				<?php if ( $icons ) : ?>
					<ul class="cond-icons">
						<?php foreach ( $icons as $i ) : ?>
							<li><?php echo csgame_icon( $i[0] ); // phpcs:ignore ?><span><small><?php echo esc_html( $i[1] ); ?></small><strong><?php echo esc_html( $i[2] ); ?></strong></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( $code ) : ?>
					<button class="promo promo--block" type="button" data-copy="<?php echo esc_attr( $code ); ?>">
						<span class="promo__code"><?php echo esc_html( $code ); ?></span><?php echo csgame_icon( 'copy' ); // phpcs:ignore ?>
					</button>
				<?php endif; ?>
				<a class="btn btn--primary btn--block btn--lg" href="<?php echo esc_url( csgame_aff_url( $id ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Získat bonus', 'csgame' ); ?></a>
				<small class="muted">18+ | <?php esc_html_e( 'Platí obchodní podmínky', 'csgame' ); ?></small>
			</aside>
		</div>

		<?php /* Desktop: hlavní karta bonusu pod návodem. */ ?>
		<section class="bonus-big card" aria-label="<?php esc_attr_e( 'Karta bonusu', 'csgame' ); ?>">
			<div class="bonus-big__brand">
				<?php echo csgame_logo( $casino ? $casino : $id, 'logo-box logo-box--lg' ); // phpcs:ignore ?>
				<?php if ( $casino ) : ?>
					<div class="bonus-big__rating"><?php echo csgame_stars( csgame_meta( 'rating', $casino ) ); // phpcs:ignore ?></div>
					<a class="bonus-big__casino" href="<?php echo esc_url( get_permalink( $casino ) ); ?>"><?php echo esc_html( get_the_title( $casino ) ); ?></a>
				<?php endif; ?>
			</div>
			<div class="bonus-big__body">
				<div class="bonus-big__head">
					<div>
						<p class="bonus-big__title"><?php echo esc_html( csgame_meta( 'bonus_amount' ) ? csgame_meta( 'bonus_amount' ) : get_the_title() ); ?></p>
						<?php if ( has_excerpt() ) : ?>
							<p class="bonus-big__sub"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</div>
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( csgame_aff_url( $id ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Získat bonus', 'csgame' ); ?></a>
				</div>
				<?php
				$tiles = array_filter(
					array(
						array( 'coins', __( 'Min. vklad', 'csgame' ), csgame_meta( 'min_deposit' ) ),
						array( 'repeat', __( 'Protočení', 'csgame' ), csgame_meta( 'wagering' ) ),
						array( 'clock', __( 'Platnost', 'csgame' ), csgame_meta( 'validity' ) ),
						$code ? array( 'gift', __( 'Promo kód', 'csgame' ), $code ) : array( 'trophy', __( 'Max. výhra', 'csgame' ), csgame_meta( 'max_win' ) ),
					),
					function ( $t ) {
						return '' !== (string) $t[2];
					}
				);
				?>
				<?php if ( $tiles ) : ?>
					<ul class="bonus-big__tiles">
						<?php foreach ( $tiles as $t ) : ?>
							<li>
								<span class="bonus-big__ico"><?php echo csgame_icon( $t[0] ); // phpcs:ignore ?></span>
								<span>
									<small><?php echo esc_html( $t[1] ); ?></small>
									<?php if ( $code && $t[2] === $code ) : ?>
										<button class="bonus-big__code" type="button" data-copy="<?php echo esc_attr( $code ); ?>"><span class="promo__code"><?php echo esc_html( $code ); ?></span> <?php echo csgame_icon( 'copy' ); // phpcs:ignore ?></button>
									<?php else : ?>
										<strong><?php echo esc_html( $t[2] ); ?></strong>
									<?php endif; ?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<p class="bonus-big__tags">
					<?php if ( '1' === csgame_meta( 'exclusive' ) ) : ?>
						<span class="label label--gold">💎 <?php esc_html_e( 'Exkluzivní', 'csgame' ); ?></span>
					<?php endif; ?>
					<?php if ( $types && ! is_wp_error( $types ) ) : ?>
						<?php foreach ( $types as $t ) : ?>
							<span class="label"><?php echo esc_html( $t->name ); ?></span>
						<?php endforeach; ?>
					<?php endif; ?>
					<small class="muted">18+ | <?php esc_html_e( 'Platí obchodní podmínky', 'csgame' ); ?></small>
				</p>
			</div>
		</section>

		<?php echo csgame_byline(); // phpcs:ignore ?>

		<?php if ( $conds ) : ?>
			<section class="section">
				<h2 class="section__title"><?php esc_html_e( 'Úplné podmínky bonusu', 'csgame' ); ?></h2>
				<div class="table-wrap">
					<table class="kv-table">
						<tbody>
						<?php foreach ( $conds as $c ) : ?>
							<tr><th scope="row"><?php echo esc_html( $c[0] ); ?></th><td><?php echo esc_html( isset( $c[1] ) ? $c[1] : '' ); ?></td></tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</section>
		<?php endif; ?>

		<div class="content-layout">
			<aside class="content-layout__aside"><?php csgame_toc(); ?></aside>
			<div class="content-layout__main">
				<div class="entry-content"><?php the_content(); ?></div>
				<?php get_template_part( 'template-parts/disclaimer' ); ?>
				<?php get_template_part( 'template-parts/faq' ); ?>
			</div>
		</div>
		</div><!-- .bonus-page__main -->
		<?php get_template_part( 'template-parts/bonus-side', null, array( 'exclude' => $id ) ); ?>
		</div><!-- .bonus-page -->

		<?php
		$other = csgame_get_bonuses(
			array(
				'posts_per_page' => 4,
				'post__not_in'   => array( $id ),
			)
		);
		if ( $other->have_posts() ) :
			?>
			<section class="section other-bonuses-section">
				<h2 class="section__title"><?php esc_html_e( 'Další bonusy', 'csgame' ); ?></h2>
				<div class="other-bonuses">
					<?php
					while ( $other->have_posts() ) :
						$other->the_post();
						?>
						<article class="mini-bonus card">
							<div class="mini-bonus__head">
								<?php $oc = (int) csgame_meta( 'casino_id' ); ?>
								<?php echo csgame_logo( $oc ? $oc : get_the_ID() ); // phpcs:ignore ?>
								<h3 class="mini-bonus__title"><?php echo esc_html( csgame_meta( 'bonus_amount' ) ? csgame_meta( 'bonus_amount' ) : get_the_title() ); ?></h3>
							</div>
							<p class="mini-bonus__text muted"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
							<div class="mini-bonus__actions">
								<a class="btn btn--ghost" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Detail', 'csgame' ); ?></a>
								<a class="btn btn--primary" href="<?php echo esc_url( csgame_aff_url( get_the_ID() ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Do kasina', 'csgame' ); ?></a>
							</div>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>

		<div class="container--narrow">
			<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'author', 'subscribe' ) ) ); ?>
		</div>
	</div>
	<?php
endwhile;
get_footer();
