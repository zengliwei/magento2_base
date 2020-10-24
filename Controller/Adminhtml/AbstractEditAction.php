<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

abstract class AbstractEditAction extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param PageFactory $resultPageFactory
     * @param Context     $context
     * @param Registry    $coreRegistry
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Context $context,
        Registry $coreRegistry
    ) {
        parent::__construct($context, $coreRegistry);

        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @param string $modelName
     * @param string $resourceModelName
     * @param string $noEntityMessage
     * @param string $activeMenu
     * @param string $newModelTitle
     * @param string $editModelTitle
     * @return Page|Redirect
     */
    protected function parsePage(
        string $modelName,
        string $resourceModelName,
        string $noEntityMessage,
        string $activeMenu,
        string $newModelTitle,
        string $editModelTitle
    ) {
        try {
            [$model] = $this->loadModel($modelName, $resourceModelName);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($noEntityMessage));
            /* @var $resultRedirect Redirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        /* @var $resultPage Page */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu($activeMenu);
        $resultPage->getConfig()->getTitle()->set(
            $model->getId() ? __($editModelTitle, $model->getId()) : __($newModelTitle)
        );
        return $resultPage;
    }
}
