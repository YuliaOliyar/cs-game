<?php
/**
 * Customizer: texty patičky, disclaimer, odběr, archivy.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_defaults() {
	return array(
		'csgame_footer_title'   => 'CS Game',
		'csgame_footer_text'    => 'CS Game je nezávislý průvodce světem legálních online kasin v Česku. Recenze, bonusy a automaty hodnotíme podle jasných kritérií.',
		'csgame_copyright'      => '© {year} CS Game. Všechna práva vyhrazena.',
		'csgame_disclaimer'     => 'Hazardní hry jsou určeny pouze osobám starším 18 let. Hraní může vést ke vzniku závislosti – hrajte zodpovědně a jen u kasin s licencí Ministerstva financí ČR. Pokud máte s hraním problém, vyhledejte odbornou pomoc.',
		'csgame_sub_title'      => 'Nenechte si ujít nejlepší bonusy',
		'csgame_sub_text'       => 'Exkluzivní promo kódy a novinky ze světa online kasin jednou týdně do vašeho e-mailu.',
		'csgame_bonus_title'    => 'Casino bonusy',
		'csgame_bonus_intro'    => '',
		'csgame_slots_title'    => 'Herní automaty',
		'csgame_slots_intro'    => '',
		'csgame_casinos_title'  => 'Recenze online kasin',
		'csgame_blog_title'     => 'Novinky',
	);
}

function csgame_mod( $key ) {
	$d = csgame_defaults();
	$v = get_theme_mod( $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
	return str_replace( '{year}', gmdate( 'Y' ), $v );
}

function csgame_customize( $wp_customize ) {
	$wp_customize->add_panel( 'csgame', array( 'title' => __( 'CS Game – nastavení', 'csgame' ), 'priority' => 30 ) );

	$sections = array(
		'csgame_footer'  => array(
			'title'  => __( 'Patička a disclaimer', 'csgame' ),
			'fields' => array(
				'csgame_footer_title' => array( 'text', __( 'Název v patičce', 'csgame' ) ),
				'csgame_footer_text'  => array( 'textarea', __( 'Text v patičce', 'csgame' ) ),
				'csgame_copyright'    => array( 'text', __( 'Copyright ({year} = aktuální rok)', 'csgame' ) ),
				'csgame_disclaimer'   => array( 'textarea', __( 'Disclaimer 18+', 'csgame' ) ),
			),
		),
		'csgame_sub'     => array(
			'title'  => __( 'Formulář odběru', 'csgame' ),
			'fields' => array(
				'csgame_sub_title' => array( 'text', __( 'Nadpis', 'csgame' ) ),
				'csgame_sub_text'  => array( 'textarea', __( 'Text', 'csgame' ) ),
			),
		),
		'csgame_archives' => array(
			'title'  => __( 'Archivy (bonusy, automaty, kasina, blog)', 'csgame' ),
			'fields' => array(
				'csgame_bonus_title'   => array( 'text', __( 'Bonusy – nadpis H1', 'csgame' ) ),
				'csgame_bonus_intro'   => array( 'textarea', __( 'Bonusy – úvodní text (HTML povoleno)', 'csgame' ) ),
				'csgame_slots_title'   => array( 'text', __( 'Automaty – nadpis H1', 'csgame' ) ),
				'csgame_slots_intro'   => array( 'textarea', __( 'Automaty – rozbalovací text (HTML povoleno)', 'csgame' ) ),
				'csgame_casinos_title' => array( 'text', __( 'Kasina – nadpis H1', 'csgame' ) ),
				'csgame_blog_title'    => array( 'text', __( 'Blog – nadpis H1', 'csgame' ) ),
			),
		),
	);

	$d = csgame_defaults();
	foreach ( $sections as $sid => $s ) {
		$wp_customize->add_section( $sid, array( 'title' => $s['title'], 'panel' => 'csgame' ) );
		foreach ( $s['fields'] as $key => $f ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
					'sanitize_callback' => 'textarea' === $f[0] ? 'wp_kses_post' : 'sanitize_text_field',
				)
			);
			$wp_customize->add_control( $key, array( 'label' => $f[1], 'section' => $sid, 'type' => $f[0] ) );
		}
	}
}
add_action( 'customize_register', 'csgame_customize' );
