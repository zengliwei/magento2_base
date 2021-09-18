<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Controller\Adminhtml;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractDeleteAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * @param $modelName
     * @param $noEntityMessage
     * @param $successMessage
     * @return ResultInterface
     */
    protected function delete(
        $modelName,
        $noEntityMessage,
        $successMessage
    ) {
        /** @var $resultRedirect Redirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            [$model, $resourceModel] = $this->loadModel($modelName);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($noEntityMessage));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $resourceModel->delete($model);
            $this->messageManager->addSuccessMessage(__($successMessage));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
