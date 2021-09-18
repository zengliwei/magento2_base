<?php
/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */

namespace CrazyCat\Base\Controller\Adminhtml;

use Exception;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * @author  Zengliwei <zengliwei@163.com>
 * @url https://github.com/zengliwei/magento2_base
 */
abstract class AbstractEditAction extends AbstractAction implements HttpGetActionInterface
{
    /**
     * Render page
     *
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
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($noEntityMessage));
            /** @var $resultRedirect Redirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        /** @var $resultPage Page */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu($activeMenu);
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? __($editModelTitle, $model->getId()) : __($newModelTitle)
        );
        return $resultPage;
    }
}
