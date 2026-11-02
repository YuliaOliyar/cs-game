<?php
/**
 * Stránka autora (ТЗ bod 11): jméno, karta autora, články autora.
 *
 * @package CSGame
 */

get_header();
$author = get_queried_object();
?>
<div class="container">
	<header class="page-head">
		<h1 class="page-head__title"><?php echo esc_html( $author->display_name ); ?></h1>
	</header>

	<?php get_template_part( 'template-parts/author-card', null, array( 'user_id' => $author->ID, 'heading' => false ) ); ?>

	<h2 class="section__title"><?php esc_html_e( 'Články autora', 'csgame' ); ?></h2>
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
		<p class="muted"><?php esc_html_e( 'Autor zatím nepublikoval žádné články.', 'csgame' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
