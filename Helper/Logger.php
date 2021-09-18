<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Helper;

use Magento\Framework\Logger\Handler\Base;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\ObjectManagerInterface;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Logger
{
    private ObjectManagerInterface $objectManager;
    private array $loggers = [];

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param $fileName
     * @return Monolog
     */
    private function getLogger($fileName)
    {
        if (!isset($this->loggers[$fileName])) {
            $this->loggers[$fileName] = $this->objectManager->create(
                Monolog::class,
                ['handlers' => ['debug' => $this->objectManager->create(Base::class, ['fileName' => $fileName])]]
            );
        }
        return $this->loggers[$fileName];
    }

    /**
     * @param mixed  $data
     * @param string $fileName
     */
    public function log($data, $fileName = 'system.log')
    {
        $this->getLogger('/var/log/' . $fileName)->addDebug(print_r($data, true));
    }
}
