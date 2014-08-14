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
     * @var Log
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
        $this->addListener($events->attach(MvcEvent::EVENT_FINISH, array($this, 'shutdown'), -1000));
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
        if ($event->getRequest() instanceOf \Zend\Http\PhpEnvironment\Request) {

            $contentType = $event->getResponse()->getHeaders()->get('Content-Type');
            $content = $event->getResponse()->getContent();

            if($contentType instanceof \Zend\Http\Header\ContentType) {
                if(in_array($event->getResponse()->getHeaders()->get('Content-Type')->getMediaType(), array('image/png','application/pdf'))) {
                    $content = 'BINARY';
                }
            }

            $this->getLog()->debug(
                print_r(
                    array(
                        $event->getRequest()->getUri()->getHost() => array(
                            'Response' => array(
                                'statusCode'  => $event->getResponse()->getStatusCode(),
                                'contentType' => (!$event->getResponse()->getHeaders()->get('Content-Type'))
                                        ? 'unknown' : $event->getResponse()->getHeaders()->get('Content-Type')->getMediaType(),
                                'content'     => $content,
                            )
                        )
                    )
                    ,
                    true
                )
            );
        }
    }

    /**
     * @param EventInterface $event
     */
    public function shutdown(EventInterface $event)
    {
        foreach($this->getLog()->getWriters() as $writer) {
            $writer->shutdown();
        }
    }
}
