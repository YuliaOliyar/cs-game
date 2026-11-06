<?php
/**
 * Demo: informační stránky (O nás, Kontakt, Jak hodnotíme kasina, Ochrana osobních údajů)
 * + ukázka recenze nelegálního kasina (Neon Fortune).
 *
 * Bezpečné spustit opakovaně – existující stránky/kasino (podle slugu) se jen aktualizují,
 * nic se nezdvojí. Spouští se automaticky ze seed-demo.php, nebo samostatně:
 *
 *   CSGAME_DEMO_IMG=/cesta/k/obrazkum wp eval-file wp-content/themes/cs-game/demo/seed-pages.php
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$csp_img    = getenv( 'CSGAME_DEMO_IMG' );
$csp_author = get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0];
$csp_check  = (int) username_exists( 'tomas' );

/**
 * Create or update a post by slug.
 */
$csp_upsert = function ( $type, $slug, $data, $meta = array(), $template = '' ) use ( $csp_author ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	$data     = array_merge(
		array(
			'post_type'   => $type,
			'post_status' => 'publish',
			'post_name'   => $slug,
			'post_author' => $csp_author->ID,
		),
		$data
	);
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		$id         = wp_update_post( $data );
	} else {
		$id = wp_insert_post( $data );
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, '_cs_' . $k, $v );
	}
	if ( $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return $id;
};

/* ---------------------------------------------------------------------------
 * 1) Nelegální kasino – ukázka varování
 * ------------------------------------------------------------------------- */
$neon = $csp_upsert(
	'casino',
	'neon-fortune',
	array(
		'post_title'     => 'Neon Fortune Casino',
		'post_excerpt'   => 'Zahraniční kasino bez české licence. Vysvětlujeme, proč u něj nedoporučujeme hrát a jaké jsou legální alternativy.',
		'menu_order'     => 50,
		'comment_status' => 'open',
		'post_content'   => '<h2>Proč Neon Fortune nedoporučujeme</h2>
<p>Neon Fortune Casino láká české hráče na vysoký vstupní bonus a tisíce automatů. Provozovatel ale <strong>nemá licenci Ministerstva financí ČR</strong> a web je zařazen na seznam nepovolených internetových her. Hraní u takového kasina je pro hráče riskantní – a v případě problému se nemáte kam obrátit.</p>
<blockquote><h4>Tip redakce</h4><p>Před registrací v jakémkoli kasinu si ověřte, zda je na seznamu povolených provozovatelů na webu Ministerstva financí. U licencovaných kasin máte zaručenou výplatu výher i ochranu osobních údajů.</p></blockquote>
<h2>Co jsme při testu zjistili</h2>
<p>Web je dostupný v češtině, ale obchodní podmínky existují jen v angličtině. Provozovatel sídlí na Curaçau a hraje se v eurech, takže při každém vkladu platíte za převod měny. Ověření identity probíhá až při první žádosti o výběr – a podle zkušeností hráčů se může táhnout týdny.</p>
<h3>Bonus s háčkem</h3>
<p>Vstupní bonus 200 % vypadá lákavě, jenže má protočení 60× a maximální výhru omezenou na pětinásobek bonusu. Podmínky se navíc mohou kdykoli změnit bez upozornění.</p>
<h3>Výběry a podpora</h3>
<p>Minimální výběr je 100 € a týdenní limit 2 000 €. Zákaznická podpora komunikuje jen anglicky a na dotazy k výběrům odpovídá obecně.</p>
<h2>Je hraní v nelegálním kasinu trestné?</h2>
<p>Česká legislativa postihuje především provozovatele, ale hráč riskuje ztrátu peněz bez možnosti právní ochrany. Banky navíc platby na zakázané weby blokují. Proto doporučujeme hrát výhradně u kasin s licencí MF ČR – výběr najdete níže v přehledu.</p>',
	),
	array(
		'unlicensed'         => '1',
		'unlicensed_note'    => '',
		'alternatives'       => '',
		'rating'             => '1.4',
		'affiliate_url'      => '',
		'bonus_text'         => '200 % až 500 €',
		'bonus_type_label'   => 'Vstupní bonus',
		'advantages'         => "Velký výběr automatů\nWeb v češtině",
		'pros'               => "Velký výběr automatů\nWeb přeložený do češtiny",
		'cons'               => "Nemá licenci MF ČR – web je na seznamu zakázaných\nVýhry nejsou nijak chráněny\nProtočení bonusu 60× a limit výhry\nVklady jen v eurech\nPodpora pouze v angličtině\nZdlouhavé a nejisté výběry",
		'deposit_methods'    => "Visa\nMastercard\nKryptoměny",
		'withdrawal_methods' => "Bankovní převod\nKryptoměny",
		'game_types'         => "Automaty\nRuleta\nŽivé casino",
		'providers_list'     => '',
		'review_table'       => "Provozovatel | Neon Fortune N.V. (Curaçao)\nLicence | Bez licence MF ČR\nStav v ČR | Na seznamu nepovolených her\nMěna | EUR\nMin. výběr | 100 €\nDoba výběru | 5–30 dní\nPodpora | e-mail, chat (anglicky)",
		'faq'                => "Je Neon Fortune legální v Česku? | Ne. Kasino nemá licenci Ministerstva financí ČR a je na seznamu nepovolených internetových her.\nVyplatí Neon Fortune výhru? | Výplatu nikdo nezaručuje. Při sporu se nemůžete obrátit na český úřad ani soud.\nKde hrát místo toho? | U licencovaných kasin z našeho přehledu – mají ověřené výběry, českou podporu a bonusy s férovými podmínkami.",
		'fact_checker'       => (string) $csp_check,
		'toplist_show'       => '0',
	)
);
if ( $csp_img && file_exists( $csp_img . '/casino-neon-fortune.png' ) && ! has_post_thumbnail( $neon ) ) {
	$tmp = wp_tempnam( 'casino-neon-fortune.png' );
	copy( $csp_img . '/casino-neon-fortune.png', $tmp );
	$att = media_handle_sideload( array( 'name' => 'casino-neon-fortune.png', 'tmp_name' => $tmp ), $neon );
	if ( ! is_wp_error( $att ) ) {
		set_post_thumbnail( $neon, $att );
	}
}

/* ---------------------------------------------------------------------------
 * 2) Stránky
 * ------------------------------------------------------------------------- */
$csp_about = $csp_upsert(
	'page',
	'o-nas',
	array(
		'post_title'     => 'O nás',
		'comment_status' => 'closed',
		'post_content'   => '<p><strong>CS Game je nezávislý průvodce světem legálních online kasin v Česku.</strong> Recenze píšeme pro hráče, kteří chtějí vědět, do čeho jdou – jaké jsou skutečné podmínky bonusů, jak rychle chodí výplaty a kde se dá hrát bezpečně.</p>
<p>Každé kasino testujeme s vlastním vkladem. Projdeme registraci, ověření identity, bonus i výběr výhry a teprve potom píšeme recenzi. Hodnocení pravidelně aktualizujeme – když kasino změní podmínky, dozvíte se to u nás.</p>
<p>Web financujeme z partnerských odkazů. Pokud se přes nás zaregistrujete, kasino nám může zaplatit provizi – <strong>na hodnocení to ale nemá vliv</strong> a kasina bez licence MF ČR nikdy nedoporučujeme.</p>',
	),
	array(
		'intro'          => 'Jsme tým hráčů, novinářů a analytiků. Online kasina testujeme s reálnými penězi, aby vy nemuseli riskovat.',
		'info_cards'     => "🛡️ | Jen legální kasina | Doporučujeme pouze provozovatele s licencí Ministerstva financí ČR.\n🔍 | Testujeme s vlastním vkladem | Každý bonus i výběr si sami vyzkoušíme, než o něm napíšeme.\n⚖️ | Nezávislé hodnocení | Provize od partnerů nemají vliv na pořadí ani na známku.\n💚 | Zodpovědné hraní | Upozorňujeme na rizika a odkazujeme na odbornou pomoc.",
		'info_steps'     => "2016 – první recenze | Začínali jsme jako blog dvou hráčů, kteří chtěli mít přehled o podmínkách bonusů.\n2019 – nový zákon o hazardu | Po otevření trhu jsme začali systematicky testovat všechna licencovaná kasina.\n2023 – redakce a ověřování faktů | Rozšířili jsme tým o editora, který kontroluje každou recenzi.\nDnes | Přehled bonusů, automatů a recenzí aktualizujeme každý týden.",
		'info_team'      => '',
		'show_subscribe' => '0',
		'show_disclaimer' => '1',
	),
	'templates/template-about.php'
);

$csp_contact = $csp_upsert(
	'page',
	'kontakt',
	array(
		'post_title'     => 'Kontakt',
		'comment_status' => 'closed',
		'post_content'   => '<p>Našli jste v recenzi chybu nebo zastaralou informaci? Napište nám – opravu zpravidla provedeme do 48 hodin. Rádi zodpovíme i dotazy k bonusům a podmínkám kasin.</p>
<p><strong>Upozornění:</strong> nejsme provozovatelem žádného kasina a nemáme přístup k vašemu hráčskému účtu. S problémem s výběrem nebo účtem se obraťte přímo na podporu kasina. Pokud vám nepomůže, napište nám a poradíme, jak postupovat.</p>',
	),
	array(
		'intro'          => 'Máte dotaz, připomínku nebo nabídku spolupráce? Ozvěte se nám přes formulář nebo e-mailem.',
		'info_cards'     => "✉️ | E-mail redakce | redakce@vasedomena.cz\n⏱️ | Doba odpovědi | Obvykle do 2 pracovních dnů (Po–Pá 9:00–17:00)\n🤝 | Spolupráce a reklama | partneri@vasedomena.cz\n🔒 | Osobní údaje (GDPR) | gdpr@vasedomena.cz",
		'faq'            => "Můžete mi pomoct s výběrem výhry z kasina? | Nejsme provozovatel kasina, ale poradíme, jak postupovat a na koho se obrátit.\nJak rychle opravíte chybu v recenzi? | Nahlášené chyby ověřujeme a opravujeme zpravidla do 48 hodin.\nPřijímáte placené recenze? | Ne. Spolupráce s kasiny nemá vliv na naše hodnocení.",
		'show_subscribe' => '0',
	),
	'templates/template-contact.php'
);

$csp_method = $csp_upsert(
	'page',
	'jak-hodnotime-kasina',
	array(
		'post_title'     => 'Jak hodnotíme kasina',
		'comment_status' => 'closed',
		'post_content'   => '<h2>Proč máme vlastní metodiku</h2>
<p>Hodnocení kasina by nemělo záviset na tom, kolik zaplatí za reklamu. Proto používáme pevná kritéria, která platí pro všechna kasina stejně. Výsledná známka (0–5 hvězdiček) je váženým průměrem dílčích hodnocení.</p>
<h2>Licence je podmínka, ne bonus</h2>
<p>Kasino bez licence Ministerstva financí ČR nemůže získat kladné hodnocení, ať nabízí cokoli. Takové weby v recenzích jasně označujeme a místo odkazu do kasina doporučujeme legální alternativy.</p>
<blockquote><h4>Jak poznat legální kasino</h4><p>Seznam povolených provozovatelů zveřejňuje Ministerstvo financí. Legální kasino má českou doménu, podmínky v češtině a vyžaduje ověření identity už při registraci.</p></blockquote>
<h2>Bonusy počítáme do koruny</h2>
<p>U každého bonusu přepočítáme, kolik musíte skutečně protočit, jaký je maximální vklad a výhra a za jak dlouho bonus propadne. Velký bonus s nereálnými podmínkami hodnotíme hůř než menší, ale férový.</p>
<h3>Výběry testujeme s reálnými penězi</h3>
<p>Výběr výhry zkoušíme alespoň dvěma metodami a měříme čas od žádosti po připsání na účet. Výsledky zapisujeme do tabulky v recenzi.</p>
<h2>Aktualizace hodnocení</h2>
<p>Recenze kontrolujeme nejméně jednou za čtvrtletí a vždy, když kasino změní podmínky nebo nám hráči nahlásí problém. Datum poslední aktualizace najdete u každé recenze.</p>',
	),
	array(
		'intro'          => 'Každé kasino posuzujeme podle šesti kritérií se stejnými vahami. Takhle vzniká hodnocení, které vidíte v recenzích a toplistech.',
		'info_cards'     => "🛡️ | Licence a bezpečnost | 25 | Licence MF ČR, ochrana dat, ověření identity, nástroje zodpovědného hraní.\n🎁 | Bonusy a podmínky | 20 | Výše bonusu, protočení, limity výher a srozumitelnost podmínek.\n💸 | Vklady a výběry | 20 | Platební metody, poplatky, limity a skutečná rychlost výplaty.\n🎰 | Nabídka her | 15 | Počet a kvalita automatů, živé casino, poskytovatelé a RTP.\n📱 | Mobilní verze | 10 | Aplikace pro iOS a Android, rychlost webu a pohodlí ovládání.\n💬 | Zákaznická podpora | 10 | Dostupnost, čeština, rychlost a kvalita odpovědí.",
		'info_steps'     => "Registrace a ověření | Založíme si účet a projdeme ověřením identity jako běžný hráč.\nVklad a bonus | Vložíme vlastní peníze a aktivujeme vstupní bonus nebo free spiny.\nHraní | Otestujeme automaty, živé casino i mobilní verzi.\nVýběr výhry | Požádáme o výplatu a měříme, za jak dlouho dorazí.\nPodpora | Položíme podpoře několik dotazů přes chat i e-mail.\nOvěření faktů | Editor zkontroluje údaje v recenzi s podmínkami kasina.",
		'faq'            => "Mohou kasina zaplatit za lepší hodnocení? | Ne. Hodnocení vychází jen z kritérií na této stránce. Provize od partnerů na něj nemají vliv.\nJak často hodnocení aktualizujete? | Nejméně jednou za čtvrtletí a vždy při změně podmínek kasina.\nHodnotíte i kasina bez licence? | Recenzujeme je jen jako varování. Nikdy nedostanou kladné hodnocení ani odkaz do kasina.",
		'fact_checker'   => (string) $csp_check,
		'show_subscribe' => '0',
	),
	'templates/template-methodology.php'
);

$csp_privacy = $csp_upsert(
	'page',
	'ochrana-osobnich-udaju',
	array(
		'post_title'     => 'Ochrana osobních údajů',
		'comment_status' => 'closed',
		'post_content'   => '<p>Tyto zásady vysvětlují, jaké osobní údaje zpracováváme při používání webu, proč a jak dlouho, a jaká máte práva podle nařízení (EU) 2016/679 (GDPR) a zákona č. 110/2019 Sb.</p>
<h2>1. Správce osobních údajů</h2>
<p>Správcem je <strong>[Název provozovatele]</strong>, IČO [00000000], se sídlem [ulice, PSČ, město] (dále jen „správce“). Kontakt pro otázky ochrany osobních údajů: <strong>gdpr@vasedomena.cz</strong>.</p>
<h2>2. Jaké údaje zpracováváme</h2>
<ul>
<li><strong>Kontaktní formulář:</strong> jméno, e-mail, obsah zprávy.</li>
<li><strong>Odběr novinek:</strong> e-mailová adresa a datum přihlášení.</li>
<li><strong>Komentáře a hodnocení:</strong> jméno nebo přezdívka, e-mail, text a hodnocení.</li>
<li><strong>Technické údaje:</strong> IP adresa, typ prohlížeče, soubory cookies.</li>
</ul>
<h2>3. Účely a právní základ zpracování</h2>
<ul>
<li>Vyřízení vašeho dotazu – oprávněný zájem správce a váš souhlas (čl. 6 odst. 1 písm. a) a f) GDPR).</li>
<li>Zasílání novinek – váš souhlas, který můžete kdykoli odvolat.</li>
<li>Zobrazení komentářů a ochrana před spamem – oprávněný zájem.</li>
<li>Měření návštěvnosti a affiliate odkazy – souhlas udělený v liště cookies.</li>
</ul>
<h2>4. Doba uložení</h2>
<p>Zprávy z kontaktního formuláře uchováváme nejdéle 2 roky od vyřízení. E-mail pro odběr novinek do odvolání souhlasu. Komentáře po dobu zveřejnění článku. Analytické cookies nejdéle 13 měsíců.</p>
<h2>5. Příjemci údajů</h2>
<p>Údaje mohou zpracovávat poskytovatelé hostingu, e-mailových a analytických služeb, se kterými máme uzavřenou smlouvu o zpracování. Údaje neprodáváme a nepředáváme provozovatelům kasin.</p>
<h2>6. Cookies</h2>
<p>Nezbytné cookies zajišťují fungování webu. Analytické a marketingové cookies (včetně partnerských odkazů) používáme jen s vaším souhlasem, který můžete kdykoli změnit v nastavení cookies.</p>
<h2>7. Vaše práva</h2>
<ul>
<li>právo na přístup k údajům a jejich opravu,</li>
<li>právo na výmaz a omezení zpracování,</li>
<li>právo na přenositelnost údajů,</li>
<li>právo vznést námitku a odvolat souhlas,</li>
<li>právo podat stížnost u Úřadu pro ochranu osobních údajů (www.uoou.cz).</li>
</ul>
<p>Žádost můžete poslat e-mailem nebo přes kontaktní formulář s tématem „Osobní údaje (GDPR)“. Odpovíme nejpozději do 30 dnů.</p>
<h2>8. Změny zásad</h2>
<p>Zásady můžeme aktualizovat. Aktuální znění je vždy na této stránce s datem platnosti.</p>',
	),
	array(
		'intro'           => '',
		'show_toc'        => '1',
		'show_disclaimer' => '0',
		'show_subscribe'  => '0',
	),
	'templates/template-document.php'
);
update_option( 'wp_page_for_privacy_policy', $csp_privacy );

/* ---------------------------------------------------------------------------
 * 3) Patička: odkazy místo „#“
 * ------------------------------------------------------------------------- */
$csp_links = array(
	'O nás'                  => $csp_about,
	'Kontakt'                => $csp_contact,
	'Jak hodnotíme kasina'   => $csp_method,
	'Ochrana osobních údajů' => $csp_privacy,
);
$csp_menu = wp_get_nav_menu_object( 'Patička' );
if ( $csp_menu ) {
	$found = array();
	foreach ( (array) wp_get_nav_menu_items( $csp_menu->term_id ) as $item ) {
		if ( isset( $csp_links[ $item->title ] ) ) {
			$found[ $item->title ] = true;
			wp_update_nav_menu_item(
				$csp_menu->term_id,
				$item->ID,
				array(
					'menu-item-title'     => $item->title,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $csp_links[ $item->title ],
					'menu-item-type'      => 'post_type',
					'menu-item-position'  => $item->menu_order,
					'menu-item-status'    => 'publish',
				)
			);
		}
	}
	foreach ( array_diff_key( $csp_links, $found ) as $title => $pid ) {
		wp_update_nav_menu_item( $csp_menu->term_id, 0, array( 'menu-item-title' => $title, 'menu-item-object' => 'page', 'menu-item-object-id' => $pid, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
	}
}

echo "Informační stránky a ukázka nelegálního kasina připraveny.\n";
