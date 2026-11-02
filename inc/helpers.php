<?php
/**
 * Helper functions.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get theme post meta (prefixed with _cs_).
 */
function csgame_meta( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return get_post_meta( $post_id, '_cs_' . $key, true );
}

/**
 * Split a textarea into non-empty lines.
 */
function csgame_lines( $text ) {
	if ( ! $text ) {
		return array();
	}
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
}

/**
 * Split lines of "a | b | c" into arrays.
 */
function csgame_pairs( $text ) {
	$out = array();
	foreach ( csgame_lines( $text ) as $line ) {
		$out[] = array_map( 'trim', explode( '|', $line ) );
	}
	return $out;
}

/**
 * Star rating markup. $rating from 0 to 5.
 */
function csgame_stars( $rating, $show_number = true ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	$pct    = ( $rating / 5 ) * 100;
	$html   = '<span class="stars" role="img" aria-label="' . esc_attr( sprintf( __( 'Hodnocení %s z 5', 'csgame' ), number_format_i18n( $rating, 1 ) ) ) . '">';
	$html  .= '<span class="stars__bg">★★★★★</span><span class="stars__fg" style="width:' . esc_attr( $pct ) . '%">★★★★★</span></span>';
	if ( $show_number ) {
		$html .= '<span class="stars__num">' . esc_html( number_format_i18n( $rating, 1 ) ) . '</span>';
	}
	return $html;
}

/**
 * Affiliate URL of a casino / bonus.
 */
function csgame_aff_url( $post_id ) {
	$url = csgame_meta( 'affiliate_url', $post_id );
	if ( ! $url && 'bonus' === get_post_type( $post_id ) ) {
		$casino = (int) csgame_meta( 'casino_id', $post_id );
		if ( $casino ) {
			$url = csgame_meta( 'affiliate_url', $casino );
		}
	}
	return $url ? $url : '#';
}

/**
 * Logo of casino (featured image) or fallback initials.
 */
function csgame_logo( $post_id, $class = 'logo-box' ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return '<span class="' . esc_attr( $class ) . '">' . get_the_post_thumbnail( $post_id, 'csgame-logo', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $post_id ) ) ) ) . '</span>';
	}
	$title = get_the_title( $post_id );
	$init  = function_exists( 'mb_substr' ) ? mb_strtoupper( mb_substr( $title, 0, 2 ) ) : strtoupper( substr( $title, 0, 2 ) );
	return '<span class="' . esc_attr( $class ) . ' logo-box--fallback">' . esc_html( $init ) . '</span>';
}

/**
 * Published / updated dates line.
 */
function csgame_dates( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$pub     = get_the_date( '', $post_id );
	$mod     = get_the_modified_date( '', $post_id );
	ob_start();
	?>
	<span class="meta-dates">
		<span class="meta-dates__item">
			<svg class="ico" aria-hidden="true"><use href="#i-calendar"></use></svg>
			<?php esc_html_e( 'Publikováno:', 'csgame' ); ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>"><?php echo esc_html( $pub ); ?></time>
		</span>
		<span class="meta-dates__item">
			<svg class="ico" aria-hidden="true"><use href="#i-refresh"></use></svg>
			<?php esc_html_e( 'Aktualizováno:', 'csgame' ); ?>
			<time datetime="<?php echo esc_attr( get_the_modified_date( 'c', $post_id ) ); ?>"><?php echo esc_html( $mod ); ?></time>
		</span>
	</span>
	<?php
	return ob_get_clean();
}

/**
 * Author + fact check + dates bar.
 */
function csgame_byline( $post_id = null, $with_checker = true ) {
	$post_id   = $post_id ? $post_id : get_the_ID();
	$author_id = (int) get_post_field( 'post_author', $post_id );
	$checker   = $with_checker ? (int) csgame_meta( 'fact_checker', $post_id ) : 0;
	ob_start();
	?>
	<div class="byline">
		<a class="byline__person" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
			<?php echo csgame_avatar( $author_id, 40 ); // phpcs:ignore ?>
			<span>
				<small><?php esc_html_e( 'Autor', 'csgame' ); ?></small>
				<strong><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></strong>
			</span>
		</a>
		<?php if ( $checker ) : ?>
			<a class="byline__person" href="<?php echo esc_url( get_author_posts_url( $checker ) ); ?>">
				<?php echo csgame_avatar( $checker, 40 ); // phpcs:ignore ?>
				<span>
					<small><?php esc_html_e( 'Ověřil(a) fakta', 'csgame' ); ?></small>
					<strong><?php echo esc_html( get_the_author_meta( 'display_name', $checker ) ); ?></strong>
				</span>
			</a>
		<?php endif; ?>
		<?php echo csgame_dates( $post_id ); // phpcs:ignore ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Avatar: custom photo URL from profile or gravatar.
 */
function csgame_avatar( $user_id, $size = 96 ) {
	$photo = get_user_meta( $user_id, 'csgame_photo', true );
	$name  = get_the_author_meta( 'display_name', $user_id );
	if ( $photo ) {
		return '<img class="avatar" src="' . esc_url( $photo ) . '" width="' . (int) $size . '" height="' . (int) $size . '" alt="' . esc_attr( $name ) . '" loading="lazy">';
	}
	return get_avatar( $user_id, $size, '', $name, array( 'class' => 'avatar' ) );
}

/**
 * Query casinos for the toplist.
 */
function csgame_get_casinos( $limit = 10, $ids = array() ) {
	$args = array(
		'post_type'      => 'casino',
		'posts_per_page' => $limit,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	);
	if ( $ids ) {
		$args['post__in'] = array_map( 'intval', $ids );
		$args['orderby']  = 'post__in';
	}
	return new WP_Query( $args );
}

/**
 * Query bonuses.
 */
function csgame_get_bonuses( $args = array() ) {
	$defaults = array(
		'post_type'      => 'bonus',
		'posts_per_page' => 20,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	);
	return new WP_Query( wp_parse_args( $args, $defaults ) );
}

/**
 * Inline SVG sprite (printed once in footer).
 */
function csgame_svg_sprite() {
	?>
	<svg xmlns="http://www.w3.org/2000/svg" style="display:none">
		<symbol id="i-calendar" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M3 5h18v16H3zM3 9h18M8 3v4M16 3v4"/></symbol>
		<symbol id="i-refresh" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M20 11a8 8 0 1 0-2.3 5.7M20 4v7h-7"/></symbol>
		<symbol id="i-check" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.5" d="m5 12 5 5L20 7"/></symbol>
		<symbol id="i-x" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2.5" d="M6 6l12 12M18 6 6 18"/></symbol>
		<symbol id="i-chevron" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="m6 9 6 6 6-6"/></symbol>
		<symbol id="i-copy" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M8 8h12v12H8zM4 16V4h12"/></symbol>
		<symbol id="i-gift" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M3 8h18v4H3zM5 12v9h14v-9M12 8v13M12 8S10 3 7.5 4 9 8 12 8zm0 0s2-5 4.5-4S15 8 12 8z"/></symbol>
		<symbol id="i-coins" viewBox="0 0 24 24"><ellipse cx="9" cy="7" rx="6" ry="3" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" d="M3 7v5c0 1.7 2.7 3 6 3s6-1.3 6-3V7M9 15v3c0 1.7 2.7 3 6 3s6-1.3 6-3v-5c0-1.5-2-2.7-5-3"/></symbol>
		<symbol id="i-repeat" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M17 2l4 4-4 4M3 11V9a3 3 0 0 1 3-3h15M7 22l-4-4 4-4M21 13v2a3 3 0 0 1-3 3H3"/></symbol>
		<symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"/><path fill="none" stroke="currentColor" stroke-width="2" d="M12 7v5l3 2"/></symbol>
		<symbol id="i-trophy" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M7 4h10v5a5 5 0 0 1-10 0zM7 6H3v2a4 4 0 0 0 4 4M17 6h4v2a4 4 0 0 1-4 4M12 14v4M8 21h8"/></symbol>
		<symbol id="i-shield" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z"/></symbol>
		<symbol id="i-alert" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M12 3 2 21h20zM12 10v5M12 18v.5"/></symbol>
		<symbol id="i-menu" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M3 6h18M3 12h18M3 18h18"/></symbol>
		<symbol id="i-mail" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M3 5h18v14H3zM3 6l9 7 9-7"/></symbol>
		<symbol id="i-play" viewBox="0 0 24 24"><path fill="currentColor" d="M8 5v14l11-7z"/></symbol>
		<symbol id="i-filter" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M3 5h18l-7 8v6l-4-2v-4z"/></symbol>
		<symbol id="i-arrow" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M5 12h14M13 6l6 6-6 6"/></symbol>
		<symbol id="i-facebook" viewBox="0 0 24 24"><path fill="currentColor" d="M14 8h3V4h-3c-2.8 0-4 1.8-4 4.3V10H7v4h3v8h4v-8h3l1-4h-4V8.6c0-.4.3-.6 1-.6z"/></symbol>
		<symbol id="i-x-social" viewBox="0 0 24 24"><path fill="currentColor" d="M17.5 3h3.2l-7 8 8.3 10h-6.5l-5-6.2L4.8 21H1.6l7.5-8.6L1.2 3h6.6l4.5 5.7zM16.4 19.2h1.8L6.7 4.7H4.8z"/></symbol>
		<symbol id="i-linkedin" viewBox="0 0 24 24"><path fill="currentColor" d="M4 3a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM2 9h4v12H2zm7 0h4v1.7c.6-1 1.9-2 4-2 4 0 5 2.4 5 6V21h-4v-5.5c0-1.5-.3-3-2-3s-3 1.2-3 3V21H9z"/></symbol>
		<symbol id="i-instagram" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/></symbol>
	</svg>
	<?php
}
add_action( 'wp_footer', 'csgame_svg_sprite', 1 );

/**
 * Icon shorthand.
 */
function csgame_icon( $name, $class = 'ico' ) {
	return '<svg class="' . esc_attr( $class ) . '" aria-hidden="true"><use href="#i-' . esc_attr( $name ) . '"></use></svg>';
}

/**
 * Fallback menu when none is assigned.
 */
function csgame_menu_fallback() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Kasina', 'csgame' ) . '</a></li>';
	foreach ( array( 'bonus', 'slot', 'casino' ) as $pt ) {
		$sec = csgame_section( $pt );
		if ( $sec && $sec[1] ) {
			echo '<li><a href="' . esc_url( $sec[1] ) . '">' . esc_html( $sec[0] ) . '</a></li>';
		}
	}
	$blog = (int) get_option( 'page_for_posts' );
	if ( $blog ) {
		echo '<li><a href="' . esc_url( get_permalink( $blog ) ) . '">' . esc_html( get_the_title( $blog ) ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Pagination.
 */
function csgame_pagination( $query = null ) {
	$args = array(
		'mid_size'  => 1,
		'prev_text' => '‹',
		'next_text' => '›',
		'type'      => 'list',
	);
	if ( $query ) {
		$args['total']   = $query->max_num_pages;
		$args['current'] = max( 1, get_query_var( 'paged' ) );
	}
	$links = paginate_links( $args );
	if ( $links ) {
		echo '<nav class="pagination" aria-label="' . esc_attr__( 'Stránkování', 'csgame' ) . '">' . $links . '</nav>'; // phpcs:ignore
	}
}

/**
 * Page that uses a given template (cached).
 */
function csgame_template_page( $template ) {
	static $cache = array();
	if ( isset( $cache[ $template ] ) ) {
		return $cache[ $template ];
	}
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template', // phpcs:ignore
			'meta_value' => $template, // phpcs:ignore
			'number'     => 1,
		)
	);
	$cache[ $template ] = $pages ? (int) $pages[0]->ID : 0;
	return $cache[ $template ];
}

/**
 * Section [name, url] for breadcrumbs / links by post type.
 */
function csgame_section( $type ) {
	if ( 'bonus' === $type || 'slot' === $type ) {
		$page = csgame_template_page( 'bonus' === $type ? 'templates/template-bonuses.php' : 'templates/template-slots.php' );
		return $page ? array( get_the_title( $page ), get_permalink( $page ) ) : null;
	}
	if ( 'casino' === $type ) {
		return array( csgame_mod( 'csgame_casinos_title' ), get_post_type_archive_link( 'casino' ) );
	}
	return null;
}
