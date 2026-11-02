<?php
/**
 * Pole poskytovatele: navázaná stránka s dlouhým textem, FAQ a recenzemi.
 *
 * Taxonomie nemá vlastní obsah ani komentáře, proto se dlouhý text,
 * obsah (TOC), FAQ, autor a recenze berou z vybrané (klidně skryté) stránky.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

function csgame_provider_field_add() {
	?>
	<div class="form-field">
		<label for="csgame_page"><?php esc_html_e( 'Stránka s textem, FAQ a recenzemi', 'csgame' ); ?></label>
		<?php wp_dropdown_pages( array( 'name' => 'csgame_page', 'id' => 'csgame_page', 'show_option_none' => '—', 'option_none_value' => '0', 'post_status' => array( 'publish', 'private', 'draft' ) ) ); ?>
	</div>
	<?php
}
add_action( 'provider_add_form_fields', 'csgame_provider_field_add' );

function csgame_provider_field_edit( $term ) {
	$val = (int) get_term_meta( $term->term_id, 'csgame_page', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="csgame_page"><?php esc_html_e( 'Stránka s textem, FAQ a recenzemi', 'csgame' ); ?></label></th>
		<td>
			<?php wp_dropdown_pages( array( 'name' => 'csgame_page', 'id' => 'csgame_page', 'selected' => $val, 'show_option_none' => '—', 'option_none_value' => '0', 'post_status' => array( 'publish', 'private', 'draft' ) ) ); ?>
			<p class="description"><?php esc_html_e( 'Popis poskytovatele (výše) se zobrazí jako rozbalovací úvod. Obsah vybrané stránky se vypíše pod seznamem automatů.', 'csgame' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'provider_edit_form_fields', 'csgame_provider_field_edit' );

function csgame_provider_field_save( $term_id ) {
	if ( isset( $_POST['csgame_page'] ) && current_user_can( 'manage_categories' ) ) { // phpcs:ignore
		update_term_meta( $term_id, 'csgame_page', (int) $_POST['csgame_page'] ); // phpcs:ignore
	}
}
add_action( 'created_provider', 'csgame_provider_field_save' );
add_action( 'edited_provider', 'csgame_provider_field_save' );
