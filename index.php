<?php
/**
 * Výchozí šablona.
 *
 * @package CSGame
 */

get_header();
?>
<div class="container">
	<header class="page-head">
		<h1 class="page-head__title">
			<?php
			if ( is_search() ) {
				printf( esc_html__( 'Výsledky hledání: %s', 'csgame' ), esc_html( get_search_query() ) );
			} else {
				the_archive_title();
			}
			?>
		</h1>
		<?php the_archive_description( '<div class="page-head__intro">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="post-grid">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/post-card' );
			}
			?>
		</div>
		<?php csgame_pagination(); ?>
	<?php else : ?>
		<p class="muted"><?php esc_html_e( 'Nic nebylo nalezeno.', 'csgame' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
