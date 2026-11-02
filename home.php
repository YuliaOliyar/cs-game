<?php
/**
 * Blog / novinky (ТЗ bod 9).
 *
 * @package CSGame
 */

get_header();
$blog_id = (int) get_option( 'page_for_posts' );
$title   = $blog_id ? get_the_title( $blog_id ) : csgame_mod( 'csgame_blog_title' );
$cats    = get_categories( array( 'hide_empty' => true ) );
?>
<div class="container">
	<header class="page-head">
		<h1 class="page-head__title"><?php echo esc_html( is_category() ? single_cat_title( '', false ) : $title ); ?></h1>
		<?php if ( $blog_id && csgame_meta( 'intro', $blog_id ) ) : ?>
			<div class="page-head__intro"><?php echo wpautop( wp_kses_post( csgame_meta( 'intro', $blog_id ) ) ); // phpcs:ignore ?></div>
		<?php endif; ?>
	</header>

	<?php if ( $cats ) : ?>
		<nav class="cat-nav" aria-label="<?php esc_attr_e( 'Kategorie', 'csgame' ); ?>">
			<a class="chip<?php echo ! is_category() ? ' is-active' : ''; ?>" href="<?php echo esc_url( $blog_id ? get_permalink( $blog_id ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Vše', 'csgame' ); ?></a>
			<?php foreach ( $cats as $cat ) : ?>
				<a class="chip<?php echo is_category( $cat->term_id ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

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
		<p class="muted"><?php esc_html_e( 'Zatím zde nejsou žádné články.', 'csgame' ); ?></p>
	<?php endif; ?>

	<div class="container--narrow">
		<?php if ( $blog_id && ! is_paged() && get_post_field( 'post_content', $blog_id ) ) : ?>
			<div class="entry-content"><?php echo apply_filters( 'the_content', get_post_field( 'post_content', $blog_id ) ); // phpcs:ignore ?></div>
		<?php endif; ?>
		<?php echo csgame_subscribe_form(); // phpcs:ignore ?>
	</div>
</div>
<?php
get_footer();
