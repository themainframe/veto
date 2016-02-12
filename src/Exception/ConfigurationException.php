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
namespace Veto\Exception;

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
        return new self(
            sprintf('Key "%s" must be specified in the application configuration.', $expectedKey)
        );
    }

    public static function missingSubkey($parentKey, $expectedSubkey)
    {
        return new self(
            sprintf(
                'Key "%s" must contain a child "%s" in the application configuration.',
                $parentKey,
                $expectedSubkey
            )
        );
    }

    public static function missingImportedFile($file, $importedFrom)
    {
        return new self(
            sprintf('File %s (imported from %s) does not exist.', $file, $importedFrom)
        );
    }
}
