<?php
/**
 * Extends default Walker_Nav_Menu to use the
 * the new Markup System, this enables
 *
 * @package Theme
 */

namespace Theme\Classes;

use function Theme\Markup\{ get_open_markup, get_close_markup };

/**
 * Walker Handler.
 */
class Custom_Nav_Menu extends \Walker_Nav_Menu {
	/**
	 * ARIA dropdown reference.
	 *
	 * @var null|string
	 */
	private $aria_labelledby = null;

	/**
	 * Start level.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output HTML Output.
	 * @param int    $depth  Depth level.
	 * @param array  $args   Arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= get_open_markup(
			'nav_menu',
			'ul',
			array(
				'class'           => 'menu sub-menu',
				'aria-labelledby' => $this->aria_labelledby,
			),
			$depth
		); // <ul ...> .
	}

	/**
	 * End level.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output HTML Output.
	 * @param int    $depth  Depth level.
	 * @param array  $args   Arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= get_close_markup( 'nav_menu', $depth ); // </ul> .
	}

	/**
	 * Start element.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output HTML Output.
	 * @param object $item   Menu item.
	 * @param int    $depth  Depth level.
	 * @param array  $args   Arguments.
	 * @param int    $id     Item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$instance_counter  = 1;

		$classes         = empty( $item->classes ) ? array() : array_filter( (array) $item->classes );
		$item_attributes = array( 'class' => 'menu__item' );
		$link_attributes = array(
			'class' => 'menu__link',
			'id'    => "link-{$item->ID}-{$instance_counter}{$depth}",
		);

		if ( ! empty( $item->classes ) ) {
			$item_attributes['class'] .= ' ' . implode( ' ', array_filter( (array) $item->classes ) );
		}

		if ( $this->has_children ) {
			$this->aria_labelledby = "link-{$item->ID}-{$instance_counter}{$depth}";

			$item_attributes['class'] .= ' menu__item--dropdown';
		}

		if ( $item->current || $item->current_item_ancestor || $item->current_item_parent ) {
			$item_attributes['class'] .= ' menu__item--active';
		}

		if ( ! empty( $item->url ) ) {
			$link_attributes['href'] = esc_url( $item->url );
		}

		if ( ! empty( $item->xfn ) ) {
			$link_attributes['rel'] = esc_attr( $item->xfn );
		} elseif ( '_blank' === $item->target ) {
			$link_attributes['rel'] = 'noopener noreferrer';
		}

		if ( ! empty( $item->attr_title ) ) {
			$link_attributes['title'] = esc_attr( $item->attr_title );
		}

		if ( ! empty( $item->target ) ) {
			$link_attributes['target'] = esc_attr( $item->target );
		}

		if ( $item->current ) {
			$link_attributes['aria-current'] = 'page';
		}

		// Output.

		$output .= get_open_markup( 'nav_menu_item', 'li', $item_attributes ); // <li ...> .
		$output .= get_open_markup( 'nav_menu_link', 'a', $link_attributes, $item, $this->has_children, $depth ); // <a ...> .
		$output .= esc_html( $item->title );
		$output .= get_close_markup( 'nav_menu_link', $item, $this->has_children, $depth ); // </a> .
	}

	/**
	 * End element.
	 *
	 * @since 1.0.0
	 *
	 * @param string $output HTML Output.
	 * @param object $item   Menu item.
	 * @param int    $depth  Depth level.
	 * @param array  $args   Arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= get_close_markup( 'nav_menu_item' ); // </li> .
	}
}
