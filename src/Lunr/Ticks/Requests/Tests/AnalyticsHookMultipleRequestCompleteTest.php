<?php

/**
 * This file contains the AnalyticsHookMultipleRequestCompleteTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

use WpOrg\Requests\Exception\Http\Status400 AS RequestException400;
use WpOrg\Requests\Response;

/**
 * This class contains tests for the AnalyticsHook class.
 *
 * @covers Lunr\Ticks\Requests\AnalyticsHook
 */
class AnalyticsHookMultipleRequestCompleteTest extends AnalyticsHookTestCase
{

    /**
     * Test multipleRequestComplete() calls failed method when response is request exception.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::multipleRequestComplete
     */
    public function testMultipleRequestCompleteCallsFailedMethod(): void
    {
        $response = new RequestException400('request failed');

        $mockFunction = function ($exception, $url, $headers, $data, $type, $options, $id) use ($response) {
            if ($response !== $exception
                || $url !== ''
                || $headers !== []
                || $data !== ''
                || $type !== ''
                || $options !== []
                || $id !== 'endpoint1'
            )
            {
                echo 'FALSE';
            }
            else
            {
                echo 'TRUE';
            }
        };

        $this->mockMethod([ $this->class, 'failed' ], $mockFunction);

        $this->expectCustomOutputString('TRUE');

        $this->class->multipleRequestComplete($response, 'endpoint1');

        $this->unmockMethod([ $this->class, 'failed' ]);
    }

    /**
     * Test multipleRequestComplete() calls afterRequest method when response is request exception.
     *
     * @covers \Lunr\Ticks\Requests\AnalyticsHook::multipleRequestComplete
     */
    public function testMultipleRequestCompleteCallsAfterRequestMethod(): void
    {
        $response = new Response();

        $mockFunction = function (Response &$return, &$headers, &$data, &$options, string|int $id = 0) use ($response) {
            echo ($return !== $response || $id !== 'endpoint1') ? 'FALSE' : 'TRUE';
        };

        $this->mockMethod([ $this->class, 'afterRequest' ], $mockFunction);

        $this->expectCustomOutputString('TRUE');

        $this->class->multipleRequestComplete($response, 'endpoint1');

        $this->unmockMethod([ $this->class, 'afterRequest' ]);
    }

}

?>
