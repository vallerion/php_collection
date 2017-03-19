<?php

namespace Collection\Tests;

use Collection\Collection;
use ReflectionProperty;


class CollectionTest extends \PHPUnit_Framework_TestCase {

    protected $collection;

    protected $property;

    public function setUp() {

        $this->collection = new Collection();

        $this->property = new ReflectionProperty($this->collection, 'items');
        $this->property->setAccessible(true);
    }

    public function testInitialize() {

        $array = [
            'foo' => 'bar',
            'php' => '7.0'
        ];

        $collection = new Collection($array);

        $property = new ReflectionProperty($collection, 'items');
        $property->setAccessible(true);

        $this->assertEquals($array, $property->getValue($collection));
    }

    public function testOffsetSet() {

        $this->collection->offsetSet('foo', 'bar');

        $this->assertArrayHasKey('foo', $this->property->getValue($this->collection));
        $this->assertEquals([ 'foo' => 'bar' ], $this->property->getValue($this->collection));

    }

    public function testSet() {

        $this->collection->set('foo', 'bar');

        $this->assertArrayHasKey('foo', $this->property->getValue($this->collection));
        $this->assertEquals([ 'foo' => 'bar' ], $this->property->getValue($this->collection));
    }

    public function testHas() {

        $this->collection->set('foo', 'bar');

        $this->assertTrue($this->collection->has('foo'));
        $this->assertFalse($this->collection->has('bar'));
    }

    public function testOffsetGet() {

        $this->collection->set('foo', 'bar');

        $this->assertEquals($this->collection->offsetGet('foo'), 'bar');
    }

    public function testGet() {

        $this->collection->set('foo', 'bar');

        $this->assertEquals($this->collection->get('foo'), 'bar');
        $this->assertNull($this->collection->get('bar'));
    }

    public function testSearch() {

        $this->collection->set('foo', 'bar');

        $this->assertEquals($this->collection->search('bar'), 'foo');
    }

    public function testReplace() {

        $this->collection->set('foo', 'bar');
        $item = $this->collection->replace('bar', 'new bar')->get('foo');
        $this->assertEquals($item, 'new bar');

        $this->collection->set('foo', 'asd');
        $this->collection->set('php', 5);


        $this->collection->replace(
            [ 'asd', 5 ],
            [ 'bar', 7 ]
        );


        $this->assertEquals($this->collection->get('foo'), 'bar');
        $this->assertEquals($this->collection->get('php'), 7);
    }

    public function testMerge() {

        $this->collection->set('foo', 'bar');

        $collection = new Collection([
            'php' => 7
        ]);


        $this->assertEquals($this->collection->merge($collection)->all(), [
            'foo' => 'bar',
            'php' => 7
        ]);

        $this->assertEquals($this->collection->mergeWith($collection)->all(), [
            'foo' => 'bar',
            'php' => 7
        ]);
    }

    public function testIsEmpty() {

        $this->collection->set('foo', 'bar');
        $this->property->setValue($this->collection, []);

        $this->assertTrue($this->collection->isEmpty());
        $this->assertEmpty($this->property->getValue($this->collection));
    }

    public function testClear() {

        $this->collection->set('foo', 'bar')->clear();

        $this->assertEmpty($this->property->getValue($this->collection));
        $this->assertTrue($this->collection->isEmpty());
    }



    
}