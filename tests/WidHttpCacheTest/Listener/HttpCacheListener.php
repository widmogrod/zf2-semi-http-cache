<?php
namespace WidHttpCacheTest;

use WidHttpCache\Listener\HttpCacheListener as TestObject;
use Zend\Mvc\MvcEvent;

class HttpCacheListener extends \PHPUnit_Framework_TestCase {

    public function testAttach() {
        // prepare events mock
        {{
            $events = $this->getMock('Zend\EventManager\EventManager');
            $call = 0;
            $events->expects($this->at($call++))->method('attach')->with($this->equalTo(MvcEvent::EVENT_ROUTE));
            $events->expects($this->at($call++))->method('attach')->with($this->equalTo(MvcEvent::EVENT_FINISH));
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
            $callback1 = $this->getMockBuilder('Zend\Stdlib\CallbackHandler')->disableOriginalConstructor()->getMock();
            $callback2 = $this->getMockBuilder('Zend\Stdlib\CallbackHandler')->disableOriginalConstructor()->getMock();
        }}

        // prepare events mock
        {{
            $events = $this->getMock('Zend\EventManager\EventManager');
            $call = 0;
            $events->expects($this->at($call++))->method('attach')->with($this->equalTo(MvcEvent::EVENT_ROUTE))->will($this->returnValue($callback1));
            $events->expects($this->at($call++))->method('attach')->with($this->equalTo(MvcEvent::EVENT_FINISH))->will($this->returnValue($callback2));
            $events->expects($this->at($call++))->method('detach')->with($this->equalTo($callback1));
            $events->expects($this->at($call++))->method('detach')->with($this->equalTo($callback2));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
        }}

        $object = new TestObject($config);
        $object->attach($events);
        $object->detach($events);
    }
}