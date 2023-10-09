<?php

namespace WPDaddy\Builder;

use Walker_Nav_Menu as Wordpress_Walker;
use WP_Post;
use stdClass;

/**
 * Nav Menu API: Walker_Nav_Menu class
 *
 * @package    WordPress
 * @subpackage Nav_Menus
 * @since      4.6.0
 */

/**
 * Core class used to implement an HTML list of nav menu items.
 *
 * @since 3.0.0
 *
 * @see   Walker
 */
class Walker_Nav_Menu extends Wordpress_Walker {
	private $block_menu_item = false;
	private $block_menu_lvl = false;


	/**
	 * What the class handles.
	 *
	 * @since 3.0.0
	 * @var string
	 *
	 * @see   Walker::$tree_type
	 */
	public $tree_type = array('post_type', 'taxonomy', 'custom');

	/**
	 * Database fields to use.
	 *
	 * @since 3.0.0
	 * @todo  Decouple this.
	 * @var array
	 *
	 * @see   Walker::$db_fields
	 */
	public $db_fields = array(
		'parent' => 'menu_item_parent',
		'id'     => 'db_id',
	);

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 *
	 * @see   Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 */
	public function start_lvl(&$output, $depth = 0, $args = null) {
		if ($this->block_menu_item) return;

		if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat($t, $depth);

		// Default class.
		$classes = array(
			'sub-menu',
			'wpda-menu',
		);
		if (property_exists($args, 'mega_menu') && $args->mega_menu) {
			$classes[] = 'wpda-mega-menu';
		}

		/**
		 * Filters the CSS class(es) applied to a menu list element.
		 *
		 * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
		 * @param stdClass $args    An object of `wp_nav_menu()` arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 *
		 * @since 4.8.0
		 *
		 */
		$class_names = implode(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
		$class_names = $class_names ? ' class="'.esc_attr($class_names).'"' : '';

		$output .= "{$n}{$indent}<ul$class_names>{$n}";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 *
	 * @see   Walker::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 */
	public function end_lvl(&$output, $depth = 0, $args = null) {
		if ($this->block_menu_item) return;

		if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat($t, $depth);
		$output .= "$indent</ul>{$n}";
	}

	/**
	 * Starts the element output.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 *
	 * @see   Walker::start_el()
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 *
	 */
	public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
		if ($this->block_menu_item) return;

		if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ($depth) ? str_repeat($t, $depth) : '';
		$meta = Mega_Menu::get_menu_item_meta($item->ID);

		if (Mega_Menu::is_mega_menu_item($item->ID) && !$meta['originalOnMobile']) {
			$this->block_menu_item = $item->ID;
		}

		$classes   = empty($item->classes) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-'.$item->ID;
		$classes[] = 'wpda-menu-item-'.$item->ID;
		if (Mega_Menu::is_mega_menu_item($item->ID)) {
			$classes[] = 'mega-menu-wrapper';
		}
		if ($meta['originalOnMobile']) {
			$classes[] = 'original-mobile-menu';
		}
		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param WP_Post  $item  Menu item data object.
		 * @param int      $depth Depth of menu item. Used for padding.
		 *
		 * @since 4.4.0
		 *
		 */
		$args = apply_filters('nav_menu_item_args', $args, $item, $depth);

		/**
		 * Filters the CSS classes applied to a menu item's list item element.
		 *
		 * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 */
		$class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
		$class_names = $class_names ? ' class="'.esc_attr($class_names).'"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 */
		$id = apply_filters('nav_menu_item_id', 'menu-item-'.$item->ID, $item, $args, $depth);
		$id = $id ? ' id="'.esc_attr($id).'"' : '';

		$output .= $indent.'<li'.$id.$class_names.'>';

		$atts           = array();
		$atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
		$atts['target'] = !empty($item->target) ? $item->target : '';
		if ('_blank' === $item->target && empty($item->xfn)) {
			$atts['rel'] = 'noopener';
		} else {
			$atts['rel'] = $item->xfn;
		}
		$atts['href']         = !empty($item->url) ? $item->url : '';
		$atts['aria-current'] = $item->current ? 'page' : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @param array    $atts         {
		 *                               The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 * @type string    $title        Title attribute.
		 * @type string    $target       Target attribute.
		 * @type string    $rel          The rel attribute.
		 * @type string    $href         The href attribute.
		 * @type string    $aria_current The aria-current attribute.
		 * }
		 *
		 * @param WP_Post  $item         The current menu item.
		 * @param stdClass $args         An object of wp_nav_menu() arguments.
		 * @param int      $depth        Depth of menu item. Used for padding.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 */
		$atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

		$attributes = '';
		foreach ($atts as $attr => $value) {
			if (is_scalar($value) && '' !== $value && false !== $value) {
				$value      = ('href' === $attr) ? esc_url($value) : esc_attr($value);
				$attributes .= ' '.$attr.'="'.$value.'"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters('the_title', $item->title, $item->ID);

		/**
		 * Filters a menu item's title.
		 *
		 * @param string   $title The menu item's title.
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 *
		 * @since 4.4.0
		 *
		 */
		$title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

		$item_output = $args->before;
		$item_output .= '<a'.$attributes.'>';
		$item_output .= $args->link_before.$title.$args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $item        Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 *
		 * @since 3.0.0
		 *
		 */
		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Page data object. Not used.
	 * @param int      $depth  Depth of page. Not Used.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 *
	 * @since 3.0.0
	 *
	 * @see   Walker::end_el()
	 *
	 */
	public function end_el(&$output, $item, $depth = 0, $args = null) {
		$meta = Mega_Menu::get_menu_item_meta($item->ID);

		if ($this->block_menu_item && $this->block_menu_item !== $item->ID) {
			return;
		}

		if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}



		if ($this->block_menu_item && $this->block_menu_item === $item->ID) {
			$this->block_menu_item = false;
		}

		if (Mega_Menu::is_mega_menu_item($item->ID) && class_exists('Elementor\Plugin')) {
			$element = (int)$meta['library'];
			if ($element !== 0) {
				$builder_post = get_post($element);
				if ($builder_post instanceof WP_Post && $builder_post->post_status === 'publish') {
					$args->mega_menu = true;
					$elementor = \Elementor\Plugin::instance();
					$output    .= $elementor->frontend->get_builder_content_for_display($builder_post->ID);
				}
			}
		}
		$output .= "</li>{$n}";
	}
}
