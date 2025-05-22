<?php

/**
 * This file contains a simple ArrayAccess mock.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests\Helpers;

use ArrayAccess;

/**
 * The MockArrayAccess class.
 */
class MockArrayAccess implements ArrayAccess
{

    /**
     * Mock array.
     * @var array
     */
    private array $data;

    /**
     * Constructor.
     *
     * @param array $data Mock array data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->data);
    }

    /**
     * Whether or not an offset exists.
     *
     * @param mixed $offset An offset to check for
     *
     * @return bool Whether the offset exists or not
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param mixed $offset The offset to retrieve
     *
     * @return mixed The value
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? NULL;
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value  The value to set
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset The offset to unset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

}

?>
