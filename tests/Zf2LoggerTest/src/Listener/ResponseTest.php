<?php
namespace EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest\Listener;

use EddieJaoude\Zf2Logger\Listener\Response;


/**
 * Class ResponseTest
 *
 * @package EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest\Listener
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \EddieJaoude\Zf2Logger\Listener\Response
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

        $this->instance = new Response($this->logger);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('EddieJaoude\Zf2Logger\Listener\Response', $this->instance);
    }

    public function testConstruct()
    {
        $request = new Response();

        $this->assertNull($request->getLog());
    }

    public function testLogSetterGetter()
    {
        $request = new Response();

        $request->setLog($this->logger);

        $this->assertNotNull($request->getLog());
        $this->assertInstanceOf('Zend\Log\Logger', $request->getLog());
    }

    public function testListenerAddGetterRemove()
    {
        $this->assertEquals(array(), $this->instance->getListeners());

        $this->assertInstanceOf(
            'EddieJaoude\Zf2Logger\Listener\Response',
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

    public function testAttachDettach()
    {
        $eventManager = \Mockery::mock('Zend\EventManager\EventManager')->shouldDeferMissing();
        $this->instance->attach($eventManager);

        $this->assertEquals(2, count($this->instance->getListeners()));

        $this->instance->detach($eventManager);

        $this->assertEquals(0, count($this->instance->getListeners()));
    }

    public function testLogResponse()
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

        $eventManager->shouldReceive('getResponse')
            ->andReturn(\Mockery::self());
        $eventManager->shouldReceive('getStatusCode')
            ->andReturn('200');
        $eventManager->shouldReceive('getContent')
            ->andReturn(
                json_encode(
                    array(
                        'user' => array(
                            'id' => 123,
                            'name' => 'Test me',
                        )
                    )
                )
            );

        $this->instance->logResponse($eventManager);

        $this->assertTrue(is_int(strpos($this->writer->events[0]['message'], 'mock.host')));
        $this->assertTrue(is_int(strpos($this->writer->events[0]['message'], '200')));
        $this->assertTrue(is_int(strpos($this->writer->events[0]['message'], '123')));
        $this->assertTrue(is_int(strpos($this->writer->events[0]['message'], 'Test me')));
    }
}
