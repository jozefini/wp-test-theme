<?php
/**
 * Markup handles the dynamic rendering of HTML elements.
 * A unique key is used for each element, and uses the
 * WordPress hooks to manipulate the output.
 *
 * @author Jozef B.
 * @package Theme
 * @since 1.0.0
 */

namespace Theme\Markup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct file access.
}

/**
 * Debug flag to print Markup Id's in the frontend.
 *
 * Helps developers to find easily the markup ID
 * by inspecting with the Browser.
 *
 * The markup ID of each element will be added with an
 * attribute name `data-markup-id`.
 *
 * e.g. `<article class="entry" data-markup-id="theme_post">...</article>`.
 */
if ( ! defined( 'THEME_DEBUG_MARKUP' ) ) {
	define( 'THEME_DEBUG_MARKUP', false );
}

/**
 * Prints out opening HTML element
 *
 * The Markup ID never get printed out, is used internally
 * to prefix hooks, so it can be used to manipulate or extend the
 * output using built-in filters and actions.
 *
 * Usage:
 * open_markup( 'theme_post_title', 'h1', array( 'class' => 'entry-title' ) );
 * Outputs:
 * <h1 class="entry-title">
 *
 * @since 1.0.0
 *
 * @param string $markup_id  An unique markup ID.
 * @param string $tag        HTML Tag name.
 * @param array  $attributes Optional. HTML Attributes, written in PHP array ['class' => 'btn is-red'].
 * @param mixed  ...$args    Optional. Additional arguments passed to the before and prepend actions.
 */
function open_markup( string $markup_id, string $tag, array $attributes = array(), ...$args ): void {
	echo get_open_markup( $markup_id, $tag, $attributes, ...$args ); // phpcs:ignore
}

/**
 * Returns opening HTML element
 *
 * The Markup ID never get returned, is used internally
 * to prefix hooks, so it can be used to manipulate or extend the
 * output using built-in filters and actions.
 *
 * Example:
 * get_open_markup( 'theme_post_title', 'h1', array( 'class' => 'entry-title' ) );
 * Returns:
 * <h1 class="entry-title">
 *
 * @since 1.0.0
 *
 * @param string $markup_id  An unique markup ID.
 * @param string $tag        HTML Tag name.
 * @param array  $attributes Optional. HTML Attributes, written in PHP array ['class' => 'btn is-red'].
 * @param mixed  ...$args    Optional. Additional arguments passed to the before and prepend actions.
 *
 * @return string
 */
function get_open_markup( string $markup_id, string $tag, array $attributes = array(), ...$args ): string {
	global $_theme_opening_markup_tags;

	/**
	 * Filters the HTML tag name
	 *
	 * For markup ID: `theme_post_title`
	 * The applied filter is `theme_post_title_markup_tag`
	 *
	 * @param string $tag The HTML tag name.
	 */
	$tag = apply_filters( THEME_SLUG . "{$markup_id}_markup", $tag );
	if ( ! $tag ) {
		return ''; // If filtered tag is falsy, no markup is returned.
	}

	ob_start();
	/**
	 * Buffers the action so it will be added before
	 * the HTML element is open.
	 */
	do_action( THEME_SLUG . "{$markup_id}_before_markup", ...$args );
	$before_hook = ob_get_clean();

	/**
	 * Temporary store the opening tag.
	 *
	 * When closing the markup, only the ID will be used
	 * to get the opening tag.
	 */
	$_theme_opening_markup_tags[ $markup_id ] = $tag;

	/**
	 * 1. Applies a filter to attributes (array) prefixed with current markup ID.
	 * 2. Auto-escapes each filtered attribute, name + value on their respective
	 *    escaping methods. E.g. `esc_attr`, `esc_url` etc.
	 * 3. Returns valid inline HTML attributes (from initally array).
	 */
	$esc_attributes = get_attributes( $markup_id, $attributes );

	ob_start();
	/**
	 * Buffers the action so it will be added after
	 * the HTML element is open.
	 */
	do_action( THEME_SLUG . "{$markup_id}_prepend_markup", ...$args );
	$prepended_hook = ob_get_clean();

	/**
	 * Since the tag also can be modified with filters,
	 * the new tag should never be trusted.
	 */
	$esc_tag = esc_attr( $tag );

	return "{$before_hook}<{$esc_tag}{$esc_attributes}>{$prepended_hook}";
}

/**
 * Prints out closing HTML element
 *
 * The opening Markup ID is used to get the opening HTML tag,
 * if there is no opening tags in the global variable,
 * nothing will be printed.
 *
 * Example:
 * close_markup( 'theme_post_title' );
 * Output if markup `theme_post_title` was opened early:
 * </h1>
 *
 * @since 1.0.0
 *
 * @param string $markup_id      Same markup ID used when opening the markup.
 * @param mixed  ...$args Optional. Additional arguments passed to the append and after actions.
 */
function close_markup( string $markup_id, ...$args ): void {
	echo get_close_markup( $markup_id, ...$args ); // phpcs:ignore
}

/**
 * Returns the closing HTML element
 *
 * The opening Markup ID is used to get the opening HTML tag,
 * if there is no opening tags in the global variable,
 * nothing will be printed.
 *
 * Example:
 * close_markup( 'theme_post_title' );
 * Output if markup `theme_post_title` was opened early:
 * </h1>
 *
 * @since 1.0.0
 *
 * @param string $markup_id Same markup ID used when opening the markup.
 * @param mixed  ...$args   Optional. Additional arguments passed to the append and after actions.
 *
 * @return string
 */
function get_close_markup( string $markup_id, ...$args ): string {
	global $_theme_opening_markup_tags;

	/**
	 * Checks if markup ID was opened early using one of,
	 * `open_markup` or `get_open_markup`, if not
	 * then the closing tag is invalid and nothing
	 * gets returned.
	 */
	if ( ! isset( $_theme_opening_markup_tags[ $markup_id ] ) ) {
		return '';
	}

	/**
	 * Since the tag also can be modified with filters,
	 * the new tag should never be trusted.
	 */
	$esc_tag = esc_attr( $_theme_opening_markup_tags[ $markup_id ] );

	ob_start();
	/**
	 * Buffers the action so it will be added before
	 * the element is closed.
	 */
	do_action( THEME_SLUG . "{$markup_id}_append_markup", ...$args );
	$appended_hook = ob_get_clean();

	/**
	 * Since when opening the HTML tag is temporary stored,
	 * when closing the markup the tag should be removed.
	 * Also saving few bytes from PHP memory.
	 */
	unset( $_theme_opening_markup_tags[ $markup_id ] );

	ob_start();
	/**
	 * Buffers the action so it will be added after
	 * the element is closed.
	 */
	do_action( THEME_SLUG . "{$markup_id}_after_markup", ...$args );
	$after_hook = ob_get_clean();

	return "{$appended_hook}</{$esc_tag}>{$after_hook}";
}

/**
 * Prints out self-closing HTML element
 *
 * The Markup ID never get printed out, is used internally
 * to prefix hooks, so it can be used to manipulate or extend the
 * output using built-in filters and actions.
 *
 * Usage:
 * selfclose_markup( 'theme_post_image', 'img', array( 'class' => 'entry-thumbnail' ) );
 * Outputs:
 * <img class="entry-thumbnail" />
 *
 * @since 1.0.0
 *
 * @param string $markup_id  An unique markup ID.
 * @param string $tag        Self-closing HTML Tag name ('img', 'br', 'link', etc.).
 * @param array  $attributes Optional. HTML Attributes, written in PHP array ['class' => 'img is-rounded'].
 * @param mixed  ...$args    Optional. Additional arguments passed to the before and after actions.
 */
function selfclose_markup( string $markup_id, string $tag, array $attributes = array(), ...$args ): void {
	echo get_selfclose_markup( $markup_id, $tag, $attributes, ...$args ); // phpcs:ignore
}

/**
 * Returns self-closing HTML element
 *
 * The Markup ID never get returned, is used internally
 * to prefix hooks, so it can be used to manipulate or extend the
 * output using built-in filters and actions.
 *
 * Usage:
 * get_selfclose_markup( 'theme_post_image', 'img', array( 'class' => 'entry-thumbnail' ) );
 * Returns:
 * <img class="entry-thumbnail" />
 *
 * @since 1.0.0
 *
 * @param string $markup_id  An unique markup ID.
 * @param string $tag        Self-closing HTML Tag name ('img', 'br', 'link', etc.).
 * @param array  $attributes Optional. HTML Attributes, written in PHP array ['class' => 'img is-rounded'].
 * @param mixed  ...$args    Optional. Additional arguments passed to the before and after actions.
 *
 * @return string
 */
function get_selfclose_markup( string $markup_id, $tag, array $attributes = array(), ...$args ): string {
	/**
	 * Filters the HTML tag name
	 *
	 * For markup ID: `theme_post_thumbnail`
	 * The applied filter is `theme_post_thumbnail_markup_tag`
	 *
	 * @param string $tag The HTML tag name.
	 */
	$tag = apply_filters( THEME_SLUG . "{$markup_id}_markup", $tag );
	if ( ! $tag ) {
		return ''; // If filtered tag is falsy, no markup is returned.
	}

	ob_start();
	/**
	 * Buffers the action so it will be added before
	 * the self-closing HTML element.
	 */
	do_action( THEME_SLUG . "{$markup_id}_before_markup", ...$args );
	$before_hook = ob_get_clean();

	/**
	 * 1. Applies a filter to attributes (array) prefixed with current markup ID.
	 * 2. Auto-escapes each filtered attribute, name + value on their respective
	 *    escaping methods. E.g. `esc_attr`, `esc_url` etc.
	 * 3. Returns valid inline HTML attributes (from initally array).
	 */
	$esc_attributes = get_attributes( $markup_id, $attributes );

	ob_start();
	/**
	 * Buffers the action so it will be added after
	 * the self-closing HTML element.
	 */
	do_action( THEME_SLUG . "{$markup_id}_after_markup", ...$args );
	$after_hook = ob_get_clean();

	/**
	 * Since the tag also can be modified with filters,
	 * the new tag should never be trusted.
	 */
	$esc_tag = esc_attr( $tag );

	return "{$before_hook}<{$esc_tag}{$esc_attributes}/>{$after_hook}";
}

/**
 * Returns dynamic HTML attributes
 *
 * The Attributes ID never get returned, is used internally
 * to prefix the filter, so it can be used to manipulate
 * the returned attributes.
 *
 * @since 1.0.0
 *
 * @param string $markup_id  An unique markup ID.
 * @param array  $attributes Optional. HTML Attributes, written in PHP array ['class' => 'btn is-red'].
 * @param mixed  ...$args    Optional. Additional arguments passed to the filter.
 *
 * @return string
 */
function get_attributes( string $markup_id, array $attributes = array(), ...$args ): string {
	/**
	 * Filters the HTML tag name
	 *
	 * For markup ID: `theme_post_thumbnail`
	 * The applied filter is `theme_post_thumbnail_markup_tag`
	 *
	 * @param array $attributes HTML Attributes written in PHP array.
	 */
	$new_attributes = apply_filters( THEME_SLUG . "{$markup_id}_markup_attributes", $attributes, ...$args );

	/**
	 * When development mode is activated,
	 * Adds the Attributes ID alongside other
	 * HTML attributes to be displayed in the frontend.
	 *
	 * Using the Inspector, is very easy to find the ID
	 * from the frontend so it can be used to manipulate
	 * that specific element.
	 */
	if ( THEME_DEBUG_MARKUP ) {
		$new_attributes['data-markup-id'] = $markup_id;
	}

	return esc_attributes( $new_attributes );
}

/**
 * Returns formatted HTML attributes from PHP array
 *
 * Every attribute name and value is auto-escaped
 * using their respective escaping methods
 * like `esc_attr`, `esc_url`, `esc_js`.
 *
 * Beware:
 * If none of attributes is valid, empty string is returned,
 * otherwise a space is intentionally added before the returned HTML attributes
 * like ` class="btn is-red" id="button"`,
 * as it should be concated with the element tag like:
 * `<button class="btn is-red" id="button">`
 *
 * @since 1.0.0
 *
 * @param array $attributes Optional. HTML Attributes, written in PHP array ['class' => 'img is-rounded'].
 *
 * @return string
 */
function esc_attributes( array $attributes = array() ): string {
	$esc_attributes = '';
	/**
	 * Map of escaping methods for some of known
	 * attribute names and their respective escaping
	 * functions.
	 *
	 * If attribute name is not listed, the `esc_attr` is used.
	 */
	$esc_callbacks = array(
		'href'    => 'esc_url',
		'src'     => 'esc_url',
		'action'  => 'esc_url',
		'onclick' => 'esc_js',
	);

	foreach ( $attributes as $name => $value ) {
		if ( is_null( $value ) ) {
			continue; // Skip attribute if value is NULL.
		}

		// Add and escape the attribute name.
		$esc_attributes .= ' ' . esc_attr( $name );

		if ( is_bool( $value ) ) {
			continue; // Skip attribute value if is a boolean (false|true)
		}

		$esc_callback = 'esc_attr'; // define a escape fallback.
		if ( isset( $esc_callbacks[ $name ] ) ) {
			// Apply a more specific escape method.
			$esc_callback = $esc_callbacks[ $name ];
		}

		// Add and escape the attribute value.
		$esc_attributes .= '="' . $esc_callback( $value ) . '"';
	}

	return $esc_attributes;
}

/**
 * Checks if markup ID is still open
 *
 * If this function is called in between opening
 * and closing markups (open_markup, close_markup),
 * it will return true, otherwise false.
 *
 * Used programatic rendering by detecting
 * the position in the DOM, e.g. if is
 * inside Header, Main, Sidebar, Footer or any
 * markup element created with this System.
 *
 * @since 1.0.0
 *
 * @param string $markup_id Markup ID to check if is opened.
 *
 * @return bool
 */
function is_markup_open( string $markup_id ): bool {
	global $_theme_opening_markup_tags;

	return isset( $_theme_opening_markup_tags[ $markup_id ] );
}

/**
 * Change markup's tag name
 *
 * DRY Helper function, uses the tag filter applied
 * in the opening or self-closing markup.
 * Automatically it does the extra work needed
 * instead of manually filtering
 *
 * If new tag is a falsy value, it will remove
 * the entire markup (opening/closing).
 *
 * @since 1.0.0
 *
 * @param string $markup_id Target Markup ID.
 * @param string $new_tag   New Tag name.
 * @param int    $priority  Optional. Filter priority.
 */
function change_markup( string $markup_id, string $new_tag, int $priority = 10 ): void {
	$anonymous_callback = function () use ( $new_tag ) {
		return $new_tag;
	};

	add_filter( THEME_SLUG . "{$markup_id}_markup", $anonymous_callback, $priority );
}

/**
 * Change markup's tag name with a callback
 *
 * Advanced version of `change_markup()`, this time
 * requires a callback so it gives the full
 * control of what tag should be returned.
 *
 * If new tag is a falsy value, it will remove
 * the entire markup (opening/closing).
 *
 * @since 1.0.0
 *
 * @param string   $markup_id       Target Markup ID.
 * @param callable $filter_callback Filter callback used to manipulate the tag name.
 * @param int      $priority        Optional. Filter priority.
 * @param int      $accepted_args   Optional. Number of accepted args passed to the filter.
 */
function change_markup_filter( string $markup_id, callable $filter_callback, int $priority = 10, $accepted_args = 1 ) {
	add_filter( THEME_SLUG . "{$markup_id}_markup", $filter_callback, $priority, $accepted_args );
}

/**
 * Removes a markup (opening & closing)
 *
 * By filtering the tag to a falsy value,
 * it will prevent it from rendering both
 * opening and closing tags.
 *
 * @since 1.0.0
 *
 * @param string $markup_id Target Markup ID.
 * @param int    $priority  Optional. Filter priority.
 */
function remove_markup( string $markup_id, int $priority = 10 ): void {
	add_filter( THEME_SLUG . "{$markup_id}_markup", '__return_null', $priority );
}

/**
 * Restores the original markup
 *
 * It removes any manipulations done with helpers
 * like `change_markup()` or `remove_markup()`.
 *
 * @since 1.0.0
 *
 * @param string $markup_id Target Markup ID.
 * @param int    $priority  Optional. Filter priority.
 */
function restore_markup( string $markup_id, int $priority = 10 ): void {
	remove_all_filters( THEME_SLUG . "{$markup_id}_markup", $priority );
}

/**
 * Add markup attributes
 *
 * No need to escape, added attributes are automatically
 * escaped when they get rendered.
 *
 * If an attribute exist on the markup, the value
 * of that attribute will be appended next to existing one
 * with a single space in between.
 * E.g. 'some-value another-value' . ' new-value'
 *
 * @since 1.0.0
 *
 * @param string $markup_id          Target Markup ID.
 * @param array  $attributes         HTML Attributes to add, written in PHP array ['class' => 'img is-rounded'].
 * @param string $overwrite_existing Optional. Decides if existing attributes should be overwritten.
 * @param int    $priority           Optional. Filter priority.
 */
function add_attributes( string $markup_id, array $attributes, bool $overwrite_existing = false, int $priority = 10 ): void {
	$anonymous_callback = function ( $current_attributes ) use ( $attributes, $overwrite_existing ) {
		foreach ( $attributes as $name => $value ) {
			if ( ! $overwrite_existing && isset( $current_attributes[ $name ] ) ) {
				$current_attributes[ $name ] .= ' ' . $value;
			} else {
				$current_attributes[ $name ] = $value;
			}
		}
		return $current_attributes;
	};

	add_filter( THEME_SLUG . "{$markup_id}_markup_attributes", $anonymous_callback, $priority );
}

/**
 * Add markup attributes filter
 *
 * Advanced version of `add_attributes()`, this time
 * requires a callback so it gives the full
 * control of what attributes should be returned.
 *
 * If new tag is a falsy value, it will remove
 * the entire markup (opening/closing).
 *
 * @since 1.0.0
 *
 * @param string   $markup_id       Target Markup ID.
 * @param callable $filter_callback Filter callback used to manipulate the tag name.
 * @param int      $priority        Optional. Filter priority.
 * @param int      $accepted_args   Optional. Number of accepted args passed to the filter.
 */
function add_attributes_filter( string $markup_id, callable $filter_callback, int $priority = 10, int $accepted_args = 1 ) {
	add_filter( THEME_SLUG . "{$markup_id}_markup_attributes", $filter_callback, $priority, $accepted_args );
}

/**
 * Remove markup attributes
 *
 * @since 1.0.0
 *
 * @param string $markup_id  Target Markup ID.
 * @param array  $attributes A list of attribute names to remove.
 * @param string $scope      Optional. Required parent Markup ID to check if element is part of.
 * @param int    $priority   Optional. Filter priority.
 */
function remove_attributes( string $markup_id, array $attributes, string $group = '', int $priority = 10 ): void {
	$anonymous_callback = function ( $current_attributes ) use ( $attributes, $group ) {
		if ( $group && ! $this->is_open( $group ) ) {
			return $current_attributes;
		}

		foreach ( $attributes as $name ) {
			unset( $current_attributes[ $name ] );
		}
		return $current_attributes;
	};

	add_filter( THEME_SLUG . "{$markup_id}_markup_attributes", $anonymous_callback, $priority );
}

/**
 * Restore the original markup attributes
 *
 * It removes any manipulations done with helpers
 * like `add_attributes()`, `set_attributes()`
 * or `remove_attributes()`.
 *
 * @since 1.0.0
 *
 * @param string $markup_id Target Markup ID.
 * @param int    $priority  Optional. Filter priority.
 */
function restore_attributes( string $markup_id, int $priority = 10 ): void {
	remove_all_filters( THEME_SLUG . "{$markup_id}_markup_attributes", $priority );
}

/**
 * Add action before a markup element
 *
 * Usage:
 * add_before_action( 'theme_post_content', 'the_title' );
 *
 * @since 1.0.0
 *
 * @param string   $markup_id     The target markup ID.
 * @param callable $callback      The callback to be called when markup action triggers.
 * @param int      $priority      Optional. Action priority.
 * @param int      $accepted_args Optional. Number of accepted args passed to the action.
 */
function add_before_action( string $markup_id, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
	add_action( THEME_SLUG . "{$markup_id}_before_markup", $callback, $priority, $accepted_args );
}

/**
 * Add action prepend a markup element
 *
 * Usage:
 * add_prepend_action( 'theme_post_content', 'the_title' );
 *
 * @since 1.0.0
 *
 * @param string   $markup_id     The target markup ID.
 * @param callable $callback      The callback to be called when markup action triggers.
 * @param int      $priority      Optional. Action priority.
 * @param int      $accepted_args Optional. Number of accepted args passed to the action.
 */
function add_prepend_action( string $markup_id, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
	add_action( THEME_SLUG . "{$markup_id}_prepend_markup", $callback, $priority, $accepted_args );
}

/**
 * Add action append a markup element
 *
 * Usage:
 * add_append_action( 'theme_post_content', 'the_title' );
 *
 * @since 1.0.0
 *
 * @param string   $markup_id     The target markup ID.
 * @param callable $callback      The callback to be called when markup action triggers.
 * @param int      $priority      Optional. Action priority.
 * @param int      $accepted_args Optional. Number of accepted args passed to the action.
 */
function add_append_action( string $markup_id, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
	add_action( THEME_SLUG . "{$markup_id}_append_markup", $callback, $priority, $accepted_args );
}

/**
 * Add action after a markup element
 *
 * Usage:
 * add_after_action( 'theme_post_content', 'the_title' );
 *
 * @since 1.0.0
 *
 * @param string   $markup_id     The target markup ID.
 * @param callable $callback      The callback to be called when markup action triggers.
 * @param int      $priority      Optional. Action priority.
 * @param int      $accepted_args Optional. Number of accepted args passed to the action.
 */
function add_after_action( string $markup_id, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
	add_action( THEME_SLUG . "{$markup_id}_after_markup", $callback, $priority, $accepted_args );
}

if ( ! isset( $GLOBALS['_theme_opening_markup_tags'] ) ) {
	/**
	 * Keeps track of opening tags
	 *
	 * When a markup is opened, the markup ID and the
	 * HTML tag name is stored.
	 * When markup is closed, both markup ID and the
	 * HTML tag name are removed.
	 */
	$GLOBALS['_theme_opening_markup_tags'] = array();
}
