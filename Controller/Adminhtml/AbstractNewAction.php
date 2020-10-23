<?php

namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;

abstract class AbstractNewAction extends Action implements HttpGetActionInterface
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param ForwardFactory $resultForwardFactory
     * @param Context        $context
     * @param Registry       $coreRegistry
     */
    public function __construct(
        ForwardFactory $resultForwardFactory,
        Context $context,
        Registry $coreRegistry
    ) {
        parent::__construct($context, $coreRegistry);

        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /* @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
