<?php
namespace EddieJaoude\Zf2Logger;

use EddieJaoude\Zf2Logger\Listener\Request;
use EddieJaoude\Zf2Logger\Listener\Response;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
            new Request($e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger'))
        );

        $config = $e->getApplication()->getServiceManager()->get('Config')['EddieJaoude\Zf2Logger'];

        $response   = new Response($e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger'));
        $mediaTypes = empty($config['doNotLog']['mediaTypes']) ? [] : $config['doNotLog']['mediaTypes'];
        $response->setIgnoreMediaTypes($mediaTypes);
        $eventManager->attach($response);

        return;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src',
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'EddieJaoude\Zf2Logger' => 'EddieJaoude\Zf2Logger\Factory\Zf2Logger'
            )
        );
    }

}
