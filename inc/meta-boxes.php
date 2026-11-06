<?php
/**
 * Meta boxes (bez závislosti na ACF).
 *
 * Víceřádková pole: jedna položka = jeden řádek.
 * Párová pole: „Levá část | Pravá část“ na řádek.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions per box.
 */
function csgame_meta_boxes() {
	$users = array( '' => __( '— nikdo —', 'csgame' ) );
	foreach ( get_users( array( 'capability' => 'edit_posts', 'fields' => array( 'ID', 'display_name' ) ) ) as $u ) {
		$users[ $u->ID ] = $u->display_name;
	}

	$casinos = array( '' => __( '— vyberte kasino —', 'csgame' ) );
	foreach ( get_posts( array( 'post_type' => 'casino', 'numberposts' => 200, 'orderby' => 'title', 'order' => 'ASC' ) ) as $c ) {
		$casinos[ $c->ID ] = $c->post_title;
	}

	return array(
		'csgame_page'   => array(
			'title'  => __( 'CS Game – obsah stránky', 'csgame' ),
			'screen' => array( 'page', 'post', 'casino', 'bonus', 'slot' ),
			'fields' => array(
				'intro'         => array( 'type' => 'textarea', 'label' => __( 'Úvodní text (pod nadpisem H1)', 'csgame' ) ),
				'features'      => array( 'type' => 'textarea', 'label' => __( 'Blok výhod – „emoji | text“, např. 🎁 | Ověřené bonusy (nebo název ikony: gift, shield, clock, coins, trophy)', 'csgame' ) ),
				'fact_checker'  => array( 'type' => 'select', 'label' => __( 'Fakta ověřil(a)', 'csgame' ), 'options' => $users ),
				'show_toc'      => array( 'type' => 'checkbox', 'label' => __( 'Zobrazit obsah článku (H2/H3)', 'csgame' ), 'default' => '1' ),
				'show_disclaimer' => array( 'type' => 'checkbox', 'label' => __( 'Zobrazit disclaimer 18+', 'csgame' ), 'default' => '1' ),
				'show_subscribe' => array( 'type' => 'checkbox', 'label' => __( 'Zobrazit formulář odběru', 'csgame' ), 'default' => '1' ),
				'faq_title'     => array( 'type' => 'text', 'label' => __( 'Nadpis FAQ', 'csgame' ), 'placeholder' => __( 'Často kladené otázky', 'csgame' ) ),
				'faq'           => array( 'type' => 'textarea', 'label' => __( 'FAQ – na řádek „Otázka | Odpověď“', 'csgame' ), 'rows' => 6 ),
			),
		),
		'csgame_toplist' => array(
			'title'  => __( 'CS Game – toplist kasin (vitrína)', 'csgame' ),
			'screen' => array( 'page', 'post', 'slot' ),
			'fields' => array(
				'toplist_show'  => array( 'type' => 'checkbox', 'label' => __( 'Zobrazit toplist kasin', 'csgame' ) ),
				'toplist_title' => array( 'type' => 'text', 'label' => __( 'Nadpis před toplistem', 'csgame' ), 'placeholder' => __( 'Nejlepší online kasina v Česku', 'csgame' ) ),
				'toplist_ids'   => array( 'type' => 'text', 'label' => __( 'ID kasin oddělená čárkou (prázdné = podle pořadí v administraci)', 'csgame' ) ),
				'toplist_count' => array( 'type' => 'number', 'label' => __( 'Počet kasin', 'csgame' ), 'placeholder' => '10' ),
			),
		),
		'csgame_casino' => array(
			'title'  => __( 'Údaje o kasinu', 'csgame' ),
			'screen' => array( 'casino' ),
			'fields' => array(
				'rating'             => array( 'type' => 'number', 'label' => __( 'Hodnocení (0–5)', 'csgame' ), 'step' => '0.1' ),
				'affiliate_url'      => array( 'type' => 'url', 'label' => __( 'Affiliate odkaz', 'csgame' ) ),
				'bonus_text'         => array( 'type' => 'text', 'label' => __( 'Hlavní bonus (krátce)', 'csgame' ), 'placeholder' => '300 Kč + 50 FS' ),
				'bonus_type_label'   => array( 'type' => 'text', 'label' => __( 'Typ hlavního bonusu', 'csgame' ), 'placeholder' => 'Vítací bonus' ),
				'advantages'         => array( 'type' => 'textarea', 'label' => __( 'Výhody pro toplist (řádek = výhoda)', 'csgame' ) ),
				'pros'               => array( 'type' => 'textarea', 'label' => __( 'Plusy (řádek = položka)', 'csgame' ) ),
				'cons'               => array( 'type' => 'textarea', 'label' => __( 'Mínusy (řádek = položka)', 'csgame' ) ),
				'deposit_methods'    => array( 'type' => 'textarea', 'label' => __( 'Způsoby vkladu – řádek = metoda, volitelně „Název | URL loga“', 'csgame' ) ),
				'withdrawal_methods' => array( 'type' => 'textarea', 'label' => __( 'Způsoby výběru – řádek = metoda, volitelně „Název | URL loga“', 'csgame' ) ),
				'game_types'         => array( 'type' => 'textarea', 'label' => __( 'Typy her (řádek = typ)', 'csgame' ) ),
				'providers_list'     => array( 'type' => 'textarea', 'label' => __( 'Poskytovatelé – „Název | #kotva-sekce“', 'csgame' ) ),
				'review_table'       => array( 'type' => 'textarea', 'label' => __( 'Tabulka recenze – „Parametr | Hodnota“', 'csgame' ), 'rows' => 8 ),
			),
		),
		'csgame_bonus'  => array(
			'title'  => __( 'Údaje o bonusu', 'csgame' ),
			'screen' => array( 'bonus' ),
			'fields' => array(
				'casino_id'     => array( 'type' => 'select', 'label' => __( 'Kasino', 'csgame' ), 'options' => $casinos ),
				'bonus_amount'  => array( 'type' => 'text', 'label' => __( 'Bonus (hlavní text)', 'csgame' ), 'placeholder' => '300 Kč + 50 FS' ),
				'promo_code'    => array( 'type' => 'text', 'label' => __( 'Promo kód', 'csgame' ) ),
				'affiliate_url' => array( 'type' => 'url', 'label' => __( 'Affiliate odkaz (prázdné = odkaz kasina)', 'csgame' ) ),
				'exclusive'     => array( 'type' => 'checkbox', 'label' => __( 'Exkluzivní bonus', 'csgame' ) ),
				'wagering'      => array( 'type' => 'text', 'label' => __( 'Protočení', 'csgame' ), 'placeholder' => '35×' ),
				'min_deposit'   => array( 'type' => 'text', 'label' => __( 'Min. vklad', 'csgame' ), 'placeholder' => '200 Kč' ),
				'max_win'       => array( 'type' => 'text', 'label' => __( 'Max. výhra', 'csgame' ) ),
				'validity'      => array( 'type' => 'text', 'label' => __( 'Platnost', 'csgame' ), 'placeholder' => '7 dní' ),
				'how_to_get'    => array( 'type' => 'textarea', 'label' => __( 'Jak získat bonus (řádek = krok)', 'csgame' ) ),
				'details'       => array( 'type' => 'textarea', 'label' => __( 'Rozbalovací detaily získání bonusu (krátce)', 'csgame' ) ),
				'conditions'    => array( 'type' => 'textarea', 'label' => __( 'Úplné podmínky – „Podmínka | Hodnota“', 'csgame' ), 'rows' => 8 ),
			),
		),
		'csgame_slot'   => array(
			'title'  => __( 'Údaje o automatu', 'csgame' ),
			'screen' => array( 'slot' ),
			'fields' => array(
				'rating'        => array( 'type' => 'number', 'label' => __( 'Hodnocení (0–5)', 'csgame' ), 'step' => '0.1' ),
				'demo_url'      => array( 'type' => 'url', 'label' => __( 'URL demo verze (iframe)', 'csgame' ) ),
				'affiliate_url' => array( 'type' => 'url', 'label' => __( 'Odkaz „Hrát o peníze“', 'csgame' ) ),
				'rtp'           => array( 'type' => 'number', 'label' => __( 'RTP (%)', 'csgame' ), 'step' => '0.01' ),
				'lines'         => array( 'type' => 'number', 'label' => __( 'Počet linií', 'csgame' ) ),
				'reels'         => array( 'type' => 'text', 'label' => __( 'Válce', 'csgame' ), 'placeholder' => '5×3' ),
				'volatility'    => array( 'type' => 'select', 'label' => __( 'Volatilita', 'csgame' ), 'options' => array( '' => '—', 'nizka' => 'Nízká', 'stredni' => 'Střední', 'vysoka' => 'Vysoká' ) ),
				'min_bet'       => array( 'type' => 'text', 'label' => __( 'Min. sázka', 'csgame' ) ),
				'max_bet'       => array( 'type' => 'text', 'label' => __( 'Max. sázka', 'csgame' ) ),
				'max_win'       => array( 'type' => 'text', 'label' => __( 'Max. výhra', 'csgame' ) ),
				'release'       => array( 'type' => 'text', 'label' => __( 'Rok vydání', 'csgame' ) ),
				'paytable'      => array( 'type' => 'textarea', 'label' => __( 'Výherní tabulka – „Symbol | 3× | 4× | 5×“', 'csgame' ), 'rows' => 8 ),
			),
		),
	);
}

function csgame_add_meta_boxes() {
	foreach ( csgame_meta_boxes() as $id => $box ) {
		add_meta_box( $id, $box['title'], 'csgame_render_meta_box', $box['screen'], 'normal', 'high', array( 'fields' => $box['fields'] ) );
	}
}
add_action( 'add_meta_boxes', 'csgame_add_meta_boxes' );

function csgame_render_meta_box( $post, $box ) {
	wp_nonce_field( 'csgame_meta', 'csgame_meta_nonce' );
	$is_new = 'auto-draft' === $post->post_status;
	echo '<div class="csgame-fields">';
	foreach ( $box['args']['fields'] as $key => $f ) {
		$name  = 'csgame[' . $key . ']';
		$value = get_post_meta( $post->ID, '_cs_' . $key, true );
		if ( $is_new && '' === $value && isset( $f['default'] ) ) {
			$value = $f['default'];
		}
		if ( ! $is_new && 'checkbox' === $f['type'] && isset( $f['default'] ) && ! metadata_exists( 'post', $post->ID, '_cs_' . $key ) ) {
			$value = $f['default'];
		}
		$ph = isset( $f['placeholder'] ) ? $f['placeholder'] : '';
		echo '<p class="csgame-field"><label><strong>' . esc_html( $f['label'] ) . '</strong><br>';
		switch ( $f['type'] ) {
			case 'textarea':
				$rows = isset( $f['rows'] ) ? (int) $f['rows'] : 4;
				echo '<textarea class="widefat" rows="' . $rows . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $ph ) . '">' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'select':
				echo '<select class="widefat" name="' . esc_attr( $name ) . '">';
				foreach ( $f['options'] as $ov => $ol ) {
					echo '<option value="' . esc_attr( $ov ) . '"' . selected( (string) $value, (string) $ov, false ) . '>' . esc_html( $ol ) . '</option>';
				}
				echo '</select>';
				break;
			case 'checkbox':
				echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0">';
				echo '<input type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . checked( $value, '1', false ) . '> ' . esc_html__( 'Ano', 'csgame' );
				break;
			default:
				$step = isset( $f['step'] ) ? ' step="' . esc_attr( $f['step'] ) . '"' : '';
				echo '<input class="widefat" type="' . esc_attr( $f['type'] ) . '"' . $step . ' name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $ph ) . '">';
		}
		echo '</label></p>';
	}
	echo '</div>';
}

function csgame_save_meta( $post_id ) {
	if ( ! isset( $_POST['csgame_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['csgame_meta_nonce'] ), 'csgame_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( empty( $_POST['csgame'] ) || ! is_array( $_POST['csgame'] ) ) {
		return;
	}
	$data = wp_unslash( $_POST['csgame'] ); // phpcs:ignore

	$types = array();
	foreach ( csgame_meta_boxes() as $box ) {
		foreach ( $box['fields'] as $k => $f ) {
			$types[ $k ] = $f['type'];
		}
	}

	foreach ( $data as $key => $val ) {
		if ( ! isset( $types[ $key ] ) ) {
			continue;
		}
		switch ( $types[ $key ] ) {
			case 'textarea':
				$val = sanitize_textarea_field( $val );
				break;
			case 'url':
				$val = esc_url_raw( $val );
				break;
			case 'number':
				$val = '' === $val ? '' : (string) floatval( str_replace( ',', '.', $val ) );
				break;
			case 'checkbox':
				$val = '1' === $val ? '1' : '0';
				break;
			default:
				$val = sanitize_text_field( $val );
		}
		update_post_meta( $post_id, '_cs_' . sanitize_key( $key ), $val );
	}
}
add_action( 'save_post', 'csgame_save_meta' );

/**
 * Checkbox getter that respects the default when meta was never saved.
 */
function csgame_flag( $key, $default = '1', $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! metadata_exists( 'post', $post_id, '_cs_' . $key ) ) {
		return '1' === $default;
	}
	return '1' === get_post_meta( $post_id, '_cs_' . $key, true );
}

/**
 * Small admin CSS.
 */
add_action(
	'admin_head',
	function () {
		echo '<style>.csgame-fields .csgame-field{margin:0 0 14px}.csgame-fields textarea{font-family:monospace}</style>';
	}
);
