<?php

/**
 * This file contains the AnalyticsHookBeforeRequestTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

use Lunr\Ticks\AnalyticsDetailLevel;

/**
 * This class contains tests for the AnalyticsHook class.
 *
 * @covers \Lunr\Ticks\Requests\AnalyticsHook
 */
class AnalyticsHookBeforeRequestTest extends AnalyticsHookTestCase
{

    /**
     * Test that beforeRequest() sets the start_timestamp.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestSetsStartTimestamp(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->mockFunction('microtime', function () { return 1622536730.791435; });

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('startTimestamps', [ 1622536730.791435 ]);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRequest() with analytics detail level Info.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtInfo(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url' => $url,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtDetailedWithEmptyHeadersAndShortStringData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [];
        $data    = 'language=en';
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => $url,
            'requestHeaders' => NULL,
            'data'           => 'language=en',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtDetailedWithHeadersShortStringData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = 'language=en';
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => $url,
            'requestHeaders' => '{"Authentication":"Bearer Foo"}',
            'data'           => 'language=en',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtDetailedWithLongStringData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = file_get_contents(TEST_STATICS . '/data.json');
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => $url,
            'requestHeaders' => '{"Authentication":"Bearer Foo"}',
            'data'           => file_get_contents(TEST_STATICS . '/data_short.json'),
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtDetailedWithEmptyArrayData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [];
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => $url,
            'requestHeaders' => '{"Authentication":"Bearer Foo"}',
            'data'           => NULL,
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtDetailedWithArrayData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'             => $url,
            'requestHeaders' => '{"Authentication":"Bearer Foo"}',
            'data'            => '{"language":"en"}',
            'options'         => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Full.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtFullWithStringData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = file_get_contents(TEST_STATICS . '/data.json');
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => $url,
            'requestHeaders' => '{"Authentication":"Bearer Foo"}',
            'data'           => file_get_contents(TEST_STATICS . '/data.json'),
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Full.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtFullWithEmptyArrayData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [];
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => $url,
            'requestHeaders' => '{"Authentication":"Bearer Foo"}',
            'data'           => NULL,
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics detail level Full.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestAtFullWithArrayData(): void
    {
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $type    = 'GET';
        $options = [
            'timeout' => 60,
        ];

        $this->eventLogger->expects($this->once())
                          ->method('newEvent')
                          ->with('outbound_requests_log')
                          ->willReturn($this->event);

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type'   => 'GET',
            'domain' => 'www.example.com',
            'call'   => 'controller/method',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'url'            => $url,
            'requestHeaders' => '{"Authentication":"Bearer Foo"}',
            'data'           => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

}

?>
