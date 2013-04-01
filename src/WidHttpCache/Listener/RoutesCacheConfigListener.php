<?php
namespace WidHttpCache\Listener;

use WidHttpCache\Config;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

class RoutesCacheConfigListener implements ListenerAggregateInterface
{
    /**
     * @var string
     */
    const CHILD_ROUTE_TOKEN = '/';

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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -10);
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
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            return;
        }

        $config = $e->getApplication()->getServiceManager()->get('Config');
        if (!isset($config['router'], $config['router']['routes'])) {
            return;
        }

        $config = (array) $config['router']['routes'];

        $routeName = $routeMatch->getMatchedRouteName();
        if (false !== strpos($routeName, self::CHILD_ROUTE_TOKEN)) {
            $part = strtok($routeName, self::CHILD_ROUTE_TOKEN);
            while ($part !== false) {
                if (isset($config[$part])) {
                    $config = (array)$config[$part];
                } else if (isset($config['child_routes'], $config['child_routes'][$part])) {
                    $config = (array)$config['child_routes'][$part];
                } else {
                    return;
                }
                $part = strtok(self::CHILD_ROUTE_TOKEN);
            };
        } else if (isset($config[$routeName])) {
            $config = (array) $config[$routeName];
        } else {
            return;
        }

        if (!isset($config[Config::CONFIG_NAMESPACE])) {
            return;
        }

        $config = $config[Config::CONFIG_NAMESPACE];
        $this->config->merge($config);
    }
}