<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function _callbackFirePhpHandlerAutoload($class)
{
    $file = dirname(__FILE__).'/../../../../src/'.strtr($class, '\\', '/').'.php';
    if (file_exists($file)) {
        require $file;

        return true;
    }
}

spl_autoload_register('_callbackFirePhpHandlerAutoload');

$logger = new ehough_epilog_Logger('firephp');
$logger->pushHandler(new ehough_epilog_handler_FirePHPHandler);
$logger->pushHandler(new ehough_epilog_handler_ChromePHPHandler());

$logger->addDebug('Debug');
$logger->addInfo('Info');
$logger->addWarning('Warning');
$logger->addError('Error');
