<?php
namespace WidHttpCacheTest;

use WidHttpCache\Listener\RoutesCacheConfigListener as TestObject;
use Zend\Mvc\MvcEvent;

class RoutesCacheConfigListener extends \PHPUnit_Framework_TestCase {

    public function testAttach() {
        // prepare events mock
        {{
            $events = $this->getMock('Zend\EventManager\EventManager');
            $events->expects($this->once())->method('attach')->with($this->equalTo(MvcEvent::EVENT_ROUTE));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
        }}

        $object = new TestObject($config);
        $object->attach($events);
    }

    public function testDettach() {
        // prepare callback mock
        {{
            $callback = $this->getMockBuilder('Zend\Stdlib\CallbackHandler')->disableOriginalConstructor()->getMock();
        }}

        // prepare events mock
        {{
            $events = $this->getMock('Zend\EventManager\EventManager');
            $events->expects($this->once())->method('attach')->with($this->equalTo(MvcEvent::EVENT_ROUTE))->will($this->returnValue($callback));
            $events->expects($this->once())->method('detach')->with($this->equalTo($callback));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
        }}

        $object = new TestObject($config);
        $object->attach($events);
        $object->detach($events);
    }

    public function testOnRouteBreakNoRouteMatch() {
        // prepare event mock
        {{
            $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')->disableOriginalConstructor()->getMock();
            $call = 0;
            $event->expects($this->at($call++))->method('getRouteMatch')->will($this->returnValue(false));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
        }}

        $object = new TestObject($config);
        $object->onRoute($event);
    }

    public function testOnRouteBreakNoConfig() {
        // prepare service manager mock
        {{
            $sm = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
            $call = 0;
            $sm->expects($this->at($call++))->method('get')->with('Config')->will($this->returnValue(array()));
        }}

        // prepare service manager mock
        {{
            $application = $this->getMockBuilder('Zend\Mvc\Application')->disableOriginalConstructor()->getMock();
            $call = 0;
            $application->expects($this->at($call++))->method('getServiceManager')->will($this->returnValue($sm));
        }}

        // prepare router match mock
        {{
            $routeMatch = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')->disableOriginalConstructor()->getMock();
        }}

        // prepare event mock
        {{
            $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')->disableOriginalConstructor()->getMock();
            $call = 0;
            $event->expects($this->at($call++))->method('getRouteMatch')->will($this->returnValue($routeMatch));
            $event->expects($this->at($call++))->method('getApplication')->will($this->returnValue($application));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
        }}

        $object = new TestObject($config);
        $object->onRoute($event);
    }

    /**
     * @dataProvider getConfigNoRouteMatchProvider
     */
    public function testOnRouteBreakConfigNoRoute($config) {
        // prepare service manager mock
        {{
            $sm = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
            $call = 0;
            $sm->expects($this->at($call++))->method('get')->with('Config')->will($this->returnValue($config));
        }}

        // prepare service manager mock
        {{
            $application = $this->getMockBuilder('Zend\Mvc\Application')->disableOriginalConstructor()->getMock();
            $call = 0;
            $application->expects($this->at($call++))->method('getServiceManager')->will($this->returnValue($sm));
        }}

        // prepare router match mock
        {{
            $routeMatch = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')->disableOriginalConstructor()->getMock();
        }}

        // prepare event mock
        {{
            $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')->disableOriginalConstructor()->getMock();
            $call = 0;
            $event->expects($this->at($call++))->method('getRouteMatch')->will($this->returnValue($routeMatch));
            $event->expects($this->at($call++))->method('getApplication')->will($this->returnValue($application));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
        }}

        $object = new TestObject($config);
        $object->onRoute($event);
    }

    public function getConfigNoRouteMatchProvider() {
        return array(
            'sample' => array(
                '$config' => array(
                    'router' => array(
                        'routes' => array(),
                    )
                )
            ),
        );
    }

    /**
     * @dataProvider getConfigRouteMatchProvider
     */
    public function testOnRouteBreakConfigRouteMatched($config, $routeName) {
        // prepare service manager mock
        {{
            $sm = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
            $call = 0;
            $sm->expects($this->at($call++))->method('get')->with('Config')->will($this->returnValue($config));
        }}

        // prepare service manager mock
        {{
            $application = $this->getMockBuilder('Zend\Mvc\Application')->disableOriginalConstructor()->getMock();
            $call = 0;
            $application->expects($this->at($call++))->method('getServiceManager')->will($this->returnValue($sm));
        }}

        // prepare router match mock
        {{
            $routeMatch = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')->disableOriginalConstructor()->getMock();
            $call = 0;
            $routeMatch->expects($this->at($call++))->method('getMatchedRouteName')->will($this->returnValue($routeName));
        }}

        // prepare event mock
        {{
            $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')->disableOriginalConstructor()->getMock();
            $call = 0;
            $event->expects($this->at($call++))->method('getRouteMatch')->will($this->returnValue($routeMatch));
            $event->expects($this->at($call++))->method('getApplication')->will($this->returnValue($application));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
            $config->expects($this->once())->method('merge');
        }}

        $object = new TestObject($config);
        $object->onRoute($event);
    }

    public function getConfigRouteMatchProvider() {
        return array(
            'sample' => array(
                '$config' => array(
                    'router' => array(
                        'routes' => array(
                            'api-route' => array(
                                \WidHttpCache\Config::CONFIG_NAMESPACE => array(),
                            )
                        ),
                    )
                ),
                '$routeName' => 'api-route'
            ),
        );
    }

    /**
     * @dataProvider getConfigRouteMatchPartsProvider
     */
    public function testOnRouteBreakConfigRouteMatchedParts($config, $routeName) {
        // prepare service manager mock
        {{
            $sm = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
            $call = 0;
            $sm->expects($this->at($call++))->method('get')->with('Config')->will($this->returnValue($config));
        }}

        // prepare service manager mock
        {{
            $application = $this->getMockBuilder('Zend\Mvc\Application')->disableOriginalConstructor()->getMock();
            $call = 0;
            $application->expects($this->at($call++))->method('getServiceManager')->will($this->returnValue($sm));
        }}

        // prepare router match mock
        {{
            $routeMatch = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')->disableOriginalConstructor()->getMock();
            $call = 0;
            $routeMatch->expects($this->at($call++))->method('getMatchedRouteName')->will($this->returnValue($routeName));
        }}

        // prepare event mock
        {{
            $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')->disableOriginalConstructor()->getMock();
            $call = 0;
            $event->expects($this->at($call++))->method('getRouteMatch')->will($this->returnValue($routeMatch));
            $event->expects($this->at($call++))->method('getApplication')->will($this->returnValue($application));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
            $config->expects($this->once())->method('merge');
        }}

        $object = new TestObject($config);
        $object->onRoute($event);
    }

    public function getConfigRouteMatchPartsProvider() {
        return array(
            'sample' => array(
                '$config' => array(
                    'router' => array(
                        'routes' => array(
                            'api-route' => array(
                                'child_routes' => array(
                                    'my' => array(
                                        'child_routes' => array(
                                            'create' => array(
                                                \WidHttpCache\Config::CONFIG_NAMESPACE => array(),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    )
                ),
                '$routeName' => 'api-route/my/create',
            ),
        );
    }
}