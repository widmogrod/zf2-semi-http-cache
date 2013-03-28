<?php
namespace WidHttpCache;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Header\IfModifiedSince;
use Zend\Http\Header\LastModified;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

class Module implements ListenerAggregateInterface, BootstrapListenerInterface
{
    /**
     * @var CallbackHandler[]
     */
    protected $listeners = [];

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $target = $e->getTarget();
        if (!$target instanceof Application) {
            return;
        }
        $target->getEventManager()->attach($this);
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'));
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

        /** @var $modifiedSince IfModifiedSince */
        $modifiedSince = $request->getHeader('If-Modified-Since');
        if (!$modifiedSince instanceof IfModifiedSince) {
            return;
        }

        $cacheForSeconds = '';

        $headers = $response->getHeaders();
        $lastModified = $headers->get('Last-Modified');
        if ($lastModified instanceof LastModified) {
            $lastModified = new LastModified();
            $headers->addHeader($lastModified);
        }

        // check if expire
    }

    public function onFinish(MvcEvent $e)
    {
        /** @var $request Request */
        $request = $e->getRequest();
        /** @var $response Response */
        $response = $e->getResponse();

        $headers = $response->getHeaders();
        $lastModified = $headers->get('Last-Modified');
        if ($lastModified instanceof LastModified) {
            $lastModified = new LastModified();
            $headers->addHeader($lastModified);
        }
    }
}