<?php
/**
 * Často kladené otázky (akordeon + FAQPage schema).
 *
 * Args: post_id
 *
 * @package CSGame
 */

$post_id = ! empty( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$pairs   = array_filter(
	csgame_pairs( csgame_meta( 'faq', $post_id ) ),
	function ( $p ) {
		return count( $p ) >= 2 && '' !== $p[0];
	}
);
if ( ! $pairs ) {
	return;
}
$title  = csgame_meta( 'faq_title', $post_id ) ? csgame_meta( 'faq_title', $post_id ) : __( 'Často kladené otázky', 'csgame' );
$schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array(),
);
?>
<section class="section faq" id="faq">
	<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
	<div class="faq__list">
		<?php
		foreach ( $pairs as $i => $p ) :
			$q = $p[0];
			$a = implode( ' | ', array_slice( $p, 1 ) );
			$schema['mainEntity'][] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ),
			);
			?>
			<details class="faq__item"<?php echo 0 === $i ? ' open' : ''; ?>>
				<summary><span><?php echo esc_html( $q ); ?></span><?php echo csgame_icon( 'chevron' ); // phpcs:ignore ?></summary>
				<div class="faq__answer"><?php echo wpautop( esc_html( $a ) ); // phpcs:ignore ?></div>
			</details>
		<?php endforeach; ?>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $schema ); ?></script>
</section>
