<?php
/**
 * Logo „CS Game“ – kasinový žeton + wordmark (inline SVG).
 *
 * Args: id (unikátní přípona pro gradient), height (px)
 *
 * @package CSGame
 */

$uid = isset( $args['id'] ) ? sanitize_key( $args['id'] ) : 'h';
$h   = isset( $args['height'] ) ? (int) $args['height'] : 44;
$g   = 'csg-' . $uid;
?>
<svg class="site-logo__svg" width="<?php echo (int) round( $h * 230 / 52 ); ?>" height="<?php echo (int) $h; ?>" viewBox="0 0 230 52" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="CS Game">
	<defs>
		<linearGradient id="<?php echo esc_attr( $g ); ?>" x1="0" y1="0" x2="1" y2="1">
			<stop offset="0" stop-color="#ffe08a"/><stop offset=".55" stop-color="#f5b82e"/><stop offset="1" stop-color="#c7860f"/>
		</linearGradient>
	</defs>
	<g transform="translate(26 26)">
		<circle r="24" fill="url(#<?php echo esc_attr( $g ); ?>)"/>
		<g fill="#0d0f15">
			<?php for ( $i = 0; $i < 6; $i++ ) : ?>
				<rect x="-3.5" y="-24" width="7" height="7" rx="1.5" transform="rotate(<?php echo (int) ( $i * 60 ); ?>)"/>
			<?php endfor; ?>
		</g>
		<circle r="15.5" fill="#0d0f15"/>
		<circle r="15.5" fill="none" stroke="url(#<?php echo esc_attr( $g ); ?>)" stroke-width="1.5" stroke-dasharray="2.2 2.6"/>
		<text y="5.2" text-anchor="middle" font-family="Manrope,Arial,sans-serif" font-weight="800" font-size="14.5" fill="url(#<?php echo esc_attr( $g ); ?>)">CS</text>
	</g>
	<text x="60" y="35" font-family="Manrope,Arial,sans-serif" font-weight="800" font-size="27" fill="#fff" letter-spacing="-.6">CS<tspan fill="url(#<?php echo esc_attr( $g ); ?>)" dx="6">Game</tspan></text>
</svg>
