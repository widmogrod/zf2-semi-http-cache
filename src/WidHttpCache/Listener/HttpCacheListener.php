<?php
namespace WidHttpCache\Listener;

use WidHttpCache\Config;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Header\CacheControl;
use Zend\Http\Header\IfModifiedSince;
use Zend\Http\Header\LastModified;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

class HttpCacheListener implements ListenerAggregateInterface
{
    /**
     * @var CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -100);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'), -100);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onRoute(MvcEvent $e)
    {
        /** @var $request Request */
        $request = $e->getRequest();
        /** @var $response Response */
        $response = $e->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        if (!$this->config->getUseModifiedSince()) {
            return;
        }

        /** @var $modifiedSince IfModifiedSince */
        $modifiedSince = $request->getHeader('If-Modified-Since');
        if (!$modifiedSince instanceof IfModifiedSince) {
            return;
        }

        $headers = $response->getHeaders();
        /** @var $lastModified LastModified  */
        $lastModified = $headers->get('Last-Modified');
        if (!$lastModified instanceof LastModified) {
            $lastModified = new LastModified();
            $headers->addHeader($lastModified);
        }

        // check if expire
        $age = $lastModified->date()->getTimestamp() - $modifiedSince->date()->getTimestamp();
        $maxAge = $this->config->getMaxAge();
        if ($maxAge < 1) {
            return;
        }

        if ($age < $maxAge) {
            // Nop, you have quire fresh information
            $lastModified->setDate($modifiedSince->date());
            $response->setStatusCode($response::STATUS_CODE_304);
            $response->setContent(null);
            return $response;
        }
    }

    public function onFinish(MvcEvent $e)
    {
        /** @var $response Response */
        $response = $e->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        $headers = $response->getHeaders();
        /** @var $lastModified LastModified  */
        $lastModified = $headers->get('Last-Modified');
        if (!$lastModified instanceof LastModified) {
            $lastModified = new LastModified();
            $headers->addHeader($lastModified);
        }

        /** @var $cacheControl CacheControl */
        $cacheControl = $headers->get('Cache-Control');
        if ($cacheControl instanceof CacheControl) {
            // Nope, you send it... i won't modified it.
            return;
        }

        $cacheControl = new CacheControl();
        $headers->addHeader($cacheControl);

        if (null !== ($value = $this->config->getMaxAge())) {
            $cacheControl->addDirective('max-age', $value);
        }
        if (null !== ($value = $this->config->getSMaxAge())) {
            $cacheControl->addDirective('s-maxage', $value);
        }
        if ($this->config->istMustRevalidate()) {
            $cacheControl->addDirective('must-revalidate');
        }
    }
}