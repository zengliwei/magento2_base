<?php


namespace Common\Base\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;

abstract class AbstractAjaxAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param Context     $context
     * @param Registry    $coreRegistry
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        Context $context,
        Registry $coreRegistry
    ) {
        parent::__construct($context, $coreRegistry);

        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @param array $result
     * @return Json
     */
    protected function processResult($result)
    {
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}