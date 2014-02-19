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
            new Request($e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger'))
        );

        $eventManager->attach(
            new Response($e->getApplication()->getServiceManager()->get('EddieJaoude\Zf2Logger'))
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
                'EddieJaoude\Zf2Logger' => function ($sm) {
                        $config = $sm->get('Config')['EddieJaoude\Zf2Logger'];
                        $logger = new ZendLogger;

                        foreach ($config['writers'] as $writer) {
                            if ($writer['disabled'] == false) {
                                $writerAdapter = new $writer['adapter']($writer['options']['output']);
                                $logger->addWriter($writerAdapter);

                                $writerAdapter->addFilter(
                                    new Priority(
                                        $writer['filter']
                                    )
                                );
                            }
                        }

                        !$config['registerErrorHandler'] ? : ZendLogger::registerErrorHandler($logger);
                        !$config['registerExceptionHandler'] ? : ZendLogger::registerExceptionHandler($logger);

                        return $logger;
                    },
            )
        );
    }

}
