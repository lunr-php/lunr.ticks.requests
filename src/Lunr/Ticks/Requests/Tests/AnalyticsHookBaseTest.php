<?php

/**
 * This file contains the AnalyticsHookBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

use Lunr\Ticks\AnalyticsDetailLevel;
use Lunr\Ticks\Requests\Tests\Helpers\MockArrayAccess;
use Lunr\Ticks\Requests\Tests\Helpers\MockIterator;

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

    /**
     * Test that setAnalyticsDetailLevel() overrides the default analytics detail level.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::setAnalyticsDetailLevel
     */
    public function testSettingAnalyticsDetailLevel(): void
    {
        $this->class->setAnalyticsDetailLevel(AnalyticsDetailLevel::Detailed);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Detailed);
        $this->assertPropertySame('defaultLevel', AnalyticsDetailLevel::Detailed);
    }

    /**
     * Test that setDomainFilter() sets custom domain filters.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::setDomainFilter
     */
    public function testSettingDomainFilter(): void
    {
        $filter = [
            'www.example.com' => AnalyticsDetailLevel::Full,
        ];

        $this->class->setDomainFilter($filter);

        $this->assertPropertySame('domainFilter', $filter);
    }

    /**
     * Test that setDomainFilter() sets custom domain filters.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::setDomainFilter
     */
    public function testSettingArrayAccessDomainFilter(): void
    {
        $filter = [
            'www.example.com' => AnalyticsDetailLevel::Full,
        ];

        $arrayAccess = new MockArrayAccess($filter);

        $this->class->setDomainFilter($arrayAccess);

        $this->assertPropertySame('domainFilter', $arrayAccess);
    }

    /**
     * Test that setUrlFilter() sets custom regex based url filters.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::setUrlFilter
     */
    public function testSettingUrlFilter(): void
    {
        $filter = [
            '/example/i' => AnalyticsDetailLevel::Full,
        ];

        $this->class->setUrlFilter($filter);

        $this->assertPropertySame('urlFilter', $filter);
    }

    /**
     * Test that setUrlFilter() sets custom regex based url filters.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::setUrlFilter
     */
    public function testSettingIterableUrlFilter(): void
    {
        $filter = [
            '/example/i' => AnalyticsDetailLevel::Full,
        ];

        $iterable = new MockIterator($filter);

        $this->class->setUrlFilter($iterable);

        $this->assertPropertySame('urlFilter', $iterable);
    }

}

?>
