<?php

/**
 * This file contains the AnalyticsHookAfterRequestTest class.
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
class AnalyticsHookAfterRequestTest extends AnalyticsHookTestCase
{

    /**
     * Test that afterRequest() resets profiling data.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestResetsProfilingData(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->assertPropertySame('startTimestamps', []);
        $this->assertPropertySame('events', []);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Info.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtInfoWithIntegerStatus(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->response->status_code = 404;

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Info.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtInfoWithBooleanStatus(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->response->status_code = FALSE;

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime' => 0.9901
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Info with preset executionTime.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtInfoWithPresetDuration(): void
    {
        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->response->status_code = 404;

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with([]);

        $this->event->expects($this->once())
                    ->method('getFields')
                    ->willReturn([ 'executionTime' => 0.9900200366973877 ]);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);
    }

    /**
     * Test afterRequest() at profiling level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtDetailedWithEmptyHeadersAndShortString(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $this->response->status_code = 404;
        $this->response->body        = 'language=en';

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'        => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => 'language=en',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtDetailedWithHeaders(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $this->response->status_code = 404;
        $this->response->body        = '{"language":"en"}';

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $this->headers->expects($this->once())
                      ->method('getAll')
                      ->willReturn($headers);

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'        => 0.9901,
            'responseHeaders' => '{"Content-Type":"application\/json"}',
            'responseBody'    => '{"language":"en"}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtDetailedWithEmptyHeadersAndLongString(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $this->response->status_code = 404;
        $this->response->body        = file_get_contents(TEST_STATICS . '/data.json');

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'        => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => file_get_contents(TEST_STATICS . '/data_short.json'),
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Full.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtFullWithEmptyHeadersAndShortString(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $this->response->status_code = 404;
        $this->response->body        = 'language=en';

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'        => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => 'language=en',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Full.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtFullWithHeaders(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $this->response->status_code = 404;
        $this->response->body        = '{"language":"en"}';

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $this->headers->expects($this->once())
                      ->method('getAll')
                      ->willReturn($headers);

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'        => 0.9901,
            'responseHeaders' => '{"Content-Type":"application\/json"}',
            'responseBody'    => '{"language":"en"}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = [];
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test afterRequest() at profiling level Full.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::afterRequest
     */
    public function testAfterRequestAtFullWithEmptyHeadersAndLongString(): void
    {
        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $this->response->status_code = 404;
        $this->response->body        = file_get_contents(TEST_STATICS . '/data.json');

        $tags = [
            'status' => 404,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'        => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => file_get_contents(TEST_STATICS . '/data.json'),
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $headers = [];
        $data    = '';
        $options = [];

        $this->class->afterRequest($this->response, $headers, $data, $options);

        $this->unmockFunction('microtime');
    }

}

?>
