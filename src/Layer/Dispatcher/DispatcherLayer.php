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
namespace Veto\Layer\Dispatcher;

use Veto\DependencyInjection\AbstractContainerAccessor;
use Veto\DependencyInjection\Container;
use Veto\Http\Request;
use Veto\Http\Response;
use Veto\Layer\Dispatcher\Exception\DispatcherException;
use Veto\Layer\InboundLayerInterface;

/**
 * DispatcherLayer
 *
 * Dispatches a Request to a controller, obtaining a Response.
 */
class DispatcherLayer extends AbstractContainerAccessor implements InboundLayerInterface
{
    /**
     * Set the service container.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws DispatcherException
     */
    public function in(Request $request)
    {
        // Get the controller
        $controllerSpec = $request->getParameter('_controller');

        if (!$controllerSpec) {
            throw DispatcherException::notTagged(
                $request->getMethod(),
                $request->getUri() ? $request->getUri()->getPath() : ''
            );
        }

        $controller = $this->container->get($controllerSpec['class']);

        if (!method_exists($controller, $controllerSpec['method'])) {
            throw DispatcherException::controllerActionDoesNotExist(
                $controllerSpec['method'],
                $controllerSpec['class']
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

        // By the end of the inbound layer list, a response should have been obtained
        if (!$response instanceof Response) {
            throw DispatcherException::controllerActionDidNotReturnResponse(
                $controllerSpec['method'],
                $controllerSpec['class'],
                gettype($response)
            );
        }

        return $response;
    }
}
