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
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
class Logger
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $loggers = [];

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get logger
     *
     * @param string $fileName
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
     * Log
     *
     * @param mixed $data
     * @param string $fileName
     */
    public function log($data, $fileName = 'system.log')
    {
        $this->getLogger('/var/log/' . $fileName)
            ->addDebug((is_array($data) || is_object($data)) ? json_encode($data, true) : $data);
    }
}
