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
namespace Veto\Layer\Router\Exception;

/**
 * RouterException
 *
 * Represents a problem with the router layer.
 *
 * @since 0.1
 */
class RouterException extends \Exception
{
    public static function noRouteExists($method, $path)
    {
        return new self(
            sprintf('No route defined for %s %s', $method, $path), 404
        );
    }

    public static function nonExistentRoute($route)
    {
        return new self(
            sprintf('The route %s does not exist.', $route), 500
        );
    }
}
