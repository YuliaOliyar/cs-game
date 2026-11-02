<?php
/**
 * Článek (ТЗ bod 10).
 *
 * @package CSGame
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'container container--narrow article' ); ?>>
		<header class="page-head">
			<?php
			$cats = get_the_category();
			if ( $cats ) :
				?>
				<a class="label" href="<?php echo esc_url( get_category_link( $cats[0] ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a>
			<?php endif; ?>
			<h1 class="page-head__title"><?php the_title(); ?></h1>
			<?php echo csgame_byline(); // phpcs:ignore ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="article__cover"><?php the_post_thumbnail( 'large' ); ?></figure>
		<?php endif; ?>

		<?php if ( csgame_meta( 'intro' ) ) : ?>
			<div class="page-head__intro"><?php echo wpautop( wp_kses_post( csgame_meta( 'intro' ) ) ); // phpcs:ignore ?></div>
		<?php endif; ?>

		<?php csgame_toc(); ?>

		<?php if ( csgame_flag( 'toplist_show', '0' ) ) : ?>
			<?php get_template_part( 'template-parts/toplist', null, array( 'query' => csgame_get_casinos( csgame_meta( 'toplist_count' ) ? (int) csgame_meta( 'toplist_count' ) : 5 ), 'title' => csgame_meta( 'toplist_title' ) ) ); ?>
		<?php endif; ?>

		<div class="entry-content"><?php the_content(); ?></div>

		<?php
		wp_link_pages();
		$tags = get_the_tags();
		if ( $tags ) :
			?>
			<p class="hashtags">
				<?php foreach ( $tags as $tag ) : ?>
					<a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
				<?php endforeach; ?>
			</p>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/disclaimer' ); ?>
		<?php csgame_article_rating(); ?>
		<?php get_template_part( 'template-parts/author-card' ); ?>
		<?php get_template_part( 'template-parts/faq' ); ?>
	</article>

	<?php
	$related_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post__not_in'        => array( get_the_ID() ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( $cats ) {
		$related_args['cat'] = $cats[0]->term_id;
	}
	$related = new WP_Query( $related_args );
	if ( $related->have_posts() ) :
		?>
		<section class="container section">
			<h2 class="section__title"><?php esc_html_e( 'Další články', 'csgame' ); ?></h2>
			<div class="post-grid">
				<?php
				while ( $related->have_posts() ) {
					$related->the_post();
					get_template_part( 'template-parts/post-card' );
				}
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<div class="container container--narrow">
		<?php csgame_reviews_block( __( 'Komentáře', 'csgame' ) ); ?>
	</div>
	<?php
endwhile;
get_footer();
