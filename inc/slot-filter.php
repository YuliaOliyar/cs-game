<?php
/**
 * Filtr automatů: poskytovatel, linie, RTP, tématika, typ bonusu.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_slot_ranges() {
	return array(
		'lines' => array(
			'1-10'  => '1–10',
			'11-25' => '11–25',
			'26-50' => '26–50',
			'51-'   => '51+',
		),
		'rtp'   => array(
			'-94'   => __( 'do 94 %', 'csgame' ),
			'94-96' => '94–96 %',
			'96-'   => __( 'nad 96 %', 'csgame' ),
		),
	);
}

function csgame_range_meta_query( $key, $range ) {
	$parts = explode( '-', $range );
	$min   = '' !== $parts[0] ? (float) $parts[0] : null;
	$max   = isset( $parts[1] ) && '' !== $parts[1] ? (float) $parts[1] : null;
	if ( null !== $min && null !== $max ) {
		return array( 'key' => '_cs_' . $key, 'value' => array( $min, $max ), 'compare' => 'BETWEEN', 'type' => 'DECIMAL(10,2)' );
	}
	if ( null !== $min ) {
		return array( 'key' => '_cs_' . $key, 'value' => $min, 'compare' => '>=', 'type' => 'DECIMAL(10,2)' );
	}
	return array( 'key' => '_cs_' . $key, 'value' => $max, 'compare' => '<', 'type' => 'DECIMAL(10,2)' );
}

/**
 * Apply GET filter params to query vars (array or WP_Query).
 *
 * @param WP_Query $q Query object.
 */
function csgame_apply_slot_filter( $q ) {
	// phpcs:disable WordPress.Security.NonceVerification
	$tax  = array();
	$meta = array();
	if ( ! empty( $_GET['f_provider'] ) && ! $q->is_tax( 'provider' ) ) {
		$tax[] = array( 'taxonomy' => 'provider', 'field' => 'slug', 'terms' => sanitize_title( wp_unslash( $_GET['f_provider'] ) ) );
	}
	if ( ! empty( $_GET['f_theme'] ) ) {
		$tax[] = array( 'taxonomy' => 'slot_theme', 'field' => 'slug', 'terms' => sanitize_title( wp_unslash( $_GET['f_theme'] ) ) );
	}
	if ( ! empty( $_GET['f_btype'] ) ) {
		$tax[] = array( 'taxonomy' => 'bonus_type', 'field' => 'slug', 'terms' => sanitize_title( wp_unslash( $_GET['f_btype'] ) ) );
	}
	$ranges = csgame_slot_ranges();
	foreach ( array( 'lines', 'rtp' ) as $key ) {
		$val = isset( $_GET[ 'f_' . $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'f_' . $key ] ) ) : '';
		if ( $val && isset( $ranges[ $key ][ $val ] ) ) {
			$meta[] = csgame_range_meta_query( $key, $val );
		}
	}
	$sort = isset( $_GET['f_sort'] ) ? sanitize_key( $_GET['f_sort'] ) : '';
	// phpcs:enable
	if ( $tax ) {
		$existing = array_filter( (array) $q->get( 'tax_query' ) );
		$q->set( 'tax_query', array_merge( array( 'relation' => 'AND' ), $existing, $tax ) );
	}
	if ( $meta ) {
		$q->set( 'meta_query', array_merge( array( 'relation' => 'AND' ), $meta ) );
	}
	if ( 'rating' === $sort || 'rtp' === $sort ) {
		$q->set( 'meta_key', '_cs_' . $sort );
		$q->set( 'orderby', 'meta_value_num' );
		$q->set( 'order', 'DESC' );
	} elseif ( 'az' === $sort ) {
		$q->set( 'orderby', 'title' );
		$q->set( 'order', 'ASC' );
	}
}

/**
 * Main query on provider / theme archives.
 */
function csgame_slot_filter_query( $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! $q->is_tax( array( 'provider', 'slot_theme' ) ) ) {
		return;
	}
	csgame_apply_slot_filter( $q );
}

/**
 * Custom slots query for the „Automaty“ page template.
 */
function csgame_slots_query( $per_page = 24 ) {
	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$q     = new WP_Query();
	$q->set( 'post_type', 'slot' );
	$q->set( 'posts_per_page', $per_page );
	$q->set( 'paged', $paged );
	csgame_apply_slot_filter( $q );
	$q->get_posts();
	return $q;
}
add_action( 'pre_get_posts', 'csgame_slot_filter_query', 20 );

/**
 * Select helper.
 */
function csgame_filter_select( $name, $label, $options, $current ) {
	echo '<label class="filter__field"><span>' . esc_html( $label ) . '</span><select name="' . esc_attr( $name ) . '">';
	echo '<option value="">' . esc_html__( 'Vše', 'csgame' ) . '</option>';
	foreach ( $options as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $current, (string) $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></label>';
}

function csgame_term_options( $taxonomy ) {
	$out   = array();
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			$out[ $t->slug ] = $t->name;
		}
	}
	return $out;
}

/**
 * Filter form.
 */
function csgame_slot_filter_form() {
	// phpcs:disable WordPress.Security.NonceVerification
	$g = function ( $k ) {
		return isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : '';
	};
	// phpcs:enable
	$ranges      = csgame_slot_ranges();
	$is_provider = is_tax( 'provider' );
	$action      = is_tax() ? get_term_link( get_queried_object() ) : get_permalink();
	?>
	<form class="filter card" method="get" action="<?php echo esc_url( $action ); ?>">
		<div class="filter__head">
			<?php echo csgame_icon( 'filter' ); // phpcs:ignore ?>
			<strong><?php esc_html_e( 'Filtr automatů', 'csgame' ); ?></strong>
		</div>
		<div class="filter__grid">
			<?php
			if ( $is_provider ) {
				echo '<label class="filter__field"><span>' . esc_html__( 'Poskytovatel', 'csgame' ) . '</span><select disabled><option>' . esc_html( single_term_title( '', false ) ) . '</option></select></label>';
			} else {
				csgame_filter_select( 'f_provider', __( 'Poskytovatel', 'csgame' ), csgame_term_options( 'provider' ), $g( 'f_provider' ) );
			}
			csgame_filter_select( 'f_lines', __( 'Linie', 'csgame' ), $ranges['lines'], $g( 'f_lines' ) );
			csgame_filter_select( 'f_rtp', __( 'RTP', 'csgame' ), $ranges['rtp'], $g( 'f_rtp' ) );
			csgame_filter_select( 'f_theme', __( 'Tématika', 'csgame' ), csgame_term_options( 'slot_theme' ), $g( 'f_theme' ) );
			csgame_filter_select( 'f_btype', __( 'Typ bonusu', 'csgame' ), csgame_term_options( 'bonus_type' ), $g( 'f_btype' ) );
			csgame_filter_select(
				'f_sort',
				__( 'Řazení', 'csgame' ),
				array(
					'rating' => __( 'Nejlépe hodnocené', 'csgame' ),
					'rtp'    => __( 'Nejvyšší RTP', 'csgame' ),
					'az'     => __( 'A–Z', 'csgame' ),
				),
				$g( 'f_sort' )
			);
			?>
		</div>
		<div class="filter__actions">
			<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Filtrovat', 'csgame' ); ?></button>
			<a class="btn btn--ghost" href="<?php echo esc_url( $action ); ?>"><?php esc_html_e( 'Zrušit filtr', 'csgame' ); ?></a>
		</div>
	</form>
	<?php
}
