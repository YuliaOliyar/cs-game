<?php
/**
 * Footer: název, textový blok, textové odkazy, copyright.
 *
 * @package CSGame
 */

?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="site-footer__top">
			<div class="site-footer__about">
				<p class="site-footer__title">
					<span class="logo-text__mark">CS</span>
					<?php echo esc_html( csgame_mod( 'csgame_footer_title' ) ); ?>
				</p>
				<div class="site-footer__text">
					<?php echo wpautop( wp_kses_post( csgame_mod( 'csgame_footer_text' ) ) ); // phpcs:ignore ?>
				</div>
			</div>
			<nav class="site-footer__links" aria-label="<?php esc_attr_e( 'Odkazy v patičce', 'csgame' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		</div>
		<div class="site-footer__bottom">
			<span class="badge-18">18+</span>
			<p class="site-footer__disclaimer"><?php echo esc_html( wp_strip_all_tags( csgame_mod( 'csgame_disclaimer' ) ) ); ?></p>
			<p class="site-footer__copy"><?php echo esc_html( csgame_mod( 'csgame_copyright' ) ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
