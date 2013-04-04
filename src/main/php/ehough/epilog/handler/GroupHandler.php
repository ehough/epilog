<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Forwards records to multiple handlers
 *
 * @author Lenar Lõhmus <lenar@city.ee>
 */
class ehough_epilog_handler_GroupHandler extends ehough_epilog_handler_AbstractHandler
{
    protected $handlers;

    /**
     * @param array   $handlers Array of Handlers.
     * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(array $handlers, $bubble = true)
    {
        foreach ($handlers as $handler) {
            if (!$handler instanceof ehough_epilog_handler_HandlerInterface) {
                throw new InvalidArgumentException('The first argument of the GroupHandler must be an array of ehough_epilog_handler_HandlerInterface instances.');
            }
        }

        $this->handlers = $handlers;
        $this->bubble = $bubble;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->isHandling($record)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if ($this->processors) {
            foreach ($this->processors as $processor) {

                if (is_callable($processor)) {

                    $callback = $processor;

                } else {

                    $callback = array($processor, '__invoke');
                }

                $record = call_user_func($callback, $record);
            }
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($record);
        }

        return false === $this->bubble;
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        foreach ($this->handlers as $handler) {
            $handler->handleBatch($records);
        }
    }
}
