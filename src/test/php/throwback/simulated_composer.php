<?php
/*
 * This file is part of the epilog package.
 *
 * (c) Eric Hough <ehough.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

global $deps;

$deps = array(

    array('mlehner/gelf-php', 'git://github.com/mlehner/gelf-php.git', 'src'),
    array('raven/raven', 'git://github.com/getsentry/raven-php', 'lib'),
    array('doctrine/couchdb', 'git://github.com/doctrine/couchdb-client', 'lib'),
);