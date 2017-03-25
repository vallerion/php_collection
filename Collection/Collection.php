<?php

namespace Collection;

use Collection\Interfaces\Arrayable;
use Collection\Interfaces\Collection as CollectionInterface;
use Collection\Interfaces\Jsonable;
use Traversable;
use ArrayIterator;


class Collection implements CollectionInterface {

    /**
     * Items contained in collection
     *
     * @var array
     */
    protected $items = [];


    /**
     * Collection constructor.
     *
     * @param array $items
     */
    public function __construct($items = []) {
        $this->items = $this->getItemsAsArray($items);
    }

    /**
     * Convert items to array
     *
     * @param mixed $items
     * @return array
     */
    protected function getItemsAsArray($items) {

        if(is_array($items))
            return $items;
        else if($items instanceof static)
            return $items->all();
        else if($items instanceof Arrayable)
            return $items->toArray();
        else if($items instanceof Traversable)
            return iterator_to_array($items);


        return (array) $items;
    }

    /**
     * Get iterator for items
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->items);
    }

    /**
     * Count number of items in collection
     *
     * @return int
     */
    public function count() {
        return count($this->items);
    }

    /**
     * @return Collection $this
     */
    public function clear() {

        $this->items = [];

        return $this;
    }

    /**
     * Search by value
     *
     * @see array_search()
     *
     * @param mixed $value
     * @param bool $strict
     * @return mixed
     */
    public function search($value, $strict = false) {
        return array_search($value, $this->items, $strict);
    }

    /**
     * Replace items by values
     *
     * @param mixed $what
     * @param mixed $by
     * @return Collection $this
     */
    public function replace($what, $by) {

        $what = is_array($what) ? $what : [ $what ];

        foreach ($what as $key => $value) {

            $index = $this->search($value);

            if($index) {
                if(is_array($by))
                    $this->set($index, $by[$key]);
                else
                    $this->set($index, $by);
            }

        }

        return $this;
    }

    /**
     * Merge two collection and return new result collection
     *
     * @param CollectionInterface $collection
     * @return Collection
     */
    public function merge(CollectionInterface $collection) {
        return new static(array_merge($this->all(), $collection->all()));
    }

    /**
     * Merge two collection and write result in current collection
     *
     * @param CollectionInterface $collection
     * @return Collection $this
     */
    public function mergeWith(CollectionInterface $collection) {

        $this->items = array_merge($this->all(), $collection->all());

        return $this;
    }


    /**
     * Clone collections with all items
     */
    public function __clone() {
        $this->items = static::deepCopyArray($this->items);
    }

    /**
     * Deep copying array
     *
     * @param array $array
     * @return array
     */
    protected static function deepCopyArray(array $array) {

        $resultArray = [];

        foreach ($array as $key => $value) {

            if(is_array($value))
                $resultArray[$key] = static::deepCopyArray($value);
            else if(is_object($value))
                $resultArray[$key] = clone $value;
            else
                $resultArray[$key] = $value;
        }

        return $resultArray;
    }

    /**
     * Check if collection is empty or not
     *
     * @return bool
     */
    public function isEmpty() {
        return empty($this->items);
    }

    /**
     * Get all items in collection
     *
     * @return array
     */
    public function all() {
        return $this->items;
    }

    /**
     * Execute callback for each items in collection
     *
     * @param callable $callback
     * @return Collection
     */
    public function each(callable $callback) {

        foreach ($this->items as $key => $value)
            $callback($value, $key);


        return $this;
    }

    /**
     * Check exists item at collection
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Check exists item at collection
     *
     * @see offsetExists
     *
     * @param mixed $key
     * @return bool
     */
    public function has($key) {
        return $this->offsetExists($key);
    }

    /**
     * Get item at given offset
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->items[$offset];
    }
    
    /**
     * Get item at given key
     *
     * @param mixed $key
     * @return mixed|null
     */
    public function get($key) {
        return $this->has($key) ? $this->offsetGet($key) : null;
    }
    
    /**
     * Set new item at given offset
     *
     * @param mixed $offset must be numeric|string
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {

        if(is_numeric($offset) || is_string($offset))
            $this->items[$offset] = $value;
        else
            $this->items[] = $value;
    }

    /**
     * Set new item at given offset
     *
     * @param mixed $key must be numeric|string
     * @param mixed $value
     * @return Collection
     */
    public function set($key, $value) {

        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Push item onto end of collection
     *
     * @param $value
     * @return Collection
     */
    public function push($value) {
        return $this->set(null, $value);
    }

    /**
     * Push item onto start of collection
     *
     * @param $value
     * @return Collection
     */
    public function front($value) {

        array_unshift($this->items, $value);

        return $this;
    }

    /**
     * Pop the element off the end of collection
     *
     * @return mixed
     */
    public function pop() {
        return array_pop($this->items);
    }

    /**
     * Shift an element off the beginning of collection
     *
     * @return mixed
     */
    public function shift() {
        return array_shift($this->items);
    }

    /**
     * @return mixed
     */
    public function first() {
        return array_values($this->items)[0];
    }

    /**
     * @return mixed
     */
    public function last() {
        return end($this->items);
    }
    
    /**
     * Unset item at given offset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }

    /**
     * Remove item at given key
     *
     * @param mixed $key
     * @return Collection
     */
    public function remove($key) {

        $this->offsetUnset($key);

        return $this;
    }
    
    /**
     * Get instance as array
     *
     * @return array
     */
    public function toArray() {
        return $this->items;
    }

    /**
     * Get instance as Json format
     *
     * @see json_encode
     *
     * @param int $options
     * @param int $depth
     * @return mixed
     */
    public function toJson($options = 0, $depth = 512) {
        return json_encode($this->items, $options, $depth);
    }

}