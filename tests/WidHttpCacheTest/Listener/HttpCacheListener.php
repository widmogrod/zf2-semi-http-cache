<?php
namespace WidHttpCacheTest;

use WidHttpCache\Listener\HttpCacheListener as TestObject;
use Zend\Stdlib\DateTime;
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

    /**
     * @dataProvider getRouteSendNotModifiedProvider
     */
    public function testOnRouteSendNotModified($ifModifiedSinceTime, $lastModified, $maxAge) {
        // prepare IfModifiedSince mock
        {{
            $dateTime = new DateTime($ifModifiedSinceTime);
            $modifiedSince = $this->getMockBuilder('Zend\Http\Header\IfModifiedSince')->disableOriginalConstructor()->getMock();
            $call = 0;
            $modifiedSince->expects($this->at($call++))->method('date')->will($this->returnValue($dateTime));
        }}

        // prepare Last-Modified mock
        {{
            $dateTime = new DateTime($lastModified);
            $lastModified = $this->getMockBuilder('Zend\Http\Header\LastModified')->disableOriginalConstructor()->getMock();
            $call = 0;
            $lastModified->expects($this->at($call++))->method('date')->will($this->returnValue($dateTime));
        }}

        // prepare request mock
        {{
            $request = $this->getMockBuilder('Zend\Http\Request')->disableOriginalConstructor()->getMock();
            $call = 0;
            $request->expects($this->at($call++))->method('getHeader')->will($this->returnValue($modifiedSince));
        }}

        // prepare headers mock
        {{
            $headers = $this->getMockBuilder('Zend\Http\Headers')->disableOriginalConstructor()->getMock();
            $call = 0;
            $headers->expects($this->at($call++))->method('get')->with($this->equalTo('Last-Modified'))->will($this->returnValue($lastModified));
        }}

        // prepare response mock
        {{
            $response = $this->getMockBuilder('Zend\Http\Response')->disableOriginalConstructor()->getMock();
            $call = 0;
            $response->expects($this->at($call++))->method('getHeaders')->will($this->returnValue($headers));
            $response->expects($this->at($call++))->method('setStatusCode')->with($this->equalTo(\Zend\Http\Response::STATUS_CODE_304));
            $response->expects($this->at($call++))->method('setContent')->with($this->equalTo(null));
        }}

        // prepare event mock
        {{
            $event = $this->getMockBuilder('Zend\Mvc\MvcEvent')->disableOriginalConstructor()->getMock();
            $call = 0;
            $event->expects($this->at($call++))->method('getRequest')->will($this->returnValue($request));
            $event->expects($this->at($call++))->method('getResponse')->will($this->returnValue($response));
        }}

        // prepare config mock
        {{
            $config = $this->getMock('WidHttpCache\Config');
            $config->expects($this->once())->method('getUseModifiedSince')->will($this->returnValue(true));
            $config->expects($this->once())->method('getMaxAge')->will($this->returnValue($maxAge));
        }}

        $object = new TestObject($config);
        $resut = $object->onRoute($event);
        $this->assertSame($response, $resut);
    }

    public function getRouteSendNotModifiedProvider() {
        return array(
            'no change' => array(
                '$ifModifiedSinceTime' => '2012-11-11 10:00:00',
                '$lastModified' =>        '2012-11-11 10:00:00',
                '$maxAge' =>               50,
            ),
            'second to stale' => array(
                '$ifModifiedSinceTime' => '2012-11-11 10:00:49',
                '$lastModified' =>        '2012-11-11 10:00:00',
                '$maxAge' =>               50,
            ),
        );
    }
}