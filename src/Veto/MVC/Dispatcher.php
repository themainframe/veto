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
namespace Veto\MVC;

use Veto\DI\AbstractContainerAccessor;
use Veto\DI\Container;
use Veto\HTTP\Request;

/**
 * Dispatches a Request to a controller, obtaining a Response.
 */
class Dispatcher extends AbstractContainerAccessor implements DispatcherInterface
{
    /**
     * Set the service container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dispatch a request to a controller action method.
     *
     * @throws \RuntimeException
     * @param Request $request
     * @return mixed
     */
    public function dispatch(Request $request)
    {
        // Get the controller
        $controllerSpec = $request->getParameter('_controller');

        if (!$controllerSpec) {
            throw new \RuntimeException('The request was not tagged by a router.', 500);
        }

        $controller =  $this->container->get($controllerSpec['class']);
        $controller->setContainer($this->container);

        if (!method_exists($controller, $controllerSpec['method'])) {
            throw new \RuntimeException(
                'The controller action "' . $controllerSpec['method'] .
                '" does not exist for controller "' .
                $controllerSpec['class'] . '".'
            );
        }

        // Prepare to run the action method
        $actionMethod = new \ReflectionMethod($controller, $controllerSpec['method']);
        $parameters = $actionMethod->getParameters();
        $passedArgs = array();

        foreach ($parameters as $parameter) {

            $hintedClass = $parameter->getClass();
            $parameterName = $parameter->getName();

            if ($hintedClass) {
                $hintedClass = $hintedClass->getName();
            }

            // Special case - should the Request object be passed here?
            if ($parameterName == 'request' && $hintedClass == 'Veto\HTTP\Request') {
                $passedArgs[] = $request;
            }

            // Should a request parameter be passed here?
            if ($request->hasParameter($parameterName)) {
                $passedArgs[] = $request->getParameter($parameterName);
            }
        }

        $response = $actionMethod->invokeArgs($controller, $passedArgs);

        return $response;
    }
}
