<?php

/**
 * This file contains the AnalyticsHookBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

use Lunr\Ticks\AnalyticsDetailLevel;

/**
 * This class contains tests for the AnalyticsHook class.
 *
 * @covers Lunr\Ticks\Requests\AnalyticsHook
 */
class AnalyticsHookBaseTest extends AnalyticsHookTestCase
{

    /**
     * Test that the eventlogger class was passed correctly.
     */
    public function testEventLoggerPassed(): void
    {
        $this->assertPropertySame('eventLogger', $this->eventLogger);
    }

    /**
     * Test that the tracing controller class was passed correctly.
     */
    public function testTracingControllerPassed(): void
    {
        $this->assertPropertySame('tracingController', $this->controller);
    }

    /**
     * Test that events is initialized as empty array.
     */
    public function testEventsIsEmptyArray(): void
    {
        $this->assertPropertySame('events', []);
    }

    /**
     * Test that the current profiling level is set correctly.
     */
    public function testCurrentProfilingLevel(): void
    {
        $this->assertPropertySame('level', AnalyticsDetailLevel::Info);
    }

    /**
     * Test that startTimestamps is initialized as empty array.
     */
    public function testStartTimestampsIsEmptyArray(): void
    {
        $this->assertPropertySame('startTimestamps', []);
    }

}

?>
