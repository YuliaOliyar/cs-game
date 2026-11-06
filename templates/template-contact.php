<?php
/**
 * Template Name: Kontakt
 * Template Post Type: page
 *
 * H1, úvod, kontaktní karty (pole „Karty“ – „emoji | Nadpis | Text“), obsah stránky, kontaktní formulář.
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	$cards = csgame_pairs( csgame_meta( 'info_cards' ) );
	?>
	<div class="container">
		<?php get_template_part( 'template-parts/page-head', null, array( 'dates' => false ) ); ?>
		<div class="contact-layout">
			<div class="contact-layout__info">
				<?php if ( $cards ) : ?>
					<ul class="info-cards info-cards--stack">
						<?php foreach ( $cards as $c ) : ?>
							<li class="info-card card">
								<span class="info-card__emoji" aria-hidden="true"><?php echo esc_html( $c[0] ); ?></span>
								<div>
									<h2 class="info-card__title"><?php echo esc_html( isset( $c[1] ) ? $c[1] : '' ); ?></h2>
									<p class="info-card__text"><?php echo wp_kses_post( make_clickable( isset( $c[2] ) ? $c[2] : '' ) ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( get_the_content() ) : ?>
					<div class="entry-content"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
			<div class="contact-layout__form">
				<?php echo csgame_contact_form(); // phpcs:ignore ?>
			</div>
		</div>
		<?php get_template_part( 'template-parts/page-bottom', null, array( 'order' => array( 'faq' ) ) ); ?>
	</div>
	<?php
endwhile;
get_footer();
