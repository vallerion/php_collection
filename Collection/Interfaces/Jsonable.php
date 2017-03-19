<?php

namespace Collection\Interfaces;


interface Jsonable {


    /**
     * Get instance as Json format
     *
     * @see json_encode
     *
     * @param int $options
     * @param int $depth
     * @return mixed
     */
    public function toJson($options = 0, $depth = 512);
}