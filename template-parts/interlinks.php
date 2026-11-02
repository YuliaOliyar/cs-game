<?php
/**
 * Blok prolinkování z menu.
 *
 * Args: location (menu location), title, style (vertical|horizontal), current (název aktuální stránky)
 *
 * @package CSGame
 */

$a = wp_parse_args(
	$args,
	array(
		'location' => 'interlinks',
		'title'    => '',
		'style'    => 'vertical',
		'current'  => '',
	)
);

$locations = get_nav_menu_locations();
if ( empty( $locations[ $a['location'] ] ) ) {
	return;
}
$items = wp_get_nav_menu_items( $locations[ $a['location'] ] );
if ( ! $items ) {
	return;
}
$menu  = wp_get_nav_menu_object( $locations[ $a['location'] ] );
$title = $a['title'] ? $a['title'] : ( $menu ? $menu->name : '' );
$here  = isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] )
	? trailingslashit( ( is_ssl() ? 'https://' : 'http://' ) . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . strtok( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), '?' ) )
	: '';
?>
<nav class="interlinks interlinks--<?php echo esc_attr( $a['style'] ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
	<?php if ( 'horizontal' === $a['style'] && $a['current'] ) : ?>
		<span class="interlinks__current"><?php echo esc_html( $a['current'] ); ?></span>
	<?php elseif ( $title ) : ?>
		<h2 class="interlinks__title"><?php echo esc_html( $title ); ?></h2>
	<?php endif; ?>
	<ul>
		<?php foreach ( $items as $item ) : ?>
			<?php $active = trailingslashit( $item->url ) === $here; ?>
			<li<?php echo $active ? ' class="is-active"' : ''; ?>>
				<a href="<?php echo esc_url( $item->url ); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $item->title ); ?>
					<?php if ( 'vertical' === $a['style'] ) : ?>
						<?php echo csgame_icon( 'arrow' ); // phpcs:ignore ?>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
