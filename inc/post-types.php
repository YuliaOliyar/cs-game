<?php
/**
 * Custom post types & taxonomies.
 *
 * casino  – recenze kasina        /recenze/{slug}/
 * bonus   – recenze bonusu        /bonus/{slug}/     přehled = stránka se šablonou „Bonusy“
 * slot    – herní automat         /casino/automat/{slug}/  přehled = stránka „Automaty“
 * provider (tax for slot)         /casino/automaty/{slug}/
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_register_types() {

	register_post_type(
		'casino',
		array(
			'labels'       => array(
				'name'          => __( 'Kasina', 'csgame' ),
				'singular_name' => __( 'Kasino', 'csgame' ),
				'add_new_item'  => __( 'Přidat kasino', 'csgame' ),
				'edit_item'     => __( 'Upravit kasino', 'csgame' ),
				'menu_name'     => __( 'Kasina', 'csgame' ),
			),
			'public'       => true,
			'has_archive'  => 'recenze',
			'rewrite'      => array( 'slug' => 'recenze', 'with_front' => false ),
			'menu_icon'    => 'dashicons-awards',
			'menu_position' => 5,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments', 'page-attributes', 'revisions' ),
			'show_in_rest' => true,
		)
	);

	register_post_type(
		'bonus',
		array(
			'labels'       => array(
				'name'          => __( 'Bonusy', 'csgame' ),
				'singular_name' => __( 'Bonus', 'csgame' ),
				'add_new_item'  => __( 'Přidat bonus', 'csgame' ),
				'edit_item'     => __( 'Upravit bonus', 'csgame' ),
			),
			'public'       => true,
			'has_archive'  => false, // stránka se šablonou „Bonusy“
			'rewrite'      => array( 'slug' => 'bonus', 'with_front' => false ),
			'menu_icon'    => 'dashicons-tickets-alt',
			'menu_position' => 6,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'page-attributes', 'revisions' ),
			'show_in_rest' => true,
		)
	);

	register_post_type(
		'slot',
		array(
			'labels'       => array(
				'name'          => __( 'Automaty', 'csgame' ),
				'singular_name' => __( 'Automat', 'csgame' ),
				'add_new_item'  => __( 'Přidat automat', 'csgame' ),
				'edit_item'     => __( 'Upravit automat', 'csgame' ),
			),
			'public'       => true,
			'has_archive'  => false, // stránka se šablonou „Automaty“ (/casino/automaty/)
			'rewrite'      => array( 'slug' => 'casino/automat', 'with_front' => false ),
			'menu_icon'    => 'dashicons-games',
			'menu_position' => 7,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments', 'revisions' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'provider',
		array( 'slot' ),
		array(
			'labels'            => array(
				'name'          => __( 'Poskytovatelé', 'csgame' ),
				'singular_name' => __( 'Poskytovatel', 'csgame' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'casino/automaty', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'slot_theme',
		array( 'slot' ),
		array(
			'labels'            => array(
				'name'          => __( 'Tématika automatů', 'csgame' ),
				'singular_name' => __( 'Tématika', 'csgame' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'tematika', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'bonus_type',
		array( 'bonus', 'slot' ),
		array(
			'labels'            => array(
				'name'          => __( 'Typy bonusů', 'csgame' ),
				'singular_name' => __( 'Typ bonusu', 'csgame' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'bonusy/typ', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'bonus_tag',
		array( 'bonus' ),
		array(
			'labels'            => array(
				'name'          => __( 'Hashtagy bonusů', 'csgame' ),
				'singular_name' => __( 'Hashtag', 'csgame' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'bonusy/tag', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'csgame_register_types' );

/**
 * Default bonus types + flush rewrites on activation.
 */
function csgame_activate() {
	csgame_register_types();
	$types = array(
		'vitaci-bonus'      => 'Vítací bonusy',
		'za-registraci'     => 'Za registraci',
		'free-spiny'        => 'Free spiny',
		'promo-kody'        => 'Promo kódy',
		'bonus-za-vklad'    => 'Bonus za vklad',
		'bonus-bez-vkladu'  => 'Bez vkladu',
	);
	foreach ( $types as $slug => $name ) {
		if ( ! term_exists( $slug, 'bonus_type' ) ) {
			wp_insert_term( $name, 'bonus_type', array( 'slug' => $slug ) );
		}
	}
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'csgame_activate' );

/**
 * Archive page sizes.
 */
function csgame_archive_sizes( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_tax( array( 'bonus_type', 'bonus_tag' ) ) ) {
		$q->set( 'posts_per_page', 50 );
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	if ( $q->is_post_type_archive( 'casino' ) ) {
		$q->set( 'posts_per_page', 30 );
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
	if ( $q->is_tax( array( 'provider', 'slot_theme' ) ) ) {
		$q->set( 'posts_per_page', 24 );
	}
}
add_action( 'pre_get_posts', 'csgame_archive_sizes' );

/**
 * Author archive: include casino / bonus / slot reviews too.
 */
function csgame_author_types( $q ) {
	if ( ! is_admin() && $q->is_main_query() && $q->is_author() ) {
		$q->set( 'post_type', array( 'post', 'casino', 'bonus', 'slot' ) );
	}
}
add_action( 'pre_get_posts', 'csgame_author_types' );
