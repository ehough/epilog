<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MockRavenClient extends Raven_Client
{
    public function capture($data, $stack, $vars = null)
    {
        $this->lastData = $data;
        $this->lastStack = $stack;
    }

    public $lastData;
    public $lastStack;
}
