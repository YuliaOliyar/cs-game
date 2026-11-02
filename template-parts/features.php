<?php
/**
 * Blok textu s ikonami (pole „Blok s ikonami“).
 *
 * @package CSGame
 */

$allowed = array( 'gift', 'shield', 'clock', 'coins', 'trophy', 'repeat', 'check', 'alert', 'mail', 'play' );
$rows    = csgame_pairs( csgame_meta( 'features' ) );
if ( ! $rows ) {
	return;
}
?>
<ul class="features">
	<?php foreach ( $rows as $r ) : ?>
		<?php
		$icon = isset( $r[1] ) && in_array( $r[0], $allowed, true ) ? $r[0] : 'check';
		$text = isset( $r[1] ) ? $r[1] : $r[0];
		?>
		<li class="features__item">
			<span class="features__icon"><?php echo csgame_icon( $icon ); // phpcs:ignore ?></span>
			<span><?php echo esc_html( $text ); ?></span>
		</li>
	<?php endforeach; ?>
</ul>
