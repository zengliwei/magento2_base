<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Helper\Api;

use CrazyCat\Base\Helper\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractApi
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isTestMode = false;

    /**
     * @var bool
     */
    protected $logApi = false;

    /**
     * @var string
     */
    protected $logFile = 'api.log';

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->curlFactory = $context->getCurlFactory();
        $this->logger = $context->getLogger();
    }

    /**
     * Log request and response
     *
     * @param string            $method
     * @param string            $targetUrl
     * @param array             $requestHeader
     * @param string|array|null $requestBody
     * @param int               $startTime
     * @param int               $endTime
     * @param string            $response
     * @param string|null       $error
     */
    protected function log($method, $targetUrl, $requestHeader, $requestBody, $startTime, $endTime, $response, $error)
    {
        $this->logger->log(
            sprintf(
                "%s %s\nExecuted: %s ms\nRequest Header: %s\nRequest Body: %s\n%s",
                $method,
                $targetUrl,
                round(($endTime - $startTime) * 1000),
                json_encode($requestHeader, true),
                json_encode($requestBody, true),
                $error ? sprintf('Error: %s', $error) : sprintf('Response: %s', $response)
            ),
            $this->logFile
        );
    }

    /**
     * Request
     *
     * @param string            $method
     * @param string            $sourceUrl
     * @param string|array|null $data
     * @param array             $header
     * @return string
     * @throws LocalizedException
     */
    protected function request($method, $sourceUrl, $data = null, $header = [])
    {
        /** @var $curl Curl */
        $curl = $this->curlFactory->create();
        $curl->setConfig(['header' => false]);
        if ($this->isTestMode) {
            $curl->setConfig(['verifypeer' => false, 'verifyhost' => false, 'timeout' => 5]);
        }
        $curl->write($method, $sourceUrl, '1.1', $header, $data);
        $startTime = microtime(true);
        $response = $curl->read();
        $error = $curl->getError();

        if ($this->logApi) {
            $this->log($method, $sourceUrl, $header, $data, $startTime, microtime(true), $response, $error);
        }
        if ($error) {
            throw new LocalizedException(__('System could not connect to remote server.'));
        }
        return $response;
    }

    /**
     * GET
     *
     * @param string $sourceUrl
     * @param array  $data
     * @param array  $header
     * @return string
     * @throws LocalizedException
     */
    protected function get($sourceUrl, $data = [], $header = [])
    {
        $query = empty($data) ? '' : http_build_query($data);
        $sourceUrl .= ($query ? ((strpos($sourceUrl, '?') === false ? '?' : '&') . $query) : '');

        return $this->request(self::METHOD_GET, $sourceUrl, null, $header);
    }

    /**
     * POST
     *
     * @param string            $sourceUrl
     * @param string|array|null $data
     * @param array             $header
     * @return string
     * @throws LocalizedException
     */
    protected function post($sourceUrl, $data = null, $header = [])
    {
        return $this->request(self::METHOD_POST, $sourceUrl, $data, $header);
    }

    /**
     * PUT
     *
     * @param string            $sourceUrl
     * @param string|array|null $data
     * @param array             $header
     * @return string
     * @throws LocalizedException
     */
    protected function put($sourceUrl, $data = null, $header = [])
    {
        return $this->request(self::METHOD_PUT, $sourceUrl, $data, $header);
    }

    /**
     * DELETE
     *
     * @param string            $sourceUrl
     * @param string|array|null $data
     * @param array             $header
     * @return string
     * @throws LocalizedException
     */
    protected function delete($sourceUrl, $data = null, $header = [])
    {
        return $this->request(self::METHOD_DELETE, $sourceUrl, $data, $header);
    }
}
