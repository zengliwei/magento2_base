<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;

abstract class AbstractIndexAction extends Action implements HttpGetActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param IndexContext $context
     */
    public function __construct(IndexContext $context)
    {
        parent::__construct($context);

        $this->dataPersistor = $context->getDataPersistor();
    }

    /**
     * @param string $persistKey
     * @param string $activeMenu
     * @param string $pageTitle
     * @return Page|Redirect
     */
    protected function render(
        string $persistKey,
        string $activeMenu,
        string $pageTitle
    ) {
        $this->dataPersistor->clear($persistKey);

        /* @var $resultPage Page */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu($activeMenu);
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
