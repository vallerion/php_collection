<?php

namespace Collection\Interfaces;

use ArrayAccess, Countable, IteratorAggregate;

interface Collection extends ArrayAccess, IteratorAggregate, Countable, Jsonable, Arrayable {

    /**
     * Check exists item at collection
     *
     * @see offsetExists
     *
     * @param mixed $key
     * @return bool
     */
    public function has($key);

    /**
     * Get item at given key
     *
     * @param mixed $key
     * @return mixed|null
     */
    public function get($key);

    /**
     * Set new item at given offset
     *
     * @param mixed $key must be numeric|string
     * @param mixed $value
     * @return Collection
     */
    public function set($key, $value);

    /**
     * Replace items by values
     *
     * @param mixed $what
     * @param mixed $by
     * @return Collection
     */
    public function replace($what, $by);

    /**
     * Remove item at given key
     *
     * @param mixed $key
     * @return Collection
     */
    public function remove($key);

    /**
     * @return Collection
     */
    public function clear();

    /**
     * Merge two collection and return new result collection
     *
     * @param Collection $collection
     * @return Collection
     */
    public function merge(Collection $collection);

    /**
     * Merge two collection and write result in current collection
     *
     * @param Collection $collection
     * @return Collection
     */
    public function mergeWith(Collection $collection);

    /**
     * Search by value
     *
     * @param mixed $key
     * @param bool $strict
     * @return mixed
     */
    public function search($key, $strict = false);

    /**
     * Get all items in collection
     *
     * @return array
     */
    public function all();

    /**
     * Check if collection is empty or not
     *
     * @return bool
     */
    public function isEmpty();
}