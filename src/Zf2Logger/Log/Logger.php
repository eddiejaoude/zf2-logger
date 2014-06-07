<?php
namespace EddieJaoude\Zf2Logger\Log;

use Zend\Log\Logger as ZendLogger;
use Zend\Authentication\AuthenticationService;
use Zend\Http\PhpEnvironment\Request;

/**
 * Class Logger
 * @package EddieJaoude\Zf2Logger\Log
 */
class Logger extends ZendLogger
{

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $customExtra = array();

    /**
     * @param int   $priority
     * @param mixed $message
     * @param array $extra
     *
     * @return ZendLogger
     */
    final public function log($priority, $message, $extra = array())
    {
        $customExtra = array(
            'Zf2Logger' => array(
                'sessionId' => session_id(),
                'host'      => !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI',
                'ip'        => !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unavailable'
            )
        );

        return parent::log($priority, $message, array_merge($extra, $customExtra, $this->customExtra));
    }

    /**
     * @param AuthenticationService $authenticationService
     *
     * @return Logger
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;

        return $this;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    /**
     * @param Request $request
     *
     * @return Logger
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Request Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param array $customExtra
     *
     * @return Logger
     */
    public function setCustomExtra(array $customExtra)
    {
        $this->customExtra = $customExtra;

        return $this;
    }

    /**
     * @param array $customExtra
     *
     * @return Logger
     */
    public function addCustomExtra(array $customExtra)
    {
        $this->customExtra[] = $customExtra;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomExtra()
    {
        return $this->customExtra;
    }
}
