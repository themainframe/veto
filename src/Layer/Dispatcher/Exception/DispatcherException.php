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
namespace Veto\Layer\Dispatcher\Exception;

/**
 * DispatcherException
 *
 * Represents a problem with the dispatcher layer.
 *
 * @since 0.4
 */
class DispatcherException extends \RuntimeException
{
    public static function notTagged($method, $path)
    {
        return new self(
            sprintf('The request was not tagged by a router: %s %s', $method, $path), 500
        );
    }

    public static function controllerActionDoesNotExist($method, $controller)
    {
        return new self(
            sprintf('The controller action %s does not exist for controller %s', $method, $controller), 500
        );
    }

    public static function controllerActionDidNotReturnResponse($method, $controller, $actualType)
    {
        return new self(
            sprintf(
                'The controller action method %s for controller %s must return an instance of Veto\\Http\\Response. ' .
                'A %s was returned instead.',
                $method,
                $controller,
                $actualType
            ), 500
        );
    }
}
