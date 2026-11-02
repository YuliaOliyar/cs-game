<?php
/**
 * Karta článku: obrázek, název, autor, datum, perex.
 *
 * @package CSGame
 */

?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card__img" href="<?php the_permalink(); ?>">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'csgame-card', array( 'loading' => 'lazy' ) );
		} else {
			echo '<span class="post-card__ph"></span>';
		}
		?>
	</a>
	<div class="post-card__body">
		<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="post-card__meta muted">
			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
			·
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<p class="post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
	</div>
</article>
