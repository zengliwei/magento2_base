<?php
/*
 * Copyright (c) 2020 Zengliwei
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace CrazyCat\Base\Helper\Api;

use CrazyCat\Base\Helper\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\Curl;
use Magento\Framework\HTTP\Adapter\CurlFactory;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractApi
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    protected CurlFactory $curlFactory;
    protected Logger $logger;

    protected bool $isTestMode = false;
    protected bool $logApi = false;
    protected string $logFile = 'api.log';

    public function __construct(Context $context)
    {
        $this->curlFactory = $context->getCurlFactory();
        $this->logger = $context->getLogger();
    }

    /**
     * @param string            $method
     * @param string            $targetUrl
     * @param array             $requestHeader
     * @param string|array|null $requestBody
     * @param int               $startTime
     * @param int               $endTime
     * @param string            $response
     * @param                   $error
     */
    protected function log($method, $targetUrl, $requestHeader, $requestBody, $startTime, $endTime, $response, $error)
    {
        $this->logger->log(
            sprintf(
                "%s %s\nExecuted: %s ms\nRequest Header: %s\nRequest Body: %s\n%s",
                $method,
                $targetUrl,
                round(($endTime - $startTime) * 1000),
                print_r($requestHeader, true),
                print_r($requestBody, true),
                $error ? sprintf('Error: %s', $error) : sprintf('Response: %s', $response)
            ),
            $this->logFile
        );
    }

    /**
     * @param string            $method
     * @param string            $sourceUrl
     * @param string|array|null $data
     * @param array             $header
     * @return string
     * @throws LocalizedException
     */
    protected function request($method, $sourceUrl, $data = null, $header = [])
    {
        /* @var $curl Curl */
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
