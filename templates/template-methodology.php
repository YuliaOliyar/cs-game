<?php
/**
 * Template Name: Jak hodnotíme
 * Template Post Type: page
 *
 * H1 + úvod, kritéria s vahami (Karty – „emoji | Název | váha % | popis“),
 * stupnice hodnocení, postup testování (Kroky), obsah s TOC, FAQ, autor.
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	$crit  = csgame_pairs( csgame_meta( 'info_cards' ) );
	$steps = csgame_pairs( csgame_meta( 'info_steps' ) );
	$scale = apply_filters(
		'csgame_rating_scale',
		array(
			array( '4,5 – 5,0', __( 'Výborné', 'csgame' ), __( 'Špička trhu, doporučujeme bez výhrad.', 'csgame' ), 'top' ),
			array( '4,0 – 4,4', __( 'Velmi dobré', 'csgame' ), __( 'Spolehlivé kasino s drobnými slabinami.', 'csgame' ), 'good' ),
			array( '3,0 – 3,9', __( 'Průměrné', 'csgame' ), __( 'Funguje, ale konkurence nabízí víc.', 'csgame' ), 'mid' ),
			array( '0 – 2,9', __( 'Slabé', 'csgame' ), __( 'Nedoporučujeme. Kasina bez licence MF ČR nehodnotíme kladně nikdy.', 'csgame' ), 'low' ),
		)
	);
	?>
	<div class="container">
		<?php get_template_part( 'template-parts/page-head', null, array( 'byline' => true ) ); ?>

		<?php if ( $crit ) : ?>
			<section class="section">
				<h2 class="section__title"><?php esc_html_e( 'Kritéria a jejich váha', 'csgame' ); ?></h2>
				<ul class="criteria">
					<?php foreach ( $crit as $c ) : ?>
						<?php $w = isset( $c[2] ) ? (int) $c[2] : 0; ?>
						<li class="criteria__item card">
							<div class="criteria__head">
								<span class="criteria__emoji" aria-hidden="true"><?php echo esc_html( $c[0] ); ?></span>
								<h3 class="criteria__name"><?php echo esc_html( isset( $c[1] ) ? $c[1] : '' ); ?></h3>
								<?php if ( $w ) : ?>
									<span class="criteria__weight"><?php echo (int) $w; ?> %</span>
								<?php endif; ?>
							</div>
							<?php if ( $w ) : ?>
								<span class="criteria__bar" aria-hidden="true"><span style="width:<?php echo (int) min( 100, $w * 3 ); ?>%"></span></span>
							<?php endif; ?>
							<?php if ( isset( $c[3] ) ) : ?>
								<p class="criteria__text muted"><?php echo esc_html( $c[3] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<section class="section">
			<h2 class="section__title"><?php esc_html_e( 'Co znamená naše hodnocení', 'csgame' ); ?></h2>
			<ul class="scale">
				<?php foreach ( $scale as $s ) : ?>
					<li class="scale__item scale__item--<?php echo esc_attr( $s[3] ); ?>">
						<strong class="scale__range">★ <?php echo esc_html( $s[0] ); ?></strong>
						<span class="scale__name"><?php echo esc_html( $s[1] ); ?></span>
						<span class="scale__text"><?php echo esc_html( $s[2] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<?php if ( $steps ) : ?>
			<section class="section">
				<h2 class="section__title"><?php esc_html_e( 'Jak probíhá test kasina', 'csgame' ); ?></h2>
				<ol class="msteps">
					<?php foreach ( $steps as $i => $s ) : ?>
						<li class="msteps__item card">
							<span class="msteps__num"><?php echo (int) $i + 1; ?></span>
							<h3 class="msteps__title"><?php echo esc_html( $s[0] ); ?></h3>
							<?php if ( isset( $s[1] ) ) : ?>
								<p class="muted"><?php echo esc_html( $s[1] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endif; ?>

		<div class="content-layout">
			<aside class="content-layout__aside"><?php csgame_toc(); ?></aside>
			<div class="content-layout__main">
				<div class="entry-content"><?php the_content(); ?></div>
				<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'faq', 'author' ) ) ); ?>
			</div>
		</div>
	</div>
	<?php
endwhile;
get_footer();
