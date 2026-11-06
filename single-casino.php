<?php
/**
 * Recenze kasina (ТЗ bod 5).
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	$id     = get_the_ID();
	$rating = (float) csgame_meta( 'rating' );
	$pros   = csgame_lines( csgame_meta( 'pros' ) );
	$cons   = csgame_lines( csgame_meta( 'cons' ) );
	$table  = csgame_pairs( csgame_meta( 'review_table' ) );
	$provs  = csgame_pairs( csgame_meta( 'providers_list' ) );
	$blocks = array(
		array( 'coins', __( 'Způsoby vkladu', 'csgame' ), csgame_lines( csgame_meta( 'deposit_methods' ) ), 'deposit_methods' ),
		array( 'repeat', __( 'Způsoby výběru', 'csgame' ), csgame_lines( csgame_meta( 'withdrawal_methods' ) ), 'withdrawal_methods' ),
		array( 'play', __( 'Typy her', 'csgame' ), csgame_lines( csgame_meta( 'game_types' ) ) ),
	);
	$bonuses = csgame_get_bonuses(
		array(
			'posts_per_page' => 20,
			'meta_key'       => '_cs_casino_id', // phpcs:ignore
			'meta_value'     => $id, // phpcs:ignore
		)
	);
	?>
	<div class="container">
		<section class="casino-hero card">
			<?php echo csgame_logo( $id, 'logo-box logo-box--lg' ); // phpcs:ignore ?>
			<div class="casino-hero__body">
				<h1 class="page-head__title"><?php the_title(); ?></h1>
				<?php if ( csgame_meta( 'bonus_type_label' ) ) : ?>
					<span class="label"><?php echo csgame_icon( 'gift' ); // phpcs:ignore ?><?php echo esc_html( csgame_meta( 'bonus_type_label' ) ); ?></span>
				<?php endif; ?>
				<?php if ( csgame_meta( 'bonus_text' ) ) : ?>
					<p class="casino-hero__bonus"><?php echo esc_html( csgame_meta( 'bonus_text' ) ); ?></p>
				<?php endif; ?>
				<?php if ( has_excerpt() ) : ?>
					<p class="muted"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
			<div class="casino-hero__cta">
				<a class="btn btn--primary btn--lg btn--block" href="<?php echo esc_url( csgame_aff_url( $id ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Hrát v kasinu', 'csgame' ); ?></a>
				<?php if ( $bonuses->have_posts() ) : ?>
					<a class="btn btn--ghost btn--block" href="#bonusy"><?php esc_html_e( 'Další bonusy', 'csgame' ); ?></a>
				<?php endif; ?>
			</div>
		</section>

		<section class="score card">
			<div class="score__total">
				<span class="score__num"><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
				<?php echo csgame_stars( $rating, false ); // phpcs:ignore ?>
				<small class="muted"><?php esc_html_e( 'Hodnocení redakce', 'csgame' ); ?></small>
			</div>
			<?php if ( $pros ) : ?>
				<ul class="score__list score__list--pros">
					<li class="score__head"><?php esc_html_e( 'Plusy', 'csgame' ); ?></li>
					<?php foreach ( $pros as $p ) : ?>
						<li><?php echo csgame_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $p ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $cons ) : ?>
				<ul class="score__list score__list--cons">
					<li class="score__head"><?php esc_html_e( 'Mínusy', 'csgame' ); ?></li>
					<?php foreach ( $cons as $c ) : ?>
						<li><?php echo csgame_icon( 'x' ); // phpcs:ignore ?><?php echo esc_html( $c ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>

		<div class="expand-grid">
			<?php foreach ( $blocks as $b ) : ?>
				<?php
				if ( ! $b[2] ) {
					continue;
				}
				?>
				<details class="expand card">
					<summary>
						<span class="expand__icon"><?php echo csgame_icon( $b[0] ); // phpcs:ignore ?></span>
						<span class="expand__title"><?php echo esc_html( $b[1] ); ?> <small class="muted">(<?php echo count( $b[2] ); ?>)</small></span>
						<?php echo csgame_icon( 'chevron' ); // phpcs:ignore ?>
					</summary>
					<?php if ( ! empty( $b[3] ) ) : ?>
						<div class="expand__body"><?php echo csgame_pay_list( csgame_meta( $b[3] ) ); // phpcs:ignore ?></div>
					<?php else : ?>
						<ul class="chips">
							<?php foreach ( $b[2] as $item ) : ?>
								<li class="chip"><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>
		</div>

		<?php if ( $provs ) : ?>
			<section class="providers card">
				<h2 class="providers__title"><?php esc_html_e( 'Poskytovatelé her', 'csgame' ); ?></h2>
				<ul class="providers__list">
					<?php foreach ( $provs as $p ) : ?>
						<?php $href = isset( $p[1] ) && $p[1] ? $p[1] : '#' . sanitize_title( $p[0] ); ?>
						<li><a class="chip chip--link" href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $p[0] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php echo csgame_byline(); // phpcs:ignore ?>

		<?php if ( $table ) : ?>
			<details class="review-table card" open>
				<summary>
					<span><?php printf( esc_html__( '%s v kostce', 'csgame' ), esc_html( get_the_title() ) ); ?></span>
					<?php echo csgame_icon( 'chevron' ); // phpcs:ignore ?>
				</summary>
				<div class="table-wrap">
					<table class="kv-table">
						<tbody>
						<?php foreach ( $table as $row ) : ?>
							<tr><th scope="row"><?php echo esc_html( $row[0] ); ?></th><td><?php echo esc_html( isset( $row[1] ) ? $row[1] : '' ); ?></td></tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</details>
		<?php endif; ?>

		<?php if ( $bonuses->have_posts() ) : ?>
			<section class="section" id="bonusy">
				<h2 class="section__title"><?php printf( esc_html__( 'Všechny bonusy %s', 'csgame' ), esc_html( get_the_title() ) ); ?></h2>
				<ol class="bonus-list">
					<?php
					while ( $bonuses->have_posts() ) {
						$bonuses->the_post();
						get_template_part( 'template-parts/bonus-card', null, array( 'compact' => true ) );
					}
					wp_reset_postdata();
					?>
				</ol>
			</section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/disclaimer' ); ?>

		<div class="content-layout">
			<aside class="content-layout__aside"><?php csgame_toc(); ?></aside>
			<div class="content-layout__main">
				<div class="entry-content"><?php the_content(); ?></div>
				<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'faq', 'author' ) ) ); ?>
				<?php csgame_reviews_block(); ?>
			</div>
		</div>
	</div>
	<?php
	// Review schema.
	$schema = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'Review',
		'name'         => get_the_title(),
		'author'       => array( '@type' => 'Person', 'name' => get_the_author() ),
		'itemReviewed' => array( '@type' => 'Organization', 'name' => get_the_title() ),
		'reviewRating' => array( '@type' => 'Rating', 'ratingValue' => $rating, 'bestRating' => 5, 'worstRating' => 0 ),
		'datePublished' => get_the_date( 'c' ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
endwhile;
get_footer();
