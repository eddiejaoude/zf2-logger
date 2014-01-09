<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EddieJaoude\Zf2Logger;

use Zend\Log\Filter\Priority;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Log\Logger as ZendLogger;
use Zend\Log\Writer as LogWriter;

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
                $logger = $e->getApplication()->getServiceManager()->get('Zend\Log\Logger');

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
                $logger = $e->getApplication()->getServiceManager()->get('Zend\Log\Logger');
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
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
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
                'Event\Logger'                                   => function ($sm) {
                        $log = $sm->get('Zend\Log\Logger');
                        $listener    = new EventLogger($log);

                        return $listener;
                    },
                'Zend\Log\Logger'                                   => function ($sm) {
                        $config = $sm->get('Config')['logger'];
                        $logger = new ZendLogger;

                        $writerStream = new LogWriter\Stream($config['path']);
                        $writerStream->addFilter(new Priority($config['level']));
                        $logger->addWriter($writerStream);

                        return $logger;
                    },
        );
    }

}
