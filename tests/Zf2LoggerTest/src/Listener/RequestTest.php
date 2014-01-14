<?php
namespace EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest\Listener;

use EddieJaoude\Zf2Logger\Listener\Request;


/**
 * Class RequestTest
 *
 * @package EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest\Listener
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \EddieJaoude\Zf2Logger\Listener\Request
     */
    private $instance;

    /**
     * @var \Zend\Log\Logger
     */
    private $logger;

    public function  setUp()
    {
        $writer = new \Zend\Log\Writer\Mock;

        $this->logger = new \Zend\Log\Logger;
        $this->logger->addWriter($writer);

        $this->instance = new Request($this->logger);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('EddieJaoude\Zf2Logger\Listener\Request', $this->instance);
    }

    public function testConstruct()
    {
        $request = new Request();

        $this->assertNull($request->getLog());
    }

    public function testLogSetterGetter()
    {
        $request = new Request();

        $request->setLog($this->logger);

        $this->assertNotNull($request->getLog());
        $this->assertInstanceOf('Zend\Log\Logger', $request->getLog());
    }

    public function testListenerAddGetterRemove()
    {
        $this->assertEquals(array(), $this->instance->getListeners());

        $this->assertInstanceOf(
            'EddieJaoude\Zf2Logger\Listener\Request',
            $this->instance->addListener(new \Zend\Stdlib\CallbackHandler(function(){}))
        );

        $this->assertEquals(1, count($this->instance->getListeners()));

        $this->assertInstanceOf(
            'Zend\Stdlib\CallbackHandler',
            $this->instance->getListeners()[0]
        );

        $this->assertTrue($this->instance->removeListener(0));
        $this->assertEquals(0, count($this->instance->getListeners()));
    }
}
