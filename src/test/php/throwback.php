<?php

__throwback::$config = array(

    'name'         => 'ehough_epilog',
    'autoload'     => dirname(__FILE__) . '/../../main/php',
    'dependencies' => array(

        array('mlehner/gelf-php', 'git://github.com/mlehner/gelf-php.git', 'src'),
        array('raven/raven', 'git://github.com/getsentry/raven-php', 'lib'),
        array('doctrine/couchdb', 'git://github.com/doctrine/couchdb-client', 'lib'),
    )
);