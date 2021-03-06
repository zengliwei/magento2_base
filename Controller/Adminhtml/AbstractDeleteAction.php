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

namespace Common\Base\Controller\Adminhtml;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * @package Common\Base
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
        /* @var $resultRedirect Redirect */
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
