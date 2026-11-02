<?php
/**
 * Hlavička stránky: H1, úvodní text, autor/data.
 *
 * Args: title, intro (HTML), byline (bool), dates (bool), class
 *
 * @package CSGame
 */

$a = wp_parse_args(
	$args,
	array(
		'title'  => get_the_title(),
		'intro'  => is_singular() ? csgame_meta( 'intro' ) : '',
		'byline' => false,
		'dates'  => true,
		'class'  => '',
	)
);
?>
<header class="page-head <?php echo esc_attr( $a['class'] ); ?>">
	<h1 class="page-head__title"><?php echo esc_html( $a['title'] ); ?></h1>
	<?php if ( $a['intro'] ) : ?>
		<div class="page-head__intro"><?php echo wpautop( wp_kses_post( $a['intro'] ) ); // phpcs:ignore ?></div>
	<?php endif; ?>
	<?php
	if ( $a['byline'] && is_singular() ) {
		echo csgame_byline(); // phpcs:ignore
	} elseif ( $a['dates'] && is_singular() ) {
		echo '<div class="page-head__dates">' . csgame_dates() . '</div>'; // phpcs:ignore
	}
	?>
</header>
