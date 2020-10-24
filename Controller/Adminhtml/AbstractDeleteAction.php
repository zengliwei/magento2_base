<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;

abstract class AbstractDeleteAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * @param $modelName
     * @param $resourceModelName
     * @param $noEntityMessage
     * @param $successMessage
     * @return ResultInterface
     */
    protected function delete(
        $modelName,
        $resourceModelName,
        $noEntityMessage,
        $successMessage
    ) {
        /* @var $resultRedirect Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            [$model, $resourceModel] = $this->loadModel($modelName, $resourceModelName);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($noEntityMessage));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $resourceModel->delete($model);
            $this->messageManager->addSuccessMessage(__($successMessage));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
