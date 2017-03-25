<?php

namespace Collection\Tests;

use Collection\Collection;
use ReflectionProperty;
use ArrayIterator;
use ReflectionClass;
use ReflectionMethod;


class CollectionTest extends \PHPUnit_Framework_TestCase {

    protected $collection;

    protected $property;

    protected $class;

    public function setUp() {

        $this->collection = new Collection();

        $this->property = new ReflectionProperty($this->collection, 'items');
        $this->property->setAccessible(true);

        $this->class = new ReflectionClass($this->collection);
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

    public function testCount() {

        $count = $this->collection->set('foo', 'bar')->count();

        $this->assertTrue($count === 1);
    }

    public function testGetIterator() {

        $arrayIterator = $this->collection->set('foo', 'bar')->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $arrayIterator);
        $this->assertCount(1, $arrayIterator->getArrayCopy());
    }

    public function testClone() {

        $clone = clone $this->collection->set('foo', 'bar');

        $this->assertEquals($clone, $this->collection);
        $this->assertNotSame($clone, $this->collection);
    }

    public function testDeepCopyArray() {

        $method = $this->class->getMethod('deepCopyArray');
        $method->setAccessible(true);

        $array = [
            'foo' => 'bar',
            'php' => 7
        ];

        $copyArray = $method->invoke($this->collection, $array);

        $this->assertEquals($array, $copyArray);
        $this->assertSame($array, $copyArray);


        array_push($array, [ 'bar?' => 'foo!' ]);

        $this->assertNotEquals($array, $copyArray);
    }

    public function testEach() {

        $this->collection->set('foo', 'bar');
        $this->collection->set('php', [
            6 => false,
            7 => true
        ]);

        $array = [];

        $this->collection->each(function($value) use(&$array) {
            $array[] = $value;
        });


        $values = array_values($this->property->getValue($this->collection));

        $this->assertSame($values, $array);
    }

    public function testPush() {

        $this->collection->push('foo')->push('bar');

        $items = $this->property->getValue($this->collection);

        $this->assertSame($items[count($items) - 1], 'bar');
    }

    public function testPop() {

        $lastItem = $this->collection->push('foo')->push('bar')->pop();

        $items = $this->property->getValue($this->collection);

        $this->assertSame($lastItem, 'bar');
        $this->assertCount(1, $items);
    }

    public function testFront() {

        $this->collection->push('foo')->front('bar');

        $items = $this->property->getValue($this->collection);

        $this->assertSame($items[0], 'bar');
    }

    public function testShift() {

        $firstItem = $this->collection->push('foo')->push('bar')->shift();

        $items = $this->property->getValue($this->collection);

        $this->assertSame($firstItem, 'foo');
        $this->assertCount(1, $items);
    }

    public function testFirst() {

        $firstItem = $this->collection->push('foo')->push('bar')->first();

        $items = $this->property->getValue($this->collection);

        $this->assertSame($firstItem, 'foo');
        $this->assertCount(2, $items);
    }

    public function testLast() {

        $lastItem = $this->collection->push('foo')->push('bar')->last();

        $items = $this->property->getValue($this->collection);

        $this->assertSame($lastItem, 'bar');
        $this->assertCount(2, $items);
    }

    public function testOffsetUnset() {

        $this->collection->push('foo')->push('bar')->offsetUnset(0);

        $items = $this->property->getValue($this->collection);

        $this->assertCount(1, $items);
    }

    public function testRemove() {

        $this->collection->set('foo', 'bar');
        $this->collection->set('php', [
            6 => false,
            7 => true
        ])->remove('foo');


        $items = $this->property->getValue($this->collection);

        $this->assertCount(1, $items);
        $this->assertArrayHasKey('php', $items);
        $this->assertArrayNotHasKey('foo', $items);
    }

    public function testToArray() {

        $this->collection->set('foo', 'bar');
        $this->collection->set('php', [
            6 => false,
            7 => true
        ]);

        $items = $this->property->getValue($this->collection);

        $itemsAsArray = $this->collection->toArray();

        $this->assertInternalType('array', $itemsAsArray);
        $this->assertSame($items, $itemsAsArray);
    }

    public function testToJson() {

        $this->collection->set('foo', 'bar');
        $this->collection->set('php', [
            6 => false,
            7 => true
        ]);

        $items = json_encode($this->property->getValue($this->collection));

        $itemsAsJson = $this->collection->toJson();

        $this->assertInternalType('string', $itemsAsJson);
        $this->assertSame($items, $itemsAsJson);
        $this->assertJson($itemsAsJson);
    }



    
}