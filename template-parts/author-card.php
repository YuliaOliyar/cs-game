<?php
/**
 * Karta autora: fotka, jméno, pozice, sociální sítě, text.
 *
 * Args: user_id, heading (bool)
 *
 * @package CSGame
 */

$uid = ! empty( $args['user_id'] ) ? (int) $args['user_id'] : (int) get_post_field( 'post_author', get_the_ID() );
if ( ! $uid ) {
	return;
}
$heading = ! isset( $args['heading'] ) || $args['heading'];
$social  = array(
	'facebook'  => get_user_meta( $uid, 'csgame_facebook', true ),
	'x-social'  => get_user_meta( $uid, 'csgame_x', true ),
	'linkedin'  => get_user_meta( $uid, 'csgame_linkedin', true ),
	'instagram' => get_user_meta( $uid, 'csgame_instagram', true ),
);
$social  = array_filter( $social );
$pos     = get_user_meta( $uid, 'csgame_position', true );
$bio     = get_the_author_meta( 'description', $uid );
?>
<section class="author-card card">
	<div class="author-card__photo"><?php echo csgame_avatar( $uid, 120 ); // phpcs:ignore ?></div>
	<div class="author-card__body">
		<?php if ( $heading ) : ?>
			<p class="author-card__kicker"><?php esc_html_e( 'O autorovi', 'csgame' ); ?></p>
		<?php endif; ?>
		<h2 class="author-card__name">
			<a href="<?php echo esc_url( get_author_posts_url( $uid ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $uid ) ); ?></a>
		</h2>
		<?php if ( $pos ) : ?>
			<p class="author-card__pos"><?php echo esc_html( $pos ); ?></p>
		<?php endif; ?>
		<?php if ( $social ) : ?>
			<ul class="social">
				<?php foreach ( $social as $icon => $url ) : ?>
					<li><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener me" aria-label="<?php echo esc_attr( $icon ); ?>"><?php echo csgame_icon( $icon ); // phpcs:ignore ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $bio ) : ?>
			<div class="author-card__bio"><?php echo wpautop( wp_kses_post( $bio ) ); // phpcs:ignore ?></div>
		<?php endif; ?>
	</div>
</section>
