<?php
/**
 * Template Name: O nás
 * Template Post Type: page
 *
 * H1 + úvod, automatické statistiky webu, obsah, náš příběh (Kroky), hodnoty (Karty),
 * tým (autoři), výzva ke kontaktu.
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	$cards = csgame_pairs( csgame_meta( 'info_cards' ) );
	$steps = csgame_pairs( csgame_meta( 'info_steps' ) );
	$ids   = array_filter( array_map( 'absint', explode( ',', (string) csgame_meta( 'info_team' ) ) ) );
	$team  = $ids
		? get_users( array( 'include' => $ids, 'orderby' => 'include' ) )
		: get_users( array( 'role__in' => array( 'administrator', 'editor', 'author' ), 'orderby' => 'ID' ) );
	$count = function ( $type ) {
		$c = wp_count_posts( $type );
		return isset( $c->publish ) ? (int) $c->publish : 0;
	};
	$stats = array(
		array( '⭐', $count( 'casino' ), __( 'recenzí kasin', 'csgame' ) ),
		array( '🎁', $count( 'bonus' ), __( 'ověřených bonusů', 'csgame' ) ),
		array( '🎰', $count( 'slot' ), __( 'otestovaných automatů', 'csgame' ) ),
		array( '✍️', count( $team ), __( 'autorů a expertů', 'csgame' ) ),
	);
	$contact = csgame_template_page( 'templates/template-contact.php' );
	$method  = csgame_template_page( 'templates/template-methodology.php' );
	?>
	<div class="container">
		<?php get_template_part( 'template-parts/page-head', null, array( 'dates' => false, 'class' => 'page-head--center' ) ); ?>

		<ul class="about-stats">
			<?php foreach ( $stats as $s ) : ?>
				<li class="about-stats__item card">
					<span class="about-stats__emoji" aria-hidden="true"><?php echo esc_html( $s[0] ); ?></span>
					<strong class="about-stats__num"><?php echo esc_html( number_format_i18n( $s[1] ) ); ?></strong>
					<span class="about-stats__label"><?php echo esc_html( $s[2] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( get_the_content() ) : ?>
			<div class="entry-content entry-content--narrow"><?php the_content(); ?></div>
		<?php endif; ?>

		<?php if ( $steps ) : ?>
			<section class="section">
				<h2 class="section__title"><?php esc_html_e( 'Náš příběh', 'csgame' ); ?></h2>
				<ol class="timeline">
					<?php foreach ( $steps as $s ) : ?>
						<li class="timeline__item">
							<span class="timeline__dot" aria-hidden="true"></span>
							<h3 class="timeline__title"><?php echo esc_html( $s[0] ); ?></h3>
							<?php if ( isset( $s[1] ) ) : ?>
								<p class="muted"><?php echo esc_html( $s[1] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endif; ?>

		<?php if ( $cards ) : ?>
			<section class="section">
				<h2 class="section__title"><?php esc_html_e( 'Na čem nám záleží', 'csgame' ); ?></h2>
				<ul class="info-cards">
					<?php foreach ( $cards as $c ) : ?>
						<li class="info-card card">
							<span class="info-card__emoji" aria-hidden="true"><?php echo esc_html( $c[0] ); ?></span>
							<div>
								<h3 class="info-card__title"><?php echo esc_html( isset( $c[1] ) ? $c[1] : '' ); ?></h3>
								<p class="info-card__text"><?php echo esc_html( isset( $c[2] ) ? $c[2] : '' ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( $team ) : ?>
			<section class="section">
				<h2 class="section__title"><?php esc_html_e( 'Náš tým', 'csgame' ); ?></h2>
				<ul class="team">
					<?php foreach ( $team as $u ) : ?>
						<li class="team__item card">
							<a class="team__photo" href="<?php echo esc_url( get_author_posts_url( $u->ID ) ); ?>"><?php echo csgame_avatar( $u->ID, 160 ); // phpcs:ignore ?></a>
							<h3 class="team__name"><a href="<?php echo esc_url( get_author_posts_url( $u->ID ) ); ?>"><?php echo esc_html( $u->display_name ); ?></a></h3>
							<?php $pos = get_user_meta( $u->ID, 'csgame_position', true ); ?>
							<?php if ( $pos ) : ?>
								<p class="team__pos"><?php echo esc_html( $pos ); ?></p>
							<?php endif; ?>
							<p class="team__bio muted"><?php echo esc_html( wp_trim_words( get_the_author_meta( 'description', $u->ID ), 22, '…' ) ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( $contact || $method ) : ?>
			<section class="about-cta card">
				<div>
					<h2 class="about-cta__title"><?php esc_html_e( 'Máte dotaz nebo jste našli chybu?', 'csgame' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'Ozvěte se nám – každou zprávu čteme a recenze pravidelně aktualizujeme.', 'csgame' ); ?></p>
				</div>
				<div class="about-cta__btns">
					<?php if ( $contact ) : ?>
						<a class="btn btn--gold" href="<?php echo esc_url( get_permalink( $contact ) ); ?>"><?php echo csgame_icon( 'mail' ); // phpcs:ignore ?> <?php esc_html_e( 'Kontaktujte nás', 'csgame' ); ?></a>
					<?php endif; ?>
					<?php if ( $method ) : ?>
						<a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $method ) ); ?>"><?php esc_html_e( 'Jak hodnotíme kasina', 'csgame' ); ?></a>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>
	<?php
endwhile;
get_footer();
