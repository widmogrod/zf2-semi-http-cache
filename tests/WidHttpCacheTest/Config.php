<?php
namespace WidHttpCacheTest;

use WidHttpCache\Config as TestObject;

class Config extends \PHPUnit_Framework_TestCase {
    /**
     * @var TestObject
     */
    protected $object;

    public function setUp(){
        $this->object = new TestObject();
    }

    public function testConfigListener() {
        $expected = TestObject::CONFIG_LISTENER;
        $value = $this->object->getConfigListener();
        $this->assertEquals($expected, $value);

        $expected = 'MyValue';
        $this->object->setConfigListener($expected);
        $value = $this->object->getConfigListener();
        $this->assertEquals($expected, $value);
    }

    public function testMaxAge() {
        $value = $this->object->getMaxAge();
        $this->assertNull($value);

        $expected = 55;
        $this->object->setMaxAge($expected);
        $value = $this->object->getMaxAge();
        $this->assertEquals($expected, $value);
    }

    public function testSMaxAge() {
        $value = $this->object->getSMaxAge();
        $this->assertNull($value);

        $expected = 55;
        $this->object->setSMaxAge($expected);
        $value = $this->object->getSMaxAge();
        $this->assertEquals($expected, $value);
    }

    public function testEnabled() {
        $value = $this->object->isEnabled();
        $this->assertFalse($value);

        $expected = true;
        $this->object->setEnabled($expected);
        $value = $this->object->isEnabled();
        $this->assertTrue($value);
    }

    public function testMustRevalidate() {
        $value = $this->object->istMustRevalidate();
        $this->assertTrue($value);

        $expected = false;
        $this->object->setMustRevalidate($expected);
        $value = $this->object->istMustRevalidate();
        $this->assertFalse($value);
    }

    public function testUseModifiedSince() {
        $value = $this->object->getUseModifiedSince();
        $this->assertTrue($value);

        $expected = false;
        $this->object->setUseModifiedSince($expected);
        $value = $this->object->getUseModifiedSince();
        $this->assertFalse($value);
    }
}