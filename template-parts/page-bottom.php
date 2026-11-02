<?php
/**
 * Spodní společné bloky: karta autora, odběr, FAQ.
 *
 * Args: order (array of: author, subscribe, faq, interlinks)
 *
 * @package CSGame
 */

$order = ! empty( $args['order'] ) ? $args['order'] : array( 'author', 'subscribe', 'faq' );
foreach ( $order as $block ) {
	switch ( $block ) {
		case 'author':
			get_template_part( 'template-parts/author-card' );
			break;
		case 'subscribe':
			if ( csgame_flag( 'show_subscribe' ) ) {
				echo csgame_subscribe_form(); // phpcs:ignore
			}
			break;
		case 'faq':
			get_template_part( 'template-parts/faq' );
			break;
		case 'interlinks':
			get_template_part( 'template-parts/interlinks', null, array( 'location' => 'interlinks' ) );
			break;
		case 'disclaimer':
			get_template_part( 'template-parts/disclaimer' );
			break;
	}
}
