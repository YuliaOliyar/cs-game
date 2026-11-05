<?php
/**
 * Blok výhod (pole „Blok s ikonami“).
 *
 * Řádek = „ikona | text“. Ikona může být emoji (🎁) nebo název SVG ikony
 * (gift, shield, clock, coins, trophy, repeat, check, alert, mail, play).
 *
 * @package CSGame
 */

$allowed = array( 'gift', 'shield', 'clock', 'coins', 'trophy', 'repeat', 'check', 'alert', 'mail', 'play' );
$rows    = csgame_pairs( csgame_meta( 'features' ) );
if ( ! $rows ) {
	return;
}
?>
<ul class="features" style="--cols:<?php echo (int) min( 4, count( $rows ) ); ?>">
	<?php foreach ( $rows as $i => $r ) : ?>
		<?php
		$text = isset( $r[1] ) ? $r[1] : $r[0];
		$ico  = isset( $r[1] ) ? $r[0] : '✅';
		?>
		<li class="features__item features__item--<?php echo (int) ( $i % 4 ); ?>">
			<span class="features__icon" aria-hidden="true">
				<?php
				if ( in_array( $ico, $allowed, true ) ) {
					echo csgame_icon( $ico ); // phpcs:ignore
				} else {
					echo '<span class="features__emoji">' . esc_html( $ico ) . '</span>';
				}
				?>
			</span>
			<span class="features__text"><?php echo esc_html( $text ); ?></span>
		</li>
	<?php endforeach; ?>
</ul>
