<?php

/**
 * This file contains the AnalyticsHookRecordTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

use RuntimeException;

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
    public function testRecordWithTraceIdUnavailable(): void
    {
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns(NULL);

        $this->controller->shouldNotReceive('getSpanId');

        $this->controller->shouldNotReceive('getParentSpanId');

        $this->controller->shouldNotReceive('stopChildSpan');

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->never())
                    ->method('record');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Trace ID not available!');

        $method = $this->getReflectionMethod('record');

        $method->invokeArgs($this->class, [ $fields, $tags, 0 ]);
    }

    /**
     * Test that record() records single event.
     */
    public function testRecordWithSpanIdUnavailable(): void
    {
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns(NULL);

        $this->controller->shouldNotReceive('getParentSpanId');

        $this->controller->shouldNotReceive('stopChildSpan');

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->never())
                    ->method('record');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Span ID not available!');

        $method = $this->getReflectionMethod('record');

        $method->invokeArgs($this->class, [ $fields, $tags, 0 ]);
    }

    /**
     * Test that record() records single event.
     */
    public function testRecordWithParentSpanIdUnavailable(): void
    {
        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $traceID = '7b333e15-aa78-4957-a402-731aecbb358e';
        $spanID  = '24ec5f90-7458-4dd5-bb51-7a1e8f4baafe';

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns($traceID);

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns($spanID);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns(NULL);

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with($spanID);

        $this->event->expects($this->never())
                    ->method('setParentSpanId');

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
    public function testRecordWithSingleEvent(): void
    {
        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $traceID      = '7b333e15-aa78-4957-a402-731aecbb358e';
        $spanID       = '24ec5f90-7458-4dd5-bb51-7a1e8f4baafe';
        $parentSpanID = '8b1f87b5-8383-4413-a341-7619cd4b9948';

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns($traceID);

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns($spanID);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns($parentSpanID);

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with($spanID);

        $this->event->expects($this->once())
                    ->method('setParentSpanId')
                    ->with($parentSpanID);

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
     * Test that record() records the last event from a requests_multiple() call.
     */
    public function testRecordWithSingleEventFromRequestMultiple(): void
    {
        $events = [
            '520b674a-4e2f-4f1b-b579-0e408900b368' => $this->event,
        ];

        $timestamps = [
            '520b674a-4e2f-4f1b-b579-0e408900b368' => 1622536730.791445,
        ];

        $this->setReflectionPropertyValue('startTimestamps', $timestamps);
        $this->setReflectionPropertyValue('events', $events);
        $this->setReflectionPropertyValue('isRequestMultiple', TRUE);

        $traceID      = '7b333e15-aa78-4957-a402-731aecbb358e';
        $parentSpanID = '8b1f87b5-8383-4413-a341-7619cd4b9948';

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns($traceID);

        $this->controller->shouldReceive('isValidSpanId')
                         ->once()
                         ->andReturns(TRUE);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns($parentSpanID);

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with('520b674a-4e2f-4f1b-b579-0e408900b368');

        $this->event->expects($this->once())
                    ->method('setParentSpanId')
                    ->with($parentSpanID);

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('record');

        $method = $this->getReflectionMethod('record');

        $method->invokeArgs($this->class, [ $fields, $tags, '520b674a-4e2f-4f1b-b579-0e408900b368' ]);
    }

    /**
     * Test that record() records an event in a list of multiple events.
     */
    public function testRecordWithMultipleEventsAndValidSpanID(): void
    {
        $event1 = $this->getMockBuilder(EventInterface::class)
                       ->getMock();

        $event2 = $this->getMockBuilder(EventInterface::class)
                       ->getMock();

        $events = [
            '2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad' => $event1,
            '520b674a-4e2f-4f1b-b579-0e408900b368' => $this->event,
            '973f184a-af59-4d20-8152-1a46551ef8e8' => $event2,
        ];

        $timestamps = [
            '2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad' => 1622536730.791435,
            '520b674a-4e2f-4f1b-b579-0e408900b368' => 1622536730.791445,
            '973f184a-af59-4d20-8152-1a46551ef8e8' => 1622536730.791455,
        ];

        $this->setReflectionPropertyValue('startTimestamps', $timestamps);
        $this->setReflectionPropertyValue('events', $events);
        $this->setReflectionPropertyValue('isRequestMultiple', TRUE);

        $traceID      = '7b333e15-aa78-4957-a402-731aecbb358e';
        $parentSpanID = '8b1f87b5-8383-4413-a341-7619cd4b9948';

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns($traceID);

        $this->controller->shouldReceive('isValidSpanId')
                         ->once()
                         ->andReturns(TRUE);

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns($parentSpanID);

        $this->controller->shouldNotReceive('stopChildSpan');

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with('520b674a-4e2f-4f1b-b579-0e408900b368');

        $this->event->expects($this->once())
                    ->method('setParentSpanId')
                    ->with($parentSpanID);

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('record');

        $method = $this->getReflectionMethod('record');

        $method->invokeArgs($this->class, [ $fields, $tags, '520b674a-4e2f-4f1b-b579-0e408900b368' ]);

        $timestamps = $this->getReflectionPropertyValue('startTimestamps');

        $this->assertCount(2, $timestamps);
        $this->assertArrayHasKey('2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad', $timestamps);
        $this->assertSame(1622536730.791435, $timestamps['2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad']);
        $this->assertArrayHasKey('973f184a-af59-4d20-8152-1a46551ef8e8', $timestamps);
        $this->assertSame(1622536730.791455, $timestamps['973f184a-af59-4d20-8152-1a46551ef8e8']);
        $this->assertArrayNotHasKey('520b674a-4e2f-4f1b-b579-0e408900b368', $timestamps);

        $events = $this->getReflectionPropertyValue('events');

        $this->assertCount(2, $events);
        $this->assertArrayHasKey('2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad', $events);
        $this->assertSame($event1, $events['2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad']);
        $this->assertArrayHasKey('973f184a-af59-4d20-8152-1a46551ef8e8', $events);
        $this->assertSame($event2, $events['973f184a-af59-4d20-8152-1a46551ef8e8']);
        $this->assertArrayNotHasKey('520b674a-4e2f-4f1b-b579-0e408900b368', $events);
    }

    /**
     * Test that record() records an event in a list of multiple events.
     */
    public function testRecordWithMultipleEventsAndInvalidSpanID(): void
    {
        $event1 = $this->getMockBuilder(EventInterface::class)
                       ->getMock();

        $event2 = $this->getMockBuilder(EventInterface::class)
                       ->getMock();

        $events = [
            '2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad' => $event1,
            '520b674a-4e2f-4f1b-b579-0e408900b368' => $this->event,
            '973f184a-af59-4d20-8152-1a46551ef8e8' => $event2,
        ];

        $timestamps = [
            '2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad' => 1622536730.791435,
            '520b674a-4e2f-4f1b-b579-0e408900b368' => 1622536730.791445,
            '973f184a-af59-4d20-8152-1a46551ef8e8' => 1622536730.791455,
        ];

        $this->setReflectionPropertyValue('startTimestamps', $timestamps);
        $this->setReflectionPropertyValue('events', $events);
        $this->setReflectionPropertyValue('isRequestMultiple', TRUE);

        $traceID      = '7b333e15-aa78-4957-a402-731aecbb358e';
        $parentSpanID = '8b1f87b5-8383-4413-a341-7619cd4b9948';

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns($traceID);

        $this->controller->shouldReceive('isValidSpanId')
                         ->once()
                         ->andReturns(FALSE);

        $this->controller->shouldReceive('getNewSpanId')
                         ->once()
                         ->andReturns('6daff7de-35b7-40c8-9879-97e5f1a18b5b');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns($parentSpanID);

        $this->controller->shouldNotReceive('stopChildSpan');

        $tags = [
            'status' => 404,
        ];

        $fields = [
            'duration' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('recordTimestamp');

        $this->event->expects($this->once())
                    ->method('setTraceId')
                    ->with($traceID);

        $this->event->expects($this->once())
                    ->method('setSpanId')
                    ->with('6daff7de-35b7-40c8-9879-97e5f1a18b5b');

        $this->event->expects($this->once())
                    ->method('setParentSpanId')
                    ->with($parentSpanID);

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->event->expects($this->once())
                    ->method('record');

        $method = $this->getReflectionMethod('record');

        $method->invokeArgs($this->class, [ $fields, $tags, '520b674a-4e2f-4f1b-b579-0e408900b368' ]);

        $timestamps = $this->getReflectionPropertyValue('startTimestamps');

        $this->assertCount(2, $timestamps);
        $this->assertArrayHasKey('2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad', $timestamps);
        $this->assertSame(1622536730.791435, $timestamps['2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad']);
        $this->assertArrayHasKey('973f184a-af59-4d20-8152-1a46551ef8e8', $timestamps);
        $this->assertSame(1622536730.791455, $timestamps['973f184a-af59-4d20-8152-1a46551ef8e8']);
        $this->assertArrayNotHasKey('520b674a-4e2f-4f1b-b579-0e408900b368', $timestamps);

        $events = $this->getReflectionPropertyValue('events');

        $this->assertCount(2, $events);
        $this->assertArrayHasKey('2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad', $events);
        $this->assertSame($event1, $events['2f9eac41-eb8e-4259-bb12-ff3eb1ea66ad']);
        $this->assertArrayHasKey('973f184a-af59-4d20-8152-1a46551ef8e8', $events);
        $this->assertSame($event2, $events['973f184a-af59-4d20-8152-1a46551ef8e8']);
        $this->assertArrayNotHasKey('520b674a-4e2f-4f1b-b579-0e408900b368', $events);
    }

}

?>
