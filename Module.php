<?php
namespace EddieJaoude\Zf2Logger;

use EddieJaoude\Zf2Logger\Listener\Request;
use EddieJaoude\Zf2Logger\Listener\Response;
use Zend\Log\Filter\Priority;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Log\Logger as ZendLogger;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
            new Request($e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger\Logger'))
        );

        $eventManager->attach(
            new Response($e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger\Logger'))
        );

        return;
    }

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
                'EddieJaoude\Zf2Logger\Logger' => function ($sm) {
                        $config = $sm->get('Config')['EddieJaoude\Zf2Logger'];
                        $logger = new ZendLogger;

                        foreach($config['writers'] as $writer) {
                            $writerStream = new $writer['adapter']($writer['options']['path']);
                            $writerStream->addFilter(
                                new Priority(\Zend\Log\Logger::DEBUG)
                            );
                            $logger->addWriter($writerStream);
                        }

                        return $logger;
                    },
            )
        );
    }

}
