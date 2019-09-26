<?php

/**
 * gyselroth Helper
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 *
 */

namespace Tests;

class Object_ArrayAccess implements \ArrayAccess
{
    /** @var array */
    private $container;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->container = [
            'key1' => 1,
            'key2' => 2,
            'key3' => [
                'key3_1' => 3,
                'key3_2' => [
                    'key3_2_1' => 4
                ]
            ],
            'key4' => 5
        ];
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * @param  int|string $offset
     * @return array|bool|null
     */
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }
}
