<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Helper\Api;

use CrazyCat\Base\Helper\Logger;
use Magento\Framework\HTTP\Adapter\CurlFactory;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Context
{
    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param CurlFactory $curlFactory
     * @param Logger      $logger
     */
    public function __construct(
        CurlFactory $curlFactory,
        Logger $logger
    ) {
        $this->curlFactory = $curlFactory;
        $this->logger = $logger;
    }

    /**
     * Get CURL factory
     *
     * @return CurlFactory
     */
    public function getCurlFactory()
    {
        return $this->curlFactory;
    }

    /**
     * Get logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
