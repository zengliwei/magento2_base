<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
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

namespace CrazyCat\Base\Controller\Adminhtml;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @package CrazyCat\Base
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractSaveAction extends AbstractAction implements HttpPostActionInterface
{
    protected DataPersistorInterface $dataPersistor;
    protected Filesystem $filesystem;

    /**
     * Media fields which are need additional processing
     */
    protected array $mediaFields = [];

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
        /** @var $resultRedirect Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $post = $this->getRequest()->getPostValue();
        if ($post) {
            $storeId = $this->getRequest()->getParam('store', 0);

            try {
                /** @var $model AbstractModel */
                /** @var $resourceModel AbstractDb */
                [$model, $resourceModel] = $this->getModels($modelName);
                if (!empty($post['data']['id'])) {
                    $resourceModel->load($model, $post['data']['id']);
                    if (!$model->getId()) {
                        throw new NoSuchEntityException();
                    }
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__($noEntityMessage));
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $model->setStoreId($storeId)
                    ->addData($this->processData($post['data']));
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
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), 'store' => $storeId]);
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
        foreach ($this->mediaFields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = $data[$field][0]['file'];
            } else {
                $data[$field] = null;
            }
        }
        return $data;
    }
}
