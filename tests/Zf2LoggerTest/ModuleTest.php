<?php
namespace EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest;

use EddieJaoude\Zf2Logger\Module;
use Zend\Mvc\MvcEvent;

/**
 * Class ModuleTest
 *
 * @package EddieJaoude\Zf2Logger\Tests\Zf2LoggerTest
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \EddieJaoude\Zf2Logger\Module
     */
    private $instance;

    public function setUp()
    {
        $this->instance = new Module();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\EddieJaoude\Zf2Logger\Module', $this->instance);
    }

    public function testGetAutoloaderConfig()
    {
        $response = $this->instance->getAutoloaderConfig();

        $this->assertTrue(array_key_exists('EddieJaoude\Zf2Logger', $response['Zend\Loader\StandardAutoloader']['namespaces']));
    }

    public function testGetServiceConfig()
    {
        $response = $this->instance->getServiceConfig();

        $this->assertTrue(array_key_exists('factories', $response));
        $this->assertTrue(array_key_exists('EddieJaoude\Zf2Logger\Logger', $response['factories']));
        $this->assertTrue(is_callable($response['factories']['EddieJaoude\Zf2Logger\Logger']));
    }

    public function testOnBootstrap()
    {
        $mvcEvent = \Mockery::mock('Zend\Mvc\MvcEvent');
        $mvcEvent->shouldReceive('getApplication')
            ->andReturn(\Mockery::self());

        $mvcEvent->shouldReceive('getServiceManager')
            ->andReturn(\Mockery::self());

        $writer = new \Zend\Log\Writer\Mock;

        $logger = new \Zend\Log\Logger;
        $logger->addWriter($writer);

        $mvcEvent->shouldReceive('get')
        ->with('EddieJaoude\Zf2Logger\Logger')
            ->andReturn($logger);

        $eventManager = \Mockery::mock('Zend\EventManager\EventManager')->shouldDeferMissing();

        $mvcEvent->shouldReceive('getEventManager')
            ->andReturn($eventManager);

        $this->instance->onBootstrap($mvcEvent);

        $this->assertEquals(array('route', 'finish'), $eventManager->getEvents());
    }
}
