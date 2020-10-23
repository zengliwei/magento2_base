<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

abstract class AbstractEditAction extends Action
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
     * @param string $resourceMenuName
     * @param string $noEntityMessage
     * @param string $activeMenu
     * @param string $newModelTitle
     * @param string $editModelTitle
     * @return Page|Redirect
     */
    protected function parsePage(
        string $modelName,
        string $resourceMenuName,
        string $noEntityMessage,
        string $activeMenu,
        string $newModelTitle,
        string $editModelTitle
    ) {
        $model = $this->_objectManager->create($modelName);
        $resourceMenu = $this->_objectManager->create($resourceMenuName);

        if (($id = $this->getRequest()->getParam('id'))) {
            $resourceMenu->load($model);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__($noEntityMessage));
                /* @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        /* @var $resultPage Page */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu($activeMenu);
        $resultPage->getConfig()->getTitle()->set($id ? __($editModelTitle, $id) : __($newModelTitle));
        return $resultPage;
    }
}
