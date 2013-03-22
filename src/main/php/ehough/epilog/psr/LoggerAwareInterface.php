<?php

/**
 * Describes a logger-aware instance
 */
interface ehough_epilog_psr_LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object
     *
     * @param ehough_epilog_psr_LoggerInterface $logger
     * @return null
     */
    public function setLogger(ehough_epilog_psr_LoggerInterface $logger);
}
