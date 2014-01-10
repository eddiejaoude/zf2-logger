<?php
namespace EddieJaoude\Zf2Logger\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;
use Zend\Log\Logger as Log;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

/**
 * Class Request
 *
 * @package Application\Event
 */
class Response implements ListenerAggregateInterface
{

    /**
     * @var array
     */
    protected $log;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @param Log $log
     */
    public function __construct(Log $log = null)
    {
        if (!is_null($log)) {
            $this->setLog($log);
        }
    }

    /**
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param Log $log
     *
     * @return Request
     */
    public function setLog(Log $log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param CallbackHandler $listeners
     *
     * @return Request
     */
    public function addListener(CallbackHandler $listeners)
    {
        $this->listeners[] = $listeners;

        return $this;
    }

    /**
     * @param int $index
     *
     * @return bool
     */
    public function removeListener($index)
    {
        if (!empty($this->listeners[$index])) {
            unset($this->listeners[$index]);

            return true;
        }

        return false;
    }

    /**
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->addListener($events->attach(MvcEvent::EVENT_FINISH, array($this, 'logResponse')));
    }

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->getListeners() as $index => $listener) {
            if ($events->detach($listener)) {
                $this->removeListener($index);
            }
        }
    }

    /**
     * @param EventInterface $event
     */
    public function logResponse(EventInterface $event)
    {
        $this->getLog()->debug(
            print_r(
                array(
                    $event->getRequest()->getUri()->getHost() => array(
                        'Response' => array(
                            'statusCode' => $event->getResponse()->getStatusCode(),
                            'content'    => $event->getResponse()->getContent()
                        )
                    )
                )
                ,
                true
            )
        );
    }
}
