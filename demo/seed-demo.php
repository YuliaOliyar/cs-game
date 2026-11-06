<?php
/**
 * Demo obsah pro rychlé otestování tématu.
 *
 * Spuštění (WP-CLI):  wp eval-file wp-content/themes/cs-game/demo/seed-demo.php
 * Volitelně obrázky:  CSGAME_DEMO_IMG=/cesta/ke/slozce wp eval-file …
 *   (soubory casino-{slug}.png, slot-{1..30}.png, post-{1..6}.png)
 *
 * Vytvoří ukázková kasina, bonusy, automaty, stránky, články a menu.
 * Spouštějte jen na testovací instalaci.
 *
 * @package CSGame
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$t       = require __DIR__ . '/demo-texts.php';
$img_dir = getenv( 'CSGAME_DEMO_IMG' );

update_option( 'permalink_structure', '/%postname%/' );
update_option( 'date_format', 'j. n. Y' );
global $wp_rewrite;
$wp_rewrite->init();
csgame_register_types();
flush_rewrite_rules();

$set = function ( $id, $meta ) {
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, '_cs_' . $k, $v );
	}
};
$thumb = function ( $post_id, $file ) use ( $img_dir ) {
	if ( ! $img_dir || ! file_exists( $img_dir . '/' . $file ) ) {
		return;
	}
	$tmp = wp_tempnam( $file );
	copy( $img_dir . '/' . $file, $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), $post_id );
	if ( ! is_wp_error( $id ) ) {
		set_post_thumbnail( $post_id, $id );
	}
};

/* Autoři */
$author = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
wp_update_user(
	array(
		'ID'           => $author->ID,
		'display_name' => 'Anežka Nováková',
		'description'  => 'Online kasina recenzuje od roku 2016. Každé kasino testuje s vlastním vkladem – od registrace přes bonus až po výběr výhry. Specializuje se na podmínky bonusů a zodpovědné hraní.',
	)
);
update_user_meta( $author->ID, 'csgame_position', 'Šéfredaktorka' );
update_user_meta( $author->ID, 'csgame_facebook', 'https://facebook.com/' );
update_user_meta( $author->ID, 'csgame_linkedin', 'https://linkedin.com/' );
update_user_meta( $author->ID, 'csgame_x', 'https://x.com/' );

$checker_id = username_exists( 'tomas' );
if ( ! $checker_id ) {
	$checker_id = wp_insert_user( array( 'user_login' => 'tomas', 'user_pass' => wp_generate_password(), 'display_name' => 'Tomáš Dvořák', 'role' => 'editor', 'description' => 'Ověřuje fakta, licence a podmínky bonusů.' ) );
}
update_user_meta( $checker_id, 'csgame_position', 'Editor, ověřování faktů' );

/* Kasina */
$casinos = array(
	array( 'zlata-sedmicka', 'Zlatá Sedmička', 4.9, '300 Kč + 50 FS', 'Bonus bez vkladu' ),
	array( 'royal-bohemia', 'Royal Bohemia', 4.7, '400 Kč + 60 FS', 'Vítací bonus' ),
	array( 'lucky-praha', 'Lucky Praha', 4.5, '500 Kč bez vkladu', 'Bonus bez vkladu' ),
	array( 'vltava-casino', 'Vltava Casino', 4.2, '100 free spinů', 'Free spiny' ),
	array( 'karluv-spin', 'Karlův Spin', 3.9, '100 % až 5 000 Kč', 'Bonus za vklad' ),
);
$advantages = array(
	"Licence MF ČR\nVýběr do 24 hodin\nBonus bez vkladu po ověření",
	"Přes 900 automatů\nAplikace pro iOS i Android\nČeská podpora 24/7",
	"Nejnižší min. vklad 100 Kč\nVýběry i o víkendu\nTýdenní free spiny",
	"Živé casino s českými krupiéry\nRychlé ověření přes BankID\nCashback 10 %",
	"Velký vstupní bonus\nVěrnostní program\nTurnaje o ceny",
);
$casino_ids = array();
foreach ( $casinos as $i => $c ) {
	$id = wp_insert_post(
		array(
			'post_type'      => 'casino',
			'post_status'    => 'publish',
			'post_title'     => $c[1],
			'post_name'      => $c[0],
			'post_content'   => str_replace( '%1$s', $c[1], $t['casino'] ),
			'post_excerpt'   => 'Licencované české casino s rychlými výběry, bonusem bez vkladu a podporou v češtině.',
			'menu_order'     => $i,
			'post_author'    => $author->ID,
			'comment_status' => 'open',
		)
	);
	$set(
		$id,
		array(
			'intro'              => '',
			'rating'             => (string) $c[2],
			'affiliate_url'      => 'https://example.com/go/' . $c[0],
			'bonus_text'         => $c[3],
			'bonus_type_label'   => $c[4],
			'advantages'         => $advantages[ $i ],
			'pros'               => "Výběry na účet do 24 hodin\nBonus bez vkladu s férovým protočením\nKvalitní mobilní aplikace\nČeská zákaznická podpora",
			'cons'               => "Vyšší protočení vstupního bonusu (35×)\nLive chat v noci jen v angličtině",
			'deposit_methods'    => "Bankovní převod\nVisa\nMastercard\nApple Pay\nGoogle Pay\nSkrill\nPaysafecard\nRevolut",
			'withdrawal_methods' => "Bankovní převod\nVisa\nMastercard\nSkrill\nRevolut",
			'game_types'         => "Automaty\nRuleta\nBlackjack\nŽivé casino\nPoker\nKostky",
			'providers_list'     => "Kajot | #kajot\nTech4Bet | #tech4bet\nEGT | #egt\nSynot\nAmusnet",
			'review_table'       => "Provozovatel | " . $c[1] . " a.s.\nLicence | Ministerstvo financí ČR\nZaloženo | 2019\nMin. vklad | 200 Kč\nMin. výběr | 300 Kč\nDoba výběru | do 24 hodin\nPočet her | 900+\nMobilní aplikace | iOS, Android\nPodpora | chat, e-mail (česky)",
			'faq'                => "Je " . $c[1] . " legální? | Ano, kasino má licenci Ministerstva financí ČR.\nJak dlouho trvá výběr? | Výběr na bankovní účet obvykle dorazí do 24 hodin.\nJaký je minimální vklad? | Minimální vklad je 200 Kč.",
			'fact_checker'       => (string) $checker_id,
			'toplist_show'       => '0',
		)
	);
	$thumb( $id, 'casino-' . $c[0] . '.png' );
	$casino_ids[] = $id;
	$c1 = wp_insert_comment( array( 'comment_post_ID' => $id, 'comment_author' => 'Petr K.', 'comment_content' => 'Výběr mi přišel druhý den ráno, bonus bez vkladu fungoval bez problémů.', 'comment_approved' => 1 ) );
	$cid = wp_insert_comment( array( 'comment_post_ID' => $id, 'comment_author' => 'Jana M.', 'comment_content' => 'Super aplikace, jen protočení vstupního bonusu je dost vysoké.', 'comment_approved' => 1 ) );
	add_comment_meta( $cid, 'csgame_rating', 4 );
	add_comment_meta( $c1, 'csgame_rating', 5 );
}

/* Bonusy */
$bonus_defs = array(
	array( 'bonus-bez-vkladu', '%d Kč bez vkladu', array( 300, 400, 500, 250, 200 ) ),
	array( 'free-spiny', '%d free spinů', array( 50, 60, 70, 100, 80 ) ),
);
$order = 0;
foreach ( $casino_ids as $i => $cid ) {
	foreach ( $bonus_defs as $j => $bd ) {
		$amount = sprintf( $bd[1], $bd[2][ $i ] );
		$slug   = sanitize_title( $amount . '-' . $casinos[ $i ][0] );
		$id     = wp_insert_post(
			array(
				'post_type'    => 'bonus',
				'post_status'  => 'publish',
				'post_title'   => $amount . ' – ' . $casinos[ $i ][1],
				'post_name'    => $slug,
				'post_content' => $t['bonus'],
				'post_excerpt' => 'Bonus pro nové hráče za registraci a ověření účtu, bez nutnosti vkladu.',
				'menu_order'   => $order++,
				'post_author'  => $author->ID,
			)
		);
		$types = array( $bd[0] );
		if ( 0 === $j ) {
			$types[] = 'za-registraci';
		}
		if ( 0 === $i % 2 ) {
			$types[] = 'promo-kody';
		}
		if ( 3 === $i ) {
			$types[] = 'vitaci-bonus';
		}
		if ( 4 === $i ) {
			$types = array( 'bonus-za-vklad', 'vitaci-bonus' );
		}
		wp_set_object_terms( $id, $types, 'bonus_type' );
		wp_set_object_terms( $id, array( 'novinka', $j ? 'freespiny' : 'bezvkladu' ), 'bonus_tag' );
		$set(
			$id,
			array(
				'intro'        => 'Exkluzivní bonus pro čtenáře CS Game – získáte ho po registraci přes náš odkaz a ověření účtu.',
				'casino_id'    => (string) $cid,
				'bonus_amount' => $amount,
				'promo_code'   => 0 === $i % 2 ? 'CSGAME' . ( $i + 1 ) : '',
				'exclusive'    => 0 === $i ? '1' : '0',
				'wagering'     => '35×',
				'min_deposit'  => $j ? '0 Kč' : '200 Kč',
				'max_win'      => '5 000 Kč',
				'validity'     => '7 dní',
				'how_to_get'   => "Klikněte na tlačítko Získat bonus\nZaregistrujte se a vyplňte osobní údaje\nOvěřte účet přes bankovní identitu\nBonus se připíše automaticky do 24 hodin",
				'details'      => 'Bonus je určen novým hráčům starším 18 let. Po ověření účtu se připíše automaticky. Free spiny platí na vybrané automaty, výhry je nutné protočit 35× do 7 dní. Maximální sázka během protočení je 100 Kč.',
				'conditions'   => "Pro koho | Noví hráči 18+\nProtočení | 35× bonus\nMax. sázka | 100 Kč\nMax. výhra | 5 000 Kč\nPlatnost | 7 dní\nHry | Automaty (100 %)\nPromo kód | " . ( 0 === $i % 2 ? 'CSGAME' . ( $i + 1 ) : 'není potřeba' ),
				'faq'          => $t['faq_bonus'],
				'fact_checker' => (string) $checker_id,
			)
		);
		$thumb( $id, 'casino-' . $casinos[ $i ][0] . '.png' );
	}
}

/* Poskytovatelé, tématika, automaty */
$providers = array( 'Kajot', 'Tech4Bet', 'EGT', 'Synot' );
foreach ( $providers as $p ) {
	if ( ! term_exists( $p, 'provider' ) ) {
		wp_insert_term( $p, 'provider', array( 'description' => '<p>' . $p . ' patří k nejoblíbenějším výrobcům automatů v Česku. Jeho hry najdete u většiny licencovaných kasin – od klasických ovocných automatů po moderní video sloty s bonusovými hrami a jackpoty.</p><p>V přehledu níže najdete všechny automaty ' . $p . ' s možností hrát demo zdarma. Pomocí filtru je seřadíte podle RTP, počtu linií nebo tématu.</p>' ) );
	}
}
foreach ( array( 'Ovoce', 'Egypt', 'Dobrodružství', 'Fantasy' ) as $th ) {
	if ( ! term_exists( $th, 'slot_theme' ) ) {
		wp_insert_term( $th, 'slot_theme' );
	}
}
$slot_names = array( 'Fruit Blaze', 'Pharaoh Gold', 'Lucky 777', 'Wild Rapids', 'Book of Bohemia', 'Hot Cherry', 'Golden Dragon', 'Mystic Moon', 'Diamond Rush', 'Crown Jewels', 'Viking Saga', 'Jungle Spin', 'Neon Nights', 'Ocean Treasure', 'Sweet Bonanza X', 'Fire Joker CZ', 'Aztec Sun', 'Gem Mine', 'Space Ace', 'Lucky Clover', 'Royal Fruits', 'Sphinx Riches', 'Dino Hunt', 'Magic Forest', 'Retro Reels', 'Pirate Bay', 'Thunder Bolt', 'Cash Tower', 'Samurai Way', 'Ice Queen' );
$themes     = array( 'Ovoce', 'Egypt', 'Ovoce', 'Dobrodružství', 'Egypt', 'Ovoce', 'Fantasy', 'Fantasy', 'Ovoce', 'Fantasy', 'Dobrodružství', 'Dobrodružství', 'Fantasy', 'Dobrodružství', 'Ovoce', 'Ovoce', 'Egypt', 'Fantasy', 'Dobrodružství', 'Ovoce', 'Ovoce', 'Egypt', 'Dobrodružství', 'Fantasy', 'Ovoce', 'Dobrodružství', 'Fantasy', 'Ovoce', 'Dobrodružství', 'Fantasy' );
foreach ( $slot_names as $i => $name ) {
	$id = wp_insert_post( array( 'post_type' => 'slot', 'post_status' => 'publish', 'post_title' => $name, 'post_content' => str_replace( '%1$s', $name, $t['slot'] ), 'post_author' => $author->ID, 'comment_status' => 'open', 'post_date' => gmdate( 'Y-m-d H:i:s', time() - $i * HOUR_IN_SECONDS ) ) );
	wp_set_object_terms( $id, $providers[ $i % 4 ], 'provider' );
	wp_set_object_terms( $id, $themes[ $i ], 'slot_theme' );
	if ( 0 === $i % 3 ) {
		wp_set_object_terms( $id, 'free-spiny', 'bonus_type' );
	}
	$set(
		$id,
		array(
			'intro'      => '',
			'rating'     => (string) round( 3.4 + ( ( $i * 7 ) % 16 ) / 10, 1 ),
			'rtp'        => (string) ( 92.5 + ( ( $i * 3 ) % 9 ) * 0.5 ),
			'lines'      => (string) array( 5, 10, 20, 25, 40, 50, 100 )[ $i % 7 ],
			'reels'      => array( '5×3', '5×4', '3×3', '6×5' )[ $i % 4 ],
			'volatility' => array( 'nizka', 'stredni', 'vysoka' )[ $i % 3 ],
			'min_bet'    => '1 Kč',
			'max_bet'    => '500 Kč',
			'max_win'    => array( '1 000×', '2 500×', '5 000×', '10 000×' )[ $i % 4 ],
			'release'    => (string) ( 2019 + $i % 7 ),
			'paytable'   => "Sedmička | 20× | 100× | 500×\nZvonek | 10× | 50× | 200×\nMeloun | 8× | 30× | 120×\nHrozny | 5× | 20× | 80×\nTřešně | 3× | 10× | 40×\nCitron | 2× | 8× | 25×",
			'demo_url'   => 'https://example.com/demo',
			'faq'        => $t['faq_slot'],
			'toplist_show' => '0',
		)
	);
	$thumb( $id, 'slot-' . ( $i + 1 ) . '.png' );
	if ( $i < 3 ) {
		$cid = wp_insert_comment( array( 'comment_post_ID' => $id, 'comment_author' => 'Lukáš V.', 'comment_content' => 'Bonusová hra padá docela často, v demu jsem vydržel přes hodinu.', 'comment_approved' => 1 ) );
		add_comment_meta( $cid, 'csgame_rating', 5 );
	}
}

/* Stránky */
$page = function ( $title, $template = '', $parent = 0, $meta = array(), $content = '' ) use ( $author, $set ) {
	$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_content' => $content, 'post_parent' => $parent, 'post_author' => $author->ID, 'comment_status' => 'open' ) );
	if ( $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	$set( $id, $meta );
	return $id;
};

$home = $page( 'Online casino v Česku 2026', '', 0, array( 'intro' => 'Srovnání legálních online kasin s licencí MF ČR. Každé kasino jsme otestovali s reálným vkladem – podívejte se, kde dostanete nejlepší bonus a nejrychlejší výplatu.', 'toplist_title' => 'Nejlepší online kasina v Česku', 'faq' => $t['faq_home'], 'fact_checker' => (string) $checker_id ), $t['home'] );
$bon  = $page( 'Casino bonusy', 'templates/template-bonuses.php', 0, array( 'intro' => 'Aktuální bonusy bez vkladu, free spiny a promo kódy z licencovaných českých kasin. Podmínky každého bonusu jsme ověřili.', 'features' => "🎁 | Ověřené bonusy\n⚡ | Aktualizováno denně\n💎 | Exkluzivní promo kódy", 'faq' => $t['faq_bonus'] ), $t['bonuses'] );
$cas  = $page( 'Casino', '', 0, array( 'toplist_show' => '1', 'intro' => 'Vše o online kasinech v Česku na jednom místě.', 'faq' => $t['faq_home'] ), $t['home'] );
$aut  = $page( 'Herní automaty', 'templates/template-slots.php', $cas, array( 'intro' => '<p>Herní automaty zdarma i o peníze. Vyzkoušejte demo verze bez registrace a vyberte si automat podle výrobce, RTP, počtu linií nebo tématu. V přehledu najdete klasické ovocné automaty od Kajotu i moderní video sloty s bonusovými hrami.</p><p>Každý automat jsme otestovali a sepsali recenzi s výherní tabulkou, parametry a tipy. Hodnocení průběžně aktualizujeme podle zkušeností hráčů.</p>', 'faq' => $t['faq_slot'] ), $t['slots_main'] );
wp_update_post( array( 'ID' => $aut, 'post_name' => 'automaty' ) );
$blog = $page( 'Novinky', '', 0, array( 'intro' => 'Zprávy ze světa online kasin, velké výhry, nové licence a bonusové akce.' ), $t['blog'] );

foreach ( $providers as $p ) {
	$pp   = $page( $p . ' – text', '', 0, array( 'faq' => $t['faq_slot'] ), str_replace( '%1$s', $p, $t['provider'] ) );
	$term = get_term_by( 'name', $p, 'provider' );
	update_term_meta( $term->term_id, 'csgame_page', $pp );
	wp_update_post( array( 'ID' => $pp, 'post_status' => 'private' ) );
	$cid = wp_insert_comment( array( 'comment_post_ID' => $pp, 'comment_author' => 'Martin S.', 'comment_content' => 'Automaty od ' . $p . ' hraju nejraději, hlavně ty ovocné.', 'comment_approved' => 1 ) );
	add_comment_meta( $cid, 'csgame_rating', 5 );
}

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'page_for_posts', $blog );

/* Články */
foreach ( $t['posts'] as $i => $p ) {
	$cat = term_exists( $p[1], 'category' );
	if ( ! $cat ) {
		$cat = wp_insert_term( $p[1], 'category' );
	}
	$pid = wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => $p[0],
			'post_content'  => str_replace( '%2$s', $p[2], $t['post'] ),
			'post_excerpt'  => $p[2],
			'post_author'   => $author->ID,
			'post_category' => array( (int) $cat['term_id'] ),
			'post_date'     => gmdate( 'Y-m-d H:i:s', time() - $i * DAY_IN_SECONDS ),
		)
	);
	$set( $pid, array( 'fact_checker' => (string) $checker_id, 'faq' => $t['faq_home'] ) );
	$thumb( $pid, 'post-' . ( $i + 1 ) . '.png' );
}

/* Menu */
$menu = function ( $name, $location, $items ) {
	$m  = wp_get_nav_menu_object( $name );
	$id = $m ? $m->term_id : wp_create_nav_menu( $name );
	foreach ( $items as $title => $url ) {
		wp_update_nav_menu_item( $id, 0, array( 'menu-item-title' => $title, 'menu-item-url' => $url, 'menu-item-status' => 'publish' ) );
	}
	$loc              = get_theme_mod( 'nav_menu_locations', array() );
	$loc[ $location ] = $id;
	set_theme_mod( 'nav_menu_locations', $loc );
};
$menu( 'Hlavní menu', 'primary', array( 'Kasina' => home_url( '/' ), 'Bonusy' => get_permalink( $bon ), 'Automaty' => get_permalink( $aut ), 'Recenze' => get_post_type_archive_link( 'casino' ), 'Novinky' => get_permalink( $blog ) ) );
$menu( 'Patička', 'footer', array( 'O nás' => '#', 'Kontakt' => '#', 'Zodpovědné hraní' => '#', 'Ochrana osobních údajů' => '#', 'Jak hodnotíme kasina' => '#', 'Reklama' => '#' ) );
$menu( 'Užitečné odkazy', 'interlinks', array( 'Casino bonusy' => get_permalink( $bon ), 'Herní automaty' => get_permalink( $aut ), 'Recenze kasin' => get_post_type_archive_link( 'casino' ), 'Free spiny zdarma' => get_term_link( 'free-spiny', 'bonus_type' ), 'Bonus bez vkladu' => get_term_link( 'bonus-bez-vkladu', 'bonus_type' ), 'Novinky' => get_permalink( $blog ) ) );
$menu( 'Typy bonusů', 'bonus_links', array( 'Bez vkladu' => get_term_link( 'bonus-bez-vkladu', 'bonus_type' ), 'Free spiny' => get_term_link( 'free-spiny', 'bonus_type' ), 'Promo kódy' => get_term_link( 'promo-kody', 'bonus_type' ), 'Za vklad' => get_term_link( 'bonus-za-vklad', 'bonus_type' ), 'Vítací bonusy' => get_term_link( 'vitaci-bonus', 'bonus_type' ) ) );
$prov_links = array();
foreach ( $providers as $p ) {
	$prov_links[ $p ] = get_term_link( sanitize_title( $p ), 'provider' );
}
$menu( 'Poskytovatelé', 'providers', $prov_links );

flush_rewrite_rules();
echo "Demo obsah vytvořen.\n";
