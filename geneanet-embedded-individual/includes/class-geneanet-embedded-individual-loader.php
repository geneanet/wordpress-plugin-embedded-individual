<?php
/**
 * Registers all actions, filters and shortcodes for the plugin
 *
 * @link http://www.geneanet.org
 * @since 1.0.0
 *
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/includes
 */

/**
 * Registers all actions, filters and shortcodes for the plugin.
 *
 * Maintains a list of all hooks that are registered throughout
 * the plugin, and registers them with the WordPress API. Calls the
 * run function to execute the list of actions, filters and shortcodes.
 *
 * @package Geneanet_Embedded_Individual
 * @subpackage Geneanet_Embedded_Individual/includes
 * @author Alan Poulain <alan.poulain@geneanet.org>
 */
class Geneanet_Embedded_Individual_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array $actions The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array $filters The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * The array of shortcodes registered with WordPress.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var array shortcodes The shortcodes registered with WordPress to fire when the plugin loads.
	 */
	protected $shortcodes;

	/**
	 * Initializes the collections used to maintain the actions, filters and shortcodes.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();
		$this->shortcodes = array();

	}

	/**
	 * Adds a new action to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook          The name of the WordPress action that is being registered.
	 * @param object $component     A reference to the instance of the object on which the action is defined.
	 * @param string $callback      The name of the function definition on the $component.
	 * @param int    $priority      Optional. he priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Adds a new filter to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook          The name of the WordPress filter that is being registered.
	 * @param object $component     A reference to the instance of the object on which the filter is defined.
	 * @param string $callback      The name of the function definition on the $component.
	 * @param int    $priority      Optional. he priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Adds a new shortcode to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook      The name of the WordPress shortcode that is being registered.
	 * @param object $component A reference to the instance of the object on which the shortcode is defined.
	 * @param string $callback  The name of the function definition on the $component.
	 */
	public function add_shortcode( $hook, $component, $callback ) {
		$this->shortcodes = $this->add( $this->shortcodes, $hook, $component, $callback, 10, 1 );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array  $hooks         The collection of hooks that is being registered (that is, actions, filters or shortcodes).
	 * @param string $hook          The name of the WordPress filter that is being registered.
	 * @param object $component     A reference to the instance of the object on which the filter is defined.
	 * @param string $callback      The name of the function definition on the $component.
	 * @param int    $priority      The priority at which the function should be fired.
	 * @param int    $accepted_args The number of arguments that should be passed to the $callback.
	 * @return array The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;

	}

	/**
	 * Registers the filters, actions and shortcodes with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->shortcodes as $hook ) {
			add_shortcode( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
		}

	}
}
