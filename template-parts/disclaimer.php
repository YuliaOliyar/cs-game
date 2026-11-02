<?php
/**
 * Disclaimer 18+.
 *
 * @package CSGame
 */

if ( is_singular() && ! csgame_flag( 'show_disclaimer' ) ) {
	return;
}
?>
<aside class="disclaimer" role="note">
	<span class="badge-18">18+</span>
	<p><?php echo wp_kses_post( csgame_mod( 'csgame_disclaimer' ) ); ?></p>
</aside>
