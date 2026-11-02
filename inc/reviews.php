<?php
/**
 * Uživatelské recenze (komentáře s hodnocením) + hodnocení článku.
 *
 * @package CSGame
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rating field in comment form.
 */
function csgame_rating_field() {
	?>
	<fieldset class="rate-input">
		<legend><?php esc_html_e( 'Vaše hodnocení', 'csgame' ); ?></legend>
		<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
			<input type="radio" id="rate-<?php echo (int) $i; ?>" name="csgame_rating" value="<?php echo (int) $i; ?>" <?php checked( 5, $i ); ?>>
			<label for="rate-<?php echo (int) $i; ?>" title="<?php echo esc_attr( $i ); ?>">★</label>
		<?php endfor; ?>
	</fieldset>
	<?php
}
add_action( 'comment_form_logged_in_after', 'csgame_rating_field' );
add_action( 'comment_form_after_fields', 'csgame_rating_field' );

function csgame_save_rating( $comment_id ) {
	if ( isset( $_POST['csgame_rating'] ) ) { // phpcs:ignore
		$r = max( 1, min( 5, (int) $_POST['csgame_rating'] ) ); // phpcs:ignore
		add_comment_meta( $comment_id, 'csgame_rating', $r, true );
		$post_id = (int) get_comment( $comment_id )->comment_post_ID;
		delete_transient( 'csgame_avg_' . $post_id );
	}
}
add_action( 'comment_post', 'csgame_save_rating' );

/**
 * Average user rating [avg, count].
 */
function csgame_user_rating( $post_id ) {
	$cached = get_transient( 'csgame_avg_' . $post_id );
	if ( false !== $cached ) {
		return $cached;
	}
	$comments = get_comments( array( 'post_id' => $post_id, 'status' => 'approve', 'meta_key' => 'csgame_rating', 'fields' => 'ids' ) );
	$sum      = 0;
	foreach ( $comments as $cid ) {
		$sum += (int) get_comment_meta( $cid, 'csgame_rating', true );
	}
	$count = count( $comments );
	$res   = array( $count ? round( $sum / $count, 1 ) : 0, $count );
	set_transient( 'csgame_avg_' . $post_id, $res, HOUR_IN_SECONDS );
	return $res;
}
add_action( 'wp_set_comment_status', function ( $cid ) {
	$c = get_comment( $cid );
	if ( $c ) {
		delete_transient( 'csgame_avg_' . $c->comment_post_ID );
	}
} );

/**
 * Single review markup.
 */
function csgame_review_cb( $comment, $args, $depth ) {
	$rating = (int) get_comment_meta( $comment->comment_ID, 'csgame_rating', true );
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'review' ); ?>>
		<div class="review__head">
			<?php echo get_avatar( $comment, 40, '', '', array( 'class' => 'avatar' ) ); ?>
			<div>
				<strong class="review__name"><?php comment_author(); ?></strong>
				<time class="review__date" datetime="<?php comment_date( 'c' ); ?>"><?php comment_date(); ?></time>
			</div>
			<?php if ( $rating ) : ?>
				<span class="review__rating"><?php echo csgame_stars( $rating, false ); // phpcs:ignore ?></span>
			<?php endif; ?>
		</div>
		<?php if ( '0' === $comment->comment_approved ) : ?>
			<p class="review__pending"><?php esc_html_e( 'Recenze čeká na schválení.', 'csgame' ); ?></p>
		<?php endif; ?>
		<div class="review__body"><?php comment_text(); ?></div>
		<?php
		comment_reply_link(
			array_merge(
				$args,
				array(
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="review__reply">',
					'after'     => '</div>',
				)
			)
		);
		?>
	<?php
}

/**
 * Reviews block: form + list.
 */
function csgame_reviews_block( $title = '', $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$number  = (int) get_comments_number( $post_id );
	if ( ! comments_open( $post_id ) && ! $number ) {
		return;
	}
	list( $avg, $count ) = csgame_user_rating( $post_id );
	$title = $title ? $title : __( 'Recenze hráčů', 'csgame' );
	?>
	<section class="section reviews" id="recenze">
		<div class="reviews__head">
			<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $count ) : ?>
				<div class="reviews__avg">
					<?php echo csgame_stars( $avg ); // phpcs:ignore ?>
					<span class="muted"><?php echo esc_html( sprintf( _n( '%s recenze', '%s recenzí', $count, 'csgame' ), number_format_i18n( $count ) ) ); ?></span>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( comments_open( $post_id ) ) : ?>
			<div class="card reviews__form">
				<?php
				comment_form(
					array(
						'title_reply'          => __( 'Napište recenzi', 'csgame' ),
						'label_submit'         => __( 'Odeslat recenzi', 'csgame' ),
						'comment_notes_before' => '',
						'class_submit'         => 'btn btn--primary',
						'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Vaše zkušenost', 'csgame' ) . '</label><textarea id="comment" name="comment" rows="5" required></textarea></p>',
					),
					$post_id
				);
				?>
			</div>
		<?php endif; ?>

		<?php
		if ( $number ) :
			$comments = get_comments(
				array(
					'post_id' => $post_id,
					'status'  => 'approve',
					'order'   => 'ASC',
				)
			);
			?>
			<ol class="reviews__list">
				<?php
				wp_list_comments(
					array(
						'callback'    => 'csgame_review_cb',
						'style'       => 'ol',
						'avatar_size' => 40,
						'max_depth'   => 3,
					),
					$comments
				);
				?>
			</ol>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Article rating (AJAX vote).
 */
function csgame_article_rating( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$sum     = (int) get_post_meta( $post_id, '_cs_votes_sum', true );
	$cnt     = (int) get_post_meta( $post_id, '_cs_votes_count', true );
	$avg     = $cnt ? round( $sum / $cnt, 1 ) : 0;
	?>
	<div class="article-rating card" data-post="<?php echo (int) $post_id; ?>">
		<span class="article-rating__label"><?php esc_html_e( 'Ohodnoťte článek', 'csgame' ); ?></span>
		<div class="article-rating__stars" role="group">
			<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
				<button type="button" data-vote="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( $i ); ?>">★</button>
			<?php endfor; ?>
		</div>
		<span class="article-rating__result muted">
			<span data-avg><?php echo esc_html( number_format_i18n( $avg, 1 ) ); ?></span> / 5
			(<span data-count><?php echo (int) $cnt; ?></span>)
		</span>
	</div>
	<?php
}

function csgame_ajax_vote() {
	check_ajax_referer( 'csgame_vote', 'nonce' );
	$post_id = isset( $_POST['post'] ) ? (int) $_POST['post'] : 0;
	$vote    = isset( $_POST['vote'] ) ? max( 1, min( 5, (int) $_POST['vote'] ) ) : 0;
	if ( ! $post_id || ! $vote || 'publish' !== get_post_status( $post_id ) ) {
		wp_send_json_error();
	}
	$cookie = 'csgame_voted_' . $post_id;
	if ( isset( $_COOKIE[ $cookie ] ) ) {
		wp_send_json_error( array( 'message' => 'voted' ) );
	}
	$sum = (int) get_post_meta( $post_id, '_cs_votes_sum', true ) + $vote;
	$cnt = (int) get_post_meta( $post_id, '_cs_votes_count', true ) + 1;
	update_post_meta( $post_id, '_cs_votes_sum', $sum );
	update_post_meta( $post_id, '_cs_votes_count', $cnt );
	setcookie( $cookie, '1', time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	wp_send_json_success(
		array(
			'avg'   => number_format_i18n( $sum / $cnt, 1 ),
			'count' => $cnt,
		)
	);
}
add_action( 'wp_ajax_csgame_vote', 'csgame_ajax_vote' );
add_action( 'wp_ajax_nopriv_csgame_vote', 'csgame_ajax_vote' );
