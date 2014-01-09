<?php
namespace Zf2Logger;

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

        // @TODO: suggestion moving to own file /Application/Event/RequestListener.php?
        $eventManager->attach(
            MvcEvent::EVENT_ROUTE,
            function ($e) {
                $logger = $e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger\Zend\Log\Logger');

                $logger->debug(
                    print_r(
                        array(
                            $e->getRequest()->getUri()->getHost() => array(
                                'Request' => $e->getRequest()->getUri()
                            )
                        )
                        ,
                        true
                    )
                );
            },
            100
        );

        // @TODO: suggestion moving to own file /Application/Event/ResponseListener.php?
        $eventManager->attach(
            MvcEvent::EVENT_FINISH,
            function ($e) {
                $logger = $e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger\Zend\Log\Logger');
                $logger->debug(
                    print_r(
                        array(
                            $e->getRequest()->getUri()->getHost() => array(
                                'Response' => array(
                                    'statusCode' => $e->getResponse()->getStatusCode(),
                                    'content'    => $e->getResponse()->getContent()
                                )
                            )
                        )
                        ,
                        true
                    )
                );
            },
            -200
        );

        return;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
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
                'EddieJaoude\Zf2Logger\Zend\Log\Logger' => function ($sm) {
                    $config = $sm->get('Config')['zf2Logger'];
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
