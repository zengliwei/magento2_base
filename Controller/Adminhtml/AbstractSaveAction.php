<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Registry;

abstract class AbstractSaveAction extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param DataPersistorInterface $dataPersistor
     * @param Context                $context
     * @param Registry               $coreRegistry
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        Context $context,
        Registry $coreRegistry
    ) {
        parent::__construct($context, $coreRegistry);

        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function processData(array $data): array
    {
        return $data;
    }

    /**
     * @param $modelName
     * @param $resourceModelName
     * @param $noEntityMessage
     * @param $successMessage
     * @param $persistKey
     * @return ResultInterface
     */
    protected function save(
        $modelName,
        $resourceModelName,
        $noEntityMessage,
        $successMessage,
        $persistKey
    ) {
        /* @var $resultRedirect Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if ($data) {
            /* @var $model AbstractModel */
            $model = $this->_objectManager->create($modelName);
            $resourceModel = $this->_objectManager->create($resourceModelName);

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $resourceModel->load($model->setId($id));
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__($noEntityMessage));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            try {
                $model->setData($this->processData($data));
                $resourceModel->save($model);
                $this->messageManager->addSuccessMessage(__($successMessage));
                $this->dataPersistor->clear($persistKey);
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set($persistKey, $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
