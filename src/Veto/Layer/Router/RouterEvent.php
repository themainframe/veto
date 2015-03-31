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
namespace Veto\Layer\Router;

use Veto\Event\Event;

/**
 * RouterEvent
 *
 * An event dispatched by the Router layer.
 */
class RouterEvent extends Event
{
    /**
     * Event type used when the router successfully matches a route.
     */
    const ROUTE_MATCHED = 'router.matched';

    /**
     * The relevant route.
     *
     * @var Route
     */
    private $route;

    /**
     * The relevant RouterLayer instance.
     *
     * @var RouterLayer
     */
    private $routerLayer;

    /**
     * @param Route $route
     * @param RouterLayer $routerLayer
     */
    public function __construct(Route $route, RouterLayer $routerLayer)
    {
        $this->route = $route;
        $this->routerLayer = $routerLayer;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return RouterLayer
     */
    public function getRouterLayer()
    {
        return $this->routerLayer;
    }
}
