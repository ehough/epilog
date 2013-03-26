<?php

//namespace Psr\Log\Test;

//use Psr\Log\LogLevel;

/**
 * Provides a base test class for ensuring compliance with the LoggerInterface
 *
 * Implementors can extend the class and implement abstract methods to run this as part of their test suite
 */
abstract class ehough_epilog_psr_test_LoggerInterfaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return ehough_epilog_psr_LoggerInterface
     */
    abstract function getLogger();

    /**
     * This must return the log messages in order with a simple formatting: "<LOG LEVEL> <MESSAGE>"
     *
     * Example ->error('Foo') would yield "error Foo"
     *
     * @return string[]
     */
    abstract function getLogs();

    public function testImplements()
    {
        $this->assertInstanceOf('ehough_epilog_psr_LoggerInterface', $this->getLogger());
    }

    /**
     * @dataProvider provideLevelsAndMessages
     */
    public function testLogsAtAllLevels($level, $message)
    {
        $logger = $this->getLogger();
        $logger->{$level}($message, array('user' => 'Bob'));
        $logger->log($level, $message, array('user' => 'Bob'));

        $expected = array(
            $level.' message of level '.$level.' with context: Bob',
            $level.' message of level '.$level.' with context: Bob',
        );
        $this->assertEquals($expected, $this->getLogs());
    }

    public function provideLevelsAndMessages()
    {
        return array(
            ehough_epilog_psr_LogLevel::EMERGENCY => array(ehough_epilog_psr_LogLevel::EMERGENCY, 'message of level emergency with context: {user}'),
            ehough_epilog_psr_LogLevel::ALERT => array(ehough_epilog_psr_LogLevel::ALERT, 'message of level alert with context: {user}'),
            ehough_epilog_psr_LogLevel::CRITICAL => array(ehough_epilog_psr_LogLevel::CRITICAL, 'message of level critical with context: {user}'),
            ehough_epilog_psr_LogLevel::ERROR => array(ehough_epilog_psr_LogLevel::ERROR, 'message of level error with context: {user}'),
            ehough_epilog_psr_LogLevel::WARNING => array(ehough_epilog_psr_LogLevel::WARNING, 'message of level warning with context: {user}'),
            ehough_epilog_psr_LogLevel::NOTICE => array(ehough_epilog_psr_LogLevel::NOTICE, 'message of level notice with context: {user}'),
            ehough_epilog_psr_LogLevel::INFO => array(ehough_epilog_psr_LogLevel::INFO, 'message of level info with context: {user}'),
            ehough_epilog_psr_LogLevel::DEBUG => array(ehough_epilog_psr_LogLevel::DEBUG, 'message of level debug with context: {user}'),
        );
    }

    /**
     * @expectedException ehough_epilog_psr_InvalidArgumentException
     */
    public function testThrowsOnInvalidLevel()
    {
        $logger = $this->getLogger();
        $logger->log('invalid level', 'Foo');
    }

    public function testContextReplacement()
    {
        $logger = $this->getLogger();
        $logger->info('{Message {nothing} {user} {foo.bar} a}', array('user' => 'Bob', 'foo.bar' => 'Bar'));

        $expected = array('info {Message {nothing} Bob Bar a}');
        $this->assertEquals($expected, $this->getLogs());
    }

    public function testObjectCastToString()
    {
        $dummy = $this->getMock('ehough_epilog_psr_test_DummyTest', array('__toString'));
        $dummy->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('DUMMY'));

        $this->getLogger()->warning($dummy);
    }

    public function testContextCanContainAnything()
    {
        $context = array(
            'bool' => true,
            'null' => null,
            'string' => 'Foo',
            'int' => 0,
            'float' => 0.5,
            'nested' => array('with object' => new ehough_epilog_psr_test_DummyTest),
            'object' => new DateTime,
            'resource' => fopen('php://memory', 'r'),
        );

        $this->getLogger()->warning('Crazy context data', $context);
    }

    public function testContextExceptionKeyCanBeExceptionOrOtherValues()
    {
        $this->getLogger()->warning('Random message', array('exception' => 'oops'));
        $this->getLogger()->critical('Uncaught Exception!', array('exception' => new LogicException('Fail')));
    }
}

class ehough_epilog_psr_test_DummyTest
{
}