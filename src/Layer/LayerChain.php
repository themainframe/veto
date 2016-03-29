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

use Veto\DependencyInjection\AbstractContainerAccessor;
use Veto\Http\Request;
use Veto\Http\Response;

/**
 * LayerChain
 *
 * Groups layers together to form a Request->Response flow
 */
class LayerChain extends AbstractContainerAccessor
{
    /**
     * An associative, 2D array of layers that are configured.
     * Layers are stored here keyed by priority then name.
     *
     * @var array
     */
    private $layers = array();

    /**
     * Add a layer to this LayerChain.
     *
     * @param object $layer
     * @param int $priority
     */
    public function addLayer($layer, $priority = 0)
    {
        // Enforce the type of $layer
        if (!($layer instanceof InboundLayerInterface || $layer instanceof OutboundLayerInterface)) {
            throw new \InvalidArgumentException(
                'Argument 1 of '  . __CLASS__ . '::' . __METHOD__ .
                ' must be either an InboundLayerInterface or an OutboundLayerInterface instance.'
            );
        }

        $this->layers[$priority][] = $layer;

        // TODO: Improve the efficiency of this by _not_ sorting after every insertion
        ksort($this->layers);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \RuntimeException
     */
    public function processLayers(Request $request)
    {
        // There must be at least one inbound layer, otherwise our Request will never become a Response
        if (0 === count($this->layers)) {
            throw new \RuntimeException(
                'At least one layer must be defined in order to handle requests.'
            );
        }

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
                        'The "' . $layerName . '" layer returned ' . gettype($response) . '.'
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Get the layers currently added to this LayerChain, keyed by priority group.
     *
     * @return array[]
     */
    public function getLayers()
    {
        return $this->layers;
    }
}
