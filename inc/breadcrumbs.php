<?php
/**
 * Breadcrumbs (with BreadcrumbList schema).
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_breadcrumb_items() {
	$items = array( array( __( 'Domů', 'csgame' ), home_url( '/' ) ) );

	if ( is_front_page() ) {
		return array();
	}

	if ( is_page() && ! get_queried_object()->post_parent ) {
		$items[] = array( get_the_title( get_queried_object() ), '' );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$type = get_post_type( $post );

		if ( in_array( $type, array( 'casino', 'bonus', 'slot' ), true ) ) {
			$sec = csgame_section( $type );
			if ( $sec ) {
				$items[] = $sec;
			}
		}
		if ( 'slot' === $type ) {
			$terms = get_the_terms( $post, 'provider' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$items[] = array( $terms[0]->name, get_term_link( $terms[0] ) );
			}
		}
		if ( 'post' === $type ) {
			$blog = (int) get_option( 'page_for_posts' );
			if ( $blog ) {
				$items[] = array( get_the_title( $blog ), get_permalink( $blog ) );
			}
			$cats = get_the_category( $post->ID );
			if ( $cats ) {
				$items[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
			}
		}
		if ( 'page' === $type && $post->post_parent ) {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $anc ) {
				$items[] = array( get_the_title( $anc ), get_permalink( $anc ) );
			}
		}
		$items[] = array( get_the_title( $post ), '' );
	} elseif ( is_home() ) {
		$items[] = array( single_post_title( '', false ), '' );
	} elseif ( is_post_type_archive() ) {
		$items[] = array( post_type_archive_title( '', false ), '' );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		if ( 'provider' === $term->taxonomy ) {
			$sec = csgame_section( 'slot' );
			if ( $sec ) {
				$items[] = $sec;
			}
		}
		if ( in_array( $term->taxonomy, array( 'bonus_type', 'bonus_tag' ), true ) ) {
			$sec = csgame_section( 'bonus' );
			if ( $sec ) {
				$items[] = $sec;
			}
		}
		$items[] = array( $term->name, '' );
	} elseif ( is_author() ) {
		$items[] = array( get_the_author_meta( 'display_name', get_queried_object_id() ), '' );
	} elseif ( is_search() ) {
		$items[] = array( sprintf( __( 'Hledání: %s', 'csgame' ), get_search_query() ), '' );
	} elseif ( is_404() ) {
		$items[] = array( __( 'Stránka nenalezena', 'csgame' ), '' );
	}

	return $items;
}

/**
 * Emoji for a breadcrumb item (by URL or current context).
 */
function csgame_crumb_emoji( $item, $index, $is_last ) {
	if ( 0 === $index ) {
		return '🏠';
	}
	$map = array();
	$bp  = csgame_template_page( 'templates/template-bonuses.php' );
	$sp  = csgame_template_page( 'templates/template-slots.php' );
	$blg = (int) get_option( 'page_for_posts' );
	if ( $bp ) {
		$map[ get_permalink( $bp ) ] = '🎁';
	}
	if ( $sp ) {
		$map[ get_permalink( $sp ) ] = '🎰';
	}
	if ( $blg ) {
		$map[ get_permalink( $blg ) ] = '📰';
	}
	$arch = get_post_type_archive_link( 'casino' );
	if ( $arch ) {
		$map[ $arch ] = '⭐';
	}
	if ( $item[1] && isset( $map[ $item[1] ] ) ) {
		return $map[ $item[1] ];
	}
	if ( $is_last ) {
		if ( is_page_template( 'templates/template-bonuses.php' ) || is_tax( array( 'bonus_type', 'bonus_tag' ) ) ) {
			return '🎁';
		}
		if ( is_page_template( 'templates/template-slots.php' ) || is_tax( 'provider' ) ) {
			return '🎰';
		}
		if ( is_home() || is_category() ) {
			return '📰';
		}
		if ( is_post_type_archive( 'casino' ) ) {
			return '⭐';
		}
		if ( is_author() ) {
			return '👤';
		}
	}
	return '';
}

function csgame_breadcrumbs() {
	$items = csgame_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(),
	);
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Drobečková navigace', 'csgame' ) . '"><ol>';
	foreach ( $items as $i => $item ) {
		$last  = ( $i === count( $items ) - 1 );
		$emoji = apply_filters( 'csgame_breadcrumb_emoji', csgame_crumb_emoji( $item, $i, $last ), $item, $i );
		$label = ( $emoji ? '<span class="crumb-emoji" aria-hidden="true">' . esc_html( $emoji ) . '</span>' : '' ) . esc_html( $item[0] );
		echo '<li>';
		if ( $item[1] && ! $last ) {
			echo '<a href="' . esc_url( $item[1] ) . '">' . $label . '</a>'; // phpcs:ignore
		} else {
			echo '<span aria-current="page">' . $label . '</span>'; // phpcs:ignore
		}
		echo '</li>';
		$el = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => wp_strip_all_tags( $item[0] ),
		);
		if ( $item[1] ) {
			$el['item'] = $item[1];
		}
		$schema['itemListElement'][] = $el;
	}
	echo '</ol></nav>';
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
}
