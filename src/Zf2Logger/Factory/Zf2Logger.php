<?php
namespace EddieJaoude\Zf2Logger\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger as ZendLogger;
use Zend\Log\Filter\Priority;
use EddieJaoude\Zf2Logger\Log\Logger;

class Zf2Logger implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Zend\Http\Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config')['EddieJaoude\Zf2Logger'];
        $logger = new Logger();

        $writers = 0;
        foreach ($config['writers'] as $writer) {
            if ($writer['enabled']) {
                $writerAdapter = new $writer['adapter']($writer['options']['output']);
                $logger->addWriter($writerAdapter);

                $writerAdapter->addFilter(
                    new Priority(
                        $writer['filter']
                    )
                );
                $writers++;
            }
        }

        !$config['registerErrorHandler'] ? : ZendLogger::registerErrorHandler($logger);
        !$config['registerExceptionHandler'] ? : ZendLogger::registerExceptionHandler($logger);

        $writers > 0 ? : $logger->addWriter(new \Zend\Log\Writer\Null);

        return $logger;
    }
}
