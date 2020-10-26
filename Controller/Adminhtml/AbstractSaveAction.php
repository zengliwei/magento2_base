<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

abstract class AbstractSaveAction extends AbstractAction implements HttpPostActionInterface
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
        if (isset($data['id']) && !$data['id']) {
            unset($data['id']);
        }
        return $data;
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
            } catch (\Exception $e) {
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
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set($persistKey, $post);
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
