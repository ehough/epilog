<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$loader = new ehough_pulsar_ComposerClassLoader(dirname(__FILE__) . '/../../../../../vendor');
$loader->registerPrefixFallback(dirname(__FILE__) . '/../../');
$loader->register();