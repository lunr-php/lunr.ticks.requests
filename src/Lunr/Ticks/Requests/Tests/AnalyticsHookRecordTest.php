<?php

/**
 * This file contains the AnalyticsHookRecordTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

/**
 * This class contains tests for the AnalyticsHook class.
 *
 * @covers Lunr\Ticks\Requests\AnalyticsHook
 */
class AnalyticsHookRecordTest extends AnalyticsHookTestCase
{

    /**
     * Test that record() records single event.
     */
    public function testRecordWithSingleEvent(): void
    {
        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('record');

        $method = $this->getReflectionMethod('record');

        $method->invokeArgs($this->class, [ $fields, $tags, 0 ]);
    }

    /**
     * Test that record() records single event.
     */
    public function testRecordWithMultipleEvents(): void
    {
        $event1 = $this->getMockBuilder(EventInterface::class)
                       ->getMock();

        $event2 = $this->getMockBuilder(EventInterface::class)
                       ->getMock();

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435, 1622536730.791445, 1622536730.791455 ]);
        $this->setReflectionPropertyValue('events', [ $event1, $this->event, $event2 ]);

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('record');

        $method = $this->getReflectionMethod('record');

        $method->invokeArgs($this->class, [ $fields, $tags, 1 ]);

        $timestamps = $this->getReflectionPropertyValue('startTimestamps');

        $this->assertCount(2, $timestamps);
        $this->assertArrayHasKey(0, $timestamps);
        $this->assertSame(1622536730.791435, $timestamps[0]);
        $this->assertArrayHasKey(2, $timestamps);
        $this->assertSame(1622536730.791455, $timestamps[2]);
        $this->assertArrayNotHasKey(1, $timestamps);

        $events = $this->getReflectionPropertyValue('events');

        $this->assertCount(2, $events);
        $this->assertArrayHasKey(0, $events);
        $this->assertSame($event1, $events[0]);
        $this->assertArrayHasKey(2, $events);
        $this->assertSame($event2, $events[2]);
        $this->assertArrayNotHasKey(1, $events);
    }

}

?>
