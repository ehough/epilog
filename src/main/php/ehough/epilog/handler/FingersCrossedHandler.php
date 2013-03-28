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
 * Buffers all records until a certain level is reached
 *
 * The advantage of this approach is that you don't get any clutter in your log files.
 * Only requests which actually trigger an error (or whatever your actionLevel is) will be
 * in the logs, but they will contain all records, not only those above the level threshold.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ehough_epilog_handler_FingersCrossedHandler extends ehough_epilog_handler_AbstractHandler
{
    protected $handler;
    protected $activationStrategy;
    protected $buffering = true;
    protected $bufferSize;
    protected $buffer = array();
    protected $stopBuffering;

    /**
     * @param callable|ehough_epilog_handler_HandlerInterface       $handler            Handler or factory callable($record, $fingersCrossedHandler).
     * @param int|ehough_epilog_handler_fingerscrossed_ActivationStrategyInterface $activationStrategy Strategy which determines when this handler takes action
     * @param int                             $bufferSize         How many entries should be buffered at most, beyond that the oldest items are removed from the buffer.
     * @param Boolean                         $bubble             Whether the messages that are handled can bubble up the stack or not
     * @param Boolean                         $stopBuffering      Whether the handler should stop buffering after being triggered (default true)
     */
    public function __construct($handler, $activationStrategy = null, $bufferSize = 0, $bubble = true, $stopBuffering = true)
    {
        if (null === $activationStrategy) {
            $activationStrategy = new ehough_epilog_handler_fingerscrossed_ErrorLevelActivationStrategy(ehough_epilog_Logger::WARNING);
        }
        if (!$activationStrategy instanceof ehough_epilog_handler_fingerscrossed_ActivationStrategyInterface) {
            $activationStrategy = new ehough_epilog_handler_fingerscrossed_ErrorLevelActivationStrategy($activationStrategy);
        }

        $this->handler = $handler;
        $this->activationStrategy = $activationStrategy;
        $this->bufferSize = $bufferSize;
        $this->bubble = $bubble;
        $this->stopBuffering = $stopBuffering;
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if ($this->processors) {
            foreach ($this->processors as $processor) {
                $record = call_user_func($processor, $record);
            }
        }

        if ($this->buffering) {
            $this->buffer[] = $record;
            if ($this->bufferSize > 0 && count($this->buffer) > $this->bufferSize) {
                array_shift($this->buffer);
            }
            if ($this->activationStrategy->isHandlerActivated($record)) {
                if ($this->stopBuffering) {
                    $this->buffering = false;
                }
                if (!$this->handler instanceof ehough_epilog_handler_HandlerInterface) {
                    if (!is_callable($this->handler)) {
                        throw new RuntimeException("The given handler (".json_encode($this->handler).") is not a callable nor a ehough_epilog_handler_HandlerInterface object");
                    }
                    $this->handler = call_user_func($this->handler, $record, $this);
                    if (!$this->handler instanceof ehough_epilog_handler_HandlerInterface) {
                        throw new RuntimeException("The factory callable should return a ehough_epilog_handler_HandlerInterface");
                    }
                }
                $this->handler->handleBatch($this->buffer);
                $this->buffer = array();
            }
        } else {
            $this->handler->handle($record);
        }

        return false === $this->bubble;
    }

    /**
     * Resets the state of the handler. Stops forwarding records to the wrapped handler.
     */
    public function reset()
    {
        $this->buffering = true;
    }
}
