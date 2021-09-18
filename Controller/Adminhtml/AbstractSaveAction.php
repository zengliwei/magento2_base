<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
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
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
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
     * Media fields which are need additional processing
     *
     * @var array
     */
    protected $mediaFields = [];

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
     * Save given model with posted data
     *
     * @param string $modelName
     * @param string $noEntityMessage
     * @param string $successMessage
     * @param string $persistKey
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
     * Process data
     *
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
