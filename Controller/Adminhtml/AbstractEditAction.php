<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

abstract class AbstractEditAction extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @param string $modelName
     * @param string $noEntityMessage
     * @param string $activeMenu
     * @param string $newModelTitle
     * @param string $editModelTitle
     * @return Page|Redirect
     */
    protected function render(
        string $modelName,
        string $noEntityMessage,
        string $activeMenu,
        string $newModelTitle,
        string $editModelTitle
    ) {
        try {
            [$model] = $this->loadModel($modelName);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($noEntityMessage));
            /* @var $resultRedirect Redirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        /* @var $resultPage Page */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu($activeMenu);
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? __($editModelTitle, $model->getId()) : __($newModelTitle)
        );
        return $resultPage;
    }
}
