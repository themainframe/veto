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
namespace Veto\Layer;

use Veto\Configuration\Hive;
use Veto\DI\AbstractContainerAccessor;
use Veto\DI\Container;
use Veto\Exception\ConfigurationException;
use Veto\HTTP\Request;
use Veto\HTTP\Response;

/**
 * LayerChain
 *
 * Groups layers together to form a Request->Response flow
 */
class LayerChain extends AbstractContainerAccessor
{
    /**
     * An associative array of layers that are configured
     *
     * @var array
     */
    private $layers = array();

    /**
     * @param Hive $config
     * @param Container $container
     * @throws ConfigurationException
     */
    public function __construct(Hive $config, Container $container)
    {
        $this->container = $container;

        // Register layers from configuration
        if (isset($config['layers']) && is_array($config['layers'])) {
            foreach ($config['layers'] as $layerName => $layer) {

                if (!array_key_exists('service', $layer)) {
                    throw ConfigurationException::missingSubkey('layer', 'service');
                }

                $newLayer = $this->container->get($layer['service']);

                // TODO: Split inbound and outbound priority for bidirectional layers
                $priority = array_key_exists('priority', $layer) ? intval($layer['priority']) : 0;

                // TODO: Layers might not be container accessors... check
                $newLayer->setContainer($this->container);
                $this->layers[$priority][$layerName] = $newLayer;
            }

            // Sort layers by priority
            ksort($this->layers);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \RuntimeException
     */
    public function processLayers(Request $request)
    {
        $response = $this->processInboundLayers($request);

        // By the end of the inbound layer list, a response should have been obtained
        if (!$response instanceof Response) {
            throw new \RuntimeException(
                'At least one inbound layer must produce a Response instance. ' .
                'The final processed layer returned a "' . gettype($response) . '".'
            );
        }

        // The response should now be processed by the outbound layers
        return $this->processOutboundLayers($response);
    }

    /**
     * @param Request $request
     * @return Request
     */
    private function processInboundLayers(Request $request)
    {
        $result = $request;

        // Pass through layers inwards
        foreach ($this->layers as $priority => $layers) {
            foreach ($layers as $layerName => $layer) {

                if (!($layer instanceof InboundLayerInterface)) {
                    continue;
                }

                $result = $layer->in($result);

                // If the layer produces a response, no more inbound layers may execute
                if ($result instanceof Response) {
                    return $result;
                }

                if (!$result instanceof Request) {
                    throw new \RuntimeException(
                        'Each inbound layer of the chain must produce a Request or Response type. ' .
                        'The "' . $layerName . '" layer returned ' . gettype($request) . '.'
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @param Response $response
     * @return Response
     */
    private function processOutboundLayers(Response $response)
    {
        $result = $response;

        foreach ($this->layers as $priority => $layers) {
            foreach ($layers as $layerName => $layer) {

                if (!($layer instanceof OutboundLayerInterface)) {
                    continue;
                }

                $result = $layer->out($result);

                if (!$result instanceof Response) {
                    throw new \RuntimeException(
                        'Each outbound layer of the chain must produce a Response type. ' .
                        'The "' . $layer->getName() . '" layer returned ' . gettype($response) . '.'
                    );
                }
            }
        }

        return $result;
    }
}
