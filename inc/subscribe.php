<?php
/**
 * Formulář odběru novinek. E-maily se ukládají do administrace
 * (Odběratelé). Pro napojení na Mailchimp/Ecomail lze použít filtr
 * `csgame_subscribe_form_html` a nahradit formulář vlastním.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'cs_subscriber',
			array(
				'labels'          => array(
					'name'          => __( 'Odběratelé', 'csgame' ),
					'singular_name' => __( 'Odběratel', 'csgame' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'menu_icon'       => 'dashicons-email',
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

function csgame_subscribe_form( $title = '', $text = '' ) {
	$title = $title ? $title : get_theme_mod( 'csgame_sub_title', __( 'Nenechte si ujít nejlepší bonusy', 'csgame' ) );
	$text  = $text ? $text : get_theme_mod( 'csgame_sub_text', __( 'Exkluzivní promo kódy a novinky ze světa online kasin jednou týdně do vašeho e-mailu.', 'csgame' ) );
	$state = isset( $_GET['sub'] ) ? sanitize_key( $_GET['sub'] ) : ''; // phpcs:ignore

	ob_start();
	?>
	<section class="subscribe" id="odber">
		<div class="subscribe__icon"><?php echo csgame_icon( 'mail' ); // phpcs:ignore ?></div>
		<div class="subscribe__text">
			<h2 class="subscribe__title"><?php echo esc_html( $title ); ?></h2>
			<p><?php echo esc_html( $text ); ?></p>
		</div>
		<form class="subscribe__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="csgame_subscribe">
			<input type="hidden" name="redirect" value="<?php echo esc_url( get_permalink() ? get_permalink() : home_url( '/' ) ); ?>">
			<?php wp_nonce_field( 'csgame_subscribe', 'csgame_sub_nonce' ); ?>
			<label class="screen-reader-text" for="sub-email"><?php esc_html_e( 'E-mail', 'csgame' ); ?></label>
			<input id="sub-email" type="email" name="email" required placeholder="<?php esc_attr_e( 'Váš e-mail', 'csgame' ); ?>">
			<input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
			<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Odebírat', 'csgame' ); ?></button>
			<label class="subscribe__consent">
				<input type="checkbox" name="consent" value="1" required>
				<?php esc_html_e( 'Je mi 18+ a souhlasím se zpracováním osobních údajů.', 'csgame' ); ?>
			</label>
			<?php if ( 'ok' === $state ) : ?>
				<p class="subscribe__msg subscribe__msg--ok"><?php esc_html_e( 'Děkujeme, přihlášení k odběru proběhlo úspěšně.', 'csgame' ); ?></p>
			<?php elseif ( 'err' === $state ) : ?>
				<p class="subscribe__msg subscribe__msg--err"><?php esc_html_e( 'Zadejte prosím platný e-mail.', 'csgame' ); ?></p>
			<?php endif; ?>
		</form>
	</section>
	<?php
	return apply_filters( 'csgame_subscribe_form_html', ob_get_clean() );
}

function csgame_handle_subscribe() {
	$redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : home_url( '/' );
	$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );

	if ( ! isset( $_POST['csgame_sub_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['csgame_sub_nonce'] ), 'csgame_subscribe' ) || ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sub', 'err', $redirect ) . '#odber' );
		exit;
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! is_email( $email ) || empty( $_POST['consent'] ) ) {
		wp_safe_redirect( add_query_arg( 'sub', 'err', $redirect ) . '#odber' );
		exit;
	}
	$exists = get_posts( array( 'post_type' => 'cs_subscriber', 'title' => $email, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( ! $exists ) {
		wp_insert_post(
			array(
				'post_type'   => 'cs_subscriber',
				'post_title'  => $email,
				'post_status' => 'private',
			)
		);
		do_action( 'csgame_new_subscriber', $email );
	}
	wp_safe_redirect( add_query_arg( 'sub', 'ok', $redirect ) . '#odber' );
	exit;
}
add_action( 'admin_post_csgame_subscribe', 'csgame_handle_subscribe' );
add_action( 'admin_post_nopriv_csgame_subscribe', 'csgame_handle_subscribe' );
