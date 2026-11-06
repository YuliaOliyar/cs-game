<?php
/**
 * Kontaktní formulář: odeslání e-mailem + uložení do administrace (Zprávy).
 *
 * Příjemce: Vzhled → Přizpůsobit → CS Game → Kontakt (prázdné = e-mail administrátora).
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'cs_message',
			array(
				'labels'          => array(
					'name'          => __( 'Zprávy', 'csgame' ),
					'singular_name' => __( 'Zpráva', 'csgame' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'menu_icon'       => 'dashicons-format-chat',
				'supports'        => array( 'title', 'editor' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

/**
 * Subjects for the select.
 */
function csgame_contact_subjects() {
	return apply_filters(
		'csgame_contact_subjects',
		array(
			'obecny'    => __( 'Obecný dotaz', 'csgame' ),
			'recenze'   => __( 'Recenze kasina / oprava údajů', 'csgame' ),
			'stiznost'  => __( 'Problém s kasinem', 'csgame' ),
			'spoluprace' => __( 'Spolupráce a reklama', 'csgame' ),
			'gdpr'      => __( 'Osobní údaje (GDPR)', 'csgame' ),
		)
	);
}

/**
 * Contact form markup.
 */
function csgame_contact_form() {
	$state  = isset( $_GET['sent'] ) ? sanitize_key( $_GET['sent'] ) : ''; // phpcs:ignore
	$errors = array(
		'err'  => __( 'Vyplňte prosím všechna povinná pole a zadejte platný e-mail.', 'csgame' ),
		'spam' => __( 'Zprávu se nepodařilo odeslat. Zkuste to prosím znovu.', 'csgame' ),
	);
	ob_start();
	?>
	<div class="cform card" id="kontakt-form">
		<?php if ( 'ok' === $state ) : ?>
			<div class="cform__done">
				<span class="cform__done-icon"><?php echo csgame_icon( 'check' ); // phpcs:ignore ?></span>
				<p class="cform__done-title"><?php esc_html_e( 'Děkujeme, zprávu jsme přijali.', 'csgame' ); ?></p>
				<p class="muted"><?php esc_html_e( 'Odpovíme vám obvykle do 2 pracovních dnů na zadaný e-mail.', 'csgame' ); ?></p>
			</div>
		<?php else : ?>
			<h2 class="cform__title"><?php esc_html_e( 'Napište nám', 'csgame' ); ?></h2>
			<?php if ( isset( $errors[ $state ] ) ) : ?>
				<p class="cform__err" role="alert"><?php echo esc_html( $errors[ $state ] ); ?></p>
			<?php endif; ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
				<input type="hidden" name="action" value="csgame_contact">
				<input type="hidden" name="redirect" value="<?php echo esc_url( csgame_current_url() ); ?>">
				<input type="hidden" name="ts" value="<?php echo esc_attr( time() ); ?>">
				<?php wp_nonce_field( 'csgame_contact', 'csgame_contact_nonce' ); ?>
				<input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
				<div class="cform__grid">
					<label class="cform__field">
						<span><?php esc_html_e( 'Jméno', 'csgame' ); ?> *</span>
						<input type="text" name="name" required maxlength="100" autocomplete="name">
					</label>
					<label class="cform__field">
						<span><?php esc_html_e( 'E-mail', 'csgame' ); ?> *</span>
						<input type="email" name="email" required maxlength="150" autocomplete="email">
					</label>
					<label class="cform__field cform__field--full">
						<span><?php esc_html_e( 'Téma', 'csgame' ); ?></span>
						<select name="subject">
							<?php foreach ( csgame_contact_subjects() as $k => $v ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<label class="cform__field cform__field--full">
						<span><?php esc_html_e( 'Zpráva', 'csgame' ); ?> *</span>
						<textarea name="message" rows="6" required maxlength="5000"></textarea>
					</label>
				</div>
				<label class="cform__consent">
					<input type="checkbox" name="consent" value="1" required>
					<span>
						<?php
						$privacy = get_privacy_policy_url();
						if ( $privacy ) {
							/* translators: %s: privacy policy link */
							printf( esc_html__( 'Souhlasím se zpracováním osobních údajů za účelem vyřízení dotazu podle %s.', 'csgame' ), '<a href="' . esc_url( $privacy ) . '">' . esc_html__( 'zásad ochrany osobních údajů', 'csgame' ) . '</a>' ); // phpcs:ignore
						} else {
							esc_html_e( 'Souhlasím se zpracováním osobních údajů za účelem vyřízení dotazu.', 'csgame' );
						}
						?>
					</span>
				</label>
				<button class="btn btn--primary btn--lg" type="submit"><?php echo csgame_icon( 'mail' ); // phpcs:ignore ?> <?php esc_html_e( 'Odeslat zprávu', 'csgame' ); ?></button>
			</form>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'cs_contact_form', 'csgame_contact_form' );

/**
 * Handle submission.
 */
function csgame_handle_contact() {
	$redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : home_url( '/' );
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );
	$back     = function ( $state ) use ( $redirect ) {
		wp_safe_redirect( add_query_arg( 'sent', $state, $redirect ) . '#kontakt-form' );
		exit;
	};

	// Anti-spam: nonce, honeypot, min. 3 s on page.
	$ts = isset( $_POST['ts'] ) ? (int) $_POST['ts'] : 0;
	if ( ! isset( $_POST['csgame_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['csgame_contact_nonce'] ), 'csgame_contact' ) || ! empty( $_POST['website'] ) || ( time() - $ts ) < 3 ) {
		$back( 'spam' );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$subj    = isset( $_POST['subject'] ) ? sanitize_key( $_POST['subject'] ) : 'obecny';
	$subs    = csgame_contact_subjects();
	$subj    = isset( $subs[ $subj ] ) ? $subs[ $subj ] : reset( $subs );

	if ( '' === $name || ! is_email( $email ) || '' === $message || empty( $_POST['consent'] ) ) {
		$back( 'err' );
	}

	wp_insert_post(
		array(
			'post_type'    => 'cs_message',
			'post_status'  => 'private',
			'post_title'   => wp_trim_words( $subj . ' – ' . $name . ' (' . $email . ')', 20, '…' ),
			'post_content' => $message,
			'meta_input'   => array(
				'_cs_name'    => $name,
				'_cs_email'   => $email,
				'_cs_subject' => $subj,
				'_cs_page'    => $redirect,
			),
		)
	);

	$to = get_theme_mod( 'csgame_contact_email', '' );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );
	wp_mail(
		$to,
		'[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $subj,
		$name . ' <' . $email . ">\n\n" . $message . "\n\n— " . $redirect,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);
	do_action( 'csgame_contact_sent', $name, $email, $subj, $message );

	$back( 'ok' );
}
add_action( 'admin_post_csgame_contact', 'csgame_handle_contact' );
add_action( 'admin_post_nopriv_csgame_contact', 'csgame_handle_contact' );

/**
 * Show sender in admin list.
 */
add_filter(
	'manage_cs_message_posts_columns',
	function ( $cols ) {
		$cols['cs_email'] = __( 'E-mail', 'csgame' );
		return $cols;
	}
);
add_action(
	'manage_cs_message_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'cs_email' === $col ) {
			$e = get_post_meta( $post_id, '_cs_email', true );
			echo '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>';
		}
	},
	10,
	2
);
