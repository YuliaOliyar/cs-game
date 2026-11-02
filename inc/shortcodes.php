<?php
/**
 * Shortcodes – bloky lze vložit kamkoli do textu.
 *
 * [cs_toplist count="10" ids="12,15" title="Nejlepší kasina"]
 * [cs_bonuses type="free-spiny" count="6" filter="1"]
 * [cs_faq]  [cs_disclaimer]  [cs_subscribe]  [cs_author]
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_part( $slug, $args = array() ) {
	ob_start();
	get_template_part( 'template-parts/' . $slug, null, $args );
	return ob_get_clean();
}

add_shortcode(
	'cs_toplist',
	function ( $atts ) {
		$a   = shortcode_atts( array( 'count' => 10, 'ids' => '', 'title' => '' ), $atts );
		$ids = $a['ids'] ? array_filter( array_map( 'absint', explode( ',', $a['ids'] ) ) ) : array();
		return csgame_part( 'toplist', array( 'query' => csgame_get_casinos( (int) $a['count'], $ids ), 'title' => $a['title'] ) );
	}
);

add_shortcode(
	'cs_bonuses',
	function ( $atts ) {
		$a    = shortcode_atts( array( 'type' => '', 'count' => 10, 'filter' => '0', 'title' => '' ), $atts );
		$args = array( 'posts_per_page' => (int) $a['count'] );
		if ( $a['type'] ) {
			$args['tax_query'] = array( array( 'taxonomy' => 'bonus_type', 'field' => 'slug', 'terms' => sanitize_title( $a['type'] ) ) ); // phpcs:ignore
		}
		return csgame_part(
			'bonus-list',
			array(
				'query'  => csgame_get_bonuses( $args ),
				'filter' => '1' === $a['filter'],
				'title'  => $a['title'],
			)
		);
	}
);

add_shortcode( 'cs_faq', function () { return csgame_part( 'faq', array( 'post_id' => get_the_ID() ) ); } );
add_shortcode( 'cs_disclaimer', function () { return csgame_part( 'disclaimer' ); } );
add_shortcode( 'cs_subscribe', function () { return csgame_subscribe_form(); } );
add_shortcode(
	'cs_author',
	function ( $atts ) {
		$a = shortcode_atts( array( 'id' => 0 ), $atts );
		return csgame_part( 'author-card', array( 'user_id' => $a['id'] ? (int) $a['id'] : (int) get_post_field( 'post_author', get_the_ID() ) ) );
	}
);
