<?php
/**
 * Vitrína (toplist) kasin s vizuálně zvýrazněným 1. místem.
 *
 * Args: query (WP_Query), title
 *
 * @package CSGame
 */

$q     = isset( $args['query'] ) ? $args['query'] : csgame_get_casinos();
$title = ! empty( $args['title'] ) ? $args['title'] : '';

if ( ! $q->have_posts() ) {
	return;
}
?>
<section class="section toplist-wrap">
	<?php if ( $title ) : ?>
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>
	<ol class="toplist">
		<?php
		$pos = 0;
		while ( $q->have_posts() ) :
			$q->the_post();
			$pos++;
			$id    = get_the_ID();
			$adv   = array_slice( csgame_lines( csgame_meta( 'advantages', $id ) ), 0, 4 );
			$bonus = csgame_meta( 'bonus_text', $id );
			$btype = csgame_meta( 'bonus_type_label', $id );
			?>
			<li class="casino-row<?php echo 1 === $pos ? ' casino-row--top' : ''; ?><?php echo csgame_is_unlicensed( $id ) ? ' casino-row--unlic' : ''; ?>">
				<?php if ( 1 === $pos ) : ?>
					<span class="casino-row__ribbon"><?php echo csgame_icon( 'trophy' ); // phpcs:ignore ?> <?php esc_html_e( 'Nejlepší volba', 'csgame' ); ?></span>
				<?php endif; ?>
				<span class="casino-row__pos"><?php echo (int) $pos; ?></span>

				<div class="casino-row__brand">
					<?php echo csgame_logo( $id ); // phpcs:ignore ?>
					<div>
						<h3 class="casino-row__name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php echo csgame_stars( csgame_meta( 'rating', $id ) ); // phpcs:ignore ?>
					</div>
				</div>

				<?php if ( $adv ) : ?>
					<ul class="casino-row__adv">
						<?php foreach ( $adv as $item ) : ?>
							<li><?php echo csgame_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="casino-row__bonus">
					<span class="casino-row__bonus-ico"><?php echo csgame_icon( 'gift' ); // phpcs:ignore ?></span>
					<?php if ( $btype ) : ?>
						<small><?php echo esc_html( $btype ); ?></small>
					<?php endif; ?>
					<?php if ( $bonus ) : ?>
						<strong><?php echo esc_html( $bonus ); ?></strong>
					<?php endif; ?>
				</div>

				<div class="casino-row__cta">
					<?php if ( csgame_is_unlicensed( $id ) ) : ?>
						<span class="label label--danger casino-row__unlic"><?php echo csgame_icon( 'alert' ); // phpcs:ignore ?> <?php esc_html_e( 'Bez licence', 'csgame' ); ?></span>
					<?php else : ?>
						<a class="btn btn--primary btn--block" href="<?php echo esc_url( csgame_aff_url( $id ) ); ?>" <?php echo csgame_aff_attrs(); // phpcs:ignore ?>><?php esc_html_e( 'Hrát', 'csgame' ); ?></a>
					<?php endif; ?>
					<a class="btn btn--ghost btn--block" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Recenze', 'csgame' ); ?></a>
				</div>
			</li>
		<?php endwhile; ?>
	</ol>
	<p class="toplist__note muted">18+ | <?php esc_html_e( 'Hrajte zodpovědně. Platí obchodní podmínky kasina.', 'csgame' ); ?></p>
</section>
<?php
wp_reset_postdata();
