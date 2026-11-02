<?php
/**
 * Recenze automatu (ТЗ bod 7).
 *
 * Druhý textový blok (7.7) oddělte v editoru blokem „Více“ (<!--more-->).
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	$id     = get_the_ID();
	$rating = (float) csgame_meta( 'rating' );
	$demo   = csgame_meta( 'demo_url' );
	$terms  = get_the_terms( $id, 'provider' );
	$prov   = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
	$parts  = get_extended( get_post_field( 'post_content', $id ) );
	$vol    = array( 'nizka' => __( 'Nízká', 'csgame' ), 'stredni' => __( 'Střední', 'csgame' ), 'vysoka' => __( 'Vysoká', 'csgame' ) );
	$rows   = array_filter(
		array(
			__( 'Poskytovatel', 'csgame' ) => $prov ? $prov->name : '',
			__( 'RTP', 'csgame' )          => csgame_meta( 'rtp' ) ? number_format_i18n( (float) csgame_meta( 'rtp' ), 2 ) . ' %' : '',
			__( 'Válce', 'csgame' )        => csgame_meta( 'reels' ),
			__( 'Výherní linie', 'csgame' ) => csgame_meta( 'lines' ),
			__( 'Volatilita', 'csgame' )   => isset( $vol[ csgame_meta( 'volatility' ) ] ) ? $vol[ csgame_meta( 'volatility' ) ] : '',
			__( 'Min. sázka', 'csgame' )   => csgame_meta( 'min_bet' ),
			__( 'Max. sázka', 'csgame' )   => csgame_meta( 'max_bet' ),
			__( 'Max. výhra', 'csgame' )   => csgame_meta( 'max_win' ),
			__( 'Rok vydání', 'csgame' )   => csgame_meta( 'release' ),
		),
		'strlen'
	);
	$paytable = csgame_pairs( csgame_meta( 'paytable' ) );
	list( $u_avg, $u_cnt ) = csgame_user_rating( $id );
	?>
	<div class="container">
		<header class="page-head">
			<h1 class="page-head__title"><?php the_title(); ?></h1>
			<div class="slot-head-meta">
				<?php echo csgame_stars( $rating ); // phpcs:ignore ?>
				<?php if ( $prov ) : ?>
					<a class="label" href="<?php echo esc_url( get_term_link( $prov ) ); ?>"><?php echo esc_html( $prov->name ); ?></a>
				<?php endif; ?>
			</div>
		</header>

		<section class="demo card" data-demo>
			<?php if ( $demo ) : ?>
				<div class="demo__frame" data-src="<?php echo esc_url( $demo ); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'class' => 'demo__poster' ) ); ?>
					<?php endif; ?>
					<button class="demo__play btn btn--primary btn--lg" type="button"><?php echo csgame_icon( 'play' ); // phpcs:ignore ?> <?php esc_html_e( 'Hrát demo zdarma', 'csgame' ); ?></button>
				</div>
			<?php elseif ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'class' => 'demo__poster' ) ); ?>
			<?php endif; ?>
			<div class="demo__bar">
				<span class="muted">18+ | <?php esc_html_e( 'Demo verze je zdarma a bez registrace.', 'csgame' ); ?></span>
				<?php if ( csgame_meta( 'affiliate_url' ) ) : ?>
					<a class="btn btn--primary" href="<?php echo esc_url( csgame_meta( 'affiliate_url' ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Hrát o peníze', 'csgame' ); ?></a>
				<?php endif; ?>
			</div>
		</section>

		<?php
		get_template_part(
			'template-parts/bonus-list',
			null,
			array(
				'query' => csgame_get_bonuses( array( 'posts_per_page' => 3 ) ),
				'title' => __( 'Bonusy pro hru na automatech', 'csgame' ),
			)
		);

		$similar_args = array(
			'post_type'      => 'slot',
			'posts_per_page' => 4,
			'post__not_in'   => array( $id ),
			'no_found_rows'  => true,
		);
		if ( $prov ) {
			$similar_args['tax_query'] = array( array( 'taxonomy' => 'provider', 'terms' => $prov->term_id ) ); // phpcs:ignore
		}
		$similar = new WP_Query( $similar_args );
		if ( $similar->have_posts() ) :
			?>
			<section class="section">
				<h2 class="section__title"><?php esc_html_e( 'Podobné automaty', 'csgame' ); ?></h2>
				<div class="slot-grid slot-grid--4">
					<?php
					while ( $similar->have_posts() ) {
						$similar->the_post();
						get_template_part( 'template-parts/slot-card' );
					}
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>

		<?php echo csgame_byline(); // phpcs:ignore ?>

		<div class="content-layout">
			<aside class="content-layout__aside">
				<?php if ( $rows ) : ?>
					<div class="spec card">
						<h2 class="spec__title"><?php esc_html_e( 'Parametry automatu', 'csgame' ); ?></h2>
						<?php if ( csgame_meta( 'rtp' ) ) : ?>
							<div class="spec__rtp">
								<span>RTP</span>
								<strong><?php echo esc_html( number_format_i18n( (float) csgame_meta( 'rtp' ), 2 ) ); ?> %</strong>
								<span class="spec__bar"><span style="width:<?php echo esc_attr( min( 100, max( 0, ( (float) csgame_meta( 'rtp' ) - 85 ) / 15 * 100 ) ) ); ?>%"></span></span>
							</div>
						<?php endif; ?>
						<table class="kv-table kv-table--compact">
							<tbody>
							<?php foreach ( $rows as $k => $v ) : ?>
								<tr><th scope="row"><?php echo esc_html( $k ); ?></th><td><?php echo esc_html( $v ); ?></td></tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
				<?php csgame_toc(); ?>
			</aside>
			<div class="content-layout__main">
				<div class="entry-content"><?php echo apply_filters( 'the_content', $parts['main'] ); // phpcs:ignore ?></div>

				<?php if ( $paytable ) : ?>
					<section class="section paytable">
						<h2 class="section__title"><?php esc_html_e( 'Výherní tabulka', 'csgame' ); ?></h2>
						<div class="table-wrap">
							<table>
								<thead><tr><th><?php esc_html_e( 'Symbol', 'csgame' ); ?></th><th>3×</th><th>4×</th><th>5×</th></tr></thead>
								<tbody>
								<?php foreach ( $paytable as $r ) : ?>
									<tr>
										<?php for ( $c = 0; $c < 4; $c++ ) : ?>
											<td><?php echo esc_html( isset( $r[ $c ] ) ? $r[ $c ] : '–' ); ?></td>
										<?php endfor; ?>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</section>
				<?php endif; ?>

				<?php if ( trim( $parts['extended'] ) ) : ?>
					<div class="entry-content"><?php echo apply_filters( 'the_content', $parts['extended'] ); // phpcs:ignore ?></div>
				<?php endif; ?>

				<div class="verdict card">
					<div>
						<p class="verdict__label"><?php esc_html_e( 'Naše hodnocení', 'csgame' ); ?></p>
						<?php echo csgame_stars( $rating ); // phpcs:ignore ?>
					</div>
					<div>
						<p class="verdict__label"><?php esc_html_e( 'Hodnocení hráčů', 'csgame' ); ?></p>
						<?php echo csgame_stars( $u_avg ); // phpcs:ignore ?>
						<small class="muted">(<?php echo (int) $u_cnt; ?>)</small>
					</div>
					<a class="btn btn--ghost" href="#respond"><?php esc_html_e( 'Ohodnotit automat', 'csgame' ); ?></a>
				</div>
			</div>
		</div>

		<?php get_template_part( 'template-parts/toplist', null, array( 'query' => csgame_get_casinos( 5 ), 'title' => sprintf( __( 'Kde hrát %s o peníze', 'csgame' ), get_the_title() ) ) ); ?>

		<div class="container--narrow">
			<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'disclaimer', 'faq', 'author' ) ) ); ?>
			<?php csgame_reviews_block(); ?>
		</div>
	</div>
	<?php
endwhile;
get_footer();
