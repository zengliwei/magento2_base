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
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;

abstract class AbstractSaveAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param SaveContext $context
     */
    public function __construct(SaveContext $context)
    {
        parent::__construct($context);

        $this->dataPersistor = $context->getDataPersistor();
        $this->filesystem = $context->getFilesystem();
    }

    /**
     * @param $modelName
     * @param $noEntityMessage
     * @param $successMessage
     * @param $persistKey
     * @return ResultInterface
     */
    protected function save(
        $modelName,
        $noEntityMessage,
        $successMessage,
        $persistKey
    ) {
        /* @var $resultRedirect Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $post = $this->getRequest()->getPostValue();
        if ($post) {
            try {
                [$model, $resourceModel] = $this->loadModel($modelName);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__($noEntityMessage));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $model->addData($this->processData($post['data']));
                $resourceModel->save($model);
                $this->messageManager->addSuccessMessage(__($successMessage));
                $this->dataPersistor->clear($persistKey);
                if ($post['back'] == 'close') {
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set($persistKey, $post['data']);
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $data
     * @return array
     */
    protected function processData(array $data): array
    {
        if (isset($data['id']) && !$data['id']) {
            unset($data['id']);
        }
        return $data;
    }
}
