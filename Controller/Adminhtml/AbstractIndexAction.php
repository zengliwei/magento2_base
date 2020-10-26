<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Result\PageFactory;

abstract class AbstractIndexAction extends Action implements HttpGetActionInterface
{
    /**
     * @var DataPersistorInterface|mixed|null
     */
    protected $dataPersistor;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        DataPersistorInterface $dataPersistor,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        parent::__construct($context);

        $this->dataPersistor = $dataPersistor;
        $this->resultPageFactory = $resultPageFactory;
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
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu($activeMenu);
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
