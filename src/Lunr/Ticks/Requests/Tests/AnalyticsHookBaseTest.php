<?php

/**
 * This file contains the AnalyticsHookBaseTest class.
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

}

?>
