<?php
/**
 * Vyskakovací okno odběru novinek.
 *
 * Zobrazí se po X sekundách / při pokusu o odchod, max. jednou za N dní.
 * Okamžitě otevřít: přidejte do adresy ?newsletter=1
 *
 * @package CSGame
 */

$items = csgame_lines( csgame_mod( 'csgame_pop_items' ) );
$state = isset( $_GET['sub'] ) ? sanitize_key( $_GET['sub'] ) : ''; // phpcs:ignore
?>
<div class="nl-pop" id="nl-pop" role="dialog" aria-modal="true" aria-labelledby="nl-pop-title" hidden<?php echo $state ? ' data-state="' . esc_attr( $state ) . '"' : ''; ?>>
	<div class="nl-pop__backdrop" data-nl-close></div>
	<div class="nl-pop__box" role="document">
		<button class="nl-pop__close" type="button" data-nl-close aria-label="<?php esc_attr_e( 'Zavřít', 'csgame' ); ?>"><?php echo csgame_icon( 'x' ); // phpcs:ignore ?></button>

		<div class="nl-pop__content">
			<p class="nl-pop__kicker"><?php echo csgame_icon( 'mail' ); // phpcs:ignore ?> <?php esc_html_e( 'Newsletter CS Game', 'csgame' ); ?></p>
			<h2 class="nl-pop__title" id="nl-pop-title"><?php echo esc_html( csgame_mod( 'csgame_pop_title' ) ); ?></h2>

			<?php if ( $items ) : ?>
				<ul class="nl-pop__list">
					<?php foreach ( $items as $item ) : ?>
						<li><span class="nl-pop__check"><?php echo csgame_icon( 'check' ); // phpcs:ignore ?></span><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( 'ok' === $state ) : ?>
				<p class="nl-pop__done"><?php echo csgame_icon( 'check' ); // phpcs:ignore ?> <?php esc_html_e( 'Hotovo! Přihlášení k odběru proběhlo úspěšně.', 'csgame' ); ?></p>
			<?php else : ?>
				<p class="nl-pop__note"><?php echo esc_html( csgame_mod( 'csgame_pop_note' ) ); ?></p>
				<form class="nl-pop__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="csgame_subscribe">
					<input type="hidden" name="src" value="popup">
					<input type="hidden" name="redirect" value="<?php echo esc_url( csgame_current_url() ); ?>">
					<?php wp_nonce_field( 'csgame_subscribe', 'csgame_sub_nonce' ); ?>
					<input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
					<div class="nl-pop__row">
						<label class="screen-reader-text" for="nl-pop-email"><?php esc_html_e( 'E-mail', 'csgame' ); ?></label>
						<input id="nl-pop-email" type="email" name="email" required placeholder="<?php esc_attr_e( 'Zadejte svůj e-mail', 'csgame' ); ?>" autocomplete="email">
						<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Začít odebírat', 'csgame' ); ?></button>
					</div>
					<label class="nl-pop__consent">
						<input type="checkbox" name="consent" value="1" required>
						<span><?php esc_html_e( 'Je mi 18+ a souhlasím s obchodními podmínkami a zpracováním osobních údajů pro zasílání novinek.', 'csgame' ); ?></span>
					</label>
					<?php if ( 'err' === $state ) : ?>
						<p class="nl-pop__err"><?php esc_html_e( 'Zadejte prosím platný e-mail a potvrďte souhlas.', 'csgame' ); ?></p>
					<?php endif; ?>
				</form>
			<?php endif; ?>
		</div>

		<div class="nl-pop__art" aria-hidden="true">
			<svg viewBox="0 0 260 240" xmlns="http://www.w3.org/2000/svg">
				<defs>
					<linearGradient id="nlg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ffe08a"/><stop offset=".6" stop-color="#f5b82e"/><stop offset="1" stop-color="#c7860f"/></linearGradient>
					<radialGradient id="nlglow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#f5b82e" stop-opacity=".45"/><stop offset="1" stop-color="#f5b82e" stop-opacity="0"/></radialGradient>
				</defs>
				<circle cx="130" cy="125" r="115" fill="url(#nlglow)"/>
				<!-- machine -->
				<rect x="55" y="62" width="150" height="150" rx="22" fill="#1e2330" stroke="url(#nlg)" stroke-width="5"/>
				<rect x="72" y="40" width="116" height="34" rx="12" fill="url(#nlg)"/>
				<text x="130" y="65" text-anchor="middle" font-family="Manrope,Arial,sans-serif" font-weight="800" font-size="22" fill="#1a1405" letter-spacing="2">WIN</text>
				<rect x="70" y="92" width="120" height="66" rx="10" fill="#0d0f15"/>
				<g font-family="Manrope,Arial,sans-serif" font-weight="800" font-size="34" text-anchor="middle" fill="url(#nlg)">
					<rect x="76" y="98" width="34" height="54" rx="6" fill="#171b25"/><text x="93" y="138">7</text>
					<rect x="113" y="98" width="34" height="54" rx="6" fill="#171b25"/><text x="130" y="138">7</text>
					<rect x="150" y="98" width="34" height="54" rx="6" fill="#171b25"/><text x="167" y="138">7</text>
				</g>
				<rect x="78" y="170" width="104" height="22" rx="11" fill="#22c55e"/>
				<rect x="98" y="177" width="64" height="8" rx="4" fill="#04140a" opacity=".35"/>
				<!-- lever -->
				<rect x="205" y="110" width="12" height="30" rx="4" fill="#262c3b"/>
				<line x1="217" y1="125" x2="232" y2="78" stroke="#8a92a6" stroke-width="6" stroke-linecap="round"/>
				<circle cx="233" cy="72" r="11" fill="#ef4444"/>
				<!-- coins -->
				<g>
					<circle cx="40" cy="70" r="16" fill="url(#nlg)"/><circle cx="40" cy="70" r="10" fill="none" stroke="#1a1405" stroke-width="2" stroke-dasharray="3 3"/>
					<circle cx="228" cy="190" r="13" fill="url(#nlg)"/><circle cx="228" cy="190" r="8" fill="none" stroke="#1a1405" stroke-width="2" stroke-dasharray="3 3"/>
					<circle cx="30" cy="180" r="11" fill="url(#nlg)"/>
					<circle cx="206" cy="30" r="9" fill="url(#nlg)"/>
				</g>
				<!-- sparkles -->
				<g fill="#ffe08a">
					<path d="M60 22l3 8 8 3-8 3-3 8-3-8-8-3 8-3z"/>
					<path d="M240 140l2 5 5 2-5 2-2 5-2-5-5-2 5-2z"/>
					<path d="M20 125l2 5 5 2-5 2-2 5-2-5-5-2 5-2z"/>
				</g>
			</svg>
		</div>
	</div>
</div>
