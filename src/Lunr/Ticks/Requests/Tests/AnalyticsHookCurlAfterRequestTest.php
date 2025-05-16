<?php

/**
 * This file contains the AnalyticsHookCurlAfterRequestTest class.
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
class AnalyticsHookCurlAfterRequestTest extends AnalyticsHookTestCase
{

    /**
     * Test that curlAfterRequest() does nothing when curl_info is NULL.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::curlAfterRequest
     */
    public function testCurlAfterRequestWithCurlInfoNull(): void
    {
        $this->setReflectionPropertyValue('events', [ $this->event ]);

        $this->event->expects($this->never())
                    ->method('addFields');

        $response = '';

        $this->class->curlAfterRequest($response);

        $this->assertPropertySame('events', [ $this->event ]);
    }

    /**
     * Test curlAfterRequest() when curl_info does have IP.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::curlAfterRequest
     */
    public function testCurlAfterRequestWithCurlInfoAndIp(): void
    {
        $this->setReflectionPropertyValue('events', [ $this->event ]);
        $this->setReflectionPropertyValue('startTimestamps', [ 1622536730.791435 ]);

        $fields = [
            'ip'                => '127.0.0.1',
            'startTimestamp'    => 1622536730.791435,
            'endTimestamp'      => 1622536730.8664,
            'executionTime'     => 0.075098,
            'nameLookupTime'    => 0.014666,
            'connectTime'       => 0.031133,
            'preTransferTime'   => 0.060408,
            'startTransferTime' => 0.071682,
            'sizeDownload'      => 17324,
        ];

        $this->event->expects($this->once())
                    ->method('addFields')
                    ->with($fields);

        $response = '';
        $info     = [
            'primary_ip'         => '127.0.0.1',
            'total_time'         => 0.075098,
            'namelookup_time'    => 0.014666,
            'connect_time'       => 0.031133,
            'pretransfer_time'   => 0.060408,
            'starttransfer_time' => 0.071682,
            'size_download'      => 17324,
        ];

        $this->class->curlAfterRequest($response, $info);

        $this->assertPropertySame('events', [ $this->event ]);
    }

}

?>
