<?php
namespace EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest\Factory;

use EddieJaoude\Zf2Logger\Factory\Zf2Logger;

/**
 * Class LoggerTest
 * @package EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest\Log
 */
class Zf2LoggerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \EddieJaoude\Zf2Logger\Factory\Zf2Logger
     */
    private $loggerFactory;

    public function setUp()
    {
        $this->loggerFactory = new Zf2Logger();
    }

    public function testWriters()
    {
        $serviceLocator = \Mockery::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->shouldReceive('get')
                       ->times(1)
                       ->with('Config')
                       ->andReturn(
                           array(
                               'EddieJaoude\Zf2Logger' => $config = array(
                                   // will add the $logger object before the current PHP error handler
                                   'registerErrorHandler'     => true, // errors logged to your writers
                                   'registerExceptionHandler' => true, // exceptions logged to your writers

                                   // multiple zend writer output & zend priority filters
                                   'writers' => array(
                                       'standard-error' => array(
                                           'adapter'  => '\Zend\Log\Writer\Stream',
                                           'options'  => array(
                                               'output' => 'php://stderr'
                                           ),
                                           // options: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG
                                           'filter' => \Zend\Log\Logger::DEBUG,
                                           'enabled' => true
                                       ),
                                       'standard-file' => array(
                                           'adapter'  => '\Zend\Log\Writer\Stream',
                                           'options'  => array(
                                               'output' => '/tmp/application.log'
                                           ),
                                           // options: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG
                                           'filter' => \Zend\Log\Logger::DEBUG,
                                           'enabled' => true
                                       )
                                   )
                               )
                           )
                       );

        $this->loggerFactory->createService($serviceLocator);

        $this->assertInstanceOf('Zend\Log\Logger', $this->loggerFactory->getLogger());
        $this->assertInstanceOf('Zend\Stdlib\SplPriorityQueue', $this->loggerFactory->getLogger()->getWriters());

        $this->assertEquals(2, $this->loggerFactory->getLogger()->getWriters()->count());

        foreach($this->loggerFactory->getLogger()->getWriters() as $writer) {
            $this->assertInstanceOf('Zend\Log\Writer\Stream', $writer);
        }
    }

    public function testNullWriter()
    {
        $serviceLocator = \Mockery::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->shouldReceive('get')
                       ->times(1)
                       ->with('Config')
                       ->andReturn(
                           array(
                               'EddieJaoude\Zf2Logger' => $config = array()
                           )
                       );

        $this->loggerFactory->createService($serviceLocator);

        $this->assertInstanceOf('Zend\Log\Logger', $this->loggerFactory->getLogger());
        $this->assertInstanceOf('Zend\Stdlib\SplPriorityQueue', $this->loggerFactory->getLogger()->getWriters());

        $this->assertEquals(1, $this->loggerFactory->getLogger()->getWriters()->count());

        foreach($this->loggerFactory->getLogger()->getWriters() as $writer) {
            $this->assertInstanceOf('Zend\Log\Writer\Null', $writer);
        }
    }
}
