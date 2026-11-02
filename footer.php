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
				<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( csgame_mod( 'csgame_footer_title' ) ); ?>">
					<?php get_template_part( 'template-parts/logo', null, array( 'id' => 'foot', 'height' => 40 ) ); ?>
				</a>
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
