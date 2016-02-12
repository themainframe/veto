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

use Symfony\Component\Yaml\Parser;
use Veto\Collection\Tree;
use Veto\Exception\ConfigurationException;

/**
 * Hive
 * Implements a configuration hive that can handle loading from YAML config files.
 *
 * @since 0.1
 */
class Hive extends Tree
{
    /**
     * Load configuration data from a YAML file.
     *
     * @todo Decouple this from the filesystem - make it accept a YAML string or stream instead
     * @throws ConfigurationException
     * @param string $path The path to the YAML configuration file.
     */
    public function load($path)
    {
        $parser = new Parser();
        $configYAML = file_get_contents($path);
        $config = $parser->parse($configYAML);

        // Process any config import directives
        $config = $this->processImports($path, $config);

        // Merge the configuration hive with this file
        $this->merge($config);
    }

    /**
     * Process any @import directives that occur at the top-level of the configuration array.
     *
     * @param string $basePath The base path used for relative @import directives
     * @param array $config The configuration array being processed
     * @return array
     * @throws ConfigurationException
     */
    private function processImports($basePath, array $config)
    {
        // Process any @import directives
        if (array_key_exists('@import', $config)) {

            if (!is_array($config['@import'])) {
                throw new ConfigurationException(
                    '@import directives must contain an array of configuration file paths.'
                );
            }

            foreach($config['@import'] as $importPath) {
                $importPath = dirname($basePath) . '/' . $importPath;

                if (!file_exists($importPath)) {
                    throw ConfigurationException::missingImportedFile(
                        $importPath,
                        $basePath
                    );
                }

                $this->load($importPath);
            }

            // Do not keep these in the configuration hive
            unset($config['@import']);
        }

        return $config;
    }
}