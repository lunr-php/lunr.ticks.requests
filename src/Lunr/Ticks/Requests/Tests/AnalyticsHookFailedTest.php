<?php

/**
 * This file contains the AnalyticsHookFailedTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

use Lunr\Ticks\AnalyticsDetailLevel;
use WpOrg\Requests\Exception as RequestsException;
use WpOrg\Requests\Exception\Transport\Curl as CurlException;

/**
 * This class contains tests for the AnalyticsHook class.
 *
 * @covers Lunr\Ticks\Requests\AnalyticsHook
 */
class AnalyticsHookFailedTest extends AnalyticsHookTestCase
{

    /**
     * Unit test data provider for curl error codes and the cloudflare http status codes they map to.
     *
     * @return array Curl error codes
     */
    public static function curlErrorProvider(): array
    {
        $data   = [];
        $data[] = [ CURLE_COULDNT_CONNECT, '521' ];
        $data[] = [ CURLE_OPERATION_TIMEDOUT, '522' ];
        $data[] = [ CURLE_SSL_CONNECT_ERROR, '525' ];
        $data[] = [ CURLE_SSL_PEER_CERTIFICATE, '526' ];
        $data[] = [ CURLE_RECV_ERROR, '520' ];
        $data[] = [ CURLE_PARTIAL_FILE, '520' ];
        $data[] = [ 0, NULL ];

        return $data;
    }

    /**
     * Test that failed() resets profiling data.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedResetsProfilingData(): void
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

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $this->class->failed($this->exception, $url, $headers, $data, $type, $options);

        $this->assertPropertySame('startTimestamps', []);
        $this->assertPropertySame('events', []);

        $this->unmockFunction('microtime');
    }

    /**
     * Test failed() at analytics level Info.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfo(): void
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

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime' => 0.9901,
            'responseBody'  => 'cURL error 28: Connection timed out after 10001 milliseconds',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->failed($this->exception, $url, $headers, $data, $type, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test failed() at analytics level Detailed.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtDetailed(): void
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

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Detailed);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime' => 0.9901,
            'responseBody'  => 'cURL error 28: Connection timed out after 10001 milliseconds',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->failed($this->exception, $url, $headers, $data, $type, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test failed() at analytics level Full.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtFull(): void
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

        $this->mockFunction('microtime', function () { return 1622536731.781455; });

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('level', AnalyticsDetailLevel::Full);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'executionTime' => 0.9901,
            'responseBody'  => 'cURL error 28: Connection timed out after 10001 milliseconds',
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->failed($this->exception, $url, $headers, $data, $type, $options);

        $this->unmockFunction('microtime');
    }

    /**
     * Test failed() at analytics level Info with preset executionTime.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithPresetDuration(): void
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

        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'responseBody' => 'cURL error 28: Connection timed out after 10001 milliseconds',
        ];

        $this->event->expects($this->once())
                    ->method('getFields')
                    ->willReturn([ 'executionTime' => 0.9901 ]);

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $this->class->failed($this->exception, $url, $headers, $data, $type, $options);
    }

    /**
     * Test failed() at analytics level Info, with a curl error and available IP.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithCurlErrorSuccess(): void
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

        $info = [
            'primary_ip'         => '127.0.0.1',
            'total_time'         => 0.075098,
            'namelookup_time'    => 0.014666,
            'connect_time'       => 0.031133,
            'pretransfer_time'   => 0.060408,
            'starttransfer_time' => 0.071682,
            'size_download'      => 17324,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });
        $this->mockFunction('curl_getinfo', function () use ($info) { return $info; });
        $this->mockFunction('curl_errno', fn() => 0);

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536730.8664,
            'executionTime'     => 0.075098,
            'responseBody'      => 'cURL error 28: Connection timed out after 10001 milliseconds',
            'ip'                => '127.0.0.1',
            'nameLookupTime'    => 0.014666,
            'connectTime'       => 0.031133,
            'preTransferTime'   => 0.060408,
            'startTransferTime' => 0.071682,
            'sizeDownload'      => 17324,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $handle = curl_init();

        $exception = $this->getMockBuilder(RequestsException::class)
                          ->setConstructorArgs([ 'cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror' ])
                          ->getMock();

        $exception->expects($this->once())
                  ->method('getType')
                  ->willReturn('curlerror');

        $exception->expects($this->exactly(3))
                  ->method('getData')
                  ->willReturn($handle);

        $this->class->failed($exception, $url, $headers, $data, $type, $options);

        curl_close($handle);

        $this->unmockFunction('microtime');
        $this->unmockFunction('curl_getinfo');
        $this->unmockFunction('curl_errno');
    }

    /**
     * Test failed() at analytics level Info, with a curl "easy" type exception and available IP.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithCurlEasySuccess(): void
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

        $info = [
            'primary_ip'         => '127.0.0.1',
            'total_time'         => 0.075098,
            'namelookup_time'    => 0.014666,
            'connect_time'       => 0.031133,
            'pretransfer_time'   => 0.060408,
            'starttransfer_time' => 0.071682,
            'size_download'      => 17324,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });
        $this->mockFunction('curl_getinfo', function () use ($info) { return $info; });
        $this->mockFunction('curl_errno', fn() => 0);

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536730.8664,
            'executionTime'     => 0.075098,
            'responseBody'      => 'cURL error 28: Connection timed out after 10001 milliseconds',
            'ip'                => '127.0.0.1',
            'nameLookupTime'    => 0.014666,
            'connectTime'       => 0.031133,
            'preTransferTime'   => 0.060408,
            'startTransferTime' => 0.071682,
            'sizeDownload'      => 17324,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $handle = curl_init();

        $exception = $this->getMockBuilder(RequestsException::class)
                          ->setConstructorArgs([ 'cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror' ])
                          ->getMock();

        $exception->expects($this->once())
                  ->method('getType')
                  ->willReturn(CurlException::EASY);

        $exception->expects($this->exactly(3))
                  ->method('getData')
                  ->willReturn($handle);

        curl_close($handle);

        $this->class->failed($exception, $url, $headers, $data, $type, $options);

        $this->unmockFunction('microtime');
        $this->unmockFunction('curl_getinfo');
        $this->unmockFunction('curl_errno');
    }

    /**
     * Test failed() at analytics level Info, with a curl "multi" type exception and available IP.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithCurlMultiSuccess(): void
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

        $info = [
            'primary_ip'         => '127.0.0.1',
            'total_time'         => 0.075098,
            'namelookup_time'    => 0.014666,
            'connect_time'       => 0.031133,
            'pretransfer_time'   => 0.060408,
            'starttransfer_time' => 0.071682,
            'size_download'      => 17324,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });
        $this->mockFunction('curl_getinfo', function () use ($info) { return $info; });
        $this->mockFunction('curl_errno', fn() => 0);

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536730.8664,
            'executionTime'     => 0.075098,
            'responseBody'      => 'cURL error 28: Connection timed out after 10001 milliseconds',
            'ip'                => '127.0.0.1',
            'nameLookupTime'    => 0.014666,
            'connectTime'       => 0.031133,
            'preTransferTime'   => 0.060408,
            'startTransferTime' => 0.071682,
            'sizeDownload'      => 17324,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $handle = curl_init();

        $exception = $this->getMockBuilder(RequestsException::class)
                          ->setConstructorArgs([ 'cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror' ])
                          ->getMock();

        $exception->expects($this->once())
                  ->method('getType')
                  ->willReturn(CurlException::MULTI);

        $exception->expects($this->exactly(3))
                  ->method('getData')
                  ->willReturn($handle);

        curl_close($handle);

        $this->class->failed($exception, $url, $headers, $data, $type, $options);

        $this->unmockFunction('microtime');
        $this->unmockFunction('curl_getinfo');
        $this->unmockFunction('curl_errno');
    }

    /**
     * Test failed() at analytics level Info, with a curl "share" type exception and available IP.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithCurlShareSuccess(): void
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

        $info = [
            'primary_ip'         => '127.0.0.1',
            'total_time'         => 0.075098,
            'namelookup_time'    => 0.014666,
            'connect_time'       => 0.031133,
            'pretransfer_time'   => 0.060408,
            'starttransfer_time' => 0.071682,
            'size_download'      => 17324,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });
        $this->mockFunction('curl_getinfo', function () use ($info) { return $info; });
        $this->mockFunction('curl_errno', fn() => 0);

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536730.8664,
            'executionTime'     => 0.075098,
            'responseBody'      => 'cURL error 28: Connection timed out after 10001 milliseconds',
            'ip'                => '127.0.0.1',
            'nameLookupTime'    => 0.014666,
            'connectTime'       => 0.031133,
            'preTransferTime'   => 0.060408,
            'startTransferTime' => 0.071682,
            'sizeDownload'      => 17324,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $handle = curl_init();

        $exception = $this->getMockBuilder(RequestsException::class)
                          ->setConstructorArgs([ 'cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror' ])
                          ->getMock();

        $exception->expects($this->once())
                  ->method('getType')
                  ->willReturn(CurlException::SHARE);

        $exception->expects($this->exactly(3))
                  ->method('getData')
                  ->willReturn($handle);

        $this->class->failed($exception, $url, $headers, $data, $type, $options);

        curl_close($handle);

        $this->unmockFunction('microtime');
        $this->unmockFunction('curl_getinfo');
        $this->unmockFunction('curl_errno');
    }

    /**
     * Test failed() at analytics level Info, with a curl error but unavailable IP.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithCurlErrorFalse(): void
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

        $this->mockFunction('microtime', function () { return 1622536731.781455; });
        $this->mockFunction('curl_getinfo', function () { return FALSE; });
        $this->mockFunction('curl_errno', fn() => 0);

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536731.7815,
            'executionTime'     => 0.9901,
            'responseBody'      => 'cURL error 28: Connection timed out after 10001 milliseconds',
            'ip'                => NULL,
            'nameLookupTime'    => NULL,
            'connectTime'       => NULL,
            'preTransferTime'   => NULL,
            'startTransferTime' => NULL,
            'sizeDownload'      => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $handle = curl_init();

        $exception = $this->getMockBuilder(RequestsException::class)
                          ->setConstructorArgs([ 'cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror' ])
                          ->getMock();

        $exception->expects($this->once())
                  ->method('getType')
                  ->willReturn('curlerror');

        $exception->expects($this->exactly(3))
                  ->method('getData')
                  ->willReturn($handle);

        $this->class->failed($exception, $url, $headers, $data, $type, $options);

        curl_close($handle);

        $this->unmockFunction('microtime');
        $this->unmockFunction('curl_getinfo');
        $this->unmockFunction('curl_errno');
    }

    /**
     * Test failed() at analytics level Info, with a curl error but unavailable IP and preset execution time.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithCurlErrorFalseAndPresetExecutionTime(): void
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

        $this->mockFunction('microtime', function () { return 1622536731.781455; });
        $this->mockFunction('curl_getinfo', function () { return FALSE; });
        $this->mockFunction('curl_errno', fn() => 0);

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('getFields')
                    ->willReturn([ 'executionTime' => 0.9902 ]);

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536731.7816,
            'responseBody'      => 'cURL error 28: Connection timed out after 10001 milliseconds',
            'ip'                => NULL,
            'nameLookupTime'    => NULL,
            'connectTime'       => NULL,
            'preTransferTime'   => NULL,
            'startTransferTime' => NULL,
            'sizeDownload'      => NULL,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $handle = curl_init();

        $exception = $this->getMockBuilder(RequestsException::class)
                          ->setConstructorArgs([ 'cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror' ])
                          ->getMock();

        $exception->expects($this->once())
                  ->method('getType')
                  ->willReturn('curlerror');

        $exception->expects($this->exactly(3))
                  ->method('getData')
                  ->willReturn($handle);

        $this->class->failed($exception, $url, $headers, $data, $type, $options);

        curl_close($handle);

        $this->unmockFunction('microtime');
        $this->unmockFunction('curl_getinfo');
        $this->unmockFunction('curl_errno');
    }

    /**
     * Test failed() at analytics level Info, maps curl error statuses.
     *
     * @param int $curlCode         Curl error code
     * @param int $cloudflareStatus Cloudflare HTTP status
     *
     * @dataProvider curlErrorProvider
     * @covers       \Lunr\Ticks\Requests\AnalyticsHook::failed
     */
    public function testFailedAtInfoWithCurlStatusMapping($curlCode, $cloudflareStatus): void
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

        $info = [
            'primary_ip'         => '127.0.0.1',
            'total_time'         => 0.075098,
            'namelookup_time'    => 0.014666,
            'connect_time'       => 0.031133,
            'pretransfer_time'   => 0.060408,
            'starttransfer_time' => 0.071682,
            'size_download'      => 17324,
        ];

        $this->mockFunction('microtime', function () { return 1622536731.781455; });
        $this->mockFunction('curl_getinfo', function () use ($info) { return $info; });
        $this->mockFunction('curl_errno', fn() => $curlCode);

        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->controller->shouldReceive('getTraceId')
                         ->once()
                         ->andReturns('7b333e15-aa78-4957-a402-731aecbb358e');

        $this->controller->shouldReceive('getSpanId')
                         ->once()
                         ->andReturns('24ec5f90-7458-4dd5-bb51-7a1e8f4baafe');

        $this->controller->shouldReceive('getParentSpanId')
                         ->once()
                         ->andReturns('8b1f87b5-8383-4413-a341-7619cd4b9948');

        $this->controller->shouldReceive('stopChildSpan')
                         ->once();

        $tags = [
            'status' => $cloudflareStatus,
        ];

        $this->event->expects($this->once())
                    ->method('addTags')
                    ->with($tags);

        $fields = [
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536730.8664,
            'executionTime'     => 0.075098,
            'responseBody'      => 'cURL error 28: Connection timed out after 10001 milliseconds',
            'ip'                => '127.0.0.1',
            'nameLookupTime'    => 0.014666,
            'connectTime'       => 0.031133,
            'preTransferTime'   => 0.060408,
            'startTransferTime' => 0.071682,
            'sizeDownload'      => 17324,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $handle = curl_init();

        $exception = $this->getMockBuilder(RequestsException::class)
                          ->setConstructorArgs([ 'cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror' ])
                          ->getMock();

        $exception->expects($this->once())
                  ->method('getType')
                  ->willReturn('curlerror');

        $exception->expects($this->exactly(3))
                  ->method('getData')
                  ->willReturn($handle);

        $this->class->failed($exception, $url, $headers, $data, $type, $options);

        curl_close($handle);

        $this->unmockFunction('microtime');
        $this->unmockFunction('curl_getinfo');
        $this->unmockFunction('curl_errno');
    }

}

?>
