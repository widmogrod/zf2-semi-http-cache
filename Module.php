<?php
namespace WidHttpCache;

use Zend\EventManager\EventInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManager;

class Module implements BootstrapListenerInterface, ServiceProviderInterface
{
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

        $sm = $target->getServiceManager();

        /** @var $config \WidHttpCache\Config */
        $config = $sm->get('WidHttpCache\Config');
        if (!$config->isEnabled()) {
            return;
        }

        $em = $target->getEventManager();
        $em->attach($sm->get('WidHttpCache\Listener\HttpCacheListener'));
        $em->attach($sm->get($config->getConfigListener()));
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'WidHttpCache\Config' => 'WidHttpCache\Config',
            ),
            'factories' => array(
                'WidHttpCache\Listener\HttpCacheListener' => function(ServiceManager $manager) {
                    return new Listener\HttpCacheListener($manager->get('WidHttpCache\Config'));
                },
                'WidHttpCache\Listener\RoutesCacheConfigListener' => function(ServiceManager $manager) {
                    return new Listener\RoutesCacheConfigListener($manager->get('WidHttpCache\Config'));
                },
            ),
        );
    }
}