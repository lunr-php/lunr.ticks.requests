<?php

/**
 * This file contains the AnalyticsHookTestCase class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests\Tests;

use Lunr\Halo\LunrBaseTestCase;
use Lunr\Ticks\EventLogging\EventInterface;
use Lunr\Ticks\EventLogging\EventLoggerInterface;
use Lunr\Ticks\Requests\AnalyticsHook;
use Lunr\Ticks\TracingControllerInterface;
use Lunr\Ticks\TracingInfoInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;
use WpOrg\Requests\Exception as RequestsException;
use WpOrg\Requests\Response;
use WpOrg\Requests\Response\Headers;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the AnalyticsHook class.
 *
 * @covers Lunr\Ticks\Requests\InfluxDBHook
 */
abstract class AnalyticsHookTestCase extends LunrBaseTestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * Mock Instance of an event logger.
     * @var EventLoggerInterface&MockObject
     */
    protected EventLoggerInterface&MockObject $eventLogger;

    /**
     * Mock instance of a Controller
     * @var TracingControllerInterface&TracingInfoInterface&MockInterface
     */
    protected TracingControllerInterface&TracingInfoInterface&MockInterface $controller;

    /**
     * Mock Instance of an analytics event.
     * @var EventInterface&MockObject
     */
    protected EventInterface&MockObject $event;

    /**
     * Mock Instance of the Response class.
     * @var Response&MockObject
     */
    protected Response&MockObject $response;

    /**
     * Mock Instance of the Headers class.
     * @var Headers&MockObject
     */
    protected Headers&MockObject $headers;

    /**
     * Mock Instance of the RequestsException class.
     * @var RequestsException
     */
    protected RequestsException $exception;

    /**
     * Instance of the tested class.
     * @var AnalyticsHook
     */
    protected AnalyticsHook $class;

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->eventLogger = $this->getMockBuilder(EventLoggerInterface::class)
                                  ->getMock();

        $this->event = $this->getMockBuilder(EventInterface::class)
                            ->getMock();

        $this->controller = Mockery::mock(
                                TracingControllerInterface::class,
                                TracingInfoInterface::class,
                            );

        $this->response = $this->getMockBuilder(Response::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->exception = new RequestsException('cURL error 28: Connection timed out after 10001 milliseconds', 'curlerror');

        $this->headers = $this->getMockBuilder(Headers::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->response->headers = $this->headers;

        $this->class = new AnalyticsHook($this->eventLogger, $this->controller);

        parent::baseSetUp($this->class);
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->class);
        unset($this->eventLogger);
        unset($this->event);
        unset($this->controller);
        unset($this->response);
        unset($this->headers);
        unset($this->exception);

        parent::tearDown();
    }

}

?>
