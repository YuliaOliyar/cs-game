<?php
/**
 * Demo obsah pro rychlé otestování tématu.
 *
 * Spuštění (WP-CLI):  wp eval-file wp-content/themes/cs-game/demo/seed-demo.php
 * Vytvoří ukázková kasina, bonusy, automaty, stránky, články a menu.
 * Spouštějte jen na testovací instalaci.
 *
 * @package CSGame
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$set = function ( $id, $meta ) {
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, '_cs_' . $k, $v );
	}
};

$author = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
update_user_meta( $author->ID, 'csgame_position', 'Šéfredaktorka' );
update_user_meta( $author->ID, 'csgame_facebook', 'https://facebook.com/' );
update_user_meta( $author->ID, 'csgame_linkedin', 'https://linkedin.com/' );
wp_update_user( array( 'ID' => $author->ID, 'display_name' => 'Anežka Nováková', 'description' => 'Online kasina recenzuje od roku 2016. Testuje vklady, výběry i podmínky bonusů na vlastní kůži.' ) );

$faq = "Je hraní v online kasinech v ČR legální? | Ano, pokud kasino vlastní licenci Ministerstva financí ČR.\nJak rychle dostanu výhru? | U nejlepších kasin obvykle do 24 hodin.\nCo je protočení bonusu? | Počet, kolikrát musíte bonus prosázet, než ho můžete vybrat.";

$lorem = '<p>Online kasina v Česku prošla v posledních letech velkou proměnou. V této recenzi se podíváme na to, co nabízí a pro koho se hodí.</p>
<h2>Jak vybíráme kasina</h2><p>Hodnotíme licenci, bonusy, rychlost výběrů a nabídku her. <mark>Každé kasino testujeme s reálným vkladem.</mark></p>
<h3>Licence a bezpečnost</h3><p>Hrajte pouze u kasin s licencí MF ČR.</p>
<ul><li>Licence MF ČR</li><li>Rychlé výběry</li><li>Česká podpora</li></ul>
<h3>Bonusy</h3><ol><li>Zaregistrujte se</li><li>Ověřte účet</li><li>Vložte peníze</li></ol>
<h2>Srovnání</h2><figure class="wp-block-table"><table><thead><tr><th>Kasino</th><th>Bonus</th><th>Výběr</th></tr></thead><tbody><tr><td>Alfa</td><td>300 Kč</td><td>24 h</td></tr><tr><td>Beta</td><td>100 FS</td><td>48 h</td></tr></tbody></table></figure>
<h4>Tip redakce</h4><p>Vždy si přečtěte obchodní podmínky.</p>';

$casino_ids = array();
$casinos    = array( 'Zlatá Sedmička', 'Royal Bohemia', 'Lucky Praha', 'Vltava Casino', 'Karlův Spin' );
foreach ( $casinos as $i => $name ) {
	$id = wp_insert_post( array( 'post_type' => 'casino', 'post_status' => 'publish', 'post_title' => $name, 'post_content' => $lorem, 'post_excerpt' => 'Licencované kasino s rychlými výběry a českou podporou.', 'menu_order' => $i, 'post_author' => $author->ID, 'comment_status' => 'open' ) );
	$set( $id, array(
		'rating'             => (string) ( 4.9 - $i * 0.3 ),
		'affiliate_url'      => 'https://example.com/go/' . sanitize_title( $name ),
		'bonus_text'         => ( 300 + $i * 100 ) . ' Kč + ' . ( 50 + $i * 10 ) . ' FS',
		'bonus_type_label'   => 'Vítací bonus',
		'advantages'         => "Licence MF ČR\nVýběr do 24 hodin\nČeská podpora 24/7",
		'pros'               => "Rychlé výběry\nŠiroký výběr automatů\nMobilní aplikace",
		'cons'               => "Vyšší protočení bonusu\nChybí live chat v noci",
		'deposit_methods'    => "Visa\nMastercard\nApple Pay\nBankovní převod",
		'withdrawal_methods' => "Bankovní převod\nVisa",
		'game_types'         => "Automaty\nRuleta\nBlackjack\nLive casino",
		'providers_list'     => "Kajot | #licence-a-bezpecnost\nTech4Bet | #bonusy\nEGT",
		'review_table'       => "Licence | MF ČR\nZaloženo | 2019\nMin. vklad | 200 Kč\nVýběr | do 24 h\nMobil | Ano",
		'faq'                => $faq,
		'fact_checker'       => (string) $author->ID,
	) );
	$casino_ids[] = $id;
}

$types = array( 'vitaci-bonus', 'free-spiny', 'bonus-bez-vkladu', 'promo-kody', 'bonus-za-vklad', 'za-registraci' );
foreach ( $casino_ids as $i => $cid ) {
	foreach ( array( 0, 1 ) as $j ) {
		$amount = $j ? ( 50 + $i * 25 ) . ' free spinů' : ( 300 + $i * 100 ) . ' Kč bez vkladu';
		$id     = wp_insert_post( array( 'post_type' => 'bonus', 'post_status' => 'publish', 'post_title' => $amount . ' – ' . get_the_title( $cid ), 'post_content' => $lorem, 'post_excerpt' => 'Bonus za registraci a ověření účtu.', 'menu_order' => $i * 2 + $j, 'post_author' => $author->ID ) );
		wp_set_object_terms( $id, $types[ ( $i + $j ) % count( $types ) ], 'bonus_type' );
		wp_set_object_terms( $id, array( 'novinka', $j ? 'freespiny' : 'bezvkladu' ), 'bonus_tag' );
		$set( $id, array(
			'casino_id'    => (string) $cid,
			'bonus_amount' => $amount,
			'promo_code'   => $j ? '' : 'CSGAME' . ( $i + 1 ),
			'exclusive'    => 0 === $i ? '1' : '0',
			'wagering'     => '35×',
			'min_deposit'  => '200 Kč',
			'max_win'      => '5 000 Kč',
			'validity'     => '7 dní',
			'how_to_get'   => "Klikněte na tlačítko Získat bonus\nZaregistrujte se a ověřte účet\nBonus se připíše automaticky",
			'details'      => 'Bonus je určen novým hráčům starším 18 let. Free spiny platí na vybrané automaty.',
			'conditions'   => "Protočení | 35×\nMin. vklad | 200 Kč\nMax. sázka | 100 Kč\nPlatnost | 7 dní\nHry | Automaty",
			'faq'          => $faq,
			'fact_checker' => (string) $author->ID,
		) );
	}
}

$providers = array( 'Kajot', 'Tech4Bet', 'EGT', 'Synot' );
foreach ( $providers as $p ) {
	if ( ! term_exists( $p, 'provider' ) ) {
		wp_insert_term( $p, 'provider', array( 'description' => '<p>' . $p . ' patří k nejoblíbenějším výrobcům automatů v Česku. Jeho hry najdete u většiny licencovaných kasin. Nabízí klasické ovocné automaty i moderní video sloty s bonusovými hrami.</p><p>V přehledu níže najdete všechny automaty ' . $p . ' s možností hrát demo zdarma.</p>' ) );
	}
}
foreach ( array( 'Ovoce', 'Egypt', 'Dobrodružství' ) as $t ) {
	if ( ! term_exists( $t, 'slot_theme' ) ) {
		wp_insert_term( $t, 'slot_theme' );
	}
}
for ( $i = 1; $i <= 12; $i++ ) {
	$id = wp_insert_post( array( 'post_type' => 'slot', 'post_status' => 'publish', 'post_title' => 'Automat ' . $i, 'post_content' => $lorem . '<!--more--><h2>Verdikt</h2><p>Zábavný automat s férovým RTP.</p>', 'post_author' => $author->ID, 'comment_status' => 'open' ) );
	wp_set_object_terms( $id, $providers[ $i % 4 ], 'provider' );
	wp_set_object_terms( $id, array( 'Ovoce', 'Egypt', 'Dobrodružství' )[ $i % 3 ], 'slot_theme' );
	$set( $id, array(
		'rating'     => (string) ( 3 + ( $i % 5 ) * 0.4 ),
		'rtp'        => (string) ( 93 + ( $i % 5 ) ),
		'lines'      => (string) ( 5 * $i ),
		'reels'      => '5×3',
		'volatility' => array( 'nizka', 'stredni', 'vysoka' )[ $i % 3 ],
		'min_bet'    => '1 Kč',
		'max_bet'    => '500 Kč',
		'release'    => '2024',
		'paytable'   => "Sedmička | 20× | 100× | 500×\nZvonek | 10× | 50× | 200×\nTřešně | 5× | 20× | 100×",
		'demo_url'   => 'https://example.com/demo',
		'faq'        => $faq,
	) );
}

$page = function ( $title, $template = '', $parent = 0, $meta = array(), $content = '' ) use ( $author, $set, $lorem ) {
	$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_content' => $content ? $content : $lorem, 'post_parent' => $parent, 'post_author' => $author->ID, 'comment_status' => 'open' ) );
	if ( $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	$set( $id, $meta );
	return $id;
};

$home = $page( 'Online casino v Česku', '', 0, array( 'intro' => 'Srovnání legálních online kasin s licencí MF ČR. Bonusy, rychlost výběrů a nabídka her na jednom místě.', 'toplist_title' => 'Nejlepší online kasina 2026', 'faq' => $faq ) );
$bon  = $page( 'Casino bonusy', 'templates/template-bonuses.php', 0, array( 'intro' => 'Aktuální bonusy bez vkladu, free spiny a promo kódy.', 'features' => "gift | Ověřené bonusy\nshield | Jen licencovaná kasina\nclock | Aktualizováno denně\ncoins | Exkluzivní promo kódy", 'faq' => $faq ) );
$cas  = $page( 'Casino', '', 0, array( 'toplist_show' => '1' ) );
$aut  = $page( 'Automaty', 'templates/template-slots.php', $cas, array( 'intro' => '<p>Herní automaty zdarma i o peníze. Hrajte demo verze bez registrace a vyberte si automat podle poskytovatele, RTP nebo tématu. Přehled obsahuje automaty od českých i zahraničních výrobců.</p><p>Každý automat jsme otestovali a sepsali recenzi s výherní tabulkou a parametry.</p>', 'faq' => $faq ) );
$blog = $page( 'Novinky', '', 0, array(), '' );
$prov_page = $page( 'Kajot – text', '', 0, array( 'faq' => $faq ) );
$kajot = get_term_by( 'name', 'Kajot', 'provider' );
update_term_meta( $kajot->term_id, 'csgame_page', $prov_page );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'page_for_posts', $blog );
update_option( 'permalink_structure', '/%postname%/' );

$cat = wp_insert_term( 'Výhry', 'category' );
for ( $i = 1; $i <= 4; $i++ ) {
	$pid = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Hráč ve Fortune vyhrál téměř půl milionu #' . $i, 'post_content' => $lorem, 'post_excerpt' => 'Šťastný hráč si odnesl výhru na automatu během pár minut.', 'post_author' => $author->ID, 'post_category' => array( is_array( $cat ) ? $cat['term_id'] : 1 ) ) );
}

$menu = function ( $name, $location, $items ) {
	$m = wp_get_nav_menu_object( $name );
	$id = $m ? $m->term_id : wp_create_nav_menu( $name );
	foreach ( $items as $title => $url ) {
		wp_update_nav_menu_item( $id, 0, array( 'menu-item-title' => $title, 'menu-item-url' => $url, 'menu-item-status' => 'publish' ) );
	}
	$loc = get_theme_mod( 'nav_menu_locations', array() );
	$loc[ $location ] = $id;
	set_theme_mod( 'nav_menu_locations', $loc );
};
flush_rewrite_rules();
$menu( 'Hlavní menu', 'primary', array( 'Kasina' => home_url( '/' ), 'Bonusy' => get_permalink( $bon ), 'Automaty' => get_permalink( $aut ), 'Recenze' => get_post_type_archive_link( 'casino' ), 'Novinky' => get_permalink( $blog ) ) );
$menu( 'Patička', 'footer', array( 'O nás' => home_url( '/o-nas/' ), 'Kontakt' => home_url( '/kontakt/' ), 'Zodpovědné hraní' => home_url( '/zodpovedne-hrani/' ), 'Ochrana osobních údajů' => home_url( '/gdpr/' ) ) );
$menu( 'Užitečné odkazy', 'interlinks', array( 'Casino bonusy' => get_permalink( $bon ), 'Herní automaty' => get_permalink( $aut ), 'Nová kasina' => home_url( '/nova-kasina/' ), 'Live casino' => home_url( '/live-casino/' ) ) );
$menu( 'Typy bonusů', 'bonus_links', array( 'Free spiny' => get_term_link( 'free-spiny', 'bonus_type' ), 'Bez vkladu' => get_term_link( 'bonus-bez-vkladu', 'bonus_type' ), 'Promo kódy' => get_term_link( 'promo-kody', 'bonus_type' ) ) );
$prov_links = array();
foreach ( $providers as $p ) {
	$prov_links[ $p ] = get_term_link( $p, 'provider' );
}
$menu( 'Poskytovatelé', 'providers', $prov_links );

echo "Demo obsah vytvořen.\n";
