<?php
/**
 * Veto.
 * PHP Microframework.
 *
 * @author Damien Walsh <me@damow.net>
 * @copyright Damien Walsh 2013-2014
 * @version 0.1
 * @package veto
 */
namespace Veto;

/**
 * The kernel class.
 * @since 0.1
 */
class Veto
{
	/**
	 * Registered routes.
	 * @var array
	 */
	private $routes = array();

	/**
	 * Map a GET request route to a callable function.
	 * @return Route
	 */
	function get()
	{
		return $this;
	}

	/**
	 * Register a route.
	 * @return Route
	 */
	function register()
	{
		$route = new Route();
		$this->routes[] = $route;

		return $route;
	}
}