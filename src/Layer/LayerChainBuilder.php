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
use Veto\DependencyInjection\Container;
use Veto\Configuration\Exception\ConfigurationException;

/**
 * LayerChain Builder
 *
 * Builds a LayerChain from configuration.
 */
class LayerChainBuilder
{
    /**
     * Construct a LayerChain from application configuration.
     *
     * @param Hive $config
     * @param Container $container
     * @throws ConfigurationException
     * @return LayerChain
     */
    public static function initWithConfigurationAndContainer(Hive $config, Container $container)
    {
        $layerChain = new LayerChain;

        // Register layers from configuration
        if (isset($config['layers']) && is_array($config['layers'])) {
            foreach ($config['layers'] as $layerName => $layer) {

                if (!array_key_exists('service', $layer)) {
                    throw ConfigurationException::missingSubkey('layer', 'service');
                }

                $newLayer = $container->get($layer['service']);
                $priority = array_key_exists('priority', $layer) ? intval($layer['priority']) : 0;
                $layerChain->addLayer($newLayer, $priority);
            }
        }

        return $layerChain;
    }
}
