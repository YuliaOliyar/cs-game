<?php
/**
 * Table of contents for H2 / H3.
 *
 * Kotvy se generují deterministicky z textu nadpisu, takže obsah
 * lze vypsat ještě před samotným textem.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_heading_id( $text, &$used ) {
	$id = sanitize_title( remove_accents( wp_strip_all_tags( $text ) ) );
	if ( '' === $id ) {
		$id = 'sekce';
	}
	$base = $id;
	$n    = 2;
	while ( isset( $used[ $id ] ) ) {
		$id = $base . '-' . $n++;
	}
	$used[ $id ] = true;
	return $id;
}

/**
 * Collect H2/H3 items from content.
 */
function csgame_toc_items( $content ) {
	$items = array();
	$used  = array();
	if ( preg_match_all( '/<h([23])([^>]*)>(.*?)<\/h\1>/is', $content, $m, PREG_SET_ORDER ) ) {
		foreach ( $m as $h ) {
			if ( preg_match( '/\bid=["\']([^"\']+)["\']/', $h[2], $idm ) ) {
				$id          = $idm[1];
				$used[ $id ] = true;
			} else {
				$id = csgame_heading_id( $h[3], $used );
			}
			$items[] = array(
				'level' => (int) $h[1],
				'id'    => $id,
				'text'  => wp_strip_all_tags( $h[3] ),
			);
		}
	}
	return $items;
}

/**
 * Add ids to headings in rendered content.
 */
function csgame_add_heading_ids( $content ) {
	if ( is_admin() || is_feed() ) {
		return $content;
	}
	$used = array();
	return preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
		function ( $h ) use ( &$used ) {
			if ( preg_match( '/\bid=["\']([^"\']+)["\']/', $h[2], $idm ) ) {
				$used[ $idm[1] ] = true;
				return $h[0];
			}
			$id = csgame_heading_id( $h[3], $used );
			return '<h' . $h[1] . $h[2] . ' id="' . esc_attr( $id ) . '">' . $h[3] . '</h' . $h[1] . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'csgame_add_heading_ids', 20 );

/**
 * Print TOC box.
 */
function csgame_toc( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! csgame_flag( 'show_toc', '1', $post_id ) ) {
		return;
	}
	$content = get_post_field( 'post_content', $post_id );
	$content = do_blocks( $content );
	$items   = csgame_toc_items( $content );
	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="toc" aria-label="<?php esc_attr_e( 'Obsah článku', 'csgame' ); ?>">
		<button class="toc__toggle" type="button" aria-expanded="true">
			<span><?php esc_html_e( 'Obsah', 'csgame' ); ?></span>
			<?php echo csgame_icon( 'chevron' ); // phpcs:ignore ?>
		</button>
		<ol class="toc__list">
			<?php
			$open_sub = false;
			$started  = false;
			foreach ( $items as $item ) {
				$link = '<a href="#' . esc_attr( $item['id'] ) . '">' . esc_html( $item['text'] ) . '</a>';
				if ( 2 === $item['level'] ) {
					if ( $open_sub ) {
						echo '</ol>';
						$open_sub = false;
					}
					if ( $started ) {
						echo '</li>';
					}
					echo '<li>' . $link; // phpcs:ignore
					$started = true;
				} else {
					if ( ! $started ) {
						echo '<li>';
						$started = true;
					}
					if ( ! $open_sub ) {
						echo '<ol class="toc__sub">';
						$open_sub = true;
					}
					echo '<li>' . $link . '</li>'; // phpcs:ignore
				}
			}
			if ( $open_sub ) {
				echo '</ol>';
			}
			if ( $started ) {
				echo '</li>';
			}
			?>
		</ol>
	</nav>
	<?php
}
