<?php
/**
 * Pole profilu autora: pozice, fotka, sociální sítě.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_user_fields() {
	return array(
		'csgame_position'  => __( 'Pozice (např. Šéfredaktorka)', 'csgame' ),
		'csgame_photo'     => __( 'URL fotky (prázdné = Gravatar)', 'csgame' ),
		'csgame_facebook'  => 'Facebook URL',
		'csgame_x'         => 'X (Twitter) URL',
		'csgame_linkedin'  => 'LinkedIn URL',
		'csgame_instagram' => 'Instagram URL',
	);
}

function csgame_user_profile( $user ) {
	?>
	<h2><?php esc_html_e( 'CS Game – karta autora', 'csgame' ); ?></h2>
	<table class="form-table" role="presentation">
		<?php foreach ( csgame_user_fields() as $key => $label ) : ?>
			<tr>
				<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td><input class="regular-text" type="<?php echo 'csgame_position' === $key ? 'text' : 'url'; ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>"></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}
add_action( 'show_user_profile', 'csgame_user_profile' );
add_action( 'edit_user_profile', 'csgame_user_profile' );

function csgame_save_user_profile( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	check_admin_referer( 'update-user_' . $user_id );
	foreach ( array_keys( csgame_user_fields() ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			$val = wp_unslash( $_POST[ $key ] ); // phpcs:ignore
			update_user_meta( $user_id, $key, 'csgame_position' === $key ? sanitize_text_field( $val ) : esc_url_raw( $val ) );
		}
	}
}
add_action( 'personal_options_update', 'csgame_save_user_profile' );
add_action( 'edit_user_profile_update', 'csgame_save_user_profile' );
