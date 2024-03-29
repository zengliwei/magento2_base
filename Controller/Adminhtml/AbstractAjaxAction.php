<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Controller\Adminhtml;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractAjaxAction extends AbstractAction implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Process result
     *
     * @param array $result
     * @return Json
     */
    protected function processResult($result)
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($result);
    }
}
