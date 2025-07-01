<?php

/**
 * This file contains a simple Iterator mock.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests\Helpers;

use Iterator;

/**
 * The MockIterator class.
 */
class MockIterator implements Iterator
{

    /**
     * Mock array.
     * @var array
     */
    private array $data;

    /**
     * Position of the array pointer
     * @var int
     */
    private int $position;

    /**
     * Size of the $config array
     * @var int
     */
    private int $size;

    /**
     * Whether the cached size is invalid (outdated)
     * @var bool
     */
    private bool $sizeInvalid;

    /**
     * Constructor.
     *
     * @param array $data Mock array data
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        $this->sizeInvalid = TRUE;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->data);
        unset($this->position);
        unset($this->size);
    }

    /**
    * Rewinds back to the first element of the Iterator.
    *
    * (Inherited from Iterator)
    *
    * @return void
    */
    public function rewind(): void
    {
        reset($this->data);
        $this->position = 0;
    }

    /**
    * Return the current element.
    *
    * (Inherited from Iterator)
    *
    * @return mixed The current value of the config array
    */
    public function current(): mixed
    {
        return current($this->data);
    }

    /**
    * Return the key of the current element.
    *
    * (Inherited from Iterator)
    *
    * @return scalar Scalar on success, NULL on failure
    */
    public function key(): mixed
    {
        return key($this->data);
    }

    /**
    * Move forward to next element.
    *
    * (Inherited from Iterator)
    *
    * @return void
    */
    public function next(): void
    {
        next($this->data);
        ++$this->position;
    }

    /**
    * Checks if current position is valid.
    *
    * (Inherited from Iterator)
    *
    * @return bool TRUE on success, FALSE on failure
    */
    public function valid(): bool
    {
        $return = $this->current();
        if (($return === FALSE) && ($this->position + 1 <= $this->count()))
        {
            $return = TRUE;
        }

        return $return !== FALSE;
    }

    /**
     * Count elements of an object.
     *
     * @return int Size of the config array
     */
    public function count(): int
    {
        if ($this->sizeInvalid === TRUE)
        {
            $this->size        = count($this->data);
            $this->sizeInvalid = FALSE;
        }

        return $this->size;
    }

}

?>
