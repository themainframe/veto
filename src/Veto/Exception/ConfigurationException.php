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

use Veto\App;
use Veto\Collection\Bag;
use Veto\HTTP\Request;
use Veto\Layer\AbstractLayer;
use Veto\Layer\LayerInterface;

/**
 * ConfigurationException
 *
 * Represents a problem with the configuration loaded by the application.
 *
 * @since 0.1
 */
class ConfigurationException extends \Exception
{
    public static function missingKey($expectedKey)
    {
        return new self(sprintf('Key %s must be specified in the application configuration.'));
    }
}
