<?php

/**
 * This file contains a Requests hook that collects analytics.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Ticks\Requests;

use ArrayAccess;
use CurlHandle;
use Lunr\Ticks\AnalyticsDetailLevel;
use Lunr\Ticks\EventLogging\EventInterface;
use Lunr\Ticks\EventLogging\EventLoggerInterface;
use Lunr\Ticks\TracingControllerInterface;
use Lunr\Ticks\TracingInfoInterface;
use WpOrg\Requests\Exception as RequestsException;
use WpOrg\Requests\Exception\Transport\Curl as CurlException;
use WpOrg\Requests\Response;

/**
 * The AnalyticsHook class.
 *
 * @phpstan-import-type Tags from EventInterface
 * @phpstan-import-type Fields from EventInterface
 * @phpstan-type TracingInterface TracingControllerInterface&TracingInfoInterface
 * @phpstan-type CurlInfo array{
 *     certinfo: array<int, array<string, string>>,
 *     connect_time: float,
 *     content_type: string|null,
 *     download_content_length: float,
 *     filetime: int,
 *     header_size: int,
 *     http_code: int,
 *     http_version: int,
 *     local_ip: string,
 *     local_port: int,
 *     namelookup_time: float,
 *     pretransfer_time: float,
 *     primary_ip: string,
 *     primary_port: int,
 *     protocol: int,
 *     redirect_count: int,
 *     redirect_time: float,
 *     redirect_url: string,
 *     request_size: int,
 *     scheme: string,
 *     size_download: float,
 *     size_upload: float,
 *     speed_download: float,
 *     speed_upload: float,
 *     ssl_verify_result: int,
 *     starttransfer_time: float,
 *     total_time: float,
 *     upload_content_length: float,
 *     url: string,
 * }
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
     * Current profiling level
     * @var AnalyticsDetailLevel
     */
    private AnalyticsDetailLevel $level;

    /**
     * Default profiling level
     * @var AnalyticsDetailLevel
     */
    private AnalyticsDetailLevel $defaultLevel;

    /**
     * Analytics events
     * @var array<string|int,EventInterface>
     */
    private array $events;

    /**
     * Timestamp of when the requests started.
     *
     * This is a fallback timestamp for cases when we couldn't get the actual time from
     * the curl info. Most of the times this shouldn't be needed.
     *
     * @var array<string|int,float>
     */
    private array $startTimestamps;

    /**
     * Set of domains with custom level definitions
     * @var array<string, AnalyticsDetailLevel>|ArrayAccess<string, AnalyticsDetailLevel>
     */
    private array|ArrayAccess $domainFilter;

    /**
     * Set of regex based url filters with custom level definition
     * @var iterable<string, AnalyticsDetailLevel>
     */
    private iterable $urlFilter;

    /**
     * Whether it's a Requests::request_multiple() or Requests::request() call.
     * @var bool
     */
    private bool $isRequestMultiple;

    /**
     * Set of error types that indicate a curl error.
     * @var string[]
     */
    private array $curlErrorTypes = [
        'curlerror',
        CurlException::EASY,
        CurlException::MULTI,
        CurlException::SHARE,
    ];

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
        $this->startTimestamps   = [];
        $this->isRequestMultiple = FALSE;

        $this->events = [];
        $this->level  = AnalyticsDetailLevel::Info;

        $this->defaultLevel = $this->level;
        $this->domainFilter = [];
        $this->urlFilter    = [];
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->events);
        unset($this->startTimestamps);
        unset($this->level);
        unset($this->defaultLevel);
        unset($this->domainFilter);
        unset($this->urlFilter);
        unset($this->isRequestMultiple);
    }

    /**
     * Set analytics detail level.
     *
     * @param AnalyticsDetailLevel $level Profiling level
     *
     * @return void
     */
    public function setAnalyticsDetailLevel(AnalyticsDetailLevel $level): void
    {
        $this->level        = $level;
        $this->defaultLevel = $level;
    }

    /**
     * Set list of domains with custom level definitions.
     *
     * @param array<string, AnalyticsDetailLevel>|ArrayAccess<string, AnalyticsDetailLevel> $filter Set of domains with custom level definitions
     *
     * @return void
     */
    public function setDomainFilter(array|ArrayAccess $filter): void
    {
        $this->domainFilter = $filter;
    }

    /**
     * Set list of regex based url filters with custom level definitions.
     *
     * @param iterable<string, AnalyticsDetailLevel> $filter Set of regex based url filters with custom level definitions
     *
     * @return void
     */
    public function setUrlFilter(iterable $filter): void
    {
        $this->urlFilter = $filter;
    }

    /**
     * Finalize measurement point data and send to InfluxDB.
     *
     * @param Fields     $fields Field data
     * @param Tags       $tags   Tag data
     * @param string|int $id     Id of the request
     *
     * @return void
     */
    private function record(array $fields, array $tags, string|int $id = 0): void
    {
        $this->events[$id]->recordTimestamp();
        $this->events[$id]->addTags($tags);
        $this->events[$id]->addFields($fields);
        $this->events[$id]->record();

        unset($this->events[$id]);
        unset($this->startTimestamps[$id]);

        if ($this->events !== [])
        {
            return;
        }

        $this->isRequestMultiple = FALSE;
    }

    /**
     * Temporarily override the current level based on filters.
     *
     * @param string      $url    URL of the current request
     * @param string|null $domain Domain of the current request
     *
     * @return void
     */
    private function updateAnalyticsLevel(string $url, ?string $domain = NULL): void
    {
        $highestLevel = AnalyticsDetailLevel::Info;

        $urlFilterUsed = FALSE;

        foreach ($this->urlFilter as $regex => $urlLevel)
        {
            if (!preg_match($regex, $url))
            {
                continue;
            }

            $highestLevel = $highestLevel->value > $urlLevel->value ? $highestLevel : $urlLevel;

            if ($highestLevel === AnalyticsDetailLevel::Full)
            {
                $this->level = $highestLevel;
                return;
            }

            $urlFilterUsed = TRUE;
        }

        if ($urlFilterUsed)
        {
            $this->level = $highestLevel;
            return;
        }

        if ($domain !== NULL && isset($this->domainFilter[$domain]) && $this->domainFilter[$domain] instanceof AnalyticsDetailLevel)
        {
            $this->level = $this->domainFilter[$domain];
            return;
        }

        $this->level = $this->defaultLevel;
    }

    /**
     * Alter the request before it is sent to the transport.
     *
     * @param string                         $url     URL for the request
     * @param array<string, string>          $headers HTTP Headers
     * @param array<array-key, mixed>|string $data    POST data or GET params
     * @param string                         $type    HTTP verb
     * @param array<string, mixed>           $options Options for the request
     * @param string|int|null                $id      Id of the request
     *
     * @return void
     */
    public function beforeRequest(string &$url, array &$headers, array|string &$data, string &$type, array &$options, $id = NULL): void
    {
        // If $id is not NULL the $id param is passed and we can say its called from Requests::request_multiple()
        if ($id !== NULL && $this->isRequestMultiple === FALSE)
        {
            $this->isRequestMultiple = TRUE;
        }

        $id ??= 0;

        $fields = [
            'url' => $url,
        ];

        $tags = [
            'type' => $type,
        ];

        $domain = parse_url($url, PHP_URL_HOST);

        if (is_string($domain))
        {
            $tags['domain'] = $domain;

            $this->updateAnalyticsLevel($url, $tags['domain']);
        }
        else
        {
            $this->updateAnalyticsLevel($url);
        }

        if ($this->level->atLeast(AnalyticsDetailLevel::Detailed))
        {
            $fields['requestHeaders'] = empty($headers) ? NULL : json_encode($headers);
            $fields['options']        = json_encode($options);
        }

        if ($this->level === AnalyticsDetailLevel::Detailed)
        {
            if (!is_array($data))
            {
                if (strlen($data) > 512)
                {
                    $fields['data'] = substr($data, 0, 512) . '...';
                }
                else
                {
                    $fields['data'] = $data;
                }
            }
            else
            {
                $fields['data'] = empty($data) ? NULL : json_encode($data);
            }
        }
        elseif ($this->level === AnalyticsDetailLevel::Full)
        {
            if (is_array($data))
            {
                $fields['data'] = empty($data) ? NULL : json_encode($data);
            }
            else
            {
                $fields['data'] = $data;
            }
        }

        $this->events[$id] = $this->eventLogger->newEvent('outbound_requests_log');

        $this->events[$id]->addTags(array_merge($this->tracingController->getSpanSpecificTags(), $tags));
        $this->events[$id]->addFields($fields);

        $this->startTimestamps[$id] = microtime(TRUE);
    }

    /**
     * Alter the raw HTTP response before returning for parsing.
     *
     * @param string        $response Raw HTTP response
     * @param CurlInfo|null $info     CURL info structure
     * @param string|int    $id       Id of the request
     *
     * @return void
     */
    public function curlAfterRequest(string &$response, ?array &$info = NULL, string|int $id = 0): void
    {
        if (!is_array($info))
        {
            return;
        }

        $fields = [
            'ip'                => $info['primary_ip'],
            'startTimestamp'    => $this->startTimestamps[$id],
            'endTimestamp'      => (float) bcadd((string) $this->startTimestamps[$id], (string) $info['total_time'], 4),
            'executionTime'     => $info['total_time'],
            'nameLookupTime'    => $info['namelookup_time'],
            'connectTime'       => $info['connect_time'],
            'preTransferTime'   => $info['pretransfer_time'],
            'startTransferTime' => $info['starttransfer_time'],
            'sizeDownload'      => $info['size_download'],
        ];

        $this->events[$id]->addFields($fields);
    }

    /**
     * Alter/Inspect the exception before it is returned to the user.
     *
     * @param RequestsException              $exception Transport or response parsing exception
     * @param string                         $url       URL for the request
     * @param array<string, string>          $headers   HTTP Headers
     * @param array<array-key, mixed>|string $data      POST data or GET params
     * @param string                         $type      HTTP verb
     * @param array<string, mixed>           $options   Options for the request
     * @param string|int                     $id        Id of the request
     *
     * @return void
     */
    public function failed(
        RequestsException &$exception,
        string $url,
        array $headers,
        array|string $data,
        string $type,
        array $options,
        string|int $id = 0
    ): void
    {
        $fields = [
            'responseBody' => $exception->getMessage(),
        ];

        $status              = NULL;
        $presetExecutionTime = NULL;
        $presetFields        = $this->events[$id]->getFields();

        if (array_key_exists('executionTime', $presetFields))
        {
            $presetExecutionTime = (float) $presetFields['executionTime'];
        }

        if (in_array($exception->getType(), $this->curlErrorTypes) && $exception->getData() instanceof CurlHandle)
        {
            $info  = curl_getinfo($exception->getData());
            $errno = curl_errno($exception->getData());

            if ($info === FALSE)
            {
                $info = [];
            }

            if (isset($info['total_time']))
            {
                $fields['executionTime'] = $info['total_time'];
                $fields['endTimestamp']  = (float) bcadd((string) $this->startTimestamps[$id], (string) $fields['executionTime'], 4);
            }
            elseif ($presetExecutionTime === NULL)
            {
                $fields['executionTime'] = (float) bcsub((string) microtime(TRUE), (string) $this->startTimestamps[$id], 4);
                $fields['endTimestamp']  = (float) bcadd((string) $this->startTimestamps[$id], (string) $fields['executionTime'], 4);
            }
            else
            {
                $fields['endTimestamp'] = (float) bcadd((string) $this->startTimestamps[$id], (string) $presetExecutionTime, 4);
            }

            $fields = [
                'ip'                => $info['primary_ip'] ?? NULL,
                'startTimestamp'    => $this->startTimestamps[$id],
                'nameLookupTime'    => $info['namelookup_time'] ?? NULL,
                'connectTime'       => $info['connect_time'] ?? NULL,
                'preTransferTime'   => $info['pretransfer_time'] ?? NULL,
                'startTransferTime' => $info['starttransfer_time'] ?? NULL,
                'sizeDownload'      => $info['size_download'] ?? NULL,
            ] + $fields;

            // Map some curl error codes to cloudflare HTTP response codes
            $status = match ($errno) {
                // Web Server Is Down
                CURLE_COULDNT_CONNECT      => '521',
                // Connection Timed Out
                CURLE_OPERATION_TIMEDOUT   => '522',
                // SSL Handshake Failed
                CURLE_SSL_CONNECT_ERROR    => '525',
                // Invalid SSL Certificate
                CURLE_SSL_PEER_CERTIFICATE => '526',
                // Web Server Returned an Unknown Error
                CURLE_RECV_ERROR           => '520',
                CURLE_PARTIAL_FILE         => '520',
                default                    => NULL,
            };
        }
        elseif ($presetExecutionTime === NULL)
        {
            $fields['executionTime'] = (float) bcsub((string) microtime(TRUE), (string) $this->startTimestamps[$id], 4);
        }

        $tags = [
            'status' => $status,
        ];

        $this->record($fields, $tags, $id);
    }

    /**
     * Alter the redirect information before redirecting.
     *
     * @param string                         $location URL the request is redirected to
     * @param array<string, string>          $headers  HTTP Headers
     * @param array<array-key, mixed>|string $data     POST data or GET params
     * @param array<string, mixed>           $options  Options for the request
     * @param Response                       $return   Response object
     *
     * @return void
     */
    public function beforeRedirect(string &$location, array &$headers, array|string &$data, array &$options, Response $return): void
    {
        $fields = [];

        if (!isset($this->events[0]->getFields()['executionTime']))
        {
            $fields['executionTime'] = (float) bcsub((string) microtime(TRUE), (string) $this->startTimestamps[0], 4);
        }

        if ($this->level->atLeast(AnalyticsDetailLevel::Detailed))
        {
            $responseHeaders = $return->headers->getAll();

            $fields['responseHeaders'] = empty($responseHeaders) ? NULL : json_encode($responseHeaders);
        }

        if ($this->level === AnalyticsDetailLevel::Detailed)
        {
            if (strlen($return->body) > 512)
            {
                $fields['responseBody'] = substr($return->body, 0, 512) . '...';
            }
            else
            {
                $fields['responseBody'] = $return->body;
            }
        }
        elseif ($this->level === AnalyticsDetailLevel::Full)
        {
            $fields['responseBody'] = $return->body;
        }

        $tags = [
            // phpcs:ignore Lunr.NamingConventions.CamelCapsVariableName
            'status' => is_int($return->status_code) ? (string) $return->status_code : NULL,
        ];

        $this->record($fields, $tags);
    }

    /**
     * Alter the response object before it is returned to the user.
     *
     * @param Response                       $return  Response object
     * @param array<string, string>          $headers Headers of the request
     * @param array<array-key, mixed>|string $data    Body of the request
     * @param array<string, mixed>           $options Options of the request
     * @param string|int|null                $id      Id of the request
     *
     * @return void
     */
    public function afterRequest(Response &$return, array &$headers, array|string &$data, array &$options, string|int|null $id = NULL): void
    {
        // Ignore when called from Requests::request_multiple() and the $id param is not passed.
        // For Requests::requests_multiple() we don't want this called directly, but rather call
        // it from multiple_request_complete() instead ourselves (where we do pass an ID).
        if ($id === NULL && $this->isRequestMultiple === TRUE)
        {
            return;
        }

        $fields = [];
        $id   ??= 0;

        if (!isset($this->events[$id]->getFields()['executionTime']))
        {
            $fields['executionTime'] = (float) bcsub((string) microtime(TRUE), (string) $this->startTimestamps[$id], 4);
        }

        if ($this->level->atLeast(AnalyticsDetailLevel::Detailed))
        {
            $responseHeaders = $return->headers->getAll();

            $fields['responseHeaders'] = empty($responseHeaders) ? NULL : json_encode($responseHeaders);
        }

        if ($this->level === AnalyticsDetailLevel::Detailed)
        {
            if (strlen($return->body) > 512)
            {
                $fields['responseBody'] = substr($return->body, 0, 512) . '...';
            }
            else
            {
                $fields['responseBody'] = $return->body;
            }
        }
        elseif ($this->level === AnalyticsDetailLevel::Full)
        {
            $fields['responseBody'] = $return->body;
        }

        $tags = [
            // phpcs:ignore Lunr.NamingConventions.CamelCapsVariableName
            'status' => is_int($return->status_code) ? (string) $return->status_code : NULL,
        ];

        $this->record($fields, $tags, $id);
    }

    /**
     * Alter the response for an individual request in a multi-request.
     *
     * @param Response|RequestsException $response Response of the request
     * @param string|int                 $id       Id of the request
     *
     * @return void
     */
    public function multipleRequestComplete(Response|RequestsException &$response, string|int $id): void
    {
        $headers = [];
        $data    = [];
        $options = [];

        if ($response instanceof RequestsException)
        {
            $this->failed($response, '', $headers, '', '', $options, $id);
            return;
        }

        $this->afterRequest($response, $headers, $data, $options, $id);
    }

}

?>
