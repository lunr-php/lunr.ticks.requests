<?php

/**
 * This file contains the AnalyticsHookBeforeRequestTest class.
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
 * @covers \Lunr\Ticks\Requests\AnalyticsHook
 */
class AnalyticsHookBeforeRequestTest extends AnalyticsHookTestCase
{

    /**
     * Test that beforeRequest() sets isRequestMultiple to TRUE.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestSetsIsMultiRequestTrue(): void
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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $this->class->beforeRequest($url, $headers, $data, $type, $options, 0);

        $this->assertPropertySame('startTimestamps', [ 1622536730.791435 ]);
        $this->assertPropertySame('isRequestMultiple', TRUE);

        $this->unmockFunction('microtime');
    }

    /**
     * Test that beforeRequest() sets the startTimestamp.
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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Detailed);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => 'language=en',
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
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Detailed);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => 'language=en',
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
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Detailed);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => file_get_contents(TEST_STATICS . '/data_short.json'),
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
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Detailed);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => NULL,
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
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Detailed);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
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
    public function testBeforeRequestAtFullWithStringData(): void
    {
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Full);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => file_get_contents(TEST_STATICS . '/data.json'),
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
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Full);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => NULL,
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
        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Full);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);
    }

    /**
     * Test beforeRequest() with analytics level Full from domain filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingDomainFilter(): void
    {
        $filter = [
            'www.example.com' => AnalyticsDetailLevel::Full,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('domainFilter', $filter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Full);
    }

    /**
     * Test beforeRequest() with analytics level Full from domain filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingDomainFilterArrayAccess(): void
    {
        $filter = [
            'www.example.com' => AnalyticsDetailLevel::Full,
        ];

        $arrayAccess = new MockArrayAccess($filter);

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('domainFilter', $arrayAccess);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Full);
    }

    /**
     * Test beforeRequest() with unparsable domain.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithUnparsableDomain(): void
    {
        $filter = [
            'www.example.com' => AnalyticsDetailLevel::Full,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('domainFilter', $filter);

        $url     = '/path/to/file';
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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

        $this->controller->shouldReceive('getSpanSpecifictags')
                         ->once()
                         ->andReturn([ 'call' => 'controller/method' ]);

        $tags = [
            'type' => 'GET',
            'call' => 'controller/method',
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

        $this->assertPropertySame('level', AnalyticsDetailLevel::Info);
    }

    /**
     * Test beforeRequest() with analytics level Info because of no matching filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithoutMatchingFilter(): void
    {
        $domainFilter = [
            'www.example.net' => AnalyticsDetailLevel::Full,
        ];

        $urlFilter = [
            '/net/i' => AnalyticsDetailLevel::Detailed,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('domainFilter', $domainFilter);
        $this->setReflectionPropertyValue('urlFilter', $urlFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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

        $this->assertPropertySame('level', AnalyticsDetailLevel::Info);
    }

    /**
     * Test beforeRequest() with analytics level Full from url filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingUrlFilter(): void
    {
        $filter = [
            '/example/i' => AnalyticsDetailLevel::Full,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('urlFilter', $filter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Full);
    }

    /**
     * Test beforeRequest() with analytics level Full from url filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingUrlFilterIterator(): void
    {
        $filter = [
            '/example/i' => AnalyticsDetailLevel::Full,
        ];

        $iterable = new MockIterator($filter);

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('urlFilter', $iterable);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Full);
    }

    /**
     * Test beforeRequest() with analytics level Full from url filter and Detailed from domain filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingUrlAndLowerDomainFilter(): void
    {
        $urlFilter = [
            '/example/i' => AnalyticsDetailLevel::Full,
        ];

        $domainFilter = [
            'www.example.com' => AnalyticsDetailLevel::Detailed,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('urlFilter', $urlFilter);
        $this->setReflectionPropertyValue('domainFilter', $domainFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Full);
    }

    /**
     * Test beforeRequest() with analytics level Detailed from url filter and Full from domain filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingUrlAndHigherDomainFilter(): void
    {
        $urlFilter = [
            '/example/i' => AnalyticsDetailLevel::Detailed,
        ];

        $domainFilter = [
            'www.example.com' => AnalyticsDetailLevel::Full,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('urlFilter', $urlFilter);
        $this->setReflectionPropertyValue('domainFilter', $domainFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Detailed);
    }

    /**
     * Test beforeRequest() with analytics level Detailed and Full from multiple url filter.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMultipleMatchingUrlFilter(): void
    {
        $urlFilter = [
            '/com/i' => AnalyticsDetailLevel::Detailed,
            '/example/i' => AnalyticsDetailLevel::Full,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Info);
        $this->setReflectionPropertyValue('urlFilter', $urlFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Full);
    }

    /**
     * Test beforeRequest() with analytics level Detailed from url filter and Full as default.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingUrlFilterAndMaxDefaultLevel(): void
    {
        $urlFilter = [
            '/example/i' => AnalyticsDetailLevel::Detailed,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Full);
        $this->setReflectionPropertyValue('urlFilter', $urlFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Detailed);
    }

    /**
     * Test beforeRequest() with analytics level Detailed from domain filter and Full as default.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingDomainFilterAndMaxDefaultLevel(): void
    {
        $domainFilter = [
            'www.example.com' => AnalyticsDetailLevel::Detailed,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Full);
        $this->setReflectionPropertyValue('domainFilter', $domainFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Detailed);
    }

    /**
     * Test beforeRequest() with analytics level Detailed from url filter and Info from domain filter and Full as default.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingUrlAndLowerDomainFilterAndMaxDefaultLevel(): void
    {
        $urlFilter = [
            '/example/i' => AnalyticsDetailLevel::Detailed,
        ];

        $domainFilter = [
            'www.example.com' => AnalyticsDetailLevel::Info,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Full);
        $this->setReflectionPropertyValue('urlFilter', $urlFilter);
        $this->setReflectionPropertyValue('domainFilter', $domainFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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
            'requestBody'    => '{"language":"en"}',
            'options'        => '{"timeout":60}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRequest($url, $headers, $data, $type, $options);

        $this->assertPropertySame('level', AnalyticsDetailLevel::Detailed);
    }

    /**
     * Test beforeRequest() with analytics level Info from url filter and Detailed from domain filter and Full as default.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRequest
     */
    public function testBeforeRequestWithMatchingUrlAndHigherDomainFilterAndMaxDefaultLevel(): void
    {
        $urlFilter = [
            '/example/i' => AnalyticsDetailLevel::Info,
        ];

        $domainFilter = [
            'www.example.com' => AnalyticsDetailLevel::Detailed,
        ];

        $this->setReflectionPropertyValue('defaultLevel', AnalyticsDetailLevel::Full);
        $this->setReflectionPropertyValue('urlFilter', $urlFilter);
        $this->setReflectionPropertyValue('domainFilter', $domainFilter);

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

        $this->controller->shouldReceive('startChildSpan')
                         ->once();

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

        $this->assertPropertySame('level', AnalyticsDetailLevel::Info);
    }

}

?>
