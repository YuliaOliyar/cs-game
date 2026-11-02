<?php
/**
 * Header: logo „CS Game“, menu, drobečková navigace (bez vyhledávání).
 *
 * @package CSGame
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0d0f15">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Přejít k obsahu', 'csgame' ); ?></a>

<header class="site-header">
	<div class="container site-header__inner">
		<div class="site-logo">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php get_template_part( 'template-parts/logo', null, array( 'id' => 'head', 'height' => 44 ) ); ?>
				</a>
			<?php endif; ?>
		</div>

		<button class="nav-toggle" type="button" aria-controls="primary-nav" aria-expanded="false">
			<?php echo csgame_icon( 'menu' ); // phpcs:ignore ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'csgame' ); ?></span>
		</button>

		<nav id="primary-nav" class="primary-nav" aria-label="<?php esc_attr_e( 'Hlavní menu', 'csgame' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 2,
					'fallback_cb'    => 'csgame_menu_fallback',
				)
			);
			?>
		</nav>
	</div>
</header>

<?php if ( ! is_front_page() ) : ?>
	<div class="crumbs-bar">
		<div class="container"><?php csgame_breadcrumbs(); ?></div>
	</div>
<?php endif; ?>

<main id="main" class="site-main">
