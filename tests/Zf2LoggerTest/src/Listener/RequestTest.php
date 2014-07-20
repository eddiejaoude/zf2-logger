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

    /**
     * @var \Zend\Log\Writer\Mock
     */
    private $writer;

    public function  setUp()
    {
        $this->writer = new \Zend\Log\Writer\Mock;

        $this->logger = new \Zend\Log\Logger;
        $this->logger->addWriter($this->writer);

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

        $listeners = $this->instance->getListeners();
        $this->assertInstanceOf(
            'Zend\Stdlib\CallbackHandler',
            $listeners[0]
        );

        $this->assertTrue($this->instance->removeListener(0));
        $this->assertEquals(0, count($this->instance->getListeners()));
    }

    public function testAttachDettach()
    {
        $eventManager = \Mockery::mock('Zend\EventManager\EventManager')->shouldDeferMissing();
        $this->instance->attach($eventManager);

        $this->assertEquals(1, count($this->instance->getListeners()));

        $this->instance->detach($eventManager);

        $this->assertEquals(0, count($this->instance->getListeners()));
    }

    public function testLogRequest()
    {
        $this->instance->setLog($this->logger);

        $request = \Mockery::mock('Zend\Http\PhpEnvironment\Request');
        $request->shouldReceive('getUri')
            ->andReturn(\Mockery::self());
        $request->shouldReceive('getHost')
            ->andReturn('mock.host');

        $eventManager = \Mockery::mock('Zend\EventManager\Event')->shouldDeferMissing();
        $eventManager->shouldReceive('getRequest')
            ->andReturn($request);

        $this->instance->logRequest($eventManager);

        $this->assertTrue(is_int(strpos($this->writer->events[0]['message'], 'mock.host')));
    }
}
