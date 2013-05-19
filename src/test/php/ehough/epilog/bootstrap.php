<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!class_exists('ehough_pulsar_UniversalClassLoader')) {

    require dirname(__FILE__) . '/../../../../../vendor/ehough/pulsar/src/main/php/ehough/pulsar/UniversalClassLoader.php';
}
$loader = new ehough_pulsar_UniversalClassLoader(dirname(__FILE__) . '/../../../../../vendor');
$loader->registerPrefixFallback(dirname(__FILE__) . '/../../');
$loader->register();