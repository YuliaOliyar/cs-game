<?php
/**
 * CS Game — functions and definitions.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

define( 'CSGAME_VERSION', '1.0.0' );
define( 'CSGAME_DIR', get_template_directory() );
define( 'CSGAME_URI', get_template_directory_uri() );

require CSGAME_DIR . '/inc/helpers.php';
require CSGAME_DIR . '/inc/post-types.php';
require CSGAME_DIR . '/inc/meta-boxes.php';
require CSGAME_DIR . '/inc/breadcrumbs.php';
require CSGAME_DIR . '/inc/toc.php';
require CSGAME_DIR . '/inc/reviews.php';
require CSGAME_DIR . '/inc/subscribe.php';
require CSGAME_DIR . '/inc/contact.php';
require CSGAME_DIR . '/inc/slot-filter.php';
require CSGAME_DIR . '/inc/customizer.php';
require CSGAME_DIR . '/inc/shortcodes.php';
require CSGAME_DIR . '/inc/user-fields.php';
require CSGAME_DIR . '/inc/term-fields.php';

/**
 * Theme setup.
 */
function csgame_setup() {
	load_theme_textdomain( 'csgame', CSGAME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 180,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'csgame-logo', 480, 270, false );
	add_image_size( 'csgame-card', 640, 360, true );
	add_image_size( 'csgame-slot', 400, 300, true );

	register_nav_menus(
		array(
			'primary'     => __( 'Hlavní menu (hlavička)', 'csgame' ),
			'footer'      => __( 'Odkazy v patičce', 'csgame' ),
			'interlinks'  => __( 'Prolinkování – svislý blok (hlavní stránka)', 'csgame' ),
			'bonus_links' => __( 'Prolinkování – vodorovný blok (bonusy)', 'csgame' ),
			'providers'   => __( 'Prolinkování – poskytovatelé (automaty)', 'csgame' ),
		)
	);
}
add_action( 'after_setup_theme', 'csgame_setup' );

/**
 * Content width.
 */
function csgame_content_width() {
	$GLOBALS['content_width'] = 860;
}
add_action( 'after_setup_theme', 'csgame_content_width', 0 );

/**
 * Styles & scripts.
 */
function csgame_assets() {
	wp_enqueue_style( 'csgame-fonts', 'https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Inter:wght@400;500;600&display=swap', array(), null );
	wp_enqueue_style( 'csgame-style', get_stylesheet_uri(), array(), CSGAME_VERSION );
	wp_enqueue_style( 'csgame-main', CSGAME_URI . '/assets/css/main.css', array( 'csgame-style' ), CSGAME_VERSION );

	wp_enqueue_script( 'csgame-main', CSGAME_URI . '/assets/js/main.js', array(), CSGAME_VERSION, true );
	wp_localize_script(
		'csgame-main',
		'CSGAME',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'csgame_vote' ),
			'i18n'    => array(
				'copied'   => __( 'Zkopírováno!', 'csgame' ),
				'thanks'   => __( 'Děkujeme za hodnocení!', 'csgame' ),
				'showMore' => __( 'Zobrazit více', 'csgame' ),
				'showLess' => __( 'Zobrazit méně', 'csgame' ),
			),
			'popup'   => array(
				'delay' => (int) csgame_mod( 'csgame_pop_delay' ),
				'days'  => max( 0, (int) csgame_mod( 'csgame_pop_days' ) ),
				'exit'  => (bool) csgame_mod( 'csgame_pop_exit' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'csgame_assets' );

/**
 * Preconnect for Google Fonts.
 */
function csgame_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'csgame_resource_hints', 10, 2 );


/**
 * Excerpt length / more.
 */
add_filter( 'excerpt_length', function () { return 26; } );
add_filter( 'excerpt_more', function () { return '…'; } );

/**
 * Affiliate links: rel attributes.
 */
function csgame_aff_attrs() {
	return 'target="_blank" rel="nofollow sponsored noopener"';
}

/**
 * SVG favicon fallback when no Site Icon is set.
 */
function csgame_favicon() {
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( CSGAME_URI . '/assets/img/favicon.svg' ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'csgame_favicon', 2 );

