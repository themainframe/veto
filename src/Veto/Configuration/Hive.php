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
namespace Veto\Configuration;

use Veto\Collection\Tree;
use Veto\Exception\ConfigurationException;

/**
 * Hive
 * Implements a configuration hive that can handle loading from JSON config files.
 *
 * @since 0.1
 */
class Hive extends Tree
{
    /**
     * Load configuration data from a JSON file.
     *
     * @throws ConfigurationException
     * @param string $path The path to the JSON configuration file.
     */
    public function loadJson($path)
    {
        $configJSON = file_get_contents($path);
        $config = json_decode($configJSON, true);

        // Process any @import directives
        if (array_key_exists('@import', $config)) {

            if (!is_array($config['@import'])) {
                throw new ConfigurationException(
                    '@import directives must contain an array of JSON file paths.'
                );
            }

            foreach($config['@import'] as $importPath) {
                $importPath = dirname($path) . '/' . $importPath;

                if (!file_exists($importPath)) {
                    throw ConfigurationException::missingImportedFile(
                        $importPath,
                        $path
                    );
                }

                $this->loadJson($importPath);
            }

            // Do not keep these in the configuration hive
            unset($config['@import']);
        }

        // Merge the configuration hive with this file
        $this->merge($config);
    }
}