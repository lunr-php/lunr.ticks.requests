<?php

/**
 * This file contains a Requests hook that collects analytics.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests;

use Lunr\Ticks\EventLogging\EventLoggerInterface;
use Lunr\Ticks\TracingControllerInterface;
use Lunr\Ticks\TracingInfoInterface;

/**
 * The AnalyticsHook class.
 *
 * @phpstan-type TracingInterface TracingControllerInterface&TracingInfoInterface
 */
class AnalyticsHook
{

    /**
     * Instance of an EventLogger
     * @var EventLoggerInterface
     */
    private readonly EventLoggerInterface $eventLogger;

    /**
     * Shared instance of a tracing controller
     * @var TracingInterface
     */
    private readonly TracingControllerInterface&TracingInfoInterface $tracingController;

    /**
     * Constructor.
     *
     * @param EventLoggerInterface $eventLogger       Instance of an event logger
     * @param TracingInterface     $tracingController Instance of a tracing controller
     */
    public function __construct(
        EventLoggerInterface $eventLogger,
        TracingControllerInterface&TracingInfoInterface $tracingController,
    )
    {
        $this->eventLogger       = $eventLogger;
        $this->tracingController = $tracingController;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // no-op
    }

}

?>
