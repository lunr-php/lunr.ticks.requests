<?php

/**
 * This file contains the AnalyticsHookBeforeRedirectTest class.
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
class AnalyticsHookBeforeRedirectTest extends AnalyticsHookTestCase
{

    /**
     * Test that beforeRedirect() resets profiling data.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectResetsProfilingData(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->assertPropertySame('startTimestamps', []);
        $this->assertPropertySame('events', []);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRedirect() at analytics level Info.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtInfoWithIntegerStatus(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->response->status_code = 404;

        $tags = [
            'status' => '404',
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

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRedirect() at analytics level Info.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtInfoWithBooleanStatus(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

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

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRedirect() at analytics level Info with preset executionTime.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtInfoWithPresetDuration(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->response->status_code = 404;

        $tags = [
            'status' => '404',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with([]);

        $this->event->expects($this->once())
                    ->method('getFields')
                    ->willReturn([ 'executionTime' => 0.9901 ]);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);
    }

    /**
     * Test beforeRedirect() at analytics level Detailed.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtDetailedWithEmptyHeadersAndShortString(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $this->response->status_code = 404;
        $this->response->body        = 'language=en';

        $tags = [
            'status' => '404',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'   => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => 'language=en',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRedirect() at analytics level Detailed.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtDetailedWithHeaders(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

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
            'status' => '404',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'   => 0.9901,
            'responseHeaders' => '{"Content-Type":"application\/json"}',
            'responseBody'    => '{"language":"en"}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

    /**
     * Test that beforeRedirect() at analytics level Detailed.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtDetailedWithEmptyHeadersAndLongString(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $this->response->status_code = 404;
        $this->response->body        = file_get_contents(TEST_STATICS . '/data.json');

        $tags = [
            'status' => '404',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'   => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => file_get_contents(TEST_STATICS . '/data_short.json'),
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRedirect() at analytics level Full.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtFullWithEmptyHeadersAndShortString(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $this->response->status_code = 404;
        $this->response->body        = 'language=en';

        $tags = [
            'status' => '404',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'   => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => 'language=en',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRedirect() at analytics level Full.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtFullWithHeaders(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

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
            'status' => '404',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'   => 0.9901,
            'responseHeaders' => '{"Content-Type":"application\/json"}',
            'responseBody'    => '{"language":"en"}',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

    /**
     * Test beforeRedirect() at analytics level Full.
     *
     * @covers Lunr\Ticks\Requests\AnalyticsHook::beforeRedirect
     */
    public function testBeforeRedirectAtFullWithEmptyHeadersAndLongString(): void
    {
        $url     = 'https://www.example.com/api/v1/webservice';
        $headers = [
            'Authentication' => 'Bearer Foo',
        ];
        $data    = [
            'language' => 'en',
        ];
        $options = [
            'timeout' => 60,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $this->response->status_code = 404;
        $this->response->body        = file_get_contents(TEST_STATICS . '/data.json');

        $tags = [
            'status' => '404',
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime'   => 0.9901,
            'responseHeaders' => NULL,
            'responseBody'    => file_get_contents(TEST_STATICS . '/data.json'),
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->beforeRedirect($url, $headers, $data, $options, $this->response);

        $this->unmockFunction('microtime');
    }

}

?>
