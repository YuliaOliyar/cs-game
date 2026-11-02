<?php
/**
 * Stránka poskytovatele (ТЗ bod 8).
 *
 * @package CSGame
 */

get_header();
$term    = get_queried_object();
$linked_id = (int) get_term_meta( $term->term_id, 'csgame_page', true );
$linked    = $linked_id ? get_post( $linked_id ) : null;
?>
<div class="container">
	<header class="page-head">
		<h1 class="page-head__title"><?php single_term_title(); ?></h1>
		<?php if ( term_description() ) : ?>
			<div class="collapsible" data-collapsible-text>
				<div class="collapsible__body"><?php echo wp_kses_post( term_description() ); ?></div>
				<button class="collapsible__more" type="button"><?php esc_html_e( 'Zobrazit více', 'csgame' ); ?></button>
			</div>
		<?php endif; ?>
	</header>

	<?php get_template_part( 'template-parts/interlinks', null, array( 'location' => 'providers', 'style' => 'horizontal', 'current' => single_term_title( '', false ) ) ); ?>

	<?php csgame_slot_filter_form(); ?>

	<?php if ( have_posts() ) : ?>
		<div class="slot-grid">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/slot-card' );
			}
			?>
		</div>
		<?php csgame_pagination(); ?>
	<?php else : ?>
		<p class="muted"><?php esc_html_e( 'Žádné automaty neodpovídají filtru.', 'csgame' ); ?></p>
	<?php endif; ?>

	<?php if ( $linked ) : ?>
		<?php
		global $post;
		$post = $linked; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $post );
		?>
		<div class="content-layout">
			<aside class="content-layout__aside"><?php csgame_toc( $linked->ID ); ?></aside>
			<div class="content-layout__main">
				<div class="entry-content"><?php echo apply_filters( 'the_content', $linked->post_content ); // phpcs:ignore ?></div>
			</div>
		</div>

		<?php get_template_part( 'template-parts/toplist', null, array( 'query' => csgame_get_casinos( 5 ), 'title' => sprintf( __( 'Kasina s automaty %s', 'csgame' ), single_term_title( '', false ) ) ) ); ?>

		<div class="container--narrow">
			<?php get_template_part( 'template-parts/faq', null, array( 'post_id' => $linked->ID ) ); ?>
			<?php csgame_reviews_block( '', $linked->ID ); ?>
			<?php get_template_part( 'template-parts/author-card', null, array( 'user_id' => (int) $linked->post_author ) ); ?>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/toplist', null, array( 'query' => csgame_get_casinos( 5 ), 'title' => __( 'Kde hrát automaty online', 'csgame' ) ) ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
