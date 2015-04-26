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

/*
 * We will try to load an autoloader from different paths for two different cases:
 *
 *  - Veto may be installed _not_ as a dependency of another project.
 *  - Veto may be installed in vendor/ as a dependency of another project.
 */

$autoloadersToTry = array(
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php'
);

foreach ($autoloadersToTry as $autoloader) {
    if (file_exists($autoloader)) {
        $loader = require $autoloader;
        break;
    }
}

if (!$loader) {
    print 'Unable to find a suitable autoload.php' . PHP_EOL;
}

$loader->add('Veto\\', __DIR__);
